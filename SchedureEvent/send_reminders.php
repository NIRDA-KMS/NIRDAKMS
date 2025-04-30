<?php
// include('connect.php');
include(__DIR__ . '/connect.php');
// require 'vendor/autoload.php';
require __DIR__ . '/../vendor/autoload.php';
// Ensure we're getting JSON response
header('Content-Type: application/json');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// require 'vendor/autoload.php'; // PHPMailer must be installed via Composer

// Check if PHPMailer is available
if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    echo json_encode(['success' => false, 'message' => 'PHPMailer not installed']);
    exit;
}

// Begin try block
try {
    $eventId = $_POST['event_id'] ?? null;

    if (!$eventId) {
        throw new Exception('Event ID is required.');
    }

    // Fetch event details
    $query = "SELECT * FROM schedule_events WHERE event_id = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "i", $eventId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $event = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if (!$event) {
        throw new Exception('Event not found.');
    }

    // Fetch attendees
    $attendeesQuery = "SELECT email, name FROM attendees WHERE event_id = ?";
    $stmt = mysqli_prepare($connection, $attendeesQuery);
    mysqli_stmt_bind_param($stmt, "i", $eventId);
    mysqli_stmt_execute($stmt);
    $attendeesResult = mysqli_stmt_get_result($stmt);
    
    $emails = [];
    while ($row = mysqli_fetch_assoc($attendeesResult)) {
        $emails[] = $row;
    }
    mysqli_stmt_close($stmt);

    if (empty($emails)) {
        throw new Exception('No attendees found for this event.');
    }

    // Set up PHPMailer
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'rurangirwakhassim84@gmail.com'; // Your Gmail
    $mail->Password   = 'cbsu vzxr jvgq oiol';            // App password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';
    $mail->setFrom('no-reply@nirdakms.com', 'NIRDAKMS');
    $mail->isHTML(true);

    $subject = "Reminder: " . htmlspecialchars($event['eventTitle']);
    $startDate = new DateTime($event['startDateTime']);
    $endDate = new DateTime($event['endingDateTime']);

    $sentCount = 0;
    $failedEmails = [];

    foreach ($emails as $attendee) {
        try {
            $mail->clearAddresses();
            $mail->addAddress($attendee['email'], $attendee['name'] ?? '');

            $message = "
                <html>
                <head>
                    <title>{$subject}</title>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; }
                        .event-details { margin: 20px 0; }
                        .detail { margin-bottom: 10px; }
                    </style>
                </head>
                <body>
                    <h2>Event Reminder</h2>
                    <div class='event-details'>
                        <div class='detail'><strong>Title:</strong> " . htmlspecialchars($event['eventTitle']) . "</div>
                        <div class='detail'><strong>Date & Time:</strong> " . $startDate->format('F j, Y g:i A') . " to " . $endDate->format('g:i A') . "</div>
                        <div class='detail'><strong>Location:</strong> " . htmlspecialchars($event['eventLocation']) . "</div>
                        <div class='detail'><strong>Description:</strong> " . nl2br(htmlspecialchars($event['eventDescription'])) . "</div>
                    </div>
                    <p>We look forward to your participation.</p>
                    <p>Best regards,<br>NIRDA Event Management Team</p>
                </body>
                </html>
            ";

            $mail->Subject = $subject;
            $mail->Body    = $message;
            $mail->AltBody = strip_tags($message);

            if ($mail->send()) {
                $sentCount++;
                // Mark reminder as sent
                $updateStmt = mysqli_prepare($connection, 
                    "UPDATE attendees SET reminder_sent = 1 WHERE email = ? AND event_id = ?");
                mysqli_stmt_bind_param($updateStmt, "si", $attendee['email'], $eventId);
                mysqli_stmt_execute($updateStmt);
                mysqli_stmt_close($updateStmt);
            } else {
                $failedEmails[] = $attendee['email'];
            }
        } catch (Exception $e) {
            $failedEmails[] = $attendee['email'] . " (Error: " . $e->getMessage() . ")";
        }
    }

    $response = [
        'success' => true,
        'message' => "Reminders sent to {$sentCount} attendee(s).",
        'failed' => $failedEmails
    ];

    if (!empty($failedEmails)) {
        $response['message'] .= " Failed to send to " . count($failedEmails) . " attendee(s).";
    }

    echo json_encode($response);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => "Error: " . $e->getMessage()
    ]);
} finally {
    mysqli_close($connection);
    exit;
}
