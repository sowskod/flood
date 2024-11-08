<?php
// Connect to database
include 'db.php';
$brgy_id = $_GET['brgy_id'];
// Check and debug the incoming parameters
if (isset($_GET['flood_id']) && isset($_GET['brgy_id'])) {
    $flood_id = $_GET['flood_id'];
    $brgy_id = $_GET['brgy_id'];

    // Debugging: Print the values received
    echo "Received flood_id: " . htmlspecialchars($flood_id) . "<br>";
    echo "Received brgy_id: " . htmlspecialchars($brgy_id) . "<br>";

    // Validate and ensure they are numeric
    if (is_numeric($flood_id) && is_numeric($brgy_id)) {
        $flood_id = (int) $flood_id; // Cast to integer for safety
        $brgy_id = (int) $brgy_id; // Cast to integer for safety

        // Prepare the DELETE statement
        $stmt = $con->prepare("DELETE FROM flood_data WHERE id = ?");
        $stmt->bind_param("i", $flood_id);

        // Execute the statement and check for success
        if ($stmt->execute()) {
            header("Location: brgy_details.php?brgy_id=" . $brgy_id);
            exit;
        } else {
            echo "<p>Error: " . htmlspecialchars($stmt->error) . "</p>";
        }

        $stmt->close();
    } else {
        echo "<p>Invalid flood_id or brgy_id value.</p>";
    }
}

// Fetch flood history for the barangay
$flood_result = $con->query("SELECT * FROM flood_data WHERE brgy_id = $brgy_id ORDER BY flood_date ASC");

$flood_data = [];
while ($flood = $flood_result->fetch_assoc()) {
    $flood_data[] = $flood;
}
// Fetch the barangay name
$brgy_query = $con->prepare("SELECT brgy_name FROM barangays WHERE id = ?");
$brgy_query->bind_param("i", $brgy_id);
$brgy_query->execute();
$result = $brgy_query->get_result();

if ($result->num_rows > 0) {
    $brgy = $result->fetch_assoc(); // Fetch the barangay information
    $brgy_name = htmlspecialchars($brgy['brgy_name']); // Safely escape the name
} else {
    $brgy_name = "Unknown Barangay"; // Default value if not found
}

$brgy_query->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flood History</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-b from-blue-200 to-green-200">
      <!-- Arrow Link to Barangay Details -->
<a href="brgy_details.php?brgy_id=<?php echo $brgy_id; ?>" style="position: absolute; top: 20px; left: 40px; text-decoration: none; color: black;">
    <svg width="54" height="74" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="12" cy="12" r="10" fill="#F7F7F7" stroke="black" stroke-width="2"/>
        <path d="M8 12H16M8 12L12 8M8 12L12 16" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
</a>

    <div class="flex flex-col items-center justify-center min-h-screen">
        <h1 class="text-3xl font-bold mb-6">Flood History for Barangay <?php echo htmlspecialchars($brgy['brgy_name']); ?></h1>

        <!-- Flood History Table -->
        <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-4xl">
            <h2 class="text-xl font-bold mb-4">Flood History</h2>
            <table class="table-auto w-full mb-6">
                <thead>
                    <tr>
                        <th class="px-4 py-2">Flood Date</th>
                        <th class="px-4 py-2">Flood Level</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($flood_data as $flood): ?>
                        <tr>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($flood['flood_date']); ?></td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($flood['flood_level']); ?></td>
                            <td class="border px-4 py-2">
                                <a href="edit_flood.php?brgy_id=<?php echo $brgy_id; ?>&flood_id=<?php echo $flood['id']; ?>" class="bg-yellow-500 text-white px-2 py-1 rounded-lg">Edit</a>
                                <a href="delete_flood.php?flood_id=<?php echo $flood['id']; ?>&brgy_id=<?php echo $brgy_id; ?>" class="bg-red-500 text-white px-2 py-1 rounded-lg" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
