<?php
// Connect to database
include 'db.php';
$brgy_id = $_GET['brgy_id'];

// Fetch barangay data
$brgy_query = $con->prepare("SELECT * FROM barangays WHERE id = ?");
$brgy_query->bind_param("i", $brgy_id);
$brgy_query->execute();
$brgy_result = $brgy_query->get_result();
$brgy = $brgy_result->fetch_assoc();

// Handle flood data submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_flood'])) {
    $flood_date = $_POST['flood_date'];
    $flood_level = $_POST['flood_level'] ?? null;

    if (!empty($flood_level)) {
        // Insert flood data for the specific barangay
        $stmt = $con->prepare("INSERT INTO flood_data (brgy_id, flood_date, flood_level) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $brgy_id, $flood_date, $flood_level);

        if ($stmt->execute()) {
            echo "<p></p>";
        } else {
            echo "<p>Error: " . $stmt->error . "</p>";
        }
        $stmt->close();
    } else {
        echo "<p>Error: Please select a valid flood level.</p>";
    }
}

// Fetch flood data for the barangay// Check if brgy_id is set and valid
if (isset($brgy_id) && is_numeric($brgy_id)) {
    // Prepare the query
    $flood_query = $con->prepare("SELECT * FROM flood_data WHERE brgy_id = ? ORDER BY flood_date ASC");
    $flood_query->bind_param("i", $brgy_id);
    $flood_query->execute();
    $flood_result = $flood_query->get_result();
} else {
    die('Invalid brgy_id value: ' . htmlspecialchars($brgy_id));
}

$flood_result = $con->query("SELECT * FROM flood_data WHERE brgy_id = $brgy_id ORDER BY flood_date ASC");

$flood_data = [];
while ($flood = $flood_result->fetch_assoc()) {
    $flood_data[] = $flood; // Collect flood data
}

// Calculate flood intervals and prediction
$flood_dates = array_column($flood_data, 'flood_date');
$flood_intervals = [];

// Calculate intervals
for ($i = 1; $i < count($flood_dates); $i++) {
    $date1 = new DateTime($flood_dates[$i - 1]);
    $date2 = new DateTime($flood_dates[$i]);
    $interval = $date1->diff($date2);
    $flood_intervals[] = $interval->days;
}

// Simple prediction
$next_flood_prediction = null;
$average_interval = null;
$last_flood_date = null;

if (count($flood_intervals) > 0) {
    $average_interval = array_sum($flood_intervals) / count($flood_intervals);
    $last_flood_date = end($flood_dates);

    $next_flood_date = new DateTime($last_flood_date);

    // Round the interval to the nearest whole number
    $rounded_interval = round($average_interval);

    // Modify the date using the rounded interval
    $next_flood_date->modify("+{$rounded_interval} days");

    $next_flood_prediction = $next_flood_date->format('Y-m-d');
}

if ($average_interval && $last_flood_date) {
    $whole_days = floor($average_interval);
    $fractional_days = $average_interval - $whole_days;
    $hours = round($fractional_days * 24);

    $next_flood_date = new DateTime($last_flood_date);
    $next_flood_date->modify("+{$whole_days} days");
    $next_flood_date->modify("+{$hours} hours");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Details</title>
    <style>
        body {
            background: linear-gradient(to bottom, #cce7ff, #b2f0e6);
            font-family: Arial, sans-serif;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 2rem auto;
            text-align: center;
        }
        .heading {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }
        .form-container, .history-container, .prediction-container {
            background-color: #fff;
            padding: 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        input, select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ccc;
            border-radius: 0.25rem;
            margin-bottom: 1rem;
        }
        button {
            background-color: #4a90e2;
            color: #fff;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            cursor: pointer;
        }
        button:hover {
            background-color: #357ab8;
        }
        .back-link {
            position: absolute;
            top: 20px;
            left: 20px;
            text-decoration: none;
            color: #333;
            font-weight: bold;
        }
        .link {
            text-decoration: none;
            color: #4a90e2;
        }
        .legend {
            display: flex;
            justify-content: center;
            margin-top: 1rem;
        }
        .legend-item {
            display: flex;
            align-items: center;
            margin-right: 1rem;
        }
        .legend-color {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            margin-right: 0.5rem;
        }
        .high { background-color: rgba(255, 99, 132, 0.8); }
        .medium { background-color: rgba(255, 206, 86, 0.8); }
        .low { background-color: rgba(54, 162, 235, 0.8); }
        .normal { background-color: rgba(75, 192, 192, 0.8); }
    </style>
</head>
<body>
    <a href="dashboard.php" class="back-link">&larr; Back to Dashboard</a>

    <div class="container">
        <h1 class="heading">Barangay: <?php echo htmlspecialchars($brgy['brgy_name']); ?></h1>
        
        <div class="form-container">
            <form action="brgy_details.php?brgy_id=<?php echo $brgy_id; ?>" method="POST">
                <label for="flood_date">Flood Date</label>
                <input type="date" id="flood_date" name="flood_date" required readonly value="<?php
                    $date = new DateTime('now', new DateTimeZone('Asia/Manila'));
                    echo $date->format('Y-m-d');
                ?>">
                
                <label for="flood_level">Flood Level</label>
                <select id="flood_level" name="flood_level" required>
                    <option value="">-- Select Flood Level --</option>
                    <option value="Normal">Normal</option>
                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                </select>
                
                <button type="submit" name="add_flood">Add Flood Data</button>
            </form>
        </div>
        <div class="form-container">
        <!-- Form to upload Excel file -->
        <form action="upload_excel.php?brgy_id=<?php echo $brgy_id; ?>" method="POST" enctype="multipart/form-data">
            <label for="flood_excel">Upload Excel File</label>
            <input type="file" id="flood_excel" name="flood_excel" accept=".xls,.xlsx" required>
            
            <button type="submit" name="upload_excel">Upload Flood Data</button>
        </form>
    </div>

        <div class="history-container">
            <h2>Flood History</h2>
            <canvas id="floodHistoryChart" width="400" height="200"></canvas>
            <div class="legend">
                <div class="legend-item"><div class="legend-color high"></div>High</div>
                <div class="legend-item"><div class="legend-color medium"></div>Medium</div>
                <div class="legend-item"><div class="legend-color low"></div>Low</div>
                <div class="legend-item"><div class="legend-color normal"></div>Normal</div>
            </div>
            <br><br><br>
            <div>
                <a href="flood_history.php?brgy_id=<?php echo $brgy_id; ?>" class="link">View Full Flood History</a>
                
                <button onclick="generatePDF()">Generate PDF</button>
            </div>
        </div>

        <div class="prediction-container">
            <h2>Flood Prediction</h2>
            <?php if ($next_flood_prediction): ?>
                <p>Next predicted flood using historical data is: <strong><?php echo $next_flood_prediction; ?></strong></p>
            <?php else: ?>
                <p>Not enough data to make a prediction.</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    const floodData = <?php echo json_encode($flood_data); ?>;
    const floodDates = floodData.map(flood => flood.flood_date);
    const floodLevels = floodData.map(flood => flood.flood_level);
    const floodColors = floodLevels.map(level => {
        switch(level) {
            case 'High': return 'rgba(255, 99, 132, 0.8)';
            case 'Medium': return 'rgba(255, 206, 86, 0.8)';
            case 'Low': return 'rgba(54, 162, 235, 0.8)';
            case 'Normal': return 'rgba(75, 192, 192, 0.8)';
            default: return 'rgba(75, 192, 192, 0.2)';
        }
    });

    const ctx = document.getElementById('floodHistoryChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: floodDates,
            datasets: [{
                label: 'Flood Occurrences',
                data: floodDates.map(() => 1),
                backgroundColor: floodColors,
                borderColor: floodColors.map(color => color.replace('0.8', '1')),
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            },
            plugins: {
                legend: { display: true }
            }
        }
    });

    // Function to send the chart data to generate_pdf.php
    function generatePDF() {
        // Get the chart image as base64
        const chartImage = chart.toBase64Image();

        // Send the data using a POST request
        const formData = new FormData();
        formData.append('chartImage', chartImage);
        formData.append('brgy_id', <?php echo $brgy_id; ?>); // Ensure this is correctly populated

        // Use fetch to send the form data to generate_pdf.php
        fetch('generate_pdf.php?brgy_id=' + <?php echo $brgy_id; ?>, {
            method: 'POST',
            body: formData
        })
        .then(response => response.blob())
        .then(blob => {
            // Create a link element to download the PDF
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'flood_history_chart.pdf';
            link.click();
        })
        .catch(error => {
            console.error('Error generating PDF:', error);
        });
    }
</script>
</body>
</html>
