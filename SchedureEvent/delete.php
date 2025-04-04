<?php
include('connect.php');
session_start();

// Check if event_id is provided
if (isset($_GET['event_id'])) {
    $event_id = (int)$_GET['event_id'];
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    
    // Delete the event from database
    $query = "DELETE FROM schedule_events WHERE event_id = $event_id";
    $result = mysqli_query($connection, $query);
    
    if ($result) {
        $_SESSION['message'] = 'Event deleted successfully!';
    } else {
        $_SESSION['error'] = 'Error deleting event: ' . mysqli_error($connection);
    }
    
    // Redirect back to the events page
    header("Location: ".$_SERVER['HTTP_REFERER']."?page=$page");
    exit();
} else {
    $_SESSION['error'] = 'No event specified for deletion.';
    header("Location: ".$_SERVER['HTTP_REFERER']);
    exit();
}
?>