<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add User | NIRDA Knowledge Management System</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
  <style>
    :root {
      --primary-color: #1a237e;
      --secondary-color: #2c3e50;
      --accent-color: #00A0DF;
      --background-color: #f0f2f5;
      --text-color: #333333;
      --light-text: #ffffff;
      --border-color: #d1d5db;
      --error-color: #dc3545;
      --success-color: #28a745;
    }

    body {
      font-family: 'Roboto', sans-serif;
      background-color: var(--background-color);
      color: var(--text-color);
      margin: 0;
      padding: 0;
      line-height: 1.6;
    }

    .main-content {
      padding: 20px;
    }

    .content-container {
      max-width: 800px;
      margin: 20px auto;
      background-color: white;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      padding: 30px;
    }

    h2 {
      color: var(--primary-color);
      margin-top: 0;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .form-group {
      margin-bottom: 20px;
    }

    label {
      display: block;
      margin-bottom: 8px;
      font-weight: 500;
    }

    .required-field::after {
      content: " *";
      color: var(--error-color);
    }

    input[type="text"],
    input[type="email"],
    input[type="password"],
    select {
      width: 100%;
      padding: 10px 15px;
      border: 1px solid var(--border-color);
      border-radius: 4px;
      font-size: 1rem;
      transition: border-color 0.3s;
    }

    input[type="text"]:focus,
    input[type="email"]:focus,
    input[type="password"]:focus,
    select:focus {
      outline: none;
      border-color: var(--accent-color);
      box-shadow: 0 0 0 2px rgba(0, 160, 223, 0.2);
    }

    .form-actions {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      margin-top: 30px;
    }

    .btn {
      padding: 10px 20px;
      border-radius: 4px;
      border: none;
      cursor: pointer;
      font-size: 1rem;
      font-weight: 500;
      transition: all 0.2s;
    }

    .btn-primary {
      background-color: var(--accent-color);
      color: white;
    }

    .btn-secondary {
      background-color: var(--secondary-color);
      color: white;
    }

    .btn:hover {
      opacity: 0.9;
      transform: translateY(-1px);
    }

    .error-message {
      color: var(--error-color);
      font-size: 0.85rem;
      margin-top: 5px;
      display: none;
    }

    .password-strength {
      margin-top: 5px;
      height: 5px;
      background-color: #e0e0e0;
      border-radius: 2px;
      overflow: hidden;
    }

    .password-strength-bar {
      height: 100%;
      width: 0%;
      transition: width 0.3s, background-color 0.3s;
    }

    .status-toggle {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .toggle-switch {
      position: relative;
      display: inline-block;
      width: 50px;
      height: 24px;
    }

    .toggle-switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }

    .toggle-slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: #ccc;
      transition: .4s;
      border-radius: 24px;
    }

    .toggle-slider:before {
      position: absolute;
      content: "";
      height: 16px;
      width: 16px;
      left: 4px;
      bottom: 4px;
      background-color: white;
      transition: .4s;
      border-radius: 50%;
    }

    input:checked + .toggle-slider {
      background-color: var(--success-color);
    }

    input:checked + .toggle-slider:before {
      transform: translateX(26px);
    }

    @media (max-width: 768px) {
      .content-container {
        padding: 20px;
      }
      
      .form-actions {
        flex-direction: column;
      }
      
      .btn {
        width: 100%;
      }
    }
  </style>
</head>
<body>
<?php 
require_once '../../SchedureEvent/connect.php'; // Include your database connection file
$page_title = "Add User";
include_once("../../Internees_task/header.php"); 
?>

<div class="main-content">
  <div class="content-container">
    <h2><i class="fas fa-user-plus"></i> Add New User</h2>
    
    <form id="addUserForm" action="process_add_user.php" method="POST">
      <div class="form-group">
        <label for="username" class="required-field">Username</label>
        <input type="text" id="username" name="username" required>
        <div class="error-message" id="username-error">Username is required and must be unique</div>
      </div>
      
      <div class="form-group">
        <label for="email" class="required-field">Email</label>
        <input type="email" id="email" name="email" required>
        <div class="error-message" id="email-error">Please enter a valid email address</div>
      </div>
      
      <div class="form-group">
        <label for="password" class="required-field">Password</label>
        <input type="password" id="password" name="password" required>
        <div class="password-strength">
          <div class="password-strength-bar" id="password-strength-bar"></div>
        </div>
        <div class="error-message" id="password-error">Password must be at least 8 characters</div>
      </div>
      
      <div class="form-group">
        <label for="confirm_password" class="required-field">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
        <div class="error-message" id="confirm-password-error">Passwords do not match</div>
      </div>
      
      <div class="form-group">
        <label for="role" class="required-field">Role</label>
        <select id="role" name="role" required>
          <option value="">Select a role</option>
          <option value="admin">Administrator</option>
          <option value="organizer">Event Organizer</option>
          <option value="attendee" selected>Attendee</option>
        </select>
      </div>
      
      <div class="form-group">
        <label>Status</label>
        <div class="status-toggle">
          <label class="toggle-switch">
            <input type="checkbox" id="status" name="status" checked>
            <span class="toggle-slider"></span>
          </label>
          <span id="status-text">Active</span>
        </div>
      </div>
      
      <div class="form-actions">
        <button type="button" class="btn btn-secondary" onclick="window.location.href='manage_users.php'">Cancel</button>
        <button type="submit" class="btn btn-primary">Add User</button>
      </div>
    </form>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function() {
    // Toggle switch functionality
    $('#status').change(function() {
      $('#status-text').text(this.checked ? 'Active' : 'Inactive');
    });
    
    // Password strength indicator
    $('#password').on('input', function() {
      const password = $(this).val();
      const strengthBar = $('#password-strength-bar');
      let strength = 0;
      
      if (password.length >= 8) strength += 1;
      if (password.match(/[a-z]/)) strength += 1;
      if (password.match(/[A-Z]/)) strength += 1;
      if (password.match(/[0-9]/)) strength += 1;
      if (password.match(/[^a-zA-Z0-9]/)) strength += 1;
      
      let width = strength * 20;
      let color = '#dc3545'; // red
      
      if (strength >= 4) color = '#28a745'; // green
      else if (strength >= 2) color = '#ffc107'; // yellow
      
      strengthBar.css({
        'width': width + '%',
        'background-color': color
      });
    });
    
    // Form validation
    $('#addUserForm').on('submit', function(e) {
      let isValid = true;
      
      // Validate username
      if ($('#username').val().trim() === '') {
        $('#username-error').show();
        isValid = false;
      } else {
        $('#username-error').hide();
      }
      
      // Validate email
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test($('#email').val())) {
        $('#email-error').show();
        isValid = false;
      } else {
        $('#email-error').hide();
      }
      
      // Validate password
      if ($('#password').val().length < 8) {
        $('#password-error').show();
        isValid = false;
      } else {
        $('#password-error').hide();
      }
      
      // Validate password confirmation
      if ($('#password').val() !== $('#confirm_password').val()) {
        $('#confirm-password-error').show();
        isValid = false;
      } else {
        $('#confirm-password-error').hide();
      }
      
      if (!isValid) {
        e.preventDefault();
        $('html, body').animate({
          scrollTop: $('.error-message:visible').first().parent().offset().top - 20
        }, 500);
      }
    });
    
    // Check username availability (AJAX)
    $('#username').on('blur', function() {
      const username = $(this).val().trim();
      if (username !== '') {
        $.ajax({
          url: 'check_username.php',
          method: 'POST',
          data: { username: username },
          success: function(response) {
            if (response.available) {
              $('#username-error').hide();
            } else {
              $('#username-error').text('Username is already taken').show();
            }
          }
        });
      }
    });
  });
</script>
</body>
</html>