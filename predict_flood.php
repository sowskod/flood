<?php
// Connect to database
include 'db.php';

// Handle flood prediction for all barangays
$flood_risk = [];
$wind_speed = "";
$rain = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $wind_speed = $_POST['wind_speed'];
    $rain = $_POST['rain'];

    // Fetch all barangays for display
    $brgy_query = $con->query("SELECT id, brgy_name FROM barangays");
    $barangays = $brgy_query->fetch_all(MYSQLI_ASSOC);

    foreach ($barangays as $row) {
        // Count flood records for each barangay
        $brgy_id = $row['id']; // Assuming there's a brgy_id field
        $flood_count_query = $con->query("SELECT COUNT(*) as flood_count FROM flood_data WHERE id = $brgy_id");
        $flood_count_result = $flood_count_query->fetch_assoc();
        $flood_count = $flood_count_result['flood_count'];

        // Use the correct variable to get the barangay name
    $brgy_name = $row['brgy_name']; // Define brgy_name

       // Basic prediction logic based on rainfall, wind speed, and historical data
       if ($flood_count > 5 || $rain > 100 || $wind_speed > 75) {
        $flood_risk[$brgy_name] = "High Risk";
    } elseif ($flood_count > 2 || $rain > 50 || $wind_speed > 25) {
        $flood_risk[$brgy_name] = "Medium Risk";
    } else {
        $flood_risk[$brgy_name] = "Low Risk";
    }
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flood Prediction Result</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-b from-blue-200 to-green-200">
       <!-- Arrow Link to Homepage -->
       <a href="dashboard.php" style="position: absolute; top: 20px; left: 40px; text-decoration: none; color: black;">
        <svg width="54" height="74" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="12" cy="12" r="10" fill="#F7F7F7" stroke="black" stroke-width="2"/>
            <path d="M8 12H16M8 12L12 8M8 12L12 16" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </a>
    <div class="flex flex-col items-center justify-center min-h-screen">
        <h1 class="text-3xl font-bold mb-6">Flood Prediction Result</h1>

        <form action="predict_flood.php" method="POST" class="bg-white p-6 rounded-lg shadow-lg mb-8">
            <div class="mb-4">
                <label for="wind_speed" class="block text-lg font-medium">Wind Speed (km/h)</label>
                <input type="number" id="wind_speed" name="wind_speed" required class="mt-2 p-2 border border-gray-300 rounded-lg w-full">
            </div>
            <div class="mb-4">
                <label for="rain" class="block text-lg font-medium">Rainfall (mm)</label>
                <input type="number" id="rain" name="rain" required class="mt-2 p-2 border border-gray-300 rounded-lg w-full">
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Predict Flood Risk</button>
        </form>

        <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($flood_risk)): ?>
            <div class="bg-gradient-to-b from-blue-200 to-green-200 p-6 rounded-lg shadow-lg text-center">
                <h2 class="text-xl font-bold mb-4">Flood Prediction for All Barangays</h2>
                <p class="text-lg">Wind Speed: <?php echo htmlspecialchars($wind_speed); ?> km/h</p>
                <p class="text-lg">Rainfall: <?php echo htmlspecialchars($rain); ?> mm</p>
                <h3 class="text-lg font-bold mt-4">Flood Risks:</h3>
            </div>

            <h2 class="text-xl font-bold mb-4 mt-6">Affected Barangays:</h2>
            <ul class="bg-gradient-to-b from-blue-200 to-green-200 p-4 rounded-lg shadow-lg w-full">
                <?php foreach ($barangays as $row): ?>
                    <li class="text-lg mb-2">
                        <?php echo htmlspecialchars($row['brgy_name']); ?>: 
                        <?php echo isset($flood_risk[$row['brgy_name']]) ? htmlspecialchars($flood_risk[$row['brgy_name']]) : "No data"; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</body>
</html>
