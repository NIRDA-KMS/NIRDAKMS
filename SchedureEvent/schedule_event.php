<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
include('connect.php');

// Check connection
if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['createEvent'])) {
    // Validate and sanitize inputs
    $errors = [];
    
    // Required fields
    $required = [
        'eventTitle' => 'Event title',
        'startDateTime' => 'Start date/time',
        'endDateTime' => 'End date/time',
        'eventLocation' => 'Location',
        'eventDescription' => 'Description'
    ];
    
    foreach ($required as $field => $name) {
        if (empty($_POST[$field])) {
            $errors[] = "$name is required";
        }
    }
    
    // Validate date/time
    if (!empty($_POST['startDateTime']) && !empty($_POST['endDateTime'])) {
        $startDate = new DateTime($_POST['startDateTime']);
        $endDate = new DateTime($_POST['endDateTime']);
        
        if ($endDate <= $startDate) {
            $errors[] = "End date/time must be after start date/time";
        }
    }
    
    // If no validation errors, proceed
    if (empty($errors)) {
        // Prepare data
        $eventtitle = mysqli_real_escape_string($connection, $_POST['eventTitle']);
        $startingdate = mysqli_real_escape_string($connection, $_POST['startDateTime']);
        $endingdate = mysqli_real_escape_string($connection, $_POST['endDateTime']);
        $location = mysqli_real_escape_string($connection, $_POST['eventLocation']);
        $description = mysqli_real_escape_string($connection, $_POST['eventDescription']);
        
        // Handle attendees
        $attendees = isset($_POST['attend']) ? $_POST['attend'] : '';
        
        // Handle checkboxes
        $recurrence = isset($_POST['recurrence']) ? mysqli_real_escape_string($connection, $_POST['recurrence']) : 'none';
        $emailReminder = isset($_POST['emailReminder']) ? 1 : 0;
        $appReminder = isset($_POST['appReminder']) ? 1 : 0;
        $reminderTime = isset($_POST['reminderTime']) 
            ? mysqli_real_escape_string($connection, $_POST['reminderTime']) 
            : '60'; // Default to 1 hour
        
        // Start transaction
        mysqli_begin_transaction($connection);
        
        try {
            // Insert event with prepared statement
            $sql = "INSERT INTO schedule_events (
                    eventTitle, 
                    startDateTime, 
                    endingDateTime, 
                    eventLocation, 
                    eventDescription, 
                    attend, 
                    recurrence,
                    emailReminder,  
                    appReminder, 
                    reminderTime
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = mysqli_prepare($connection, $sql);
            if (!$stmt) {
                throw new Exception("Error preparing statement: " . mysqli_error($connection));
            }
            
            mysqli_stmt_bind_param(
                $stmt, 
                "sssssssiis", 
                $eventtitle, 
                $startingdate, 
                $endingdate, 
                $location, 
                $description, 
                $attendees, 
                $recurrence,
                $emailReminder, 
                $appReminder, 
                $reminderTime
            );
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Error executing query: " . mysqli_error($connection));
            }
            
            $eventId = mysqli_insert_id($connection);
            mysqli_stmt_close($stmt);
            
            // Process attendees if provided
            if (!empty($attendees)) {
                $emails = array_map('trim', explode(',', $attendees));
                $emails = array_filter($emails, function($email) {
                    return filter_var($email, FILTER_VALIDATE_EMAIL);
                });
                
                if (!empty($emails)) {
                    $attendeeStmt = mysqli_prepare($connection, 
                        "INSERT INTO attendees (event_id, email) VALUES (?, ?)");
                    
                    if (!$attendeeStmt) {
                        throw new Exception("Error preparing attendee statement: " . mysqli_error($connection));
                    }
                    
                    foreach ($emails as $email) {
                        mysqli_stmt_bind_param($attendeeStmt, "is", $eventId, $email);
                        if (!mysqli_stmt_execute($attendeeStmt)) {
                            throw new Exception("Error adding attendee: " . mysqli_error($connection));
                        }
                    }
                    mysqli_stmt_close($attendeeStmt);
                    
                    // Send initial confirmation to attendees if email reminder is enabled
                    if ($emailReminder) {
                      require __DIR__ . '/../vendor/autoload.php';
                        $mail = new PHPMailer(true);
                        
                        try {
                            // SMTP configuration
                            $mail->isSMTP();
                            $mail->Host       = 'smtp.gmail.com';
                            $mail->SMTPAuth   = true;
                            $mail->Username   = 'rurangirwakhassim84@gmail.com';
                            $mail->Password   = 'cbsu vzxr jvgq oiol';
                            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                            $mail->Port       = 587;
                            
                            $mail->setFrom('no-reply@nirdakms.com', 'NIRDA Event System');
                            $mail->isHTML(true);
                            
                            $startDate = new DateTime($startingdate);
                            $endDate = new DateTime($endingdate);
                            
                            foreach ($emails as $email) {
                                $mail->clearAddresses();
                                $mail->addAddress($email);
                                
                                $mail->Subject = "Invitation: " . $eventtitle;
                                $mail->Body    = "
                                    <h2>You're Invited!</h2>
                                    <p>You've been invited to attend the following event:</p>
                                    <p><strong>Event:</strong> {$eventtitle}</p>
                                    <p><strong>Date:</strong> {$startDate->format('F j, Y')}</p>
                                    <p><strong>Time:</strong> {$startDate->format('g:i A')} - {$endDate->format('g:i A')}</p>
                                    <p><strong>Location:</strong> {$location}</p>
                                    <p><strong>Description:</strong> {$description}</p>
                                    <p>You will receive a reminder {$reminderTime} minutes before the event.</p>
                                ";
                                
                                $mail->send();
                            }
                        } catch (Exception $e) {
                            // Log email error but don't fail the entire operation
                            error_log("Failed to send invitation emails: " . $e->getMessage());
                        }
                    }
                }
            }
            
            // Commit transaction
            mysqli_commit($connection);
            $success = "Event scheduled successfully!";
            $_POST = array();
            
        } catch (Exception $e) {
            // Rollback on error
            mysqli_rollback($connection);
            $errors[] = $e->getMessage();
        }
    }
}

