<?php
include 'connect.php';


// SQL to create attendees table
$sql = "CREATE TABLE `group_members` (
  `member_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_admin` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`member_id`),
  UNIQUE KEY `unique_membership` (`group_id`,`user_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `fk_group_member` FOREIGN KEY (`group_id`) REFERENCES `chat_groups` (`group_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_member_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
";


// Execute query
if (mysqli_query($connection, $sql)) {
    echo "Table attendees created successfully";

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
