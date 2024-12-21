<?php
// Connect to database
include 'db.php';

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
    </style>
</head>

<body>
    <!-- Header Section -->
    <div class="header">
        <img class="logo" src="images/san.jpg" alt="Logo">
        <h1>Flood Prediction Dashboard</h1>
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

        <a href="predict_flood.php">
            <button class="button type1"></button>
        </a>

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
        </div>

        <!-- Map -->
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d123326.97863425352!2d120.89576979788184!3d14.994476919004072!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397005fc6e4e2c5%3A0x4b387b2ddd4537de!2sSan%20Rafael%2C%20Bulacan!5e0!3m2!1sen!2sph!4v1727240238400!5m2!1sen!2sph" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
</body>

</html>