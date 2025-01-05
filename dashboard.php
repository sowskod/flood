<?php
// Connect to database
include 'db.php';

$apiKey = '88bc399999fec96520c3cab69b8caa9d'; // Get your key from https://openweathermap.org/api
$city = 'San Rafael, PH'; // City name or coordinates (latitude, longitude)

// Construct the API URL to fetch the weather data
$weatherUrl = "http://api.openweathermap.org/data/2.5/weather?q=" . urlencode($city) . "&appid=" . $apiKey . "&units=metric"; // Use units=metric for Celsius

// Fetch weather data using file_get_contents
$weatherData = file_get_contents($weatherUrl);

// Decode the JSON response
$weatherArray = json_decode($weatherData, true);

// Check if the API call was successful
if ($weatherArray['cod'] == 200) {
    $weatherMain = $weatherArray['main'];
    $weatherDescription = $weatherArray['weather'][0]['description'];
    $temp = $weatherMain['temp']; // Current temperature
    $humidity = $weatherMain['humidity']; // Humidity
    $windSpeed = $weatherArray['wind']['speed']; // Wind speed
    $weatherIcon = "http://openweathermap.org/img/w/" . $weatherArray['weather'][0]['icon'] . ".png"; // Weather icon URL
} else {
    $errorMessage = $weatherArray['message']; // API error message
}
// Handle the form submission to add a new barangay
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_brgy'])) {
    $brgy = $_POST['brgy'];

    // Insert barangay into the database
    $stmt = $con->prepare("INSERT INTO barangays (brgy_name) VALUES (?)");
    $stmt->bind_param("s", $brgy);

    if ($stmt->execute()) {
        echo "<p>Barangay added successfully!</p>";
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
}
// Handle deletion of barangay
if (isset($_GET['delete_brgy'])) {
    $brgy_id = $_GET['delete_brgy'];

    // Delete barangay from the database
    $stmt = $con->prepare("DELETE FROM barangays WHERE id = ?");
    $stmt->bind_param("i", $brgy_id);

    if ($stmt->execute()) {
        echo "<p>Barangay deleted successfully!</p>";
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
}
// Fetch all barangays with the latest flood data, sorted alphabetically
$query = "
    SELECT b.id, b.brgy_name, fd.flood_date
    FROM barangays b
    LEFT JOIN (
        SELECT brgy_id, MAX(flood_date) as flood_date
        FROM flood_data
        GROUP BY brgy_id
    ) fd ON b.id = fd.brgy_id
    ORDER BY b.brgy_name ASC
";
$result = $con->query($query);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flood Prediction Dashboard</title>
    <style>
        /* Background gradient */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(90deg, rgba(176, 240, 247, 1) 25%, rgba(114, 230, 207, 1) 66%, rgba(52, 184, 182, 1) 98%);
        }

        /* Header styling */
        .header {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 40px;
            margin-bottom: 40px;
        }

        .logo {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-right: 20px;
        }

        h1 {
            font-size: 2.5em;
            font-weight: bold;
            color: #333;
        }

        /* Main container styling */
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }


        /* Barangay card styling */
        .brgy-card {
            background-color: #EDF2F7;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin: 10px;
            width: 250px;
            text-align: center;
        }

        .brgy-card h2 {
            font-size: 1.25em;
            color: #2D3748;
        }

        .brgy-card p {
            font-size: 1em;
            color: #4A5568;
        }

        .brgy-card a {
            color: #3182CE;
            /* Blue link color */
            text-decoration: none;
            font-size: 0.9em;
        }

        .brgy-card a:hover {
            text-decoration: underline;
        }

        /* Grid for barangays */
        .brgy-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            padding: 20px;
            width: 100%;
        }

        /* Map styling */
        iframe {
            width: 100%;
            max-width: 80%;
            height: 500px;
            border: none;
            margin-top: 30px;
        }

       /* Flex container for the buttons */
.button-container {
    display: flex;
    justify-content: center; /* Align buttons horizontally in the center */
    gap: 20px; /* Adds spacing between buttons */
    margin-top: 20px;
}

/* Button styles */
.button {
    height: 50px;
    width: 300px;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    transition: all 0.5s ease-in-out;
}

.button:hover {
    box-shadow: 0.5px 0.5px 150px #252525;
}

/* Type1 button */
.type1::after {
    content: "Using Random Forest";
    height: 50px;
    width: 300px;
    background-color: #008080;
    color: #fff;
    position: absolute;
    top: 0%;
    left: 0%;
    transform: translateY(50px);
    font-size: 1.2rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.5s ease-in-out;
}

.type1::before {
    content: "Predict Flood";
    height: 50px;
    width: 300px;
    background-color: light gray;
    color: #008080;
    position: absolute;
    top: 0%;
    left: 0%;
    transform: translateY(0px) scale(1.2);
    font-size: 1.2rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.5s ease-in-out;
}

.type1:hover::after {
    transform: translateY(0) scale(1.2);
}

.type1:hover::before {
    transform: translateY(-50px) scale(0) rotate(120deg);
}

/* Type2 button */
.type2::after {
    content: "Flood Data";
    height: 50px;
    width: 300px;
    background-color: #008080;
    color: #fff;
    position: absolute;
    top: 0%;
    left: 0%;
    transform: translateY(50px);
    font-size: 1.2rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.5s ease-in-out;
}

.type2::before {
    content: "View Chart";
    height: 50px;
    width: 300px;
    background-color: light gray;
    color: #008080;
    position: absolute;
    top: 0%;
    left: 0%;
    transform: translateY(0px) scale(1.2);
    font-size: 1.2rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.5s ease-in-out;
}

.type2:hover::after {
    transform: translateY(0) scale(1.2);
}

.type2:hover::before {
    transform: translateY(-50px) scale(0) rotate(120deg);
}

       .weather-widget {
    background: linear-gradient(135deg, #a0c4c8, #88d9e2);  /* Gradient background */
    padding: 30px;
    border-radius: 15px;  /* More rounded corners */
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);  /* Softer shadow with more depth */
    margin: 20px auto;
    width: 420px;  /* Slightly wider widget */
    text-align: center;
    transition: transform 0.3s ease-in-out;  /* Hover effect for smooth scaling */
}

.weather-widget:hover {
    transform: translateY(-10px);  /* Slight lift effect on hover */
}

.weather-widget img {
    width: 150px;  /* Slightly larger icon */
    height: 150px;
    margin-bottom: 15px;  /* Add space below icon */
    transition: transform 0.3s ease-in-out;
}

.weather-widget img:hover {
    transform: rotate(15deg);  /* Add slight rotation on hover */
}

.weather-widget h2 {
    font-size: 1.6em;  /* Slightly larger font size */
    color: #2d3748;  /* Darker text for the header */
    margin: 10px 0;
    font-family: 'Roboto', sans-serif;  /* Modern font */
}

.weather-widget p {
    font-size: 1.1em;
    color: #4a5568;
    margin: 5px 0;
    font-family: 'Roboto', sans-serif;
}

.weather-widget .weather-info {
    font-size: 1.1em;
    color: #2d3748;
    font-weight: 500;
}

.weather-widget .weather-info span {
    color: #38b2ac;  /* Accent color for temperature, wind speed, etc. */
    font-weight: bold;
}

        footer {
            text-align: center;
            padding: 20px;
            background: #fff;
            margin-top: 40px;
            font-size: 1.1em;
        }
    </style>
</head>

<body>
    <!-- Header Section -->
    <div class="header">
        <img class="logo" src="images/san.jpg" alt="Logo">
        <h1>Flood Prediction Dashboard</h1>
    </div>
    <div class="container">
        <!-- Weather Widget -->
        <?php if (isset($temp)): ?>
            <div class="weather-widget">
                <h2>Current Weather in <?php echo $city; ?></h2>
                <img src="<?php echo $weatherIcon; ?>" alt="Weather icon">
                <p class="weather-info">Temperature: <?php echo $temp; ?>°C</p>
                <p class="weather-info">Humidity: <?php echo $humidity; ?>%</p>
                <p class="weather-info">Wind Speed: <?php echo $windSpeed; ?> m/s</p>
                <p class="weather-info">Condition: <?php echo ucfirst($weatherDescription); ?></p>
            </div>
        <?php else: ?>
            <p>Error fetching weather data: <?php echo $errorMessage; ?></p>
        <?php endif; ?>
    </div>
    <div class="container">
        <!-- Add Barangay Form -->
        <!-- Uncomment below if needed -->
        <!--
        <form action="dashboard.php" method="POST" class="bg-white p-6 rounded-lg shadow-lg mb-8">
            <div class="mb-4">
                <label for="brgy" class="block text-lg font-medium">Add Barangay</label>
                <input type="text" id="brgy" name="brgy" required class="mt-2 p-2 border border-gray-300 rounded-lg w-full">
            </div>
            <button type="submit" name="add_brgy" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Add Brgy</button>
        </form>
        -->

        <div class="button-container">
    <a href="predict_flood.php">
        <button class="button type1"></button>
    </a>
    <a href="chart.php">
        <button class="button type2"></button>
    </a>
</div>


        <!-- Display Barangays as clickable cards -->
        <div class="brgy-grid">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="brgy-card">
                    <h2>
                        <a href="brgy_details.php?brgy_id=<?php echo $row['id']; ?>">
                            <?php echo htmlspecialchars($row['brgy_name']); ?>
                        </a>
                    </h2>
                    <p>Latest Flood: <?php echo $row['flood_date'] ? htmlspecialchars($row['flood_date']) : 'No data available'; ?></p>
                    <!-- <a href="edit_brgy.php?brgy_id=<?php echo $row['id']; ?>">Edit</a> | 
                    <a href="dashboard.php?delete_brgy=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this barangay?');">Delete</a>-->
                </div>
            <?php endwhile; ?>
            <!-- Include Leaflet.js for maps -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
</div>
<!-- Include OpenWeatherMap Tile Layers -->
<script src="https://unpkg.com/leaflet-openweathermap"></script>

<div id="map" style="width: 100%; height: 500px;"></div>

<script>
    var map = L.map('map').setView([14.9944769, 120.8957698], 10);  // Center the map to San Rafael, PH

    // Add OpenStreetMap base layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Add OpenWeatherMap layers for clouds and wind
    var cloudLayer = L.tileLayer('https://{s}.tile.openweathermap.org/map/clouds_new/{z}/{x}/{y}.png?appid=88bc399999fec96520c3cab69b8caa9d', {
        attribution: '&copy; <a href="https://openweathermap.org/">OpenWeatherMap</a>',
        opacity: 0.7
    }).addTo(map);

    var windLayer = L.tileLayer('https://{s}.tile.openweathermap.org/map/wind_new/{z}/{x}/{y}.png?appid=88bc399999fec96520c3cab69b8caa9d', {
        attribution: '&copy; <a href="https://openweathermap.org/">OpenWeatherMap</a>',
        opacity: 0.7
    }).addTo(map);

    // Toggle between layers
    var baseLayers = {
        "Clouds": cloudLayer,
        "Wind": windLayer
    };

    L.control.layers(baseLayers).addTo(map);
</script>


       

        <!-- Map -->
       
    </div>
    <footer>
        <p>Flood Prediction Dashboard - San Rafael, PH</p>
    </footer>
</body>

</html>