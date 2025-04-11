<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
include('connect.php');

// Check connection
if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Get event ID from URL parameter
$eventId = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0;

if ($eventId <= 0) {
    die("Invalid event ID");
}

// Query to get only the attend column for the specific event
$query = "SELECT attend FROM schedule_events WHERE event_id = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "i", $eventId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result || mysqli_num_rows($result) == 0) {
    die("No attend data found for this event.");
}

$row = mysqli_fetch_assoc($result);
$attendData = $row['attend'];

// Close connection
mysqli_close($connection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Attendees | NIRDA Knowledge Management System</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f0f2f5;
            color: #333333;
            margin: 0;
            padding: 20px;
            line-height: 1.6;
        }
        .container {
            max-width: 800px;
           margin: 100px 255px;
           
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #1a237e;
            margin-bottom: 20px;
        }
        .attend-content {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 5px;
            margin-top: 20px;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 8px 16px;
            background-color: #1a237e;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
        }
        .back-btn:hover {
            background-color: #00A0DF;
        }
    </style>
</head>
<body>
    <?php include ('../Internees_task/header.php'); ?>
    <div class="container">
        <h1><i class="fas fa-users"></i> Event Attendees</h1>
        <p>Viewing attendees for Event #<?php echo $eventId; ?></p>
        
        <div class="attend-content">
            <?php echo htmlspecialchars($attendData); ?>
        </div>
        
        <button class="back-btn" onclick="window.history.back()">
            <i class="fas fa-arrow-left"></i> Back to Events
        </button>
    </div>

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</body>
</html>