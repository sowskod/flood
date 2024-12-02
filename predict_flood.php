<?php
// Connect to database
include 'db.php';

// Function to call the Python script and get prediction
function predictFloodRisk($brgy_name, $wind_speed, $rainfall, $temperature) {
    // Fetch the elevation from the database
    global $con;
    $query = "SELECT elevation FROM barangays WHERE brgy_name = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("s", $brgy_name);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $elevation = $row['elevation'];

    // Prepare the command to run the Python script with the barangay name, wind speed, rainfall, temperature, and elevation
    $command = escapeshellcmd("python flood_predict.py $wind_speed $rainfall $temperature $elevation");

    // Execute the command and capture the output (prediction)
    $output = shell_exec($command);

    // Return the output (flood prediction)
    return trim($output);
}

$flood_risk = [];
$wind_speed = "";
$rain = "";
$temperature = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve inputs for wind speed, rainfall, and temperature
    $wind_speed = $_POST['wind_speed'];
    $rain = $_POST['rain'];
    $temperature = $_POST['temperature'];

    // Fetch all barangays for display
    $brgy_query = $con->query("SELECT id, brgy_name FROM barangays");
    $barangays = $brgy_query->fetch_all(MYSQLI_ASSOC);

    // Get flood risk prediction for each barangay
    foreach ($barangays as $row) {
        $brgy_name = $row['brgy_name'];
        $flood_prediction = predictFloodRisk($brgy_name, $wind_speed, $rain, $temperature);
        $flood_risk[$brgy_name] = $flood_prediction;
    }

    // Divide barangays into two halves for two tables
    $barangay_count = count($barangays);
    $first_half = array_slice($barangays, 0, ceil($barangay_count / 2));
    $second_half = array_slice($barangays, ceil($barangay_count / 2));
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

        .tables-container {
            display: flex;
            justify-content: space-between;
            width: 100%;
            
            margin-top: 20px;
            gap: 10px;
        }

        .risk-table {
            width: 48%;
            border-collapse: collapse;
            text-align: left;
        }

        .risk-table th, .risk-table td {
            padding: 12px;
            border: 1px solid #ccc;
        }

        .high-risk {
            color: red;
            font-weight: bold;
        }

        .medium-risk {
            color: orange;
            font-weight: bold;
        }

        .low-risk {
            color: green;
            font-weight: bold;
        }
        h3, h2 {
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
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: none; /* Hidden by default */
            justify-content: center;
            align-items: center;
            color: white;
            font-size: 1.5rem;
            z-index: 9999;
        }

        .loading-overlay.active {
            display: flex; /* Show overlay when active */
        }

        /* Spinner style */
        .spinner {
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid white;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin-right: 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .metrics {
            display: none; /* Hidden by default */
            margin-top: 20px;
            background-color: #f1f8e9;
            padding: 15px;
            border-radius: 10px;
            text-align: left;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .metrics h3 {
            color: #1e88e5;
            margin-bottom: 10px;
        }

        .metrics p {
            margin: 5px 0;
        }

.metrics.show {
    display: block;
    opacity: 1;
}

    </style>
</head>

<body>
<a href="dashboard.php" style="position: absolute; top: 20px; left: 40px; text-decoration: none; color: black;">
        <svg width="54" height="74" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="12" cy="12" r="10" fill="#F7F7F7" stroke="black" stroke-width="2" />
            <path d="M8 12H16M8 12L12 8M8 12L12 16" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
    </a>
    <div class="container">
        <h1>Flood Prediction Result</h1>
        <h3>using random forest</h3>
        <form id="floodForm" action="predict_flood.php" method="POST">
            <label for="wind_speed">Wind Speed (km/h)</label>
            <input type="number" id="wind_speed" name="wind_speed" required>
            <label for="rain">Rainfall (mm/h)</label>
            <input type="number" id="rain" name="rain" required>
            <label for="temperature">Temperature (°C)</label>
            <input type="number" id="temperature" name="temperature" required>
            <button type="submit">Predict Flood Risk</button>
        </form>
        <br><br>
        <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($flood_risk)): ?>
            <h2>Flood Prediction for All Barangays</h2>
            <div class="tables-container">
                <!-- First half table -->
                <table class="risk-table">
                    <thead>
                        <tr>
                            <th>Barangay</th>
                            <th>Risk Level</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($first_half as $row): ?>
                            <?php
                                $risk_level = $flood_risk[$row['brgy_name']];
                                $risk_class = ($risk_level == "High Risk") ? 'high-risk' : (($risk_level == "Medium Risk") ? 'medium-risk' : 'low-risk');
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['brgy_name']); ?></td>
                                <td class="<?php echo $risk_class; ?>">
                                    <?php echo htmlspecialchars($risk_level); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Second half table -->
                <table class="risk-table">
                    <thead>
                        <tr>
                            <th>Barangay</th>
                            <th>Risk Level</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($second_half as $row): ?>
                            <?php
                                $risk_level = $flood_risk[$row['brgy_name']];
                                $risk_class = ($risk_level == "High Risk") ? 'high-risk' : (($risk_level == "Medium Risk") ? 'medium-risk' : 'low-risk');
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['brgy_name']); ?></td>
                                <td class="<?php echo $risk_class; ?>">
                                    <?php echo htmlspecialchars($risk_level); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Add the metrics section here -->
    <div class="container">
        <h1>Flood Prediction Metrics</h1>
        <button id="showMetricsBtn">Show Metrics</button>

        <!-- Metrics Section -->
        <div class="metrics" id="metricsSection">
        <h3>Numeric Evaluation Metrics:</h3>
        <p><strong>Mean Absolute Error (MAE): 0.26</strong> - Measures the average magnitude of errors between predicted and actual values, without considering their direction. A lower value indicates better accuracy.</p>
        <p><strong>Mean Squared Error (MSE): 0.26</strong> - Similar to MAE but squares the errors before averaging them, giving more weight to larger errors. Lower is better.</p>
        <p><strong>Root Mean Squared Error (RMSE): 0.28</strong> - The square root of MSE, representing the error in the same units as the predicted values. Smaller RMSE values indicate better model performance.</p>
        <p><strong>Explained Variance Score (EVS): 0.69</strong> - Indicates how much variance in the target variable is explained by the model. Values closer to 1 are better.</p>
        <p><strong>R² Score: 0.72</strong> - Represents the proportion of the variance in the target variable that is predictable from the features. Values closer to 1 indicate a better fit.</p>

        <h3>Classification Metrics:</h3>
        <p><strong>Accuracy: 0.74</strong> - The ratio of correctly predicted instances to the total instances. A higher accuracy means better performance.</p>
        <p><strong>Precision (macro): 0.76</strong> - The ability of the model to correctly identify positive cases while avoiding false positives. Calculated as the average precision across all classes.</p>
        <p><strong>Recall (macro): 0.76</strong> - The ability of the model to identify all relevant instances of a class. It is the average recall across all classes.</p>
        <p><strong>F1 Score (macro): 0.74</strong> - The harmonic mean of precision and recall. A higher F1 score indicates a better balance between precision and recall.</p>
    </div>
    </div>
</body>

</html>
<script>
    // JavaScript to toggle the metrics display and change button text
document.addEventListener('DOMContentLoaded', function () {
    const showMetricsBtn = document.getElementById('showMetricsBtn');
    const metricsSection = document.getElementById('metricsSection');

    showMetricsBtn.addEventListener('click', function () {
        // Toggle the display of the metrics section
        if (metricsSection.style.display === 'none' || metricsSection.style.display === '') {
            metricsSection.style.display = 'block'; // Show the metrics
            showMetricsBtn.textContent = 'Hide Metrics'; // Change button text to "Hide Metrics"
        } else {
            metricsSection.style.display = 'none'; // Hide the metrics
            showMetricsBtn.textContent = 'Show Metrics'; // Change button text to "Show Metrics"
        }
    });
});

</script>