// Close connection
mysqli_close($connection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Schedule Event | NIRDA Knowledge Management System</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
  
  <!-- Your CSS styles here (I've omitted them as you mentioned you'll add them back) -->
  
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
      --error-color: #e74c3c;
      --success-color: #2ecc71;
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
      border-color: var(--error-color) !important;
    }

    .invalid-feedback {
      color: var(--error-color);
      font-size: 0.875rem;
      margin-top: 5px;
    }

    /* Success and Error Messages */
    .alert {
      padding: 15px;
      margin-bottom: 20px;
      border-radius: 4px;
      font-weight: 500;
    }

    .alert-success {
      background-color: rgba(46, 204, 113, 0.2);
      color: var(--success-color);
      border: 1px solid var(--success-color);
    }

    .alert-error {
      background-color: rgba(231, 76, 60, 0.2);
      color: var(--error-color);
      border: 1px solid var(--error-color);
    }

    .alert ul {
      margin: 5px 0 0 0;
      padding-left: 20px;
    }
    .alert-success {
    animation: fadeOut 3s forwards;
}

@keyframes fadeOut {
    0% { opacity: 1; }
    100% { opacity: 0; display: none; }
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
  <a href=".."></a>
  <?php include_once("../Internees_task/header.php"); ?>

  <div class="main-content">
    <div class="event-form-container">
      <div class="form-header">
        <h2><i class="fas fa-calendar-plus"></i> Schedule New Event</h2>
      </div>
      
      <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
          <strong>Error!</strong>
          <ul>
            <?php foreach ($errors as $error): ?>
              <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
      
      <?php if (isset($success)): ?>
        <div class="alert alert-success">
          <?php echo htmlspecialchars($success); ?>
        </div>
      <?php endif; ?>
      
      <form id="eventForm" class="event-form" method="POST" action="">
        <!-- Event Title -->
        <div class="form-group">
          <label for="eventTitle">
            <i class="fas fa-heading"></i> Event Title <span class="required">*</span>
          </label>
          <input type="text" id="eventTitle" name="eventTitle" class="form-control <?php echo (!empty($_POST)) && empty($_POST['eventTitle']) ? 'is-invalid' : ''; ?>" 
                 value="<?php echo isset($_POST['eventTitle']) ? htmlspecialchars($_POST['eventTitle']) : ''; ?>" required>
          <div class="invalid-feedback">Please provide an event title</div>
        </div>
        
        <!-- Date and Time -->
        <div class="form-row">
          <div class="form-group">
            <label for="startDateTime">
              <i class="fas fa-calendar"></i> Start Date & Time <span class="required">*</span>
            </label>
            <input type="datetime-local" id="startDateTime" name="startDateTime" class="form-control <?php echo (!empty($_POST) && empty($_POST['startDateTime']))? 'is-invalid' : ''; ?>" 
                   value="<?php echo isset($_POST['startDateTime']) ? htmlspecialchars($_POST['startDateTime']) : ''; ?>" required>
            <div class="invalid-feedback">Please select a start time</div>
          </div>
          
          <div class="form-group">
            <label for="endDateTime">
              <i class="fas fa-clock"></i> End Date & Time <span class="required">*</span>
            </label>
            <input type="datetime-local" id="endDateTime" name="endDateTime" class="form-control <?php echo (!empty($_POST) && empty($_POST['endDateTime'])) ? 'is-invalid' : ''; ?>" 
                   value="<?php echo isset($_POST['endDateTime']) ? htmlspecialchars($_POST['endDateTime']) : ''; ?>" required>
            <div class="invalid-feedback">
              <?php 
                if (!empty($_POST) && !empty($_POST['startDateTime']) && !empty($_POST['endDateTime']) && 
                    new DateTime($_POST['endDateTime']) <= new DateTime($_POST['startDateTime'])) {
                  echo "End time must be after start time";
                } else {
                  echo "Please select an end time";
                }
              ?>
            </div>
          </div>
        </div>
        
        <!-- Location -->
        <div class="form-group">
          <label for="eventLocation">
            <i class="fas fa-location-dot"></i> Location <span class="required">*</span>
          </label>
          <input type="text" id="eventLocation" name="eventLocation" class="form-control <?php echo (!empty($_POST) && empty($_POST['eventLocation']))? 'is-invalid' : ''; ?>" 

                 value="<?php echo isset($_POST['eventLocation']) ? htmlspecialchars($_POST['eventLocation']) : ''; ?>" 
                 placeholder="Physical address or meeting URL" required>
          <div class="invalid-feedback">Please provide a location</div>
        </div>
        
        <!-- Description -->
        <div class="form-group">
          <label for="eventDescription">
            <i class="fas fa-align-left"></i> Description <span class="required">*</span>
          </label>
          <textarea id="eventDescription" name="eventDescription" rows="5" class="form-control <?php echo (!empty($_POST) && empty($_POST['eventDescription'])) ? 'is-invalid' : ''; ?>" 
                    placeholder="Enter event details..." required><?php 
                    echo isset($_POST['eventDescription']) ? htmlspecialchars($_POST['eventDescription']) : ''; 
                    ?></textarea>
          <div class="invalid-feedback">Please provide a description</div>
        </div>
        
        <!-- Attendees -->
        <div class="form-group">
          <label for="attend">
            <i class="fas fa-users"></i> Invite Attendees
          </label>
          <input type="text" id="attend" name="attend" class="form-control" 
                 value="<?php echo isset($_POST['attend']) ? htmlspecialchars($_POST['attend']) : ''; ?>" 
                 placeholder="Enter email addresses separated by commas">
          <small class="text-muted">Example: user1@example.com, user2@example.com</small>
        </div>
        
        <!-- Recurring Options -->
        <div class="form-group">
          <label for="recurringOption">
            <i class="fas fa-repeat"></i> Recurrence
          </label>
          <select id="recurringOption" name="recurrence" class="form-control">
            <option value="none" <?php echo (isset($_POST['recurrence']) && $_POST['recurrence'] == 'none') ? 'selected' : ''; ?>>None</option>
            <option value="daily" <?php echo (isset($_POST['recurrence']) && $_POST['recurrence'] == 'daily') ? 'selected' : ''; ?>>Daily</option>
            <option value="weekly" <?php echo (isset($_POST['recurrence']) && $_POST['recurrence'] == 'weekly') ? 'selected' : ''; ?>>Weekly</option>
            <option value="monthly" <?php echo (isset($_POST['recurrence']) && $_POST['recurrence'] == 'monthly') ? 'selected' : ''; ?>>Monthly</option>
            <option value="yearly" <?php echo (isset($_POST['recurrence']) && $_POST['recurrence'] == 'yearly') ? 'selected' : ''; ?>>Yearly</option>
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
                <input type="checkbox" id="emailReminder" name="emailReminder" value="1" <?php echo isset($_POST['emailReminder']) ? 'checked' : ''; ?>>
                <label for="emailReminder">Email Notification</label>
              </div>
              <div class="checkbox-group">
                <input type="checkbox" id="appReminder" name="appReminder" value="1" <?php echo isset($_POST['appReminder']) ? 'checked' : ''; ?>>
                <label for="appReminder">In-App Notification</label>
              </div>
              <div class="reminder-time">
                <label for="reminderTime">Remind me:</label>
                <select id="reminderTime" name="reminderTime" class="form-control">
                  <option value="15" <?php echo (isset($_POST['reminderTime']) && $_POST['reminderTime'] == '15') ? 'selected' : ''; ?>>15 minutes before</option>
                  <option value="60" <?php echo (isset($_POST['reminderTime']) && $_POST['reminderTime'] == '60') ? 'selected' : ''; ?>>1 hour before</option>
                  <option value="1440" <?php echo (isset($_POST['reminderTime']) && $_POST['reminderTime'] == '1440') ? 'selected' : ''; ?>>1 day before</option>
                  <option value="2880" <?php echo (isset($_POST['reminderTime']) && $_POST['reminderTime'] == '2880') ? 'selected' : ''; ?>>2 days before</option>
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
          <button type="submit" class="btn btn-primary" name="createEvent">
            <i class="fas fa-calendar-plus"></i> Create Event
          </button>
        </div>
      </form>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
  $(document).ready(function() {
      // Auto-hide success message after 3 seconds
      setTimeout(() => {
          $('.alert-success').fadeOut();
      }, 3000);
      
      // Form validation
      $('#eventForm').on('submit', function(e) {
          let isValid = true;
          
          // Validate required fields
          $('[required]').each(function() {
              if (!$(this).val().trim()) {
                  $(this).addClass('is-invalid');
                  isValid = false;
              } else {
                  $(this).removeClass('is-invalid');
              }
          });
          
          // Validate date range
          const startDate = new Date($('#startDateTime').val());
          const endDate = new Date($('#endDateTime').val());
          
          if (startDate && endDate && endDate <= startDate) {
              $('#endDateTime').addClass('is-invalid');
              $('#endDateTime').next('.invalid-feedback').text('End time must be after start time');
              isValid = false;
          }
          
          // Validate email format if attendees provided
          const attendEmails = $('#attend').val().trim();
          if (attendEmails) {
              const emails = attendEmails.split(',');
              for (const email of emails) {
                  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.trim())) {
                      $('#attend').addClass('is-invalid');
                      $('#attend').next('.text-muted').addClass('text-danger').text('Invalid email format: ' + email.trim());
                      isValid = false;
                      break;
                  }
              }
          }
          
          if (!isValid) {
              e.preventDefault();
              $('.is-invalid').first().focus();
          }
      });
      
      // Cancel button
      $('#cancelBtn').on('click', function() {
          if (confirm('Are you sure you want to cancel? Any unsaved changes will be lost.')) {
              window.location.href = 'manage_events.php';
          }
      });
      
      // Clear validation on input
      $('input, textarea, select').on('input change', function() {
          $(this).removeClass('is-invalid');
          if ($(this).attr('id') === 'attend') {
              $(this).next('.text-muted').removeClass('text-danger').text('Example: user1@example.com, user2@example.com');
          }
      });
  });
  </script>
</body>
</html>