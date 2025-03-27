<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Schedule Event | NIRDA Knowledge Management System</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
  <style>
    /* Color Variables */
    :root {
      --primary-color: #1a237e;
      --secondary-color: #2c3e50;
      --accent-color: #00A0DF;
      --background-color: #f0f2f5;
      --text-color: #333333;
      --light-text: #ffffff;
      --border-color: #d1d5db;
    }

    /* Base Styles */
    body {
      font-family: 'Roboto', sans-serif;
      background-color: var(--background-color);
      color: var(--text-color);
      margin: 0;
      padding: 0;
      line-height: 1.6;
    }

    /* Form Container */
    .event-form-container {
      max-width: 650px;
      margin: 30px auto;
      background-color: white;
      border-radius: 8px;
      box-shadow: 3px 6px 15px rgba(0, 0, 0, 0.15);
      padding: 30px;
    }

    /* Form Header */
    .form-header {
      text-align: center;
      margin-bottom: 30px;
      color: var(--primary-color);
      border-bottom: 2px solid var(--accent-color);
      padding-bottom: 15px;
    }

    .form-header h2 {
      margin: 0;
      font-weight: 500;
    }

    .form-header i {
      color: var(--accent-color);
      margin-right: 10px;
    }

    /* Form Structure */
    .event-form {
      display: flex;
      flex-direction: column;
      gap: 25px;
    }

    .form-group {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .form-group label {
      font-weight: 500;
      color: var(--secondary-color);
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .form-group label i {
      width: 20px;
      text-align: center;
      color: var(--primary-color);
    }

    /* Form Inputs */
    .form-control {
      border: 1px solid var(--border-color);
      padding: 12px 15px;
      border-radius: 6px;
      font-family: 'Roboto', sans-serif;
      font-size: 15px;
      transition: all 0.3s;
      background-color: white;
      color: var(--text-color);
    }

    .form-control:focus {
      border-color: var(--accent-color);
      box-shadow: 0 0 0 3px rgba(0, 160, 223, 0.2);
      outline: none;
    }

    textarea.form-control {
      resize: vertical;
      min-height: 120px;
    }

    select[multiple].form-control {
      height: auto;
      min-height: 120px;
      padding: 10px;
    }

    /* Form Rows */
    .form-row {
      display: flex;
      gap: 20px;
    }

    .form-row .form-group {
      flex: 1;
    }

    /* Reminder Section */
    .reminder-section {
      background: rgba(240, 242, 245, 0.5);
      padding: 20px;
      border-radius: 8px;
      border: 1px solid var(--border-color);
    }

    .reminder-options {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    .checkbox-group {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .checkbox-group input[type="checkbox"] {
      width: 18px;
      height: 18px;
      accent-color: var(--accent-color);
    }

    .checkbox-group label {
      font-weight: 400;
      color: var(--text-color);
    }

    .reminder-time {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-top: 10px;
    }

    .reminder-time select {
      padding: 8px 12px;
      border-radius: 4px;
      border: 1px solid var(--border-color);
      background-color: white;
    }

    /* Form Actions */
    .form-actions {
      display: flex;
      justify-content: flex-end;
      gap: 15px;
      margin-top: 30px;
      padding-top: 20px;
      border-top: 1px solid var(--border-color);
    }

    .btn {
      padding: 12px 25px;
      border-radius: 6px;
      font-weight: 500;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 8px;
      transition: all 0.3s;
      border: none;
      font-family: 'Roboto', sans-serif;
    }

    .btn-primary {
      background-color: var(--primary-color);
      color: var(--light-text);
    }

    .btn-primary:hover {
      background-color: #121a5e;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .btn-secondary {
      background-color: var(--background-color);
      color: var(--secondary-color);
    }

    .btn-secondary:hover {
      background-color: #e0e2e5;
    }

    /* Validation Styles */
    .is-invalid {
      border-color: #e74c3c !important;
    }

    .invalid-feedback {
      color: #e74c3c;
      font-size: 0.875rem;
      margin-top: 5px;
    }

    /* Responsive Styles */
    @media (max-width: 768px) {
      .event-form-container {
        padding: 20px;
        margin: 20px;
      }

      .form-row {
        flex-direction: column;
        gap: 15px;
      }

      .form-actions {
        flex-direction: column;
        gap: 10px;
      }

      .btn {
        width: 100%;
        justify-content: center;
      }
    }
  </style>
</head>
<body>
<?php include_once("../Internees' task/header.php"); ?>

<div class="main-content">
  <div class="event-form-container">
    <div class="form-header">
      <h2><i class="fas fa-calendar-plus"></i> Schedule New Event</h2>
    </div>
    
    <form id="eventForm" class="event-form">
      <!-- Event Title -->
      <div class="form-group">
        <label for="eventTitle">
          <i class="fas fa-heading"></i> Event Title
        </label>
        <input type="text" id="eventTitle" name="eventTitle" class="form-control" >
        <div class="invalid-feedback">Please provide an event title</div>
      </div>
      
      <!-- Date and Time -->
      <div class="form-row">
        <div class="form-group">
          <label for="startDateTime">
            <i class="fas fa-calendar"></i> Start Date & Time
          </label>
          <input type="datetime-local" id="startDateTime" name="startDateTime" class="form-control" >
          <div class="invalid-feedback">Please select a start time</div>
        </div>
        
        <div class="form-group">
          <label for="endDateTime">
            <i class="fas fa-clock"></i> End Date & Time
          </label>
          <input type="datetime-local" id="endDateTime" name="endDateTime" class="form-control">
          <div class="invalid-feedback">Please select an end time</div>
        </div>
      </div>
      
      <!-- Location -->
      <div class="form-group">
        <label for="eventLocation">
          <i class="fas fa-location-dot"></i> Location
        </label>
        <input type="text" id="eventLocation" name="eventLocation" class="form-control" placeholder="Physical address or meeting URL">
      </div>
      
      <!-- Description -->
      <div class="form-group">
        <label for="eventDescription">
          <i class="fas fa-align-left"></i> Description
        </label>
        <textarea id="eventDescription" name="eventDescription" rows="5" class="form-control" placeholder="Enter event details..."></textarea>
      </div>
      
      <!-- Attendees -->
      <div class="form-group">
        <label>
          <i class="fas fa-users"></i> Invite Attendees
        </label>
        <select id="eventAttendees" name="eventAttendees" multiple class="form-control">
          <option value="user1">John Doe (Marketing)</option>
          <option value="user2">Jane Smith (Development)</option>
          <option value="user3">Mike Johnson (HR)</option>
          <option value="user4">Sarah Williams (Finance)</option>
        </select>
        <small class="text-muted" style="color: #6b7280;">Hold Ctrl/Cmd to select multiple attendees</small>
      </div>
      
      <!-- Recurring Options -->
      <div class="form-group">
        <label for="recurringOption">
          <i class="fas fa-repeat"></i> Recurrence
        </label>
        <select id="recurringOption" name="recurringOption" class="form-control">
          <option value="none">None</option>
          <option value="daily">Daily</option>
          <option value="weekly">Weekly</option>
          <option value="monthly">Monthly</option>
          <option value="yearly">Yearly</option>
        </select>
      </div>
      
      <!-- Reminders -->
      <div class="reminder-section">
        <div class="form-group">
          <label>
            <i class="fas fa-bell"></i> Reminders
          </label>
          <div class="reminder-options">
            <div class="checkbox-group">
              <input type="checkbox" id="emailReminder" name="emailReminder" checked>
              <label for="emailReminder">Email Notification</label>
            </div>
            <div class="checkbox-group">
              <input type="checkbox" id="appReminder" name="appReminder" checked>
              <label for="appReminder">In-App Notification</label>
            </div>
            <div class="reminder-time">
              <label for="reminderTime">Remind me:</label>
              <select id="reminderTime" name="reminderTime" class="form-control">
                <option value="15">15 minutes before</option>
                <option value="60" selected>1 hour before</option>
                <option value="1440">1 day before</option>
                <option value="2880">2 days before</option>
              </select>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Form Actions -->
      <div class="form-actions">
        <button type="button" class="btn btn-secondary" id="cancelBtn">
          <i class="fas fa-times"></i> Cancel
        </button>
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-calendar-plus"></i> Create Event
        </button>
      </div>
    </form>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

$(document).ready(function() {
    // Sidebar and navigation initialization
    $('#sidebarCollapse').on('click', function(e) {
        e.preventDefault();
        $('#sidebar').toggleClass('active');
        $('nav:not(.navbar)').toggleClass('sidebar-active');
        $('.main-content').toggleClass('sidebar-active');
    });
    
    // Highlight current menu item
    $('.main-nav a[href="schedule_event.php"]').addClass('active');

    // Real-time validation for all fields
    $('#eventTitle').on('input', validateTitle);
    $('#startDateTime, #endDateTime').on('change', validateDates);
    $('#eventLocation').on('input', validateLocation);
    
    // Form submission handler
    $('#eventForm').on('submit', function(e) {
        e.preventDefault();
        
        // Validate all fields
        const isTitleValid = validateTitle();
        const areDatesValid = validateDates();
        const isLocationValid = validateLocation();
        
        if (isTitleValid && areDatesValid && isLocationValid) {
            submitForm();
        }
    });
    
    // Cancel button
    $('#cancelBtn').on('click', function() {
        if (confirm('Are you sure you want to cancel? Any unsaved changes will be lost.')) {
            window.location.href = 'events.php';
        }
    });
    
    // Auto-adjust for footer height
    function adjustForFooter() {
        const footerHeight = $('.footer').outerHeight();
        $('.main-content').css('padding-bottom', footerHeight + 20);
    }
    
    adjustForFooter();
    $(window).resize(adjustForFooter);

    // Field validation functions
    function validateTitle() {
        const title = $('#eventTitle').val().trim();
        const isValid = title.length > 0;
        
        $('#eventTitle').toggleClass('is-invalid', !isValid);
        $('#eventTitle').next('.invalid-feedback').toggle(!isValid);
        
        return isValid;
    }
    
    function validateDates() {
        const startTime = $('#startDateTime').val();
        const endTime = $('#endDateTime').val();
        let isValid = true;
        
        // Validate start time
        if (!startTime) {
            $('#startDateTime').addClass('is-invalid');
            $('#startDateTime').next('.invalid-feedback').text('Please select a start time').show();
            isValid = false;
        } else {
            $('#startDateTime').removeClass('is-invalid');
            $('#startDateTime').next('.invalid-feedback').hide();
        }
        
        // Validate end time
        if (!endTime) {
            $('#endDateTime').addClass('is-invalid');
            $('#endDateTime').next('.invalid-feedback').text('Please select an end time').show();
            isValid = false;
        } else {
            $('#endDateTime').removeClass('is-invalid');
            $('#endDateTime').next('.invalid-feedback').hide();
        }
        
        // Validate date range if both exist
        if (startTime && endTime && new Date(startTime) >= new Date(endTime)) {
            $('#endDateTime').addClass('is-invalid');
            $('#endDateTime').next('.invalid-feedback').text('End time must be after start time').show();
            isValid = false;
        }
        
        return isValid;
    }
    
    function validateLocation() {
        const location = $('#eventLocation').val().trim();
        const isValid = location.length > 0;
        
        $('#eventLocation').toggleClass('is-invalid', !isValid);
        
        // Create or update feedback element
        let feedback = $('#eventLocation').next('.invalid-feedback');
        if (feedback.length === 0) {
            feedback = $('<div class="invalid-feedback">Please provide a location</div>');
            $('#eventLocation').after(feedback);
        }
        feedback.toggle(!isValid);
        
        return isValid;
    }
    
    function submitForm() {
        // AJAX form submission
        $.ajax({
            url: 'api/schedule_event.php',
            type: 'POST',
            data: $('#eventForm').serialize(),
            beforeSend: function() {
                $('.btn-primary').html('<i class="fas fa-spinner fa-spin"></i> Processing...').prop('disabled', true);
            },
            success: function(response) {
                if (response.success) {
                    alert('Event scheduled successfully!');
                    window.location.href = 'events.php';
                } else {
                    alert('Error: ' + response.message);
                    $('.btn-primary').html('<i class="fas fa-calendar-plus"></i> Create Event').prop('disabled', false);
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
                $('.btn-primary').html('<i class="fas fa-calendar-plus"></i> Create Event').prop('disabled', false);
            }
        });
    }
});



  // Sidebar Toggle Functionality
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebarCollapse');
    
    // Initialize from localStorage
    if(localStorage.getItem('sidebarState') === 'open') {
        sidebar.classList.add('active');
        document.body.classList.add('sidebar-open');
        document.querySelector('.main-content')?.classList.add('sidebar-active');
    }
    
    // Toggle sidebar
    if(toggleBtn) {
        toggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const isOpening = !sidebar.classList.contains('active');
            
            sidebar.classList.toggle('active');
            document.body.classList.toggle('sidebar-open');
            document.querySelector('.main-content')?.classList.toggle('sidebar-active');
            
            localStorage.setItem('sidebarState', isOpening ? 'open' : 'closed');
        });
    }
    
    // Highlight current page in sidebar
    const currentPage = window.location.pathname.split('/').pop() || 'index.php';
    document.querySelectorAll('.sidebar a').forEach(link => {
        if(link.getAttribute('href').includes(currentPage)) {
            link.classList.add('active');
        }
    });
});
</script>
</body>
</html>