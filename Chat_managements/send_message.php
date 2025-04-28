<?php
session_start();
require_once 'db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['group_id']) || !isset($data['content']) || empty(trim($data['content']))) {
    echo json_encode(['success' => false, 'error' => 'Invalid data']);
    exit;
}

$groupId = (int)$data['group_id'];
$content = trim($data['content']);
$senderId = $_SESSION['user_id'];

// Check if user is a member of this group
$stmt = $conn->prepare("SELECT 1 FROM group_members WHERE group_id = ? AND user_id = ?");
$stmt->bind_param("ii", $groupId, $senderId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'You are not a member of this group']);
    exit;
}

// Insert message
$stmt = $conn->prepare("INSERT INTO messages (group_id, sender_id, content) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $groupId, $senderId, $content);

if ($stmt->execute()) {
    // Update group's last updated time
    $stmt = $conn->prepare("UPDATE chat_groups SET updated_at = NOW() WHERE group_id = ?");
    $stmt->bind_param("i", $groupId);
    $stmt->execute();
    
    echo json_encode(['success' => true, 'message_id' => $stmt->insert_id]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to send message']);
}