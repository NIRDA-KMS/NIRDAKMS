<?php
// Database configuration
$host = '127.0.0.1:3307';     // Database server
$user = 'root';          // Database username
$pass = '';              // Database password
$dbname = 'NIRDAKMS';      // Database name

// Establish connection
$connection = mysqli_connect($host, $user, $pass, $dbname);

    if($connection){
        // echo "connected";
    }
    else{
        echo"could not connect to NIRDAKMS";
    }

?>

