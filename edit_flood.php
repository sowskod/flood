<?php
// Connect to database
include 'db.php';

// Get the flood_id and barangay id from URL
$flood_id = $_GET['flood_id'];
$brgy_id = $_GET['brgy_id'];

// Fetch current flood data
$flood_query = $con->prepare("SELECT * FROM flood_data WHERE id = ?");
$flood_query->bind_param("i", $flood_id);
$flood_query->execute();
$flood_result = $flood_query->get_result();
$flood = $flood_result->fetch_assoc();

// Handle form submission to update flood data
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_flood'])) {
    $flood_date = $_POST['flood_date'];
    
    // Update flood date in the database
    $stmt = $con->prepare("UPDATE flood_data SET flood_date = ? WHERE id = ?");
    $stmt->bind_param("si", $flood_date, $flood_id);

    if ($stmt->execute()) {
        header("Location: brgy_details.php?brgy_id=$brgy_id");
        exit;
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Flood Data</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
<a href="flood_history.php?brgy_id=<?php echo $brgy_id; ?>" style="position: absolute; top: 20px; left: 40px; text-decoration: none; color: black;">
    <svg width="54" height="74" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="12" cy="12" r="10" fill="#F7F7F7" stroke="black" stroke-width="2"/>
        <path d="M8 12H16M8 12L12 8M8 12L12 16" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
</a>
    <div class="flex flex-col items-center justify-center min-h-screen bg-gradient-to-b from-blue-200 to-green-200">
        <h1 class="text-3xl font-bold mb-6">Edit Flood Data</h1>

        <!-- Form to Edit Flood Data -->
        <form action="" method="POST" class="bg-white p-6 rounded-lg shadow-lg">
            <div class="mb-4">
                <label for="flood_date" class="block text-lg font-medium">Flood Date</label>
                <input type="date" id="flood_date" name="flood_date" value="<?php echo htmlspecialchars($flood['flood_date']); ?>" required class="mt-2 p-2 border border-gray-300 rounded-lg w-full">
            </div>
            
            <button type="submit" name="update_flood" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Update Flood Data</button>
        </form>
    </div>
</body>
</html>
