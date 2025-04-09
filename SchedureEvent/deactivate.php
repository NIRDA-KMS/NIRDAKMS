<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
include('connect.php');

// Check if the request is POST and has the required parameters
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the event ID from the POST data
    $eventId = isset($_POST['event_id']) ? (int)$_POST['event_id'] : 0;
    $currentState = isset($_POST['current_state']) ? $_POST['current_state'] : 'active';
    
    if ($eventId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid event ID']);
        exit;
    }
    
    // Determine the new state (toggle between active/inactive)
    $newState = ($currentState === 'active') ? 0 : 1;
    
    // Update the event status in the database using prepared statement
    $query = "UPDATE schedule_events SET isActive = ? WHERE event_id = ?";
    $stmt = mysqli_prepare($connection, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $newState, $eventId);
        $result = mysqli_stmt_execute($stmt);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'new_state' => $newState ? 'active' : 'inactive',
                'action_text' => $newState ? 'Deactivate' : 'Activate',
                'icon_class' => $newState ? 'fa-eye-slash' : 'fa-eye'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update event status']);
        }
        
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

// Close database connection
mysqli_close($connection);
?>