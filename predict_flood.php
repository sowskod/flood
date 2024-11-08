<?php
// Connect to database
include 'db.php';

// Function to call the Python script and get prediction
function predictFloodRisk($brgy_name, $wind_speed, $rainfall, $temperature) {
    // Prepare the command to run the Python script with the barangay name, wind speed, rainfall, and temperature
    $command = escapeshellcmd("python flood_predict.py $brgy_name $wind_speed $rainfall $temperature");

    // Execute the command and capture the output (prediction)
    $output = shell_exec($command);

    // Return the output (flood prediction)
    return trim($output);
}

$flood_risk = [];
$wind_speed = "";
$rain = "";
$temperature = "";  // Initialize temperature variable

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve inputs for wind speed, rainfall, and temperature
    $wind_speed = $_POST['wind_speed'];
    $rain = $_POST['rain'];
    $temperature = $_POST['temperature'];  // Fetch the temperature from the form

    // Fetch all barangays for display
    $brgy_query = $con->query("SELECT id, brgy_name FROM barangays");
    $barangays = $brgy_query->fetch_all(MYSQLI_ASSOC);

    // Get flood risk prediction for each barangay
    foreach ($barangays as $row) {
        $brgy_name = $row['brgy_name']; // Use barangay name for prediction
        $flood_prediction = predictFloodRisk($brgy_name, $wind_speed, $rain, $temperature);

        // Store the result in the $flood_risk array
        $flood_risk[$brgy_name] = $flood_prediction;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flood Prediction Result</title>
    <style>
        body {
            background: linear-gradient(to bottom, #b3e5fc, #c8e6c9);
            font-family: Arial, sans-serif;
            color: #333;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            margin-top: 60px;
            width: 90%;
            max-width: 600px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 20px;
            color: #333;
        }

        label {
            display: block;
            font-size: 1rem;
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }

        input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
        }

        button {
            background-color: #1e88e5;
            color: white;
            font-size: 1rem;
            font-weight: bold;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #1565c0;
        }

        .back-link {
            position: absolute;
            top: 20px;
            left: 40px;
            text-decoration: none;
            color: black;
        }

        .risk-list {
            background: #e0f7fa;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            margin-top: 20px;
            width: 100%;
            max-width: 800px;
            list-style: none;
        }

        .risk-list li {
            margin: 5px 0;
            font-size: 1rem;
            color: #333;
        }
        h3 {
            font-size: 1.25rem;
            font-weight: 500;
            color: #1e88e5;
            margin-top: -10px;
            text-transform: uppercase;
            letter-spacing: 2px;
            padding: 8px 0;
            border-bottom: 2px solid #1e88e5;
            width: 100%;
            text-align: center;
            background: #f1f8e9;
        }
    </style>
</head>

<body>
    <!-- Arrow Link to Homepage -->
    <a href="dashboard.php" style="position: absolute; top: 20px; left: 40px; text-decoration: none; color: black;">
        <svg width="54" height="74" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="12" cy="12" r="10" fill="#F7F7F7" stroke="black" stroke-width="2" />
            <path d="M8 12H16M8 12L12 8M8 12L12 16" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
    </a>
    <div class="container">
        <h1>Flood Prediction Result</h1>
        <h3>using random forest</h3>
        <form action="predict_flood.php" method="POST">
            <label for="wind_speed">Wind Speed (km/h)</label>
            <input type="number" id="wind_speed" name="wind_speed" required>
            <label for="rain">Rainfall (mm)</label>
            <input type="number" id="rain" name="rain" required>
            <label for="temperature">Temperature (°C)</label> <!-- Added temperature input -->
            <input type="number" id="temperature" name="temperature" required>
            <button type="submit">Predict Flood Risk</button>
        </form>

        <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($flood_risk)): ?>
            <div class="risk-list">
                <h2>Flood Prediction for All Barangays</h2>
                <p>Wind Speed: <?php echo htmlspecialchars($wind_speed); ?> km/h</p>
                <p>Rainfall: <?php echo htmlspecialchars($rain); ?> mm</p>
                <p>Temperature: <?php echo htmlspecialchars($temperature); ?> °C</p> <!-- Display temperature -->
                <h3>Flood Risk Level for Barangays:</h3>
                <ul>
                    <?php foreach ($barangays as $row): ?>
                        <li>
                            <?php echo htmlspecialchars($row['brgy_name']); ?>: 
                            <?php 
                            // Display the flood risk level for the barangay
                            echo isset($flood_risk[$row['brgy_name']]) ? htmlspecialchars($flood_risk[$row['brgy_name']]) : "No data"; 
                            ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>