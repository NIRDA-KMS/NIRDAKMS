<?php
include 'connect.php';

// SQL to create forum_topics table
$sql ="CREATE TABLE project_goals (
    goal_id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(project_id) ON DELETE CASCADE,
    INDEX (project_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
// Execute query
if (mysqli_query($connection, $sql)) {
    echo "Table project_goals created successfully";
} else {
    echo "Error creating table: " . mysqli_error($connection);
}

// Close connection
mysqli_close($connection);
?>
