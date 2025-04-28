<?php
// include('connect.php');
include(__DIR__ . '/connect.php');
// require 'vendor/autoload.php';
require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Get events that need reminders
$currentTime = date('Y-m-d H:i:s');
$query = "SELECT e.*, a.email, a.name 
          FROM schedule_events e
          JOIN attendees a ON e.event_id = a.event_id
          WHERE e.startDateTime > ? 
          AND a.reminder_sent = 0
          AND TIMESTAMPDIFF(MINUTE, NOW(), e.startDateTime) <= e.reminderTime
          AND TIMESTAMPDIFF(MINUTE, NOW(), e.startDateTime) > 0";

$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "s", $currentTime);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$events = [];
while ($row = mysqli_fetch_assoc($result)) {
    $events[] = $row;
}
mysqli_stmt_close($stmt);

if (empty($events)) {
    exit("No reminders to send at this time.");
}

$mail = new PHPMailer(true);
try {
    // SMTP Configuration
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'yvesrutembeza@gmail.com'; // Your Gmail
    $mail->Password   = 'rutembeza';    // App password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('no-reply@nirdakms.com', 'NIRDAKMS');
    $mail->isHTML(true);

    $sentCount = 0;
    foreach ($events as $event) {
        try {
            $mail->clearAddresses();
            $mail->addAddress($event['email'], $event['name'] ?? '');

            $mail->Subject = "Reminder: " . htmlspecialchars($event['eventTitle']);
            $mail->Body    = "
                <h2>Event Reminder</h2>
                <p><strong>Event:</strong> " . htmlspecialchars($event['eventTitle']) . "</p>
                <p><strong>Time:</strong> " . date('F j, Y g:i A', strtotime($event['startDateTime'])) . "</p>
                <p><strong>Location:</strong> " . htmlspecialchars($event['eventLocation']) . "</p>
            ";

            if ($mail->send()) {
                $sentCount++;
                // Update attendee record
                $updateStmt = mysqli_prepare($connection, 
                    "UPDATE attendees SET reminder_sent = 1 WHERE email = ? AND event_id = ?");
                mysqli_stmt_bind_param($updateStmt, "si", $event['email'], $event['event_id']);
                mysqli_stmt_execute($updateStmt);
                mysqli_stmt_close($updateStmt);
            }
        } catch (Exception $e) {
            error_log("Reminder failed for {$event['email']}: " . $e->getMessage());
        }
    }

    echo "Successfully sent {$sentCount} reminders.";
} catch (Exception $e) {
    error_log("PHPMailer Error: " . $e->getMessage());
    echo "Error sending reminders. Please check logs.";
}

mysqli_close($connection);