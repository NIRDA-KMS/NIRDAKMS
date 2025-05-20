<?php
include 'connect.php';


// SQL to create attendees table
$sql = "INSERT INTO `roles` (`role_id`, `role_name`, `description`) VALUES
(1, 'Administrator', 'Has all the privillages'),
(2, 'KM Specialist', 'has all privilleges'),
(3, 'KM Officer', 'limited privilegies'),
(4, 'Manager ', 'Have limited Privileges'),
(5, 'Director', 'Has 3 Previlages')";



// Execute query
if (mysqli_query($connection, $sql)) {
    echo "Table project_goals created successfully";

} else {
    echo "Error creating table: " . mysqli_error($connection);
}

// Close connection
mysqli_close($connection);
?>
