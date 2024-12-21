<?php
require 'vendor/autoload.php'; // Include PhpSpreadsheet library
require 'db.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if (isset($_POST['upload_excel']) && isset($_FILES['flood_excel'])) {
    $brgy_id = $_GET['brgy_id'];
    $file = $_FILES['flood_excel']['tmp_name'];

    try {
        $spreadsheet = IOFactory::load($file);
        $sheetData = $spreadsheet->getActiveSheet()->toArray();

        foreach ($sheetData as $row) {
            // Assume the Excel has columns: Flood Date and Flood Level
            $flood_date = $row[0]; // Replace with column index for Flood Date
            $flood_level = $row[1]; // Replace with column index for Flood Level

            // Sanitize and validate data
            $flood_date = date('Y-m-d', strtotime($flood_date));
            $flood_level = htmlspecialchars($flood_level);

            // Insert into database
            $sql = "INSERT INTO flood_data (brgy_id, flood_date, flood_level) VALUES (?, ?, ?)";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("iss", $brgy_id, $flood_date, $flood_level);
            $stmt->execute();
        }

        echo "Flood data uploaded successfully.";
    } catch (Exception $e) {
        echo "Error uploading file: " . $e->getMessage();
    }
} else {
    echo "No file uploaded.";
}
?>
