<?php
include 'connect.php';

// SQL to create events table
$sql = "CREATE TABLE IF NOT EXISTS schedule_events (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT ,
    start_datetime DATETIME NOT NULL,
    end_datetime DATETIME NOT NULL,
    location VARCHAR(255),
    location_type ENUM('physical', 'virtual') DEFAULT 'physical',
    is_recurring TINYINT(1) DEFAULT 0,
    recurrence_pattern VARCHAR(50)
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