<?php
// save_chart.php
$data = json_decode(file_get_contents('php://input'), true);
if (isset($data['img'])) {
    // Remove the header from the image data
    list($type, $data) = explode(';', $data['img']);
    list(, $data) = explode(',', $data);
    $data = base64_decode($data);
    
    // Save the image
    file_put_contents('chart_image.png', $data);
}
?>
