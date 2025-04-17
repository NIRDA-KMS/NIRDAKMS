<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session for messages
session_start();

// Database connection
include('connect.php');

// Check if event_id and current_state are provided
if (isset($_GET['event_id']) && isset($_GET['current_state'])) {
    $event_id = (int)$_GET['event_id'];
    $current_state = $_GET['current_state'];
    
    // Validate current_state
    if (!in_array($current_state, ['active', 'inactive'])) {
        $_SESSION['error'] = "Invalid current state provided.";
        header("Location: manage_events.php");
        exit();
    }
    
    // Determine new state (toggle)
    $new_state = $current_state === 'active' ? 0 : 1;
    
    // Update the event's active status
    $query = "UPDATE schedule_events SET isActive = ? WHERE event_id = ?";
    $stmt = mysqli_prepare($connection, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $new_state, $event_id);
        mysqli_stmt_execute($stmt);
        
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            $_SESSION['message'] = "Event " . ($new_state ? "activated" : "deactivated") . " successfully!";
        } else {
            $_SESSION['error'] = "No changes made. Event may not exist or already be in this state.";
        }
        
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['error'] = "Database error: " . mysqli_error($connection);
    }
} else {
    $_SESSION['error'] = "Event ID and current state not provided.";
}

// Redirect back to events page
header("Location: manage_events.php");
exit();
?>