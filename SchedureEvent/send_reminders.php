<?php
include('connect.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Make sure  PHPMailer is installed via Composer

$eventId = $_POST['event_id'] ?? null;

if (!$eventId) {
    echo json_encode(['success' => false, 'message' => 'Event ID is required.']);
    exit;
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
    echo json_encode(['success' => false, 'message' => 'Event not found.']);
    exit;
}

// Fetch attendees' email addresses
$attendeesQuery = "SELECT email FROM attendees WHERE event_id = ?";
$stmt = mysqli_prepare($connection, $attendeesQuery);
mysqli_stmt_bind_param($stmt, "i", $eventId);
mysqli_stmt_execute($stmt);
$attendeesResult = mysqli_stmt_get_result($stmt);
$emails = [];
while ($row = mysqli_fetch_assoc($attendeesResult)) {
    $emails[] = $row['email'];
}
mysqli_stmt_close($stmt);

if (empty($emails)) {
    echo json_encode(['success' => false, 'message' => 'No attendees found for this event.']);
    exit;
}

// Prepare email content
$subject = "Reminder: " . $event['eventTitle'];
$message = "Dear Attendee,<br><br>This is a reminder for the event:<br><br>" .
           "<strong>Title:</strong> " . $event['eventTitle'] . "<br>" .
           "<strong>Date & Time:</strong> " . $event['startDateTime'] . " to " . $event['endingDateTime'] . "<br>" .
           "<strong>Location:</strong> " . $event['eventLocation'] . "<br><br>" .
           "We look forward to your participation.<br><br>Best regards,<br>Event Management Team";

$mail = new PHPMailer(true);
try {
    // SMTP configuration
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'rurangirwakhassim84@gmail.com'; // Your Gmail address
    $mail->Password   = 'cbsu vzxr jvgq oiol';           // App password
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom('no-reply@nirdakms.com', 'NIRDAKMS');
    foreach ($emails as $email) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) { // Validate email address
            $mail->addAddress($email);
        }
    }

    // Email content
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $message;

    $mail->send();
    echo json_encode(['success' => true, 'message' => 'Reminders sent successfully.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => "Mailer Error: {$mail->ErrorInfo}"]);
}
exit;