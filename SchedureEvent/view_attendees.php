<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session for messages
session_start();

// Database connection
include('connect.php');

// Check connection
if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Handle RSVP update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_rsvp'])) {
    $eventId = (int)$_POST['event_id'];
    $attendee = trim($_POST['attendee']);
    $status = $_POST['status'];
    
    // Get current RSVP data
    $query = "SELECT rsvp_status FROM schedule_events WHERE event_id = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "i", $eventId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    
    $rsvpData = [];
    if (!empty($row['rsvp_status'])) {
        $rsvpData = json_decode($row['rsvp_status'], true);
    }
    
    // Update RSVP status
    $attendeeKey = 'attendee_' . md5($attendee);
    $rsvpData[$attendeeKey] = $status;
    
    // Save back to database
    $updateQuery = "UPDATE schedule_events SET rsvp_status = ? WHERE event_id = ?";
    $updateStmt = mysqli_prepare($connection, $updateQuery);
    $jsonData = json_encode($rsvpData);
    mysqli_stmt_bind_param($updateStmt, "si", $jsonData, $eventId);
    mysqli_stmt_execute($updateStmt);
    
    $_SESSION['message'] = "RSVP status updated successfully!";
    header("Location: view_attendees.php?event_id=" . $eventId);
    exit();
}

// Get event ID from URL parameter
$eventId = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0;

if ($eventId <= 0) {
    die("Invalid event ID");
}

// Query to get event details including attendees and RSVP status
$query = "SELECT eventTitle, attend, rsvp_status FROM schedule_events WHERE event_id = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "i", $eventId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result || mysqli_num_rows($result) == 0) {
    die("No event found with this ID.");
}

$row = mysqli_fetch_assoc($result);
$eventTitle = $row['eventTitle'];
$attendData = $row['attend'];
$rsvpStatus = !empty($row['rsvp_status']) ? json_decode($row['rsvp_status'], true) : [];

// Close connection
mysqli_close($connection);

// Process attendee data
$attendees = [];
if (!empty($attendData)) {
    // Split by comma and trim whitespace
    $attendees = array_map('trim', explode(',', $attendData));
    // Remove empty entries
    $attendees = array_filter($attendees);
}
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
        .attend-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .attend-table th, .attend-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .attend-table th {
            background-color: #1a237e;
            color: white;
        }
        .attend-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .attend-table tr:hover {
            background-color: #e6e6e6;
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
        .no-attendees {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 5px;
            margin-top: 20px;
            text-align: center;
            color: #666;
        }
        .rsvp-form {
            display: flex;
            gap: 10px;
        }
        .rsvp-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            color: white;
        }
        .rsvp-confirmed {
            background-color: #4CAF50;
        }
        .rsvp-declined {
            background-color: #F44336;
        }
        .rsvp-pending {
            background-color: #FFC107;
        }
        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .success {
            background-color: #dff0d8;
            color: #3c763d;
        }
        .error {
            background-color: #f2dede;
            color: #a94442;
        }
    </style>
</head>
<body>
    <?php include('../Internees_task/header.php'); ?>
    <div class="container">
        <h1><i class="fas fa-users"></i> Event Attendees</h1>
        
        <?php if (isset($_SESSION['message'])): ?>
            <div class="message success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php endif; ?>
        
        <h3><?php echo htmlspecialchars($eventTitle); ?></h3>
        <p>Viewing attendees for Event #<?php echo $eventId; ?></p>
        
        <?php if (!empty($attendees)): ?>
            <table class="attend-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Attendee Name</th>
                        <th>RSVP Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendees as $index => $attendee): 
                        $attendeeKey = 'attendee_' . md5($attendee);
                        $currentStatus = $rsvpStatus[$attendeeKey] ?? 'pending';
                    ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($attendee); ?></td>
                            <td>
                                <span class="rsvp-badge rsvp-<?php echo $currentStatus; ?>">
                                    <?php echo ucfirst($currentStatus); ?>
                                </span>
                            </td>
                            <td>
                                <form method="post" class="rsvp-form">
                                    <input type="hidden" name="event_id" value="<?php echo $eventId; ?>">
                                    <input type="hidden" name="attendee" value="<?php echo htmlspecialchars($attendee); ?>">
                                    <input type="hidden" name="update_rsvp" value="1">
                                    <button type="submit" name="status" value="confirmed" class="action-btn" title="Confirm Attendance">
                                        <i class="fas fa-check"></i> Confirm
                                    </button>
                                    <button type="submit" name="status" value="declined" class="action-btn" title="Decline Attendance">
                                        <i class="fas fa-times"></i> Decline
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-attendees">
                <p>No attendees have been recorded for this event.</p>
            </div>
        <?php endif; ?>
        
        <button class="back-btn" onclick="window.history.back()">
            <i class="fas fa-arrow-left"></i> Back to Events
        </button>
    </div>

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</body>
</html>