<?php
// Connect to database
include 'db.php';

// Fetch data for the charts

// Chart 1: Count of barangays with latest flood data
$query1 = "
    SELECT COUNT(*) AS count_with_data
    FROM barangays b
    INNER JOIN (
        SELECT DISTINCT brgy_id
        FROM flood_data
    ) fd ON b.id = fd.brgy_id
";
$result1 = $con->query($query1);
$row1 = $result1->fetch_assoc();
$count_with_data = $row1['count_with_data'] ?? 0;

$query2 = "SELECT COUNT(*) AS total_barangays FROM barangays";
$result2 = $con->query($query2);
$row2 = $result2->fetch_assoc();
$total_barangays = $row2['total_barangays'] ?? 0;
$count_without_data = $total_barangays - $count_with_data;
// Query to get the total flood data count across all barangays
$query4 = "SELECT COUNT(id) AS total_flood_data FROM flood_data";
$result4 = $con->query($query4);
$row4 = $result4->fetch_assoc();
$total_flood_data = $row4['total_flood_data'] ?? 0;

// Chart 2: Flood data count per barangay
$query3 = "
    SELECT b.brgy_name, COUNT(fd.id) AS flood_count
    FROM barangays b
    LEFT JOIN flood_data fd ON b.id = fd.brgy_id
    GROUP BY b.brgy_name
";
$result3 = $con->query($query3);
$brgy_names = [];
$flood_counts = [];
while ($row3 = $result3->fetch_assoc()) {
    $brgy_names[] = $row3['brgy_name'];
    $flood_counts[] = $row3['flood_count'];
}

// Chart 4: Total number of barangays
$query5 = "SELECT COUNT(id) AS barangay_count FROM barangays";
$result5 = $con->query($query5);
$row5 = $result5->fetch_assoc();
$total_barangays_count = $row5['barangay_count'];

// Chart 5: Recent activity (last 5 flood entries)
$query6 = "
    SELECT b.brgy_name, fd.flood_date, fd.flood_level
    FROM flood_data fd
    INNER JOIN barangays b ON fd.brgy_id = b.id
    ORDER BY fd.flood_date DESC
    LIMIT 5
