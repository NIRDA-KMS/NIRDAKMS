<?php
// auth_check.php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Save the full requested URI (including query string)
    $redirect_url = $_SERVER['REQUEST_URI'];
    $_SESSION['redirect_url'] = $redirect_url;

    // Redirect to the login page (assumed to be in /auth/)
    header("Location: /NIRDAKMS/Internees_task/auth/login.php");
    exit();
}
        $redirect_url = $_SESSION['redirect_url'] ?? '/Internees_task/Manage_Forums.php';

// Optional role/permission check
// if ($_SESSION['role_id'] != REQUIRED_ROLE_ID) {
//     header("Location: /unauthorized.php");
//     exit();
// }
?>
