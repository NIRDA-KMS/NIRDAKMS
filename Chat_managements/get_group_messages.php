<?php
session_start();
require_once 'db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$groupId = isset($_GET['group_id']) ? (int)$_GET['group_id'] : 0;

if ($groupId <= 0) {
    echo json_encode(['error' => 'Invalid group ID']);
    exit;
}

// Check if user is a member of this group
$stmt = $conn->prepare("SELECT 1 FROM group_members WHERE group_id = ? AND user_id = ?");
$stmt->bind_param("ii", $groupId, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['error' => 'You are not a member of this group']);
    exit;
}

// Fetch messages
$stmt = $conn->prepare("
    SELECT m.message_id, m.content, m.sent_at, u.username as sender_name
    FROM messages m
    JOIN users u ON m.sender_id = u.user_id
    WHERE m.group_id = ?
    ORDER BY m.sent_at ASC
");
$stmt->bind_param("i", $groupId);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = [
        'id' => $row['message_id'],
        'content' => htmlspecialchars($row['content']),
        'sent_at' => date('M j, Y g:i a', strtotime($row['sent_at'])),
        'sender_name' => htmlspecialchars($row['sender_name'])
    ];
}

echo json_encode(['messages' => $messages]);