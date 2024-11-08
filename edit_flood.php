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
        header("Location: flood_history.php?brgy_id=$brgy_id");
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
    <style>
            body {
            background: linear-gradient(to bottom, #b3e5fc, #c8e6c9);
            font-family: Arial, sans-serif;
            color: #333;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            margin-top: 60px;
            width: 90%;
            max-width: 600px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 20px;
            color: #333;
        }
        label {
            display: block;
            font-size: 1rem;
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }
        input[type="date"],
        input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
        }
        button {
            background-color: #1e88e5;
            color: white;
            font-size: 1rem;
            font-weight: bold;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #1565c0;
        }
        </style>
</head>
<body>
<a href="flood_history.php?brgy_id=<?php echo $brgy_id; ?>" style="position: absolute; top: 20px; left: 40px; text-decoration: none; color: black;">
    <svg width="54" height="74" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="12" cy="12" r="10" fill="#F7F7F7" stroke="black" stroke-width="2"/>
        <path d="M8 12H16M8 12L12 8M8 12L12 16" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
</a>
<div class="container">
        <h1>Edit Flood Data</h1>
        <form action="" method="POST">
            <label for="flood_date">Flood Date</label>
            <input type="date" id="flood_date" name="flood_date" value="<?php echo htmlspecialchars($flood['flood_date']); ?>" required>
            <button type="submit" name="update_flood">Update Flood Data</button>
        </form>
    </div>
</body>
</html>
