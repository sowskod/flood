<?php
require 'vendor/autoload.php'; // Include Composer's autoloader
include 'db.php'; // Connect to the database
require_once('tcpdf/tcpdf.php');

// Check if brgy_id and chartImage are received
if (isset($_GET['brgy_id']) && is_numeric($_GET['brgy_id']) && isset($_POST['chartImage'])) {
    $brgy_id = $_GET['brgy_id'];
    $chartImage = $_POST['chartImage'];

    // Fetch barangay data from the database
    $brgy_query = $con->prepare("SELECT * FROM barangays WHERE id = ?");
    $brgy_query->bind_param("i", $brgy_id);
    $brgy_query->execute();
    $brgy_result = $brgy_query->get_result();
    $brgy = $brgy_result->fetch_assoc();

    if (!$brgy) {
        die('Barangay data not found.');
    }

    // Decode base64 image
    $chartImage = str_replace('data:image/png;base64,', '', $chartImage);
    $chartImage = base64_decode($chartImage);

    // Save the image temporarily
    $tempImagePath = 'chart_image.png';
    file_put_contents($tempImagePath, $chartImage);

    // Create PDF
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false); // Portrait orientation, A4 size
    $pdf->AddPage();

    // Set PDF metadata
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Barangay System');
    $pdf->SetTitle('Flood History Chart');
    $pdf->SetSubject('Flood History Report');

    // Add Title
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, 'Flood History Chart', 0, 1, 'C'); // Title centered

    // Add the chart image to the PDF
    // Adjust image size and position to ensure everything fits on one page
    $pdf->Image($tempImagePath, 10, 30, 190, 100, 'PNG'); // Positioning image

    // Add a footer with barangay information
    $pdf->SetY(-20);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 10, 'Flood history data for barangay: ' . htmlspecialchars($brgy['brgy_name']), 0, 0, 'C');

    // Output the PDF
    $pdf->Output('flood_history_chart.pdf', 'I');

    // Clean up temporary image file
    unlink($tempImagePath);

} else {
    echo 'Barangay ID or chart image not received.';
}
?>
