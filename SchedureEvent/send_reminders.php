<?php
include('connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

    // Send email reminders
    $subject = "Reminder: " . $event['eventTitle'];
    $message = "Dear Attendee,\n\nThis is a reminder for the event:\n\n" .
               "Title: " . $event['eventTitle'] . "\n" .
               "Date & Time: " . $event['startDateTime'] . " to " . $event['endingDateTime'] . "\n" .
               "Location: " . $event['eventLocation'] . "\n\n" .
               "We look forward to your participation.\n\nBest regards,\nEvent Management Team";

    $headers = "From: no-reply@nirdakms.com";

    $success = true;
    foreach ($emails as $email) {
        if (!mail($email, $subject, $message, $headers)) {
            $success = false;
        }
    }

    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Reminders sent successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send some reminders.']);
    }
    exit;
}