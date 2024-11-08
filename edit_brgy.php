<?php
// Connect to database
include 'db.php';

$brgy_id = $_GET['brgy_id'];

// Fetch barangay data for editing
$brgy_query = $con->prepare("SELECT * FROM barangays WHERE id = ?");
$brgy_query->bind_param("i", $brgy_id);
$brgy_query->execute();
$brgy_result = $brgy_query->get_result();
$brgy = $brgy_result->fetch_assoc();

// Handle barangay update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_brgy'])) {
    $brgy_name = $_POST['brgy_name'];

    // Update barangay in the database
    $update_stmt = $con->prepare("UPDATE barangays SET brgy_name = ? WHERE id = ?");
    $update_stmt->bind_param("si", $brgy_name, $brgy_id);

    if ($update_stmt->execute()) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "<p>Error: " . $update_stmt->error . "</p>";
    }

    $update_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Barangay</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
        <!-- Arrow Link to Homepage -->
<a href="dashboard.php" style="position: absolute; top: 20px; left: 40px; text-decoration: none; color: black;">
    <svg width="54" height="74" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <!-- Outer circle -->
        <circle cx="12" cy="12" r="10" fill="#F7F7F7" stroke="black" stroke-width="2"/>
        <!-- Inner arrow shape -->
        <path d="M8 12H16M8 12L12 8M8 12L12 16" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
</a>
    <div class="flex flex-col items-center justify-center min-h-screen bg-gradient-to-b from-blue-200 to-green-200">
        <h1 class="text-3xl font-bold mb-6">Edit Barangay</h1>

        <!-- Edit Barangay Form -->
        <form action="edit_brgy.php?brgy_id=<?php echo $brgy_id; ?>" method="POST" class="bg-white p-6 rounded-lg shadow-lg">
            <div class="mb-4">
                <label for="brgy_name" class="block text-lg font-medium">Barangay Name</label>
                <input type="text" id="brgy_name" name="brgy_name" value="<?php echo htmlspecialchars($brgy['brgy_name']); ?>" required class="mt-2 p-2 border border-gray-300 rounded-lg w-full">
            </div>
            <button type="submit" name="update_brgy" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Update Barangay</button>
        </form>
    </div>
</body>
</html>
