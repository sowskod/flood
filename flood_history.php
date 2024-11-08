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
    <style>
        body {
            background: linear-gradient(to bottom, #cce7ff, #b2f0e6);
            font-family: Arial, sans-serif;
            color: #333;
        }
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .back-link {
            position: absolute;
            top: 20px;
            left: 40px;
            color: black;
            text-decoration: none;
        }
        .back-link svg {
            width: 54px;
            height: 74px;
        }
        .heading {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
        }
        .table-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 800px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .button {
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            color: white;
            font-size: 0.9rem;
            margin: 2px;
        }
        .edit-button {
            background-color: #f39c12;
        }
        .delete-button {
            background-color: #e74c3c;
        }
        .delete-button:hover {
            background-color: #c0392b;
        }
        .edit-button:hover {
            background-color: #e67e22;
        }
    </style>
</head>
<body>
    <a href="brgy_details.php?brgy_id=<?php echo $brgy_id; ?>" class="back-link">
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="12" cy="12" r="10" fill="#F7F7F7" stroke="black" stroke-width="2"/>
            <path d="M8 12H16M8 12L12 8M8 12L12 16" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </a>

    <div class="container">
        <h1 class="heading">Flood History for Barangay <?php echo htmlspecialchars($brgy['brgy_name']); ?></h1>

        <div class="table-container">
            <h2>Flood History </h2>
            <table>
                <thead>
                    <tr>
                        <th>Flood Date</th>
                        <th>Flood Level</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($flood_data as $flood): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($flood['flood_date']); ?></td>
                            <td><?php echo htmlspecialchars($flood['flood_level']); ?></td>
                            <td>
                                <a href="edit_flood.php?brgy_id=<?php echo $brgy_id; ?>&flood_id=<?php echo $flood['id']; ?>" class="button edit-button">Edit</a>
                                <a href="delete_flood.php?flood_id=<?php echo $flood['id']; ?>&brgy_id=<?php echo $brgy_id; ?>" class="button delete-button" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>