<?php if (isset($_SESSION['login_success']) && $_SESSION['login_success']): ?>
<div class="login-success-popup">
    <div class="popup-content">
        <h3>Login Successful!</h3>
        <p>Welcome back, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</p>
        <div class="popup-actions">
            <a href="/NIRDAKMS/dashboard.php" class="btn-continue">Continue to Dashboard</a>
            <a href="/NIRDAKMS/profile.php" class="btn-profile">View Profile</a>
        </div>
    </div>
</div>
<?php 
    unset($_SESSION['login_success']); // Clear the flag
endif; 
?>

<style>
.login-success-popup {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.7);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}
.popup-content {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    max-width: 400px;
    text-align: center;
}
.popup-actions {
    margin-top: 1.5rem;
    display: flex;
    gap: 1rem;
    justify-content: center;
}
.btn-continue, .btn-profile {
    padding: 0.5rem 1rem;
    border-radius: 4px;
    text-decoration: none;
}
.btn-continue {
    background: #3498db;
    color: white;
}
.btn-profile {
    background: #f5f5f5;
    color: #333;
}
</style>