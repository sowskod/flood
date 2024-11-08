<?php
// Connect to database
include 'db.php';

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
            header("Location: flood_history.php?brgy_id=" . $brgy_id);
            exit;
        } else {
            echo "<p>Error: " . htmlspecialchars($stmt->error) . "</p>";
        }

        $stmt->close();
    } else {
        echo "<p>Invalid flood_id or brgy_id value.</p>";
    }
} 

?>
