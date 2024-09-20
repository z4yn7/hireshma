<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load Composer's autoloader
require 'vendor/autoload.php'; // Ensure this path points to your project's vendor folder

// Database connection
$conn = new mysqli('localhost', 'root', '', 'irfan_db'); // Update with your database credentials

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if ticket_id is passed via the URL
if (!isset($_GET['ticket_id']) || empty($_GET['ticket_id'])) {
    die("Ticket ID not provided.");
}

// Fetch ticket data based on ticket ID
$ticket_id = (int)$_GET['ticket_id'];  // Ensure ticket_id is sanitized and cast as an integer
$sql = "SELECT * FROM tickets WHERE ticket_id = $ticket_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $ticket = $result->fetch_assoc();

    // Create instance of FPDI
    $pdf = new setasign\Fpdi\Fpdi();

    // Set the file path to your pre-made Canva PDF
    $filePath = 'C:/xampp/htdocs/pdf ticket/ticket.pdf'; // Make sure this file path is correct

    // Check if the PDF template exists
    if (!file_exists($filePath)) {
        die("PDF template not found.");
    }

    // Import the first page of the PDF
    $pageCount = $pdf->setSourceFile($filePath);
    $templateId = $pdf->importPage(1);
    $pdf->AddPage();
    $pdf->useTemplate($templateId);

    // Set font for the text overlay
    $pdf->SetFont('Arial', '', 12);

    // Add data from the database to the PDF (adjust coordinates as per your design)
    $pdf->SetXY(50, 70);
    $pdf->Write(10, 'Passenger Name: ' . $ticket['passenger_name']);
    
    $pdf->SetXY(50, 80);
    $pdf->Write(10, 'Flight Number: ' . $ticket['flight_number']);
    
    $pdf->SetXY(50, 90);
    $pdf->Write(10, 'Departure Date: ' . $ticket['departure_date']);
    
    $pdf->SetXY(50, 100);
    $pdf->Write(10, 'Seat Number: ' . $ticket['seat_number']);
    
    $pdf->SetXY(50, 110);
    $pdf->Write(10, 'Boarding Gate: ' . $ticket['boarding_gate']);
    
    $pdf->SetXY(50, 120);
    $pdf->Write(10, 'Boarding Time: ' . $ticket['boarding_time']);

    // Output the generated PDF to a file
    $pdfFilePath = 'C:/xampp/htdocs/pdf ticket/generated_ticket.pdf'; // Path to save the generated PDF
    $pdf->Output('F', $pdfFilePath); // Save the PDF to the specified path

    // Display buttons for actions
    echo '<button onclick="window.print()">Print Ticket</button>';
    echo '<a href="' . $pdfFilePath . '" download>Download Ticket</a>';
    
} else {
    // No ticket found with the provided ticket_id
    echo "Ticket not found.";
}

// Close the database connection
$conn->close();
?>