";
$result6 = $con->query($query6);
$recent_activity = [];
while ($row6 = $result6->fetch_assoc()) {
    $recent_activity[] = $row6;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flood Data Charts</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
   /* Body Styling */
body {
    margin: 0;
    font-family: 'Arial', sans-serif;
    background: linear-gradient(135deg, #fbc2eb, #a6c1ee);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start; /* Align content to the top for better spacing */
    min-height: 100%; /* Ensure content can stretch */
    padding: 0;
    overflow-y: auto; /* Allow vertical scrolling */
}

/* Heading Styling */
h1 {
    font-size: 3.2em;  /* Increased font size for better visibility */
    color: #333;
    margin: 30px 0 20px 0;  /* Spacing from top */
    font-weight: 700;  /* Bold for prominence */
}

/* Back Link */
.back-link {
    font-size: 1.3em;  /* Slightly larger for better visibility */
    text-decoration: none;
    color: #4CAF50;
    margin-bottom: 30px;
}

.back-link:hover {
    color: #388E3C;
}

/* Chart Container */
.chart-container {
    display: grid;  /* Changed to grid */
    grid-template-columns: repeat(3, 1fr); /* 3 columns */
    gap: 20px;  /* Adjusted the gap between charts */
    width: 90%;
    max-width: 1400px;
    padding: 20px;
    margin-top: 20px; /* Add spacing from the top */
}

/* Chart Card Styling */
.chart {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    padding: 20px;
    height: 500px;
    transition: transform 0.3s ease;
}

.chart:hover {
    transform: scale(1.05);
}

/* Canvas Styling */
canvas {
    max-width: 100%;
    height: auto;
}

/* Paragraph Styling for Chart Descriptions */
p {
    font-size: 1.2em;
    text-align: center;
    color: #333;
    font-weight: 600;
    margin-bottom: 20px;
}

/* Card Styling */
.stat-card {
    background: linear-gradient(135deg, #6e7a7d, #b0d0d3); /* Gradient background */
    border-radius: 15px;
    text-align: center;
    padding: 30px 20px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    animation: slide-down 0.5s ease-out;
    color: white;
    font-weight: 600;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
}

.stat-card h2 {
    margin: 0 0 15px;  /* Increased bottom margin for better spacing */
    font-size: 2.2em;  /* Larger heading */
    color: white;
}

.stat-card span {
    display: block;
    font-size: 15em;  /* Increased the size of the number */
    background: -webkit-linear-gradient(45deg, #ff5c8d, #ff9f00);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-weight: bold;
}


    </style>
</head>

<body>
    <a href="dashboard.php" class="back-link">&larr; Back to Dashboard</a>
    <h1>Flood Data Analysis</h1>
    <div class="chart-container">

        <!-- Chart 1: Barangays with Latest Flood Data -->
        <!-- Total Number of Barangays -->
<div class="stat-card">
    <h2>Total Number of Barangays</h2>
    <span id="totalBarangays"><?= $total_barangays_count ?></span>
</div>

    
        <div class="chart">
            <p>Flood Data Availability per Barangay</p>
            <canvas id="chart1"></canvas>
        </div>

        <!-- Chart 2: Flood Data Count per Barangay -->
        <div class="chart">
            <p>Number of Flood Data Entries per Barangay</p>
            <canvas id="chart2"></canvas>
        </div>

        <!-- Chart 3: Total Flood Data Over All Barangays -->
        <div class="chart">
            <p>Total Flood Data Entries</p>
            <canvas id="chart3"></canvas>
        </div>

   

    <!-- Chart 5: Recent Flood Activities -->
    <div class="chart">
        <p>Recent Flood Activities</p>
        <canvas id="chart5"></canvas>
    </div>

    </div>

    <script>
        // Chart 1: Pie Chart
        const ctx1 = document.getElementById('chart1').getContext('2d');
        new Chart(ctx1, {
            type: 'pie',
            data: {
                labels: ['With Flood Data', 'Without Flood Data'],
                datasets: [{
                    data: [<?= $count_with_data ?>, <?= $count_without_data ?>],
                    backgroundColor: ['#4CAF50', '#F44336'],
                    borderColor: ['#FFFFFF', '#FFFFFF'],
                    borderWidth: 2
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    }
                }
            }
        });

        // Chart 2: Bar Chart
        const ctx2 = document.getElementById('chart2').getContext('2d');
        new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: <?= json_encode($brgy_names) ?>,
                datasets: [{
                    label: 'Flood Data Count',
                    data: <?= json_encode($flood_counts) ?>,
                    backgroundColor: '#2196F3',
                    borderColor: '#1E88E5',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Barangays',
                            color: '#333',
                            font: {
                                size: 14
                            }
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Flood Data Count',
                            color: '#333',
                            font: {
                                size: 14
                            }
                        }
                    }
                }
            }
        });
        // Chart 3: Total Flood Data Entries (Single Bar)
        const ctx3 = document.getElementById('chart3').getContext('2d');
        new Chart(ctx3, {
            type: 'doughnut',
            data: {
                labels: ['Total Flood Data'],
                datasets: [{
                    label: 'Flood Data Count',
                    data: [<?= $total_flood_data ?>],
                    backgroundColor: '#FF9800',
                    borderColor: '#FB8C00',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Category',
                            color: '#333',
                            font: {
                                size: 14
                            }
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Flood Data Count',
                            color: '#333',
                            font: {
                                size: 14
                            }
                        }
                    }
                }
            }
        });
          // Chart 4: Total Number of Barangays
   

    // Chart 5: Recent Activities (Bar Chart)
    const recentLabels = <?= json_encode(array_column($recent_activity, 'brgy_name')) ?>;
    const recentData = <?= json_encode(array_column($recent_activity, 'flood_level')) ?>;
    const recentDates = <?= json_encode(array_column($recent_activity, 'flood_date')) ?>;

    const ctx5 = document.getElementById('chart5').getContext('2d');
    new Chart(ctx5, {
        type: 'bar',
        data: {
            labels: recentLabels.map((name, index) => `${name} (${recentDates[index]})`),
            datasets: [{
                label: 'Flood Level',
                data: recentData,
                backgroundColor: '#FF5722',
                borderColor: '#E64A19',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Barangay (Date)',
                        color: '#333',
                        font: {
                            size: 14
                        }
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Flood Level',
                        color: '#333',
                        font: {
                            size: 14
                        }
                    }
                }
            }
        }
    });
    </script>
</body>

</html>