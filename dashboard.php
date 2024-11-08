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
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .logo {
            width: 60px;
            height: 60px;
            border-radius: 50%;
        }
    </style>
</head>
<body class="bg-gradient-to-b from-blue-200 to-green-200">

<!-- Header Section -->
<div class="flex items-center justify-center mt-8 mb-8">
    <img class="logo mr-4" src="images/san.jpg" alt="Logo">
    <h1 class="text-4xl font-bold text-gray-700">Flood Prediction Dashboard</h1>
</div>
        <div class="flex flex-col items-center justify-center min-h-screen bg-gradient-to-b from-blue-200 to-green-200">
        <!-- Add Barangay Form -->
        <form action="dashboard.php" method="POST" class="bg-white p-6 rounded-lg shadow-lg mb-8">
            <div class="mb-4">
                <label for="brgy" class="block text-lg font-medium">Add Barangay</label>
                <input type="text" id="brgy" name="brgy" required class="mt-2 p-2 border border-gray-300 rounded-lg w-full">
            </div>
            <button type="submit" name="add_brgy" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Add Brgy</button>
        </form>
      
        <a href="predict_flood.php" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">Predict Flood Using Windspeed and Raonfall</a>
        

        <!-- Display Barangays as clickable folders -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 p-4">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="bg-blue-200 p-4 rounded-lg shadow-md">
                    <h2 class="text-lg font-bold">
                        <a href="brgy_details.php?brgy_id=<?php echo $row['id']; ?>" class="text-black hover:underline">
                            <?php echo htmlspecialchars($row['brgy_name']); ?>
                        </a>
                    </h2>
                    <p class="text-muted-foreground">
                        Latest Flood: <?php echo $row['flood_date'] ? htmlspecialchars($row['flood_date']) : 'No data available'; ?>
                    </p>
                    <a href="edit_brgy.php?brgy_id=<?php echo $row['id']; ?>" class="text-blue-600 hover:underline">Edit</a>
                    <a href="dashboard.php?delete_brgy=<?php echo $row['id']; ?>" class="text-red-500 hover:underline" onclick="return confirm('Are you sure you want to delete this barangay?');">Delete</a>
           
                </div>
            <?php endwhile; ?>
        </div>
        
       
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d123326.97863425352!2d120.89576979788184!3d14.994476919004072!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397005fc6e4e2c5%3A0x4b387b2ddd4537de!2sSan%20Rafael%2C%20Bulacan!5e0!3m2!1sen!2sph!4v1727240238400!5m2!1sen!2sph" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
</body>
</html>
