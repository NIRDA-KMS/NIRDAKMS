<?php
include 'connect.php';

// SQL to create events table
$sql = "CREATE TABLE  schedule_events (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    eventTitle VARCHAR(100) NOT NULL,
    startDateTime DATETIME NOT NULL,
    endingDateTime DATETIME NOT NULL,
    eventLocation TEXT ,
    eventDescription VARCHAR(255),
    attend TEXT,
    emailReminder VARCHAR(50),
   appReminder VARCHAR(100),
   reminderTime INT
   )";
// Execute query
if (mysqli_query($connection, $sql)) {
    echo "Table schedule_events created successfully";
} else {
    echo "Error creating table: " . mysqli_error($connection);
}

// Close connection
mysqli_close($connection);
?>