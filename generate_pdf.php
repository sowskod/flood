<?php
require 'vendor/autoload.php'; // Include Composer's autoloader
include 'db.php'; // Connect to the database
require_once('tcpdf/tcpdf.php');

// Create a new PDF instance
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Get the barangay ID from the URL
$brgy_id = $_GET['brgy_id'];

// Fetch barangay data
$brgy_query = $con->prepare("SELECT * FROM barangays WHERE id = ?");
$brgy_query->bind_param("i", $brgy_id);
$brgy_query->execute();
$brgy_result = $brgy_query->get_result();
$brgy = $brgy_result->fetch_assoc();

// Fetch flood data for the barangay
$flood_query = $con->prepare("SELECT * FROM flood_data WHERE brgy_id = ? ORDER BY flood_date ASC");
$flood_query->bind_param("i", $brgy_id);
$flood_query->execute();
$flood_result = $flood_query->get_result();

$flood_data = [];
while ($flood = $flood_result->fetch_assoc()) {
    $flood_data[] = $flood; // Collect all flood data
}

// Check if we have flood data
if (empty($flood_data)) {
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 10, 'No flood data available for this barangay.', 0, 1, 'C');
} else {
    // Generate the chart image from flood data
    $chartImagePath = generateFloodGraph($flood_data);

    // Generate the PDF content
    $pdf->AddPage();
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, 'Flood History Graph for ' . htmlspecialchars($brgy['brgy_name']), 0, 1, 'C');

    // Add the chart image to the PDF
    $pdf->Image($chartImagePath, 15, 40, 180, 100, 'PNG', '', '', false, 300, '', false, false, 0, false, false, false);

    // Add the legend
    $pdf->Ln(10);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Legend:', 0, 1);
    $pdf->SetFont('helvetica', '', 11);
    addLegend($pdf);
}

// Output the PDF
$pdf->Output('flood_history_' . $brgy['brgy_name'] . '.pdf', 'I');

// Function to generate the chart image from flood data
function generateFloodGraph($flood_data) {
    // Create the image resource
    $width = 600;
    $height = 500;
    $image = imagecreatetruecolor($width, $height);

    // Define colors
    $backgroundColor = imagecolorallocate($image, 255, 255, 255); // White background
    $barColorHigh = imagecolorallocate($image, 255, 99, 132); // Red
    $barColorMedium = imagecolorallocate($image, 255, 206, 86); // Yellow
    $barColorLow = imagecolorallocate($image, 54, 162, 235); // Blue
    $barColorNormal = imagecolorallocate($image, 75, 192, 192); // Green
    $textColor = imagecolorallocate($image, 0, 0, 0); // Black text

    // Fill background
    imagefilledrectangle($image, 0, 0, $width, $height, $backgroundColor);

    // Extract flood levels and prepare bar data
    $floodLevels = array_map(function($flood) {
        return $flood['flood_level'];
    }, $flood_data);

    $floodDates = array_map(function($flood) {
        return date('Y-m-d', strtotime($flood['flood_date'])); // Format the date
    }, $flood_data);

    // Calculate bar width
    $barWidth = $width / count($floodLevels);
    
    // Draw bars and labels
    foreach ($floodLevels as $index => $level) {
        $barHeight = ($level == 'High') ? 300 : (($level == 'Medium') ? 200 : (($level == 'Low') ? 100 : 50));
        $color = match($level) {
            'High' => $barColorHigh,
            'Medium' => $barColorMedium,
            'Low' => $barColorLow,
            'Normal' => $barColorNormal,
            default => $backgroundColor, // Default to background
        };
        
        // Draw the bar
        imagefilledrectangle($image, $index * $barWidth, $height - $barHeight, ($index + 1) * $barWidth - 1, $height, $color);
        
        // Add the flood date label below the bar
        $labelX = $index * $barWidth + ($barWidth / 2) - (strlen($floodDates[$index]) * 3); // Center the label
        imagestring($image, 3, $labelX, $height - 20, $floodDates[$index], $textColor);
    }

    // Save the image to a file
    $chartImagePath = 'chart_image.png';
    imagepng($image, $chartImagePath);
    imagedestroy($image);

    return $chartImagePath; // Return the path to the generated image
}

// Function to add legend to the PDF
function addLegend($pdf) {
    $pdf->Cell(20, 10, '', 0, 0); // Add indentation
    $pdf->SetFillColor(255, 99, 132);
    $pdf->Cell(10, 10, '', 0, 0, 'C', 1);
    $pdf->Cell(0, 10, 'High', 0, 1);

    $pdf->Cell(20, 10, '', 0, 0); // Add indentation
    $pdf->SetFillColor(255, 206, 86);
    $pdf->Cell(10, 10, '', 0, 0, 'C', 1);
    $pdf->Cell(0, 10, 'Medium', 0, 1);

    $pdf->Cell(20, 10, '', 0, 0); // Add indentation
    $pdf->SetFillColor(54, 162, 235);
    $pdf->Cell(10, 10, '', 0, 0, 'C', 1);
    $pdf->Cell(0, 10, 'Low', 0, 1);

    $pdf->Cell(20, 10, '', 0, 0); // Add indentation
    $pdf->SetFillColor(75, 192, 192);
    $pdf->Cell(10, 10, '', 0, 0, 'C', 1);
    $pdf->Cell(0, 10, 'Normal', 0, 1);
}
?>
