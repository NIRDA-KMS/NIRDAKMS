<?php
include('../SchedureEvent/connect.php'); // Include database connection file

// SQL to create tasks table
$sql = "CREATE TABLE files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    filepath VARCHAR(255) NOT NULL,
    size INT NOT NULL,
    type VARCHAR(100) NOT NULL,
    uploaded_by INT NOT NULL,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    
)";


// Execute query
$result = mysqli_query($connection, $sql);
if ($result) {
    echo "Table tasks created successfully";
} else {
    echo "Error creating table: " . mysqli_error($connection);
}

mysqli_close($connection); // Close the connection
?>
