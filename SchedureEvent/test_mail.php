<?php
// Include Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Define $emailReminder
$emailReminder = true; // Set this based on your logic

// Define $emails (replace with actual logic to fetch emails)
$emails = ['valens4607@gmail.com', 'esteruw11@gmail.com']; // Example email addresses

// Define event details
$eventtitle = "Sample Event Title"; // Replace with actual event title
$startDate = new DateTime('2025-05-15 10:00:00'); // Replace with actual start date/time
$endDate = new DateTime('2025-05-15 12:00:00'); // Replace with actual end date/time
$location = "Sample Location"; // Replace with actual location
$description = "This is a sample event description."; // Replace with actual description
$reminderTime = 60; // Replace with actual reminder time in minutes

// Send initial confirmation to attendees if email reminder is enabled
if ($emailReminder) {
    $mail = new PHPMailer(true);
    try {
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'rurangirwakhassim84@gmail.com';
        $mail->Password   = 'cbsu vzxr jvgq oiol';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('no-reply@nirdakms.com', 'NIRDA Event System');
        $mail->isHTML(true);

        if (is_array($emails) && !empty($emails)) {
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
        } else {
            error_log("No emails found to send invitations.");
        }
    } catch (Exception $e) {
        error_log("Failed to send invitation emails: " . $e->getMessage());
    }
}
