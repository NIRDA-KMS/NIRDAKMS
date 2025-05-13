<?php
include 'connect.php';


// SQL to create attendees table
$sql = "ALTER TABLE schedule_events
ADD COLUMN rsvp_status TEXT NULL";



// Execute query
if (mysqli_query($connection, $sql)) {
    echo "Table project_goals created successfully";

} else {
    echo "Error creating table: " . mysqli_error($connection);
}

// Close connection
mysqli_close($connection);
?>
