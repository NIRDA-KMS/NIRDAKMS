<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Schedule Event | NIRDA Knowledge Management System</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
  
 

  
</head>
<body>
<?php // Use the actual characters instead of URL encoding:
include_once("../Internees' task/header.php");?>

<div class="main-content">
    <div class="event-form-container">
        <h2><i class="fas fa-calendar-plus"></i> Schedule New Event</h2>
        
        <form id="eventForm" class="event-form">
            <!-- Event Title -->
            <div class="form-group">
                <label for="eventTitle">
                    <i class="fas fa-heading"></i> Event Title
                </label>
                <input type="text" id="eventTitle" name="eventTitle" required>
            </div>
            
            <!-- Date and Time -->
            <div class="form-row">
                <div class="form-group">
                    <label for="startDateTime">
                        <i class="fas fa-calendar"></i> Start Date & Time
                    </label>
                    <input type="datetime-local" id="startDateTime" name="startDateTime" required>
                </div>
                
                <div class="form-group">
                    <label for="endDateTime">
                        <i class="fas fa-clock"></i> End Date & Time
                    </label>
                    <input type="datetime-local" id="endDateTime" name="endDateTime" required>
                </div>
            </div>
            
            <!-- Location -->
            <div class="form-group">
                <label for="eventLocation">
                    <i class="fas fa-location-dot"></i> Location (Physical or URL)
                </label>
                <input type="text" id="eventLocation" name="eventLocation">
            </div>
            
            <!-- Description -->
            <div class="form-group">
                <label for="eventDescription">
                    <i class="fas fa-align-left"></i> Description
                </label>
                <textarea id="eventDescription" name="eventDescription" rows="4"></textarea>
            </div>
            
            <!-- Attendees -->
            <div class="form-group">
                <label>
                    <i class="fas fa-users"></i> Invite Attendees
                </label>
                <select id="eventAttendees" name="eventAttendees" multiple>
                    <!-- Options will be populated via JavaScript -->
                </select>
            </div>
            
            <!-- Recurring Options -->
            <div class="form-group">
                <label for="recurringOption">
                    <i class="fas fa-repeat"></i> Recurrence
                </label>
                <select id="recurringOption" name="recurringOption">
                    <option value="none">Does not repeat</option>
                    <option value="daily">Daily</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                    <option value="yearly">Yearly</option>
                </select>
            </div>
            
            <!-- Reminders -->
            <div class="form-group">
                <label>
                    <i class="fas fa-bell"></i> Reminders
                </label>
                <div class="reminder-options">
                    <div class="checkbox-group">
                        <input type="checkbox" id="emailReminder" name="emailReminder">
                        <label for="emailReminder">Email Notification</label>
                    </div>
                    <div class="checkbox-group">
                        <input type="checkbox" id="appReminder" name="appReminder">
                        <label for="appReminder">In-App Notification</label>
                    </div>
                    <div class="reminder-time">
                        <label for="reminderTime">Remind me:</label>
                        <select id="reminderTime" name="reminderTime">
                            <option value="15">15 minutes before</option>
                            <option value="60">1 hour before</option>
                            <option value="1440">1 day before</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-calendar-plus"></i> Create Event
                </button>
                <button type="button" class="btn-secondary" id="cancelBtn">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="../js/schedule-event.js"></script>
<script>
    $(document).ready(function() {
        // Initialize sidebar toggle
        $('#sidebarCollapse').on('click', function() {
            $('#sidebar, nav:not(.navbar), .main-content').toggleClass('active');
        });
        
        // Highlight current menu item
        $('.main-nav a[href="schedule_event.php"]').addClass('active');
    });
</script>


</body>
</html>