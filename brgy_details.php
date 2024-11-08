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
            echo "<p>Flood data added successfully!</p>";
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
if (count($flood_intervals) > 0) {
    $average_interval = array_sum($flood_intervals) / count($flood_intervals);
    $last_flood_date = end($flood_dates);
    
    $next_flood_date = new DateTime($last_flood_date);
    $next_flood_date->modify("+$average_interval days");
    
    $next_flood_prediction = $next_flood_date->format('Y-m-d');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Details</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gradient-to-b from-blue-200 to-green-200">
    <!-- Arrow Link to Homepage -->
    <a href="dashboard.php" style="position: absolute; top: 20px; left: 40px; text-decoration: none; color: black;">
        <svg width="54" height="74" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="12" cy="12" r="10" fill="#F7F7F7" stroke="black" stroke-width="2"/>
            <path d="M8 12H16M8 12L12 8M8 12L12 16" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </a>

    <div class="flex flex-col items-center justify-center min-h-screen bg-gradient-to-b from-blue-200 to-green-200">
        <h1 class="text-3xl font-bold mb-6">Barangay: <?php echo htmlspecialchars($brgy['brgy_name']); ?></h1>
        <form action="brgy_details.php?brgy_id=<?php echo $brgy_id; ?>" method="POST" class="bg-white p-6 rounded-lg shadow-lg mb-8">
            <div class="mb-4">
                <label for="flood_date" class="block text-lg font-medium">Flood Date</label>
                <input type="date" id="flood_date" name="flood_date" required class="mt-2 p-2 border border-gray-300 rounded-lg w-full">
            </div>

            <div class="mb-4">
                <label for="flood_level" class="block text-lg font-medium">Flood Level</label>
                <select id="flood_level" name="flood_level" required class="mt-2 p-2 border border-gray-300 rounded-lg w-full">
                    <option value="">-- Select Flood Level --</option>
                    <option value="Normal">Normal</option>
                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                </select>
            </div>

            <button type="submit" name="add_flood" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Add Flood Data</button>
        </form>

        <!-- Bar Graph for Flood History -->
        <div class="bg-white p-6 rounded-lg shadow-lg">
    <h2 class="text-xl font-bold mb-4">Flood History (Bar Graph)</h2>
    <canvas id="floodHistoryChart" width="400" height="200"></canvas>
    
    <!-- View Flood History Button -->
    <a href="flood_history.php?brgy_id=<?php echo $brgy_id; ?>" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
        View Full Flood History
    </a>
    
    <!-- Print PDF Button -->
    <a href="generate_pdf.php?brgy_id=<?php echo $brgy_id; ?>" class="mt-4 bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">Download PDF</a>
</div>

        
        <!-- Flood Prediction -->
        <div class="bg-white p-6 rounded-lg shadow-lg mt-4">
            <h2 class="text-xl font-bold mb-4">Flood Prediction</h2>
            <?php if ($next_flood_prediction): ?>
                <p>Based on historical data, the next flood is predicted to happen around: <strong><?php echo $next_flood_prediction; ?></strong></p>
            <?php else: ?>
                <p>Not enough data to make a prediction.</p>
            <?php endif; ?>
        </div>

     

        <script>
    // Get flood data for the graph
    const floodData = <?php echo json_encode($flood_data); ?>;

    // Extract dates and levels from flood data
    const floodDates = floodData.map(flood => flood.flood_date);
    const floodLevels = floodData.map(flood => flood.flood_level);

    // Assign colors based on flood levels
    const floodColors = floodLevels.map(level => {
        switch(level) {
            case 'High': return 'rgba(255, 99, 132, 0.8)';   // Red for High
            case 'Medium': return 'rgba(255, 206, 86, 0.8)'; // Yellow for Medium
            case 'Low': return 'rgba(54, 162, 235, 0.8)';    // Blue for Low
            case 'Normal': return 'rgba(75, 192, 192, 0.8)'; // Green for Normal
            default: return 'rgba(75, 192, 192, 0.2)';       // Default color
        }
    });

    const ctx = document.getElementById('floodHistoryChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: floodDates,
            datasets: [{
                label: 'Flood Occurrences',
                data: floodDates.map(() => 1), // Each flood counts as 1 occurrence
                backgroundColor: floodColors,
                borderColor: floodColors.map(color => color.replace('0.8', '1')),
                borderWidth: 1
            }]
        }, 
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: true, // Show the legend
                    labels: {
                        generateLabels: function(chart) {
                            return [
                                { text: 'High', fillStyle: 'rgba(255, 99, 132, 0.8)' }, // Red
                                { text: 'Medium', fillStyle: 'rgba(255, 206, 86, 0.8)' }, // Yellow
                                { text: 'Low', fillStyle: 'rgba(54, 162, 235, 0.8)' },  // Blue
                                { text: 'Normal', fillStyle: 'rgba(75, 192, 192, 0.8)' } // Green
                            ];
                        }
                    }
                }
            }
        }
    });
</script>
</body>
</html>
