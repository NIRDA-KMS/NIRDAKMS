<?php
// Database connection
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'NIRDAKMS';


try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

session_start();

// API Endpoints
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    try {
        switch ($_GET['action']) {
            case 'get_user':
                if (!isset($_GET['user_id'])) {
                    echo json_encode(['error' => 'User ID required']);
                    exit;
                }
                $stmt = $pdo->prepare("SELECT u.*, us.is_online, us.last_seen FROM users u LEFT JOIN user_status us ON u.user_id = us.user_id WHERE u.user_id = ?");
                $stmt->execute([$_GET['user_id']]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                echo json_encode($user ?: ['error' => 'User not found']);
                break;
                
            case 'get_contacts':
                if (!isset($_SESSION['user_id'])) {
                    echo json_encode(['error' => 'Not authenticated']);
                    exit;
                }
                
                // Get all users except current user
                $stmt = $pdo->prepare("
                    SELECT u.user_id, u.full_name, u.email, us.is_online, us.last_seen,
                           EXISTS(SELECT 1 FROM blocked_users WHERE blocker_id = ? AND blocked_id = u.user_id) as is_blocked,
                           EXISTS(SELECT 1 FROM blocked_users WHERE blocker_id = u.user_id AND blocked_id = ?) as has_blocked_me
                    FROM users u
                    LEFT JOIN user_status us ON u.user_id = us.user_id
                    WHERE u.user_id != ? AND u.is_active = 1
                    ORDER BY us.is_online DESC, u.full_name ASC
                ");
                $stmt->execute([$_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id']]);
                $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($contacts);
                break;
                
            case 'get_online_users':
                if (!isset($_SESSION['user_id'])) {
                    echo json_encode(['error' => 'Not authenticated']);
                    exit;
                }
                
                $stmt = $pdo->prepare("
                    SELECT u.user_id, u.full_name, u.email, us.is_online, us.last_seen
                    FROM users u
                    JOIN user_status us ON u.user_id = us.user_id
                    WHERE u.user_id != ? AND us.is_online = 1 AND u.is_active = 1
                    AND NOT EXISTS(SELECT 1 FROM blocked_users WHERE blocker_id = ? AND blocked_id = u.user_id)
                    AND NOT EXISTS(SELECT 1 FROM blocked_users WHERE blocker_id = u.user_id AND blocked_id = ?)
                    ORDER BY u.full_name ASC
                ");
                $stmt->execute([$_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id']]);
                $onlineUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($onlineUsers);
                break;
                
            case 'block_user':
                if (!isset($_SESSION['user_id']) || !isset($_POST['user_id'])) {
                    echo json_encode(['error' => 'Missing parameters']);
                    exit;
                }
                
                $pdo->beginTransaction();
                try {
                    // Check if already blocked
                    $stmt = $pdo->prepare("SELECT 1 FROM blocked_users WHERE blocker_id = ? AND blocked_id = ?");
                    $stmt->execute([$_SESSION['user_id'], $_POST['user_id']]);
                    if ($stmt->fetch()) {
                        echo json_encode(['success' => true, 'message' => 'User already blocked']);
                        exit;
                    }
                    
                    // Block user
                    $stmt = $pdo->prepare("INSERT INTO blocked_users (blocker_id, blocked_id, blocked_at) VALUES (?, ?, NOW())");
                    $stmt->execute([$_SESSION['user_id'], $_POST['user_id']]);
                    
                    $pdo->commit();
                    echo json_encode(['success' => true]);
                } catch (Exception $e) {
                    $pdo->rollBack();
                    echo json_encode(['error' => 'Failed to block user: ' . $e->getMessage()]);
                }
                break;
                
            case 'unblock_user':
                if (!isset($_SESSION['user_id']) || !isset($_POST['user_id'])) {
                    echo json_encode(['error' => 'Missing parameters']);
                    exit;
                }
                
                $stmt = $pdo->prepare("DELETE FROM blocked_users WHERE blocker_id = ? AND blocked_id = ?");
                $stmt->execute([$_SESSION['user_id'], $_POST['user_id']]);
                echo json_encode(['success' => $stmt->rowCount() > 0]);
                break;
                
            case 'start_private_chat':
                if (!isset($_SESSION['user_id']) || !isset($_POST['user_id'])) {
                    echo json_encode(['error' => 'Missing parameters']);
                    exit;
                }
                
                $otherUserId = $_POST['user_id'];
                
                // Check if conversation already exists
                $stmt = $pdo->prepare("
                    SELECT conversation_id FROM private_conversations 
                    WHERE (user1_id = ? AND user2_id = ?) OR (user1_id = ? AND user2_id = ?)
                ");
                $stmt->execute([$_SESSION['user_id'], $otherUserId, $otherUserId, $_SESSION['user_id']]);
                $existing = $stmt->fetch();
                
                if ($existing) {
                    echo json_encode(['success' => true, 'conversation_id' => $existing['conversation_id']]);
                    exit;
                }
                
                // Create new conversation
                $stmt = $pdo->prepare("
                    INSERT INTO private_conversations (user1_id, user2_id, created_at)
                    VALUES (?, ?, NOW())
                ");
                $stmt->execute([$_SESSION['user_id'], $otherUserId]);
                $conversationId = $pdo->lastInsertId();
                
                echo json_encode(['success' => true, 'conversation_id' => $conversationId]);
                break;
                
            case 'star_conversation':
                if (!isset($_SESSION['user_id']) || !isset($_POST['conversation_id'])) {
                    echo json_encode(['error' => 'Missing parameters']);
                    exit;
                }
                
                $stmt = $pdo->prepare("
                    INSERT INTO starred_conversations (user_id, conversation_id, starred_at)
                    VALUES (?, ?, NOW())
                    ON DUPLICATE KEY UPDATE is_starred = 1, starred_at = NOW()
                ");
                $stmt->execute([$_SESSION['user_id'], $_POST['conversation_id']]);
                echo json_encode(['success' => true]);
                break;
                
            case 'unstar_conversation':
                if (!isset($_SESSION['user_id']) || !isset($_POST['conversation_id'])) {
                    echo json_encode(['error' => 'Missing parameters']);
                    exit;
                }
                
                $stmt = $pdo->prepare("
                    UPDATE starred_conversations 
                    SET is_starred = 0, unstarred_at = NOW()
                    WHERE user_id = ? AND conversation_id = ?
                ");
                $stmt->execute([$_SESSION['user_id'], $_POST['conversation_id']]);
                echo json_encode(['success' => $stmt->rowCount() > 0]);
                break;
                
            case 'get_chats':
                if (!isset($_SESSION['user_id'])) {
                    echo json_encode(['error' => 'Not authenticated']);
                    exit;
                }
                
                // Get private conversations
                $stmt = $pdo->prepare("
                    SELECT c.conversation_id, 
                           CASE WHEN c.user1_id = ? THEN u2.user_id ELSE u1.user_id END as other_user_id,
                           CASE WHEN c.user1_id = ? THEN u2.full_name ELSE u1.full_name END as name,
                           CASE WHEN c.user1_id = ? THEN u2.email ELSE u1.email END as email,
                           'private' as type,
                           c.last_message_at,
                           m.content as last_message,
                           s.is_starred as is_starred,
                           us.is_online as is_online,
                           us.last_seen as last_seen
                    FROM private_conversations c
                    LEFT JOIN users u1 ON c.user1_id = u1.user_id
                    LEFT JOIN users u2 ON c.user2_id = u2.user_id
                    LEFT JOIN messages m ON (
                        m.message_id = (
                            SELECT message_id FROM messages 
                            WHERE (conversation_id = c.conversation_id) 
                            ORDER BY sent_at DESC LIMIT 1
                        )
                    )
                    LEFT JOIN starred_conversations s ON s.conversation_id = c.conversation_id AND s.user_id = ?
                    LEFT JOIN user_status us ON us.user_id = CASE WHEN c.user1_id = ? THEN u2.user_id ELSE u1.user_id END
                    WHERE (c.user1_id = ? OR c.user2_id = ?)
                    AND NOT EXISTS(
                        SELECT 1 FROM blocked_users 
                        WHERE (blocker_id = ? AND blocked_id = CASE WHEN c.user1_id = ? THEN u2.user_id ELSE u1.user_id END)
                        OR (blocker_id = CASE WHEN c.user1_id = ? THEN u2.user_id ELSE u1.user_id END AND blocked_id = ?)
                    )
                    ORDER BY s.is_starred DESC, c.last_message_at DESC
                ");
                $stmt->execute([
                    $_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id'], 
                    $_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id'],
                    $_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id']
                ]);
                $privateChats = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Get group chats
                $stmt = $pdo->prepare("
                    SELECT g.group_id, g.group_name as name, g.description, 
                           'group' as type, g.created_at, g.updated_at as last_message_at,
                           m.content as last_message,
                           (SELECT COUNT(*) FROM group_members WHERE group_id = g.group_id) as member_count,
                           s.is_starred as is_starred
                    FROM chat_groups g
                    JOIN group_members gm ON g.group_id = gm.group_id
                    LEFT JOIN messages m ON (
                        m.message_id = (
                            SELECT message_id FROM messages 
                            WHERE (group_id = g.group_id) 
                            ORDER BY sent_at DESC LIMIT 1
                        )
                    )
                    LEFT JOIN starred_conversations s ON s.group_id = g.group_id AND s.user_id = ?
                    WHERE gm.user_id = ? AND g.is_active = 1
                    ORDER BY s.is_starred DESC, g.updated_at DESC
                ");
                $stmt->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
                $groupChats = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Combine and return
                $allChats = array_merge($privateChats, $groupChats);
                echo json_encode($allChats);
                break;
                
            case 'get_messages':
                if (!isset($_SESSION['user_id'])) {
                    echo json_encode(['error' => 'Not authenticated']);
                    exit;
                }
                
                if (isset($_GET['conversation_id'])) {
                    // Private conversation messages
                    $stmt = $pdo->prepare("
                        SELECT m.*, u.full_name as sender_name, u.user_id as sender_id
                        FROM messages m
                        JOIN users u ON m.sender_id = u.user_id
                        WHERE m.conversation_id = ?
                        ORDER BY m.sent_at ASC
                    ");
                    $stmt->execute([$_GET['conversation_id']]);
                } elseif (isset($_GET['group_id'])) {
                    // Group messages
                    $stmt = $pdo->prepare("
                        SELECT m.*, u.full_name as sender_name, u.user_id as sender_id
                        FROM messages m
                        JOIN users u ON m.sender_id = u.user_id
                        WHERE m.group_id = ?
                        ORDER BY m.sent_at ASC
                    ");
                    $stmt->execute([$_GET['group_id']]);
                } else {
                    echo json_encode(['error' => 'Conversation or group ID required']);
                    exit;
                }
                
                $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($messages);
                break;
                
            case 'send_message':
                if (!isset($_SESSION['user_id']) || !isset($_POST['content'])) {
                    echo json_encode(['error' => 'Missing parameters']);
                    exit;
                }
                
                $content = trim($_POST['content']);
                if (empty($content)) {
                    echo json_encode(['error' => 'Message content cannot be empty']);
                    exit;
                }
                
                if (isset($_POST['conversation_id'])) {
                    // Private message
                    $stmt = $pdo->prepare("
                        INSERT INTO messages (conversation_id, sender_id, content, sent_at, status)
                        VALUES (?, ?, ?, NOW(), 'sent')
                    ");
                    $stmt->execute([
                        $_POST['conversation_id'],
                        $_SESSION['user_id'],
                        $content
                    ]);
                    
                    // Update conversation last message time
                    $stmt = $pdo->prepare("
                        UPDATE private_conversations 
                        SET last_message_at = NOW() 
                        WHERE conversation_id = ?
                    ");
                    $stmt->execute([$_POST['conversation_id']]);
                    
                } elseif (isset($_POST['group_id'])) {
                    // Group message
                    $stmt = $pdo->prepare("
                        INSERT INTO messages (group_id, sender_id, content, sent_at, status)
                        VALUES (?, ?, ?, NOW(), 'sent')
                    ");
                    $stmt->execute([
                        $_POST['group_id'],
                        $_SESSION['user_id'],
                        $content
                    ]);
                    
                    // Update group last update time
                    $stmt = $pdo->prepare("
                        UPDATE chat_groups 
                        SET updated_at = NOW() 
                        WHERE group_id = ?
                    ");
                    $stmt->execute([$_POST['group_id']]);
                } else {
                    echo json_encode(['error' => 'Conversation or group ID required']);
                    exit;
                }
                
                echo json_encode(['success' => true, 'message_id' => $pdo->lastInsertId()]);
                break;
                
            case 'create_group':
                if (!isset($_SESSION['user_id']) || !isset($_POST['group_name'])) {
                    echo json_encode(['error' => 'Missing parameters']);
                    exit;
                }
                
                $groupName = trim($_POST['group_name']);
                if (empty($groupName)) {
                    echo json_encode(['error' => 'Group name cannot be empty']);
                    exit;
                }
                
                $description = isset($_POST['description']) ? trim($_POST['description']) : '';
                $members = isset($_POST['members']) ? json_decode($_POST['members'], true) : [];
                
                $pdo->beginTransaction();
                
                try {
                    // Create the group
                    $stmt = $pdo->prepare("
                        INSERT INTO chat_groups (group_name, description, created_by, created_at, updated_at)
                        VALUES (?, ?, ?, NOW(), NOW())
                    ");
                    $stmt->execute([
                        $groupName,
                        $description,
                        $_SESSION['user_id']
                    ]);
                    $groupId = $pdo->lastInsertId();
                    
                    // Add creator as admin member
                    $stmt = $pdo->prepare("
                        INSERT INTO group_members (group_id, user_id, joined_at, is_admin)
                        VALUES (?, ?, NOW(), 1)
                    ");
                    $stmt->execute([$groupId, $_SESSION['user_id']]);
                    
                    // Add other members
                    foreach ($members as $memberId) {
                        $stmt = $pdo->prepare("
                            INSERT INTO group_members (group_id, user_id, joined_at, is_admin)
                            VALUES (?, ?, NOW(), 0)
                        ");
                        $stmt->execute([$groupId, $memberId]);
                    }
                    
                    $pdo->commit();
                    echo json_encode(['success' => true, 'group_id' => $groupId]);
                } catch (Exception $e) {
                    $pdo->rollBack();
                    echo json_encode(['error' => 'Failed to create group: ' . $e->getMessage()]);
                }
                break;
                
            case 'update_group':
                if (!isset($_SESSION['user_id']) || !isset($_POST['group_id'])) {
                    echo json_encode(['error' => 'Missing parameters']);
                    exit;
                }
                
                // Check if user is admin of the group
                $stmt = $pdo->prepare("
                    SELECT 1 FROM group_members 
                    WHERE group_id = ? AND user_id = ? AND is_admin = 1
                ");
                $stmt->execute([$_POST['group_id'], $_SESSION['user_id']]);
                if (!$stmt->fetch()) {
                    echo json_encode(['error' => 'You must be an admin to update this group']);
                    exit;
                }
                
                $groupName = isset($_POST['group_name']) ? trim($_POST['group_name']) : null;
                $description = isset($_POST['description']) ? trim($_POST['description']) : null;
                
                if ($groupName === null && $description === null) {
                    echo json_encode(['error' => 'Nothing to update']);
                    exit;
                }
                
                $updates = [];
                $params = [];
                
                if ($groupName !== null) {
                    $updates[] = "group_name = ?";
                    $params[] = $groupName;
                }
                
                if ($description !== null) {
                    $updates[] = "description = ?";
                    $params[] = $description;
                }
                
                $params[] = $_POST['group_id'];
                
                $stmt = $pdo->prepare("
                    UPDATE chat_groups 
                    SET " . implode(', ', $updates) . ", updated_at = NOW()
                    WHERE group_id = ?
                ");
                $stmt->execute($params);
                
                echo json_encode(['success' => true]);
                break;
                
            case 'add_group_members':
                if (!isset($_SESSION['user_id']) || !isset($_POST['group_id']) || !isset($_POST['members'])) {
                    echo json_encode(['error' => 'Missing parameters']);
                    exit;
                }
                
                // Check if user is admin of the group
                $stmt = $pdo->prepare("
                    SELECT 1 FROM group_members 
                    WHERE group_id = ? AND user_id = ? AND is_admin = 1
                ");
                $stmt->execute([$_POST['group_id'], $_SESSION['user_id']]);
                if (!$stmt->fetch()) {
                    echo json_encode(['error' => 'You must be an admin to add members']);
                    exit;
                }
                
                $members = json_decode($_POST['members'], true);
                if (empty($members)) {
                    echo json_encode(['error' => 'No members to add']);
                    exit;
                }
                
                $pdo->beginTransaction();
                try {
                    foreach ($members as $memberId) {
                        // Check if already a member
                        $stmt = $pdo->prepare("
                            SELECT 1 FROM group_members 
                            WHERE group_id = ? AND user_id = ?
                        ");
                        $stmt->execute([$_POST['group_id'], $memberId]);
                        if ($stmt->fetch()) continue;
                        
                        // Add member
                        $stmt = $pdo->prepare("
                            INSERT INTO group_members (group_id, user_id, joined_at, is_admin)
                            VALUES (?, ?, NOW(), 0)
                        ");
                        $stmt->execute([$_POST['group_id'], $memberId]);
                    }
                    
                    $pdo->commit();
                    echo json_encode(['success' => true]);
                } catch (Exception $e) {
                    $pdo->rollBack();
                    echo json_encode(['error' => 'Failed to add members: ' . $e->getMessage()]);
                }
                break;
                
            case 'remove_group_member':
                if (!isset($_SESSION['user_id']) || !isset($_POST['group_id']) || !isset($_POST['user_id'])) {
                    echo json_encode(['error' => 'Missing parameters']);
                    exit;
                }
                
                // Check if user is admin of the group or removing themselves
                $isSelf = $_POST['user_id'] == $_SESSION['user_id'];
                
                if (!$isSelf) {
                    $stmt = $pdo->prepare("
                        SELECT 1 FROM group_members 
                        WHERE group_id = ? AND user_id = ? AND is_admin = 1
                    ");
                    $stmt->execute([$_POST['group_id'], $_SESSION['user_id']]);
                    if (!$stmt->fetch()) {
                        echo json_encode(['error' => 'You must be an admin to remove members']);
                        exit;
                    }
                }
                
                $stmt = $pdo->prepare("
                    DELETE FROM group_members 
                    WHERE group_id = ? AND user_id = ?
                ");
                $stmt->execute([$_POST['group_id'], $_POST['user_id']]);
                
                echo json_encode(['success' => $stmt->rowCount() > 0]);
                break;
                
            case 'delete_group':
                if (!isset($_SESSION['user_id'])) {
                    echo json_encode(['error' => 'Not authenticated']);
                    exit;
                }

                $groupId = $_POST['group_id'] ?? null;
                if (!$groupId) {
                    echo json_encode(['error' => 'Group ID missing']);
                    exit;
                }

                $stmt = $pdo->prepare("SELECT created_by FROM chat_groups WHERE group_id = ?");
                $stmt->execute([$groupId]);
                $group = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$group || $group['created_by'] != $_SESSION['user_id']) {
                    echo json_encode(['error' => 'Only group creator can delete the group']);
                    exit;
                }

                $pdo->beginTransaction();

                try {
                    $pdo->prepare("DELETE FROM messages WHERE group_id = ?")->execute([$groupId]);
                    $pdo->prepare("DELETE FROM group_members WHERE group_id = ?")->execute([$groupId]);
                    $pdo->prepare("DELETE FROM group_invitations WHERE group_id = ?")->execute([$groupId]);
                    $pdo->prepare("DELETE FROM chat_groups WHERE group_id = ?")->execute([$groupId]);

                    $pdo->commit();
                    echo json_encode(['success' => true]);
                } catch (Exception $e) {
                    $pdo->rollBack();
                    echo json_encode(['error' => 'Failed to delete group: ' . $e->getMessage()]);
                }
                break;

            case 'upload_file':
                if (!isset($_SESSION['user_id'])) {
                    echo json_encode(['error' => 'Not authenticated']);
                    exit;
                }

                if (!isset($_FILES['file'])) {
                    echo json_encode(['error' => 'No file uploaded']);
                    exit;
                }

                $file = $_FILES['file'];
                $allowedTypes = [
                    'image/jpeg', 'image/png', 'image/gif', 'application/pdf',
                    'application/msword', 'application/vnd.ms-excel',
                    'text/plain', 'application/zip'
                ];

                if (!in_array($file['type'], $allowedTypes)) {
                    echo json_encode(['error' => 'File type not allowed']);
                    exit;
                }

                if ($file['size'] > 10 * 1024 * 1024) {
                    echo json_encode(['error' => 'File size too large (max 10MB)']);
                    exit;
                }

                $uploadDir = 'storage/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $filename = uniqid() . '_' . basename($file['name']);
                $filepath = $uploadDir . $filename;

                if (move_uploaded_file($file['tmp_name'], $filepath)) {
                    $stmt = $pdo->prepare("INSERT INTO files (filename, filepath, size, type, uploaded) VALUES (?, ?, ?, ?, NOW())");
                    $stmt->execute([$file['name'], $filepath, $file['size'], $file['type']]);

                    echo json_encode([
                        'success' => true,
                        'file_id' => $pdo->lastInsertId(),
                        'file_name' => $file['name'],
                        'file_type' => $file['type'],
                        'file_size' => $file['size'],
                        'file_path' => $filepath
                    ]);
                } else {
                    echo json_encode(['error' => 'Failed to upload file']);
                }
                break;

            case 'attach_file_to_message':
                if (!isset($_SESSION['user_id'])) {
                    echo json_encode(['error' => 'Not authenticated']);
                    exit;
                }

                $messageId = $_POST['message_id'] ?? null;
                $fileId = $_POST['file_id'] ?? null;

                if (!$messageId || !$fileId) {
                    echo json_encode(['error' => 'Missing parameters']);
                    exit;
                }

                $stmt = $pdo->prepare("SELECT * FROM files WHERE id = ?");
                $stmt->execute([$fileId]);
                $file = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$file) {
                    echo json_encode(['error' => 'File not found']);
                    exit;
                }

                $stmt = $pdo->prepare("INSERT INTO message_attachments (message_id, file_id, file_name, file_type, file_size, file_path, uploaded_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
                $stmt->execute([$messageId, $fileId, $file['filename'], $file['type'], $file['size'], $file['filepath']]);

                echo json_encode(['success' => true, 'attachment_id' => $pdo->lastInsertId()]);
                break;

            case 'update_profile_picture':
                if (!isset($_SESSION['user_id'])) {
                    echo json_encode(['error' => 'Not authenticated']);
                    exit;
                }

                if (!isset($_FILES['profile_picture'])) {
                    echo json_encode(['error' => 'No file uploaded']);
                    exit;
                }

                $file = $_FILES['profile_picture'];
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

                if (!in_array($file['type'], $allowedTypes)) {
                    echo json_encode(['error' => 'Only JPEG, PNG, and GIF images are allowed']);
                    exit;
                }

                if ($file['size'] > 2 * 1024 * 1024) {
                    echo json_encode(['error' => 'Image size too large (max 2MB)']);
                    exit;
                }

                $uploadDir = 'profile_pictures/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'profile_' . $_SESSION['user_id'] . '_' . time() . '.' . $ext;
                $filepath = $uploadDir . $filename;

                if (move_uploaded_file($file['tmp_name'], $filepath)) {
                    $stmt = $pdo->prepare("
                        INSERT INTO user_profiles (user_id, profile_picture, updated_at)
                        VALUES (?, ?, NOW())
                        ON DUPLICATE KEY UPDATE profile_picture = VALUES(profile_picture), updated_at = NOW()
                    ");
                    $stmt->execute([$_SESSION['user_id'], $filepath]);

                    echo json_encode(['success' => true, 'profile_picture' => $filepath]);
                } else {
                    echo json_encode(['error' => 'Failed to upload profile picture']);
                }
                break;

            case 'update_user_status':
                if (!isset($_SESSION['user_id'])) {
                    echo json_encode(['error' => 'Not authenticated']);
                    exit;
                }

                $status = $_POST['status'] ?? null;
                $allowedStatuses = ['online', 'away', 'busy', 'offline'];

                if (!$status || !in_array($status, $allowedStatuses)) {
                    echo json_encode(['error' => 'Invalid status']);
                    exit;
                }

                $isOnline = $status === 'online' ? 1 : 0;

                $stmt = $pdo->prepare("
                    INSERT INTO user_profiles (user_id, status, updated_at)
                    VALUES (?, ?, NOW())
                    ON DUPLICATE KEY UPDATE status = VALUES(status), updated_at = NOW()
                ");
                $stmt->execute([$_SESSION['user_id'], $status]);

                $stmt = $pdo->prepare("
                    INSERT INTO user_status (user_id, is_online, last_seen)
                    VALUES (?, ?, NOW())
                    ON DUPLICATE KEY UPDATE is_online = VALUES(is_online), last_seen = NOW()
                ");
                $stmt->execute([$_SESSION['user_id'], $isOnline]);

                echo json_encode(['success' => true]);
                break;

            case 'get_group_members':
                $groupId = $_GET['group_id'] ?? null;

                if (!$groupId) {
                    echo json_encode(['error' => 'Group ID required']);
                    exit;
                }

                $stmt = $pdo->prepare("
                    SELECT u.user_id, u.full_name, u.email, gm.is_admin,
                           up.profile_picture, up.status, us.is_online, us.last_seen
                    FROM group_members gm
                    JOIN users u ON gm.user_id = u.user_id
                    LEFT JOIN user_profiles up ON u.user_id = up.user_id
                    LEFT JOIN user_status us ON u.user_id = us.user_id
                    WHERE gm.group_id = ?
                ");
                $stmt->execute([$groupId]);
                $members = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode($members);
                break;

            case 'get_message_attachments':
                $messageId = $_GET['message_id'] ?? null;

                if (!$messageId) {
                    echo json_encode(['error' => 'Message ID required']);
                    exit;
                }

                $stmt = $pdo->prepare("SELECT * FROM message_attachments WHERE message_id = ?");
                $stmt->execute([$messageId]);
                $attachments = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode($attachments);
                break;
                
            case 'delete_message':
                if (!isset($_SESSION['user_id']) || !isset($_POST['message_id'])) {
                    echo json_encode(['error' => 'Missing parameters']);
                    exit;
                }
                
                // Check if user is the sender
                $stmt = $pdo->prepare("
                    UPDATE messages 
                    SET is_deleted = 1 
                    WHERE message_id = ? AND sender_id = ?
                ");
                $stmt->execute([$_POST['message_id'], $_SESSION['user_id']]);
                
                echo json_encode(['success' => $stmt->rowCount() > 0]);
                break;
                
            case 'edit_message':
                if (!isset($_SESSION['user_id']) || !isset($_POST['message_id']) || !isset($_POST['content'])) {
                    echo json_encode(['error' => 'Missing parameters']);
                    exit;
                }
                
                $content = trim($_POST['content']);
                if (empty($content)) {
                    echo json_encode(['error' => 'Message content cannot be empty']);
                    exit;
                }
                
                // Check if user is the sender
                $stmt = $pdo->prepare("
                    UPDATE messages 
                    SET content = ?, edited_at = NOW() 
                    WHERE message_id = ? AND sender_id = ?
                ");
                $stmt->execute([$content, $_POST['message_id'], $_SESSION['user_id']]);
                
                echo json_encode(['success' => $stmt->rowCount() > 0]);
                break;
                
            case 'search_messages':
                if (!isset($_SESSION['user_id']) || !isset($_GET['query'])) {
                    echo json_encode(['error' => 'Missing parameters']);
                    exit;
                }
                
                $query = '%' . $_GET['query'] . '%';
                
                // Search in private conversations
                $stmt = $pdo->prepare("
                    SELECT m.message_id, m.content, m.sent_at, 
                           'private' as type, c.conversation_id,
                           CASE WHEN c.user1_id = ? THEN u2.full_name ELSE u1.full_name END as chat_name
                    FROM messages m
                    JOIN private_conversations c ON m.conversation_id = c.conversation_id
                    JOIN users u1 ON c.user1_id = u1.user_id
                    JOIN users u2 ON c.user2_id = u2.user_id
                    WHERE (c.user1_id = ? OR c.user2_id = ?)
                    AND m.content LIKE ?
                    ORDER BY m.sent_at DESC
                ");
                $stmt->execute([$_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id'], $query]);
                $privateMessages = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Search in group chats
                $stmt = $pdo->prepare("
                    SELECT m.message_id, m.content, m.sent_at, 
                           'group' as type, g.group_id, g.group_name as chat_name
                    FROM messages m
                    JOIN chat_groups g ON m.group_id = g.group_id
                    JOIN group_members gm ON g.group_id = gm.group_id
                    WHERE gm.user_id = ?
                    AND m.content LIKE ?
                    ORDER BY m.sent_at DESC
                ");
                $stmt->execute([$_SESSION['user_id'], $query]);
                $groupMessages = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                $results = array_merge($privateMessages, $groupMessages);
                echo json_encode($results);
                break;
                
            default:
                echo json_encode(['error' => 'Invalid action']);
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}

// ... (rest of your HTML/JavaScript code remains the same)

// Frontend HTML/JS
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Application</title>
<style>
        :root {
            --primary-color: #0084ff;
            --secondary-color: #f0f2f5;
            --text-color: #050505;
            --light-text: #65676b;
            --border-color: #dddfe2;
            --online-color: #31a24c;
            --away-color: #ffaa00;
            --busy-color: #ff4d4d;
            --star-color: #ffc107;
            --danger-color: #dc3545;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            color: var(--text-color);
            display: flex;
            height: 100vh;
        }
        
        .sidebar {
            width: 350px;
            background-color: white;
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        
        .header {
            padding: 15px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h2 {
            font-size: 20px;
            font-weight: 600;
        }
        
        .header button {
            padding: 5px 10px;
            border-radius: 4px;
            border: 1px solid var(--border-color);
            background-color: var(--secondary-color);
            cursor: pointer;
            font-size: 14px;
            margin-left: 5px;
        }
        
        .header button:hover {
            background-color: #e0e0e0;
        }
        
        .header button.primary {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        .header button.primary:hover {
            background-color: #0069d9;
        }
        
        .search-bar {
            padding: 10px 15px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .search-bar input {
            width: 100%;
            padding: 8px 15px;
            border-radius: 20px;
            border: 1px solid var(--border-color);
            background-color: var(--secondary-color);
            outline: none;
        }
        
        .chat-types {
            display: flex;
            border-bottom: 1px solid var(--border-color);
        }
        
        .chat-type {
            flex: 1;
            text-align: center;
            padding: 15px 0;
            cursor: pointer;
            font-weight: 500;
            position: relative;
        }
        
        .chat-type.active {
            color: var(--primary-color);
        }
        
        .chat-type.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 50%;
            height: 3px;
            background-color: var(--primary-color);
            border-radius: 3px 3px 0 0;
        }
        
        .tab-content {
            display: none;
            flex: 1;
            overflow-y: auto;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .chat-list {
            flex: 1;
            overflow-y: auto;
        }
        
        .chat-item {
            display: flex;
            padding: 10px 15px;
            align-items: center;
            cursor: pointer;
            border-bottom: 1px solid var(--border-color);
        }
        
        .chat-item:hover {
            background-color: var(--secondary-color);
        }
        
        .chat-item.active {
            background-color: #e7f3ff;
        }
        
        .chat-item.starred {
            background-color: #fff9e6;
        }
        
        .chat-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 10px;
            position: relative;
            background-color: #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #777;
            font-weight: bold;
            font-size: 20px;
            overflow: hidden;
        }
        
        .chat-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .status-indicator {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid white;
        }
        
        .status-online {
            background-color: var(--online-color);
        }
        
        .status-away {
            background-color: var(--away-color);
        }
        
        .status-busy {
            background-color: var(--busy-color);
        }
        
        .status-offline {
            background-color: var(--light-text);
        }
        
        .chat-info {
            flex: 1;
            min-width: 0;
        }
        
        .chat-name {
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            white-space: nowrap;
            /* overflow: hidden; */
            text-overflow: ellipsis;
        }
        
        .chat-time {
            font-size: 12px;
            color: var(--light-text);
            margin-left: 5px;
            white-space: nowrap;
        }
        
        .chat-preview {
            font-size: 14px;
            color: var(--light-text);
            display: flex;
            justify-content: space-between;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .star-icon {
            color: var(--star-color);
            margin-left: 5px;
        }
        
        .main-chat {
            flex: 1;
            display: flex;
            flex-direction: column;
            background-color: white;
        }
        
        .chat-header {
            padding: 15px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .chat-header-left {
            display: flex;
            align-items: center;
            flex: 1;
            min-width: 0;
        }
        
        .chat-header-info {
            margin-left: 10px;
            flex: 1;
            min-width: 0;
        }
        
        .chat-header-name {
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .chat-header-status {
            font-size: 13px;
            color: var(--light-text);
        }
        
        .chat-header-actions {
            display: flex;
            gap: 10px;
        }
        
        .chat-header-actions button {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--light-text);
            font-size: 16px;
        }
        
        .chat-header-actions button:hover {
            color: var(--primary-color);
        }
        
        .chat-messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background-color: #f5f5f5;
        }
        
        .message {
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
            max-width: 70%;
        }
        
        .message.sent {
            align-items: flex-end;
            margin-left: auto;
        }
        
        .message.received {
            align-items: flex-start;
            margin-right: auto;
        }
        
        .message-content {
            padding: 10px 15px;
            border-radius: 18px;
            margin-bottom: 5px;
            position: relative;
            word-break: break-word;
        }
        
        .message.sent .message-content {
            background-color: var(--primary-color);
            color: white;
            border-bottom-right-radius: 0;
        }
        
        .message.received .message-content {
            background-color: var(--secondary-color);
            border-bottom-left-radius: 0;
        }
        
        .message-time {
            font-size: 11px;
            color: var(--light-text);
            display: flex;
            align-items: center;
        }
        
        .message-status {
            margin-left: 5px;
        }
        
        .message-edited {
            font-size: 10px;
            color: var(--light-text);
            font-style: italic;
            margin-left: 5px;
        }
        
        .message-actions {
            display: none;
            position: absolute;
            right: 0;
            top: 0;
            transform: translateY(-100%);
            background: white;
            border-radius: 4px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            z-index: 10;
        }
        
        .message:hover .message-actions {
            display: flex;
        }
        
        .message-actions button {
            padding: 5px 10px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 12px;
        }
        
        .message-actions button:hover {
            background-color: var(--secondary-color);
        }
        
        .message-actions button.delete {
            color: var(--danger-color);
        }
        
        .chat-input {
            padding: 15px;
            border-top: 1px solid var(--border-color);
            display: flex;
            align-items: center;
        }
        
        .chat-input input {
            flex: 1;
            padding: 10px 15px;
            border-radius: 20px;
            border: 1px solid var(--border-color);
            outline: none;
            margin-right: 10px;
        }
        
        .send-button {
            background-color: var(--primary-color);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .empty-chat {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: var(--light-text);
        }
        
        .empty-chat-icon {
            font-size: 50px;
            margin-bottom: 20px;
        }
        
        .typing-indicator {
            font-size: 13px;
            color: var(--light-text);
            font-style: italic;
            margin-bottom: 10px;
        }
        
        .group-avatar {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #e0e0e0;
            color: #555;
        }
        
        .contact-item {
            display: flex;
            padding: 10px 15px;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
        }
        
        .contact-info {
            flex: 1;
            margin-left: 10px;
        }
        
        .contact-name {
            font-weight: 600;
        }
        
        .contact-status {
            font-size: 12px;
            color: var(--light-text);
        }
        
        .contact-actions {
            display: flex;
            gap: 5px;
        }
        
        .contact-actions button {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--light-text);
        }
        
        .contact-actions button:hover {
            color: var(--primary-color);
        }
        
        .contact-actions button.blocked {
            color: var(--danger-color);
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            width: 400px;
            max-width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .modal-header h2 {
            font-size: 20px;
        }
        
        .modal-header button {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
        }
        
        .form-group textarea {
            height: 80px;
        }
        
        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }
        
        .modal-footer button {
            padding: 8px 15px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
        }
        
        .modal-footer button.cancel {
            background: var(--secondary-color);
        }
        
        .modal-footer button.submit {
            background: var(--primary-color);
            color: white;
        }
        
        .user-selector {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            margin-top: 10px;
        }
        
        .user-selector-item {
            display: flex;
            align-items: center;
            padding: 8px 10px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .user-selector-item:last-child {
            border-bottom: none;
        }
        
        .user-selector-item input {
            margin-right: 10px;
        }
        
        .user-selector-item-info {
            flex: 1;
        }
        
        .group-members {
            margin-top: 15px;
        }
        
        .group-member {
            display: flex;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid var(--border-color);
        }
        
        .group-member:last-child {
            border-bottom: none;
        }
        
        .group-member-actions {
            margin-left: auto;
        }
        
        .group-member-actions button {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--danger-color);
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
            }
            
            .main-chat {
                display: none;
            }
            
            .main-chat.active {
                display: flex;
            }
        }

  /* Add to for remaining  features CSS in chat_groups.php */

/* File attachment styles */
.attachment-container {
    margin-top: 10px;
    padding: 8px;
    border-radius: 8px;
    background-color: #f0f2f5;
    display: flex;
    align-items: center;
}

.attachment-icon {
    margin-right: 10px;
    font-size: 20px;
    color: #65676b;
}

.attachment-info {
    flex: 1;
}

.attachment-name {
    font-weight: 500;
    margin-bottom: 3px;
    word-break: break-all;
}

.attachment-size {
    font-size: 12px;
    color: #65676b;
}

.attachment-image {
    max-width: 200px;
    max-height: 200px;
    border-radius: 8px;
    margin-top: 5px;
}

/* Profile picture upload */
.profile-picture-container {
    position: relative;
    width: 120px;
    height: 120px;
    margin: 0 auto 20px;
}

.profile-picture {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #0084ff;
}

.profile-picture-upload {
    position: absolute;
    bottom: 0;
    right: 0;
    background: #0084ff;
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

/* Group deletion button */
.delete-group-btn {
    background-color: #dc3545;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 4px;
    cursor: pointer;
    margin-top: 15px;
}

.delete-group-btn:hover {
    background-color: #c82333;
}

/* File upload button */
.file-upload-btn {
    background: none;
    border: none;
    color: #0084ff;
    font-size: 18px;
    cursor: pointer;
    margin-right: 10px;
}

/* Status indicators */
.status-badge {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
    margin-left: 5px;
}

.status-online-badge {
    background-color: #31a24c;
    color: white;
}

.status-offline-badge {
    background-color: #65676b;
    color: white;
}

.status-away-badge {
    background-color: #ffaa00;
    color: white;
}

.status-busy-badge {
    background-color: #ff4d4d;
    color: white;
}

/* Modal for file preview */
.file-preview-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.8);
    z-index: 2000;
    justify-content: center;
    align-items: center;
}

.file-preview-content {
    background: white;
    padding: 20px;
    border-radius: 8px;
    max-width: 80%;
    max-height: 80vh;
    overflow: auto;
    text-align: center;
}

.file-preview-content img {
    max-width: 100%;
    max-height: 70vh;
}

.file-preview-close {
    position: absolute;
    top: 20px;
    right: 20px;
    color: white;
    font-size: 30px;
    cursor: pointer;
}

/* Progress bar for file uploads */
.upload-progress {
    width: 100%;
    height: 5px;
    background: #e0e0e0;
    margin-top: 5px;
    border-radius: 5px;
    overflow: hidden;
    display: none;
}

.upload-progress-bar {
    height: 100%;
    background: #0084ff;
    width: 0%;
    transition: width 0.3s;
}
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Sidebar with chat list -->
    <div class="sidebar">
        <div class="header">
            <h2>Messages</h2>
            <div>
                <button id="new-group-btn" class="primary">+ New Group</button>
                <button id="contacts-btn"><i class="fas fa-address-book"></i></button>
            </div>
        </div>
        
        <div class="search-bar">
            <input type="text" id="search-chats" placeholder="Search messages...">
        </div>
        
        <div class="chat-types">
            <div class="chat-type active" data-type="all">All</div>
            <div class="chat-type" data-type="private">Private</div>
            <div class="chat-type" data-type="group">Group</div>
            <div class="chat-type" data-type="online">Online</div>
        </div>
        
        <div class="tab-content active" id="all-chats">
            <div class="chat-list" id="chat-list">
                <div class="loading">Loading chats...</div>
            </div>
        </div>
        
        <div class="tab-content" id="private-chats">
            <div class="chat-list" id="private-chat-list">
                <div class="loading">Loading private chats...</div>
            </div>
        </div>
        
        <div class="tab-content" id="group-chats">
            <div class="chat-list" id="group-chat-list">
                <div class="loading">Loading group chats...</div>
            </div>
        </div>
        
        <div class="tab-content" id="online-users">
            <div class="chat-list" id="online-users-list">
                <div class="loading">Loading online users...</div>
            </div>
        </div>
    </div>
    
    <!-- Main chat area -->
    <div class="main-chat" id="main-chat">
        <div class="empty-chat">
            <div class="empty-chat-icon">💬</div>
            <h3>Select a chat to start messaging</h3>
        </div>
    </div>
    
    <!-- New Group Modal -->
    <div class="modal" id="new-group-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Create New Group</h2>
                <button id="close-group-modal">&times;</button>
            </div>
            <div class="form-group">
                <label for="group-name">Group Name</label>
                <input type="text" id="group-name" placeholder="Enter group name">
            </div>
            <div class="form-group">
                <label for="group-description">Description (Optional)</label>
                <textarea id="group-description" placeholder="Enter group description"></textarea>
            </div>
            <div class="form-group">
                <label>Add Members</label>
                <div class="user-selector" id="group-members-selector">
                    <div class="loading">Loading contacts...</div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="cancel" id="cancel-group">Cancel</button>
                <button class="submit" id="create-group">Create</button>
            </div>
        </div>
    </div>
    
    <!-- Edit Group Modal -->
    <div class="modal" id="edit-group-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Group</h2>
                <button id="close-edit-group-modal">&times;</button>
            </div>
            <div class="form-group">
                <label for="edit-group-name">Group Name</label>
                <input type="text" id="edit-group-name" placeholder="Enter group name">
            </div>
            <div class="form-group">
                <label for="edit-group-description">Description (Optional)</label>
                <textarea id="edit-group-description" placeholder="Enter group description"></textarea>
            </div>
            <div class="group-members">
                <h3>Members</h3>
                <div id="group-members-list">
                    <div class="loading">Loading members...</div>
                </div>
            </div>
            <div class="form-group">
                <label>Add More Members</label>
                <div class="user-selector" id="edit-group-members-selector">
                    <div class="loading">Loading contacts...</div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="cancel" id="cancel-edit-group">Cancel</button>
                <button class="submit" id="save-group">Save</button>
                <button class="delete-group-btn" id="delete-group-btn">Delete Group</button>
            </div>
        </div>
    </div>
    
    <!-- Contacts Modal -->
    <div class="modal" id="contacts-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Contacts</h2>
                <button id="close-contacts-modal">&times;</button>
            </div>
            <div class="search-bar">
                <input type="text" id="search-contacts" placeholder="Search contacts...">
            </div>
            <div class="chat-list" id="contacts-list">
                <div class="loading">Loading contacts...</div>
            </div>
        </div>
        <div class="profile-picture-container">
    <img src="default-profile.jpg" alt="Profile Picture" class="profile-picture" id="profile-picture">
    <div class="profile-picture-upload" title="Change profile picture">
        <i class="fas fa-camera"></i>
        <input type="file" id="profile-picture-input" accept="image/*" style="display: none;">
    </div>
</div>
    </div>
    
    <script>
        // Current user ID (should be set after login)
        const currentUserId = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>;
        
        if (!currentUserId) {
            alert('Please login first');
            window.location.href = 'login.php'; // Redirect to login if not authenticated
        }
        
        // DOM elements
        const chatList = document.getElementById('chat-list');
        const privateChatList = document.getElementById('private-chat-list');
        const groupChatList = document.getElementById('group-chat-list');
        const onlineUsersList = document.getElementById('online-users-list');
        const mainChat = document.getElementById('main-chat');
        const chatTypes = document.querySelectorAll('.chat-type');
        const tabContents = document.querySelectorAll('.tab-content');
        const newGroupModal = document.getElementById('new-group-modal');
        const editGroupModal = document.getElementById('edit-group-modal');
        const contactsModal = document.getElementById('contacts-modal');
        
        // Current active chat and filter
        let currentChat = null;
        let currentFilter = 'all';
        let currentGroup = null;
        
        // WebSocket connection
        let socket = null;
        
        // Initialize WebSocket
        function initWebSocket() {
            const protocol = window.location.protocol === 'https:' ? 'wss://' : 'ws://';
            const host = window.location.host;
            const wsUrl = protocol + host + '/ws';
            
            socket = new WebSocket(wsUrl);
            
            socket.onopen = function() {
                console.log('WebSocket connected');
                // Send authentication message
                socket.send(JSON.stringify({
                    type: 'auth',
                    userId: currentUserId
                }));
            };
            
            socket.onmessage = function(event) {
                const data = JSON.parse(event.data);
                handleWebSocketMessage(data);
            };
            
            socket.onclose = function() {
                console.log('WebSocket disconnected');
                // Try to reconnect after 5 seconds
                setTimeout(initWebSocket, 5000);
            };
            
            socket.onerror = function(error) {
                console.error('WebSocket error:', error);
            };
        }
        
        // Handle WebSocket messages
        function handleWebSocketMessage(data) {
            switch (data.type) {
                case 'message':
                    handleNewMessage(data.message);
                    break;
                case 'status':
                    updateUserStatus(data.userId, data.status);
                    break;
                case 'typing':
                    showTypingIndicator(data.userId, data.conversationId || data.groupId);
                    break;
            }
        }
        
        // Handle new message from WebSocket
        function handleNewMessage(message) {
            if (currentChat) {
                // Check if this message belongs to the current chat
                if ((currentChat.type === 'private' && message.conversation_id === currentChat.conversation_id) ||
                    (currentChat.type === 'group' && message.group_id === currentChat.group_id)) {
                    // Add message to current chat view
                    appendMessage(message);
                    
                    // Scroll to bottom
                    const messagesContainer = document.getElementById('messages-container');
                    if (messagesContainer) {
                        messagesContainer.scrollTop = messagesContainer.scrollHeight;
                    }
                }
            }

            // Delete group button
document.getElementById('delete-group-btn')?.addEventListener('click', () => {
    if (currentGroup) {
        deleteGroup(currentGroup.group_id);
    }
});

// File upload
document.getElementById('file-upload-btn')?.addEventListener('click', () => {
    document.getElementById('file-input').click();
});

document.getElementById('file-input')?.addEventListener('change', async (e) => {
    if (e.target.files.length > 0 && currentChat) {
        const file = e.target.files[0];
        const uploadResult = await uploadFile(file);
        
        if (uploadResult.error) {
            alert(uploadResult.error);
            return;
        }
        
        // Create a message with the file
        const messageContent = `[File: ${file.name}]`;
        const messageResult = await sendMessage(currentChat, messageContent);
        
        if (messageResult.error) {
            alert(messageResult.error);
            return;
        }
        
        // Attach the file to the message
        await attachFileToMessage(messageResult.message_id, uploadResult.file_id);
        
        // Refresh the chat
        await renderChat(currentChat);
        renderChatList(currentFilter);
    }
});

// Profile picture upload
document.querySelector('.profile-picture-upload')?.addEventListener('click', () => {
    document.getElementById('profile-picture-input').click();
});

document.getElementById('profile-picture-input')?.addEventListener('change', async (e) => {
    if (e.target.files.length > 0) {
        const file = e.target.files[0];
        const result = await updateProfilePicture(file);
        
        if (result.error) {
            alert(result.error);
        } else {
            // Update profile picture display
            document.getElementById('profile-picture').src = result.profile_picture;
            // Refresh any views that show the profile picture
            renderChatList(currentFilter);
            if (currentChat) {
                renderChat(currentChat);
            }
        }
    }
});

// Update user status when window gains/loses focus
window.addEventListener('focus', () => {
    updateUserStatus('online');
});

window.addEventListener('blur', () => {
    updateUserStatus('away');
});

// Update status on page load
document.addEventListener('DOMContentLoaded', () => {
    updateUserStatus('online');
});

// Update status before page unload
window.addEventListener('beforeunload', () => {
    // Note: This may not always work due to browser restrictions
    navigator.sendBeacon('chat_groups.php?action=update_user_status', 
        new URLSearchParams({status: 'offline'}));
});
            
            // Update chat list to show new message
            renderChatList(currentFilter);
        }
        
        // Update user status in UI
        function updateUserStatus(userId, status) {
            // Update in contacts list
            const contactItems = document.querySelectorAll(`.contact-item[data-user-id="${userId}"]`);
            contactItems.forEach(item => {
                const statusIndicator = item.querySelector('.status-indicator');
                if (statusIndicator) {
                    statusIndicator.className = 'status-indicator';
                    statusIndicator.classList.add(`status-${status}`);
                }
                
                const statusText = item.querySelector('.contact-status');
                if (statusText) {
                    statusText.textContent = status === 'online' ? 'Online' : 
                                           status === 'away' ? 'Away' :
                                           status === 'busy' ? 'Busy' : 'Offline';
                }
            });
            
            // Update in online users list
            const onlineUserItems = document.querySelectorAll(`.chat-item[data-user-id="${userId}"]`);
            onlineUserItems.forEach(item => {
                const statusIndicator = item.querySelector('.status-indicator');
                if (statusIndicator) {
                    statusIndicator.className = 'status-indicator';
                    statusIndicator.classList.add(`status-${status}`);
                }
            });
            
            // Update in chat list
            const chatItems = document.querySelectorAll(`.chat-item[data-other-user-id="${userId}"]`);
            chatItems.forEach(item => {
                const statusIndicator = item.querySelector('.status-indicator');
                if (statusIndicator) {
                    statusIndicator.className = 'status-indicator';
                    statusIndicator.classList.add(`status-${status}`);
                }
            });
            
            // Update in current chat header if this is the other user
            if (currentChat && currentChat.type === 'private' && currentChat.other_user_id == userId) {
                const chatHeaderStatus = document.querySelector('.chat-header-status');
                if (chatHeaderStatus) {
                    chatHeaderStatus.textContent = status === 'online' ? 'Online' : 
                                                 status === 'away' ? 'Away' :
                                                 status === 'busy' ? 'Busy' : 'Offline';
                }
                
                const statusIndicator = document.querySelector('.chat-header .status-indicator');
                if (statusIndicator) {
                    statusIndicator.className = 'status-indicator';
                    statusIndicator.classList.add(`status-${status}`);
                }
            }
        }
        
        // Show typing indicator
        function showTypingIndicator(userId, conversationIdOrGroupId) {
            if (!currentChat) return;
            
            const isCurrentChat = (currentChat.type === 'private' && currentChat.conversation_id == conversationIdOrGroupId) ||
                                 (currentChat.type === 'group' && currentChat.group_id == conversationIdOrGroupId);
            
            if (isCurrentChat) {
                const typingIndicator = document.getElementById('typing-indicator');
                if (typingIndicator) {
                    typingIndicator.textContent = 'Typing...';
                    typingIndicator.style.display = 'block';
                    
                    // Hide after 3 seconds
                    setTimeout(() => {
                        typingIndicator.style.display = 'none';
                    }, 3000);
                }
            }
        }
        
        // Send typing indicator
        function sendTypingIndicator() {
            if (!socket || socket.readyState !== WebSocket.OPEN) return;
            if (!currentChat) return;
            
            socket.send(JSON.stringify({
                type: 'typing',
                userId: currentUserId,
                [currentChat.type === 'private' ? 'conversationId' : 'groupId']: 
                    currentChat.type === 'private' ? currentChat.conversation_id : currentChat.group_id
            }));
        }
        
        // Fetch chats from server
        async function fetchChats(filter = 'all') {
            try {
                const response = await fetch(`chat_groups.php?action=get_chats`);
                const chats = await response.json();
                
                if (chats.error) {
                    throw new Error(chats.error);
                }
                
                return filter === 'all' ? chats : 
                       filter === 'private' ? chats.filter(c => c.type === 'private') :
                       filter === 'group' ? chats.filter(c => c.type === 'group') : [];
            } catch (error) {
                console.error('Error fetching chats:', error);
                return [];
            }
        }
        
        // Fetch online users from server
        async function fetchOnlineUsers() {
            try {
                const response = await fetch('chat_groups.php?action=get_online_users');
                const users = await response.json();
                
                if (users.error) {
                    throw new Error(users.error);
                }
                
                return users;
            } catch (error) {
                console.error('Error fetching online users:', error);
                return [];
            }
        }
        
        // Fetch contacts from server
        async function fetchContacts() {
            try {
                const response = await fetch('chat_groups.php?action=get_contacts');
                const contacts = await response.json();
                
                if (contacts.error) {
                    throw new Error(contacts.error);
                }
                
                return contacts;
            } catch (error) {
                console.error('Error fetching contacts:', error);
                return [];
            }
        }
        
        // Fetch messages for a chat
        async function fetchMessages(chat) {
            try {
                let url;
                if (chat.type === 'private') {
                    url = `chat_groups.php?action=get_messages&conversation_id=${chat.conversation_id}`;
                } else {
                    url = `chat_groups.php?action=get_messages&group_id=${chat.group_id}`;
                }
                
                const response = await fetch(url);
                const messages = await response.json();
                
                if (messages.error) {
                    throw new Error(messages.error);
                }
                
                return messages;
            } catch (error) {
                console.error('Error fetching messages:', error);
                return [];
            }
        }
        
        // Send a message
        async function sendMessage(chat, content) {
            try {
                const formData = new FormData();
                formData.append('content', content);
                
                let url;
                if (chat.type === 'private') {
                    formData.append('conversation_id', chat.conversation_id);
                    url = 'chat_groups.php?action=send_message';
                } else {
                    formData.append('group_id', chat.group_id);
                    url = 'chat_groups.php?action=send_message';
                }
                
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.error) {
                    throw new Error(result.error);
                }
                
                // Notify via WebSocket
                if (socket && socket.readyState === WebSocket.OPEN) {
                    socket.send(JSON.stringify({
                        type: 'message',
                        message: {
                            ...result,
                            content: content,
                            sender_id: currentUserId,
                            [chat.type === 'private' ? 'conversation_id' : 'group_id']: 
                                chat.type === 'private' ? chat.conversation_id : chat.group_id,
                            sent_at: new Date().toISOString(),
                            status: 'sent'
                        }
                    }));
                }
                
                return result;
            } catch (error) {
                console.error('Error sending message:', error);
                return { error: error.message };
            }
        }


        // additional features
        // Function to delete a group
async function deleteGroup(groupId) {
    if (confirm('Are you sure you want to delete this group? This action cannot be undone.')) {
        const formData = new FormData();
        formData.append('group_id', groupId);
        
        const response = await fetch('chat_groups.php?action=delete_group', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.error) {
            alert(result.error);
        } else {
            // Close group info modal
            editGroupModal.style.display = 'none';
            
            // If we were viewing the deleted group, clear the chat view
            if (currentChat && currentChat.type === 'group' && currentChat.group_id == groupId) {
                currentChat = null;
                mainChat.innerHTML = `
                    <div class="empty-chat">
                        <div class="empty-chat-icon">💬</div>
                        <h3>Select a chat to start messaging</h3>
                    </div>
                `;
            }
            
            // Refresh chat list
            renderChatList(currentFilter);
        }
    }
}

// Function to upload a file
async function uploadFile(file) {
    const formData = new FormData();
    formData.append('file', file);
    
    const response = await fetch('chat_groups.php?action=upload_file', {
        method: 'POST',
        body: formData
    });
    
    return await response.json();
}

// Function to attach file to message
async function attachFileToMessage(messageId, fileId) {
    const formData = new FormData();
    formData.append('message_id', messageId);
    formData.append('file_id', fileId);
    
    const response = await fetch('chat_groups.php?action=attach_file_to_message', {
        method: 'POST',
        body: formData
    });
    
    return await response.json();
}

// Function to update profile picture
async function updateProfilePicture(file) {
    const formData = new FormData();
    formData.append('profile_picture', file);
    
    const response = await fetch('chat_groups.php?action=update_profile_picture', {
        method: 'POST',
        body: formData
    });
    
    return await response.json();
}

// Function to update user status
async function updateUserStatus(status) {
    const formData = new FormData();
    formData.append('status', status);
    
    const response = await fetch('chat_groups.php?action=update_user_status', {
        method: 'POST',
        body: formData
    });
    
    return await response.json();
}

// Function to get message attachments
async function getMessageAttachments(messageId) {
    const response = await fetch(`chat_groups.php?action=get_message_attachments&message_id=${messageId}`);
    return await response.json();
}
        ////////////
        
        // Create a new group
        async function createGroup(name, description, members) {
            try {
                const formData = new FormData();
                formData.append('group_name', name);
                formData.append('description', description);
                formData.append('members', JSON.stringify(members));
                
                const response = await fetch('chat_groups.php?action=create_group', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.error) {
                    throw new Error(result.error);
                }
                
                return result;
            } catch (error) {
                console.error('Error creating group:', error);
                return { error: error.message };
            }
        }
        
        // Update group
        async function updateGroup(groupId, name, description) {
            try {
                const formData = new FormData();
                formData.append('group_id', groupId);
                if (name) formData.append('group_name', name);
                if (description) formData.append('description', description);
                
                const response = await fetch('chat_groups.php?action=update_group', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.error) {
                    throw new Error(result.error);
                }
                
                return result;
            } catch (error) {
                console.error('Error updating group:', error);
                return { error: error.message };
            }
        }
        
        // Add group members
        async function addGroupMembers(groupId, members) {
            try {
                const formData = new FormData();
                formData.append('group_id', groupId);
                formData.append('members', JSON.stringify(members));
                
                const response = await fetch('chat_groups.php?action=add_group_members', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.error) {
                    throw new Error(result.error);
                }
                
                return result;
            } catch (error) {
                console.error('Error adding group members:', error);
                return { error: error.message };
            }
        }
        
        // Remove group member
        async function removeGroupMember(groupId, userId) {
            try {
                const formData = new FormData();
                formData.append('group_id', groupId);
                formData.append('user_id', userId);
                
                const response = await fetch('chat_groups.php?action=remove_group_member', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.error) {
                    throw new Error(result.error);
                }
                
                return result;
            } catch (error) {
                console.error('Error removing group member:', error);
                return { error: error.message };
            }
        }
        
        // Block user
        async function blockUser(userId) {
            try {
                const formData = new FormData();
                formData.append('user_id', userId);
                
                const response = await fetch('chat_groups.php?action=block_user', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.error) {
                    throw new Error(result.error);
                }
                
                return result;
            } catch (error) {
                console.error('Error blocking user:', error);
                return { error: error.message };
            }
        }
        
        // Unblock user
        async function unblockUser(userId) {
            try {
                const formData = new FormData();
                formData.append('user_id', userId);
                
                const response = await fetch('chat_groups.php?action=unblock_user', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.error) {
                    throw new Error(result.error);
                }
                
                return result;
            } catch (error) {
                console.error('Error unblocking user:', error);
                return { error: error.message };
            }
        }
        
        // Star conversation
        async function starConversation(conversationId) {
            try {
                const formData = new FormData();
                formData.append('conversation_id', conversationId);
                
                const response = await fetch('chat_groups.php?action=star_conversation', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.error) {
                    throw new Error(result.error);
                }
                
                return result;
            } catch (error) {
                console.error('Error starring conversation:', error);
                return { error: error.message };
            }
        }
        
        // Unstar conversation
        async function unstarConversation(conversationId) {
            try {
                const formData = new FormData();
                formData.append('conversation_id', conversationId);
                
                const response = await fetch('chat_groups.php?action=unstar_conversation', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.error) {
                    throw new Error(result.error);
                }
                
                return result;
            } catch (error) {
                console.error('Error unstarring conversation:', error);
                return { error: error.message };
            }
        }
        
        // Start private chat
        async function startPrivateChat(userId) {
            try {
                const formData = new FormData();
                formData.append('user_id', userId);
                
                const response = await fetch('chat_groups.php?action=start_private_chat', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.error) {
                    throw new Error(result.error);
                }
                
                return result;
            } catch (error) {
                console.error('Error starting private chat:', error);
                return { error: error.message };
            }
        }
        
        // Delete message
        async function deleteMessage(messageId) {
            try {
                const formData = new FormData();
                formData.append('message_id', messageId);
                
                const response = await fetch('chat_groups.php?action=delete_message', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.error) {
                    throw new Error(result.error);
                }
                
                return result;
            } catch (error) {
                console.error('Error deleting message:', error);
                return { error: error.message };
            }
        }
        
        // Edit message
        async function editMessage(messageId, content) {
            try {
                const formData = new FormData();
                formData.append('message_id', messageId);
                formData.append('content', content);
                
                const response = await fetch('chat_groups.php?action=edit_message', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.error) {
                    throw new Error(result.error);
                }
                
                return result;
            } catch (error) {
                console.error('Error editing message:', error);
                return { error: error.message };
            }
        }
        
        // Search messages
        async function searchMessages(query) {
            try {
                const response = await fetch(`chat_groups.php?action=search_messages&query=${encodeURIComponent(query)}`);
                const results = await response.json();
                
                if (results.error) {
                    throw new Error(results.error);
                }
                
                return results;
            } catch (error) {
                console.error('Error searching messages:', error);
                return [];
            }
        }
        
        // Render chat list
        async function renderChatList(filter = 'all') {
            const container = filter === 'private' ? privateChatList : 
                            filter === 'group' ? groupChatList : 
                            filter === 'online' ? onlineUsersList : chatList;
            
            container.innerHTML = '<div class="loading">Loading chats...</div>';
            
            try {
                let items;
                
                if (filter === 'online') {
                    items = await fetchOnlineUsers();
                } else {
                    const chats = await fetchChats(filter);
                    items = chats;
                }
                
                if (items.length === 0) {
                    container.innerHTML = '<div style="padding: 20px; text-align: center; color: var(--light-text);">No items found</div>';
                    return;
                }
                
                container.innerHTML = '';
                
                items.forEach(item => {
                    const itemElement = document.createElement('div');
                    itemElement.className = 'chat-item';
                    
                    if (filter === 'online') {
                        // Online user item
                        itemElement.dataset.userId = item.user_id;
                        itemElement.addEventListener('click', () => startNewPrivateChat(item));
                        
                        const statusClass = item.is_online ? 'status-online' : 'status-offline';
                        
                        itemElement.innerHTML = `
                            <div class="chat-avatar">
                                <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(item.full_name)}&background=random" alt="${item.full_name}">
                                <div class="status-indicator ${statusClass}"></div>
                            </div>
                            <div class="chat-info">
                                <div class="chat-name">
                                    ${item.full_name}
                                </div>
                                <div class="chat-preview">
                                    ${item.email}
                                </div>
                            </div>
                        `;
                    } else {
                        // Chat item (private or group)
                        const id = item.type === 'private' ? `private_${item.conversation_id}` : `group_${item.group_id}`;
                        itemElement.dataset.id = id;
                        
                        if (item.type === 'private') {
                            itemElement.dataset.otherUserId = item.other_user_id;
                        }
                        
                        if (item.is_starred) {
                            itemElement.classList.add('starred');
                        }
                        
                        const avatar = `https://ui-avatars.com/api/?name=${encodeURIComponent(item.name)}&background=random`;
                        
                        let statusIndicator = '';
                        if (item.type === 'private' && item.is_online !== undefined) {
                            const statusClass = item.is_online ? 'status-online' : 'status-offline';
                            statusIndicator = `<div class="status-indicator ${statusClass}"></div>`;
                        }
                        
                        let starIcon = item.is_starred ? '<i class="fas fa-star star-icon"></i>' : '';
                        
                        itemElement.innerHTML = `
                            <div class="chat-avatar">
                                <img src="${avatar}" alt="${item.name}">
                                ${statusIndicator}
                            </div>
                            <div class="chat-info">
                                <div class="chat-name">
                                    ${item.name}
                                    ${starIcon}
                                    <span class="chat-time">${formatTime(item.last_message_at || item.updated_at || item.created_at)}</span>
                                </div>
                                <div class="chat-preview">
                                    ${item.last_message ? truncate(item.last_message, 30) : 'No messages yet'}
                                    ${item.type === 'group' ? `<small>(${item.member_count || 0} members)</small>` : ''}
                                </div>
                            </div>
                        `;
                        
                        itemElement.addEventListener('click', () => renderChat(item));
                    }
                    
                    container.appendChild(itemElement);
                });
            } catch (error) {
                container.innerHTML = `<div class="error">${error.message}</div>`;
            }
        }
        
        // Render contacts list
        async function renderContactsList() {
            const container = document.getElementById('contacts-list');
            container.innerHTML = '<div class="loading">Loading contacts...</div>';
            
            try {
                const contacts = await fetchContacts();
                
                if (contacts.length === 0) {
                    container.innerHTML = '<div style="padding: 20px; text-align: center; color: var(--light-text);">No contacts found</div>';
                    return;
                }
                
                container.innerHTML = '';
                
                contacts.forEach(contact => {
                    const contactElement = document.createElement('div');
                    contactElement.className = 'contact-item';
                    contactElement.dataset.userId = contact.user_id;
                    
                    const statusClass = contact.is_online ? 'status-online' : 'status-offline';
                    const statusText = contact.is_online ? 'Online' : contact.last_seen ? `Last seen ${formatTime(contact.last_seen)}` : 'Offline';
                    
                    let blockButton = contact.is_blocked ? 
                        `<button class="blocked" data-user-id="${contact.user_id}" title="Unblock"><i class="fas fa-ban"></i></button>` :
                        `<button data-user-id="${contact.user_id}" title="Block"><i class="fas fa-ban"></i></button>`;
                    
                    contactElement.innerHTML = `
                        <div class="chat-avatar">
                            <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(contact.full_name)}&background=random" alt="${contact.full_name}">
                            <div class="status-indicator ${statusClass}"></div>
                        </div>
                        <div class="contact-info">
                            <div class="contact-name">${contact.full_name}</div>
                            <div class="contact-status">${statusText}</div>
                        </div>
                        <div class="contact-actions">
                            <button data-user-id="${contact.user_id}" title="Chat"><i class="fas fa-comment"></i></button>
                            ${blockButton}
                        </div>
                    `;
                    
                    container.appendChild(contactElement);
                });
                
                // Add event listeners
                document.querySelectorAll('.contact-actions button[title="Chat"]').forEach(button => {
                    button.addEventListener('click', (e) => {
                        e.stopPropagation();
                        const userId = button.dataset.userId;
                        startNewPrivateChat({ user_id: userId });
                    });
                });
                
                document.querySelectorAll('.contact-actions button[title="Block"], .contact-actions button.blocked').forEach(button => {
                    button.addEventListener('click', async (e) => {
                        e.stopPropagation();
                        const userId = button.dataset.userId;
                        const isBlocked = button.classList.contains('blocked');
                        
                        if (isBlocked) {
                            const result = await unblockUser(userId);
                            if (!result.error) {
                                button.classList.remove('blocked');
                                button.title = 'Block';
                                renderContactsList();
                                renderChatList(currentFilter);
                            }
                        } else {
                            const result = await blockUser(userId);
                            if (!result.error) {
                                button.classList.add('blocked');
                                button.title = 'Unblock';
                                renderContactsList();
                                renderChatList(currentFilter);
                            }
                        }
                    });
                });
                
                // Click on contact item to view profile (could be implemented)
                document.querySelectorAll('.contact-item').forEach(item => {
                    item.addEventListener('click', () => {
                        // Could show user profile here
                    });
                });
            } catch (error) {
                container.innerHTML = `<div class="error">${error.message}</div>`;
            }
        }
        
        // Start new private chat with user
        async function startNewPrivateChat(user) {
            const result = await startPrivateChat(user.user_id);
            if (result.error) {
                alert(result.error);
                return;
            }
            
            // Get the conversation details
            const chats = await fetchChats('private');
            const conversation = chats.find(c => 
                c.type === 'private' && c.other_user_id == user.user_id
            );
            
            if (conversation) {
                renderChat(conversation);
                renderChatList(currentFilter);
                contactsModal.style.display = 'none';
            }
        }
        
        // Render a specific chat
        async function renderChat(chat) {
            currentChat = chat;
            
            // Highlight selected chat in list
            document.querySelectorAll('.chat-item').forEach(item => {
                item.classList.remove('active');
                if (item.dataset.id === (chat.type === 'private' ? `private_${chat.conversation_id}` : `group_${chat.group_id}`)) {
                    item.classList.add('active');
                }
            });
            
            // Load messages
            const messages = await fetchMessages(chat);
            
            // Render chat header
            const avatar = `https://ui-avatars.com/api/?name=${encodeURIComponent(chat.name)}&background=random`;
            
            let statusIndicator = '';
            let statusText = '';
            
            if (chat.type === 'private') {
                const statusClass = chat.is_online ? 'status-online' : 'status-offline';
                statusIndicator = `<div class="status-indicator ${statusClass}"></div>`;
                statusText = chat.is_online ? 'Online' : chat.last_seen ? `Last seen ${formatTime(chat.last_seen)}` : 'Offline';
            } else {
                statusText = `${chat.member_count || 0} members`;
            }
            
            let headerActions = '';
            if (chat.type === 'private') {
                headerActions = `
                    <button title="Star conversation" id="star-chat">
                        <i class="${chat.is_starred ? 'fas' : 'far'} fa-star"></i>
                    </button>
                `;
            } else {
                headerActions = `
                    <button title="Star conversation" id="star-chat">
                        <i class="${chat.is_starred ? 'fas' : 'far'} fa-star"></i>
                    </button>
                    <button title="Group info" id="group-info">
                        <i class="fas fa-info-circle"></i>
                    </button>
                `;
            }
            
            // Render messages
            let messagesHTML = '';
            messages.forEach(message => {
                const isSent = message.sender_id == currentUserId;
                const senderName = isSent ? 'You' : (message.sender_name || 'Unknown');
                
                let messageActions = '';
                if (isSent) {
                    messageActions = `
                        <div class="message-actions">
                            <button class="edit-message" data-message-id="${message.message_id}">Edit</button>
                            <button class="delete delete-message" data-message-id="${message.message_id}">Delete</button>
                        </div>
                    `;
                }
                
                let editedIndicator = message.edited_at ? '<span class="message-edited">(edited)</span>' : '';
                
                messagesHTML += `
                    <div class="message ${isSent ? 'sent' : 'received'}">
                        ${messageActions}
                        ${!isSent && chat.type === 'group' ? `<div style="font-size: 12px; margin-bottom: 2px;">${senderName}</div>` : ''}
                        <div class="message-content">${message.content}</div>
                        <div class="message-time">
                            ${formatTime(message.sent_at)}
                            ${isSent ? `<span class="message-status">${message.status === 'read' ? '✓✓' : '✓'}</span>` : ''}
                            ${editedIndicator}
                        </div>
                    </div>
                `;
            });
            
            // Add typing indicator placeholder
            messagesHTML += `<div class="typing-indicator" id="typing-indicator" style="display:none;"></div>`;
            
            // Render input area
            let inputHTML = `
                <input type="text" placeholder="Type a message..." id="message-input">
                <button class="send-button" id="send-button">➤</button>
            `;
            
            // Update main chat area

            
            mainChat.innerHTML = `
                <div class="chat-header">
                    <div class="chat-header-left">
                        <div class="chat-avatar">
                            <img src="${avatar}" alt="${chat.name}">
                            ${statusIndicator}
                        </div>
                        <div class="chat-header-info">
                            <div class="chat-header-name">${chat.name}</div>
                            <div class="chat-header-status">${statusText}</div>
                        </div>
                    </div>
                    <div class="chat-header-actions">
                        ${headerActions}
                    </div>
                </div>
                <div class="chat-messages" id="messages-container">
                    ${messagesHTML}
                </div>
                <div class="chat-input">
                <button class="file-upload-btn" id="file-upload-btn" title="Attach file">
    <i class="fas fa-paperclip"></i>
    <input type="file" id="file-input" style="display: none;">
</button>
                    ${inputHTML}
                </div>
            `;
            
            // Scroll to bottom of messages
            const messagesContainer = document.getElementById('messages-container');
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
            
            // Add event listener for sending messages
            const sendButton = document.getElementById('send-button');
            const inputField = document.getElementById('message-input');
            
            sendButton.addEventListener('click', async () => {
                const messageContent = inputField.value.trim();
                if (messageContent) {
                    const result = await sendMessage(chat, messageContent);
                    if (!result.error) {
                        // Refresh the chat view
                        await renderChat(chat);
                        // Refresh the chat list to update last message
                        renderChatList(currentFilter);
                    }
                    inputField.value = '';
                    inputField.focus();
                }
            });
            
            // Also send on Enter key
            inputField.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    sendButton.click();
                }
            });
            
            // Typing indicator
            let typingTimeout;
            inputField.addEventListener('input', () => {
                // Clear any existing timeout
                if (typingTimeout) clearTimeout(typingTimeout);
                
                // Send typing indicator
                sendTypingIndicator();
                
                // Set timeout to stop typing indicator after 3 seconds of inactivity
                typingTimeout = setTimeout(() => {
                    // The server will automatically clear the typing indicator after a timeout
                }, 3000);
            });
            
            // Add event listeners for message actions
            document.querySelectorAll('.delete-message').forEach(button => {
                button.addEventListener('click', async (e) => {
                    e.stopPropagation();
                    const messageId = button.dataset.messageId;
                    const result = await deleteMessage(messageId);
                    if (!result.error) {
                        await renderChat(chat);
                    }
                });
            });
            
            document.querySelectorAll('.edit-message').forEach(button => {
                button.addEventListener('click', async (e) => {
                    e.stopPropagation();
                    const messageId = button.dataset.messageId;
                    const messageElement = button.closest('.message');
                    const messageContent = messageElement.querySelector('.message-content').textContent;
                    
                    // Replace message with input field
                    messageElement.querySelector('.message-content').innerHTML = `
                        <input type="text" value="${messageContent}" id="edit-message-input">
                        <button id="save-edit">Save</button>
                        <button id="cancel-edit">Cancel</button>
                    `;
                    
                    const editInput = document.getElementById('edit-message-input');
                    editInput.focus();
                    
                    document.getElementById('save-edit').addEventListener('click', async () => {
                        const newContent = editInput.value.trim();
                        if (newContent && newContent !== messageContent) {
                            const result = await editMessage(messageId, newContent);
                            if (!result.error) {
                                await renderChat(chat);
                            }
                        } else {
                            await renderChat(chat);
                        }
                    });
                    
                    document.getElementById('cancel-edit').addEventListener('click', async () => {
                        await renderChat(chat);
                    });
                    
                    editInput.addEventListener('keypress', (e) => {
                        if (e.key === 'Enter') {
                            document.getElementById('save-edit').click();
                        }
                    });
                });
            });
            
            // Star/unstar conversation
            const starButton = document.getElementById('star-chat');
            if (starButton) {
                starButton.addEventListener('click', async () => {
                    if (chat.is_starred) {
                        const result = await unstarConversation(
                            chat.type === 'private' ? chat.conversation_id : chat.group_id
                        );
                        if (!result.error) {
                            chat.is_starred = false;
                            starButton.innerHTML = '<i class="far fa-star"></i>';
                            renderChatList(currentFilter);
                        }
                    } else {
                        const result = await starConversation(
                            chat.type === 'private' ? chat.conversation_id : chat.group_id
                        );
                        if (!result.error) {
                            chat.is_starred = true;
                            starButton.innerHTML = '<i class="fas fa-star"></i>';
                            renderChatList(currentFilter);
                        }
                    }
                });
            }
            
            // Group info
            const groupInfoButton = document.getElementById('group-info');
            if (groupInfoButton) {
                groupInfoButton.addEventListener('click', () => {
                    showGroupInfo(chat.group_id);
                });
            }
            
            inputField.focus();
        }
        
        // Show group info modal
        async function showGroupInfo(groupId) {
            try {
                // Fetch group details
                const response = await fetch(`chat_groups.php?action=get_chats`);
                const chats = await response.json();
                
                if (chats.error) {
                    throw new Error(chats.error);
                }
                
                const group = chats.find(c => c.type === 'group' && c.group_id == groupId);
                if (!group) {
                    throw new Error('Group not found');
                }
                
                // Fetch contacts for member selector
                const contacts = await fetchContacts();
                const groupMembers = await fetchGroupMembers(groupId);
                
                // Populate edit form
                document.getElementById('edit-group-name').value = group.name;
                document.getElementById('edit-group-description').value = group.description || '';
                
                // Render members list
                const membersList = document.getElementById('group-members-list');
                membersList.innerHTML = '';
                
                groupMembers.forEach(member => {
                    const memberElement = document.createElement('div');
                    memberElement.className = 'group-member';
                    memberElement.innerHTML = `
                        <div class="chat-avatar">
                            <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(member.full_name)}&background=random" alt="${member.full_name}">
                        </div>
                        <div class="contact-info">
                            <div class="contact-name">${member.full_name}</div>
                            <div class="contact-status">${member.is_admin ? 'Admin' : 'Member'}</div>
                        </div>
                        ${member.user_id != currentUserId ? `
                            <div class="group-member-actions">
                                <button data-user-id="${member.user_id}" title="Remove"><i class="fas fa-times"></i></button>
                            </div>
                        ` : ''}
                    `;
                    
                    // Add remove member event
                    const removeButton = memberElement.querySelector('button');
                    if (removeButton) {
                        removeButton.addEventListener('click', async () => {
                            const result = await removeGroupMember(groupId, member.user_id);
                            if (!result.error) {
                                showGroupInfo(groupId);
                                renderChatList(currentFilter);
                            }
                        });
                    }
                    
                    membersList.appendChild(memberElement);
                });
                
                // Render member selector (excluding current members)
                const selector = document.getElementById('edit-group-members-selector');
                selector.innerHTML = '';
                
                const nonMembers = contacts.filter(contact => 
                    !groupMembers.some(member => member.user_id == contact.user_id) &&
                    contact.user_id != currentUserId &&
                    !contact.is_blocked &&
                    !contact.has_blocked_me
                );
                
                if (nonMembers.length === 0) {
                    selector.innerHTML = '<div style="padding: 10px; text-align: center; color: var(--light-text);">No contacts available to add</div>';
                } else {
                    nonMembers.forEach(contact => {
                        const contactElement = document.createElement('div');
                        contactElement.className = 'user-selector-item';
                        contactElement.innerHTML = `
                            <input type="checkbox" id="edit-member-${contact.user_id}" value="${contact.user_id}">
                            <div class="user-selector-item-info">
                                <label for="edit-member-${contact.user_id}">${contact.full_name}</label>
                            </div>
                        `;
                        selector.appendChild(contactElement);
                    });
                }
                
                // Set current group
                currentGroup = group;
                
                // Show modal
                editGroupModal.style.display = 'flex';
            } catch (error) {
                console.error('Error showing group info:', error);
                alert(error.message);
            }
        }
        
        // Fetch group members
        async function fetchGroupMembers(groupId) {
            try {
                const response = await fetch(`chat_groups.php?action=get_group_members&group_id=${groupId}`);
                const members = await response.json();
                
                if (members.error) {
                    throw new Error(members.error);
                }
                
                return members;
            } catch (error) {
                console.error('Error fetching group members:', error);
                return [];
            }
        }
        
        // Render group members selector for new group
        async function renderGroupMembersSelector() {
            const selector = document.getElementById('group-members-selector');
            selector.innerHTML = '<div class="loading">Loading contacts...</div>';
            
            try {
                const contacts = await fetchContacts();
                
                // Filter out blocked users and users who blocked me
                const availableContacts = contacts.filter(contact => 
                    !contact.is_blocked && !contact.has_blocked_me && contact.user_id != currentUserId
                );
                
                if (availableContacts.length === 0) {
                    selector.innerHTML = '<div style="padding: 10px; text-align: center; color: var(--light-text);">No contacts available to add</div>';
                    return;
                }
                
                selector.innerHTML = '';
                
                availableContacts.forEach(contact => {
                    const contactElement = document.createElement('div');
                    contactElement.className = 'user-selector-item';
                    contactElement.innerHTML = `
                        <input type="checkbox" id="member-${contact.user_id}" value="${contact.user_id}">
                        <div class="user-selector-item-info">
                            <label for="member-${contact.user_id}">${contact.full_name}</label>
                        </div>
                    `;
                    selector.appendChild(contactElement);
                });
            } catch (error) {
                selector.innerHTML = `<div class="error">${error.message}</div>`;
            }
        }
        
        // Append a new message to the chat (used for real-time updates)
        function appendMessage(message) {
            const isSent = message.sender_id == currentUserId;
            const senderName = isSent ? 'You' : (message.sender_name || 'Unknown');
            
            let messageActions = '';
            if (isSent) {
                messageActions = `
                    <div class="message-actions">
                        <button class="edit-message" data-message-id="${message.message_id}">Edit</button>
                        <button class="delete delete-message" data-message-id="${message.message_id}">Delete</button>
                    </div>
                `;
            }
            
            let editedIndicator = message.edited_at ? '<span class="message-edited">(edited)</span>' : '';
            
            const messageHTML = `
                <div class="message ${isSent ? 'sent' : 'received'}">
                    ${messageActions}
                    ${!isSent && currentChat.type === 'group' ? `<div style="font-size: 12px; margin-bottom: 2px;">${senderName}</div>` : ''}
                    <div class="message-content">${message.content}</div>
                    <div class="message-time">
                        ${formatTime(message.sent_at)}
                        ${isSent ? `<span class="message-status">✓</span>` : ''}
                        ${editedIndicator}
                    </div>
                </div>
            `;
            
            const messagesContainer = document.getElementById('messages-container');
            if (messagesContainer) {
                // Insert before typing indicator
                const typingIndicator = document.getElementById('typing-indicator');
                if (typingIndicator) {
                    typingIndicator.insertAdjacentHTML('beforebegin', messageHTML);
                } else {
                    messagesContainer.insertAdjacentHTML('beforeend', messageHTML);
                }
            }
        }
        
        // Helper function to format time
        function formatTime(dateString) {
            if (!dateString) return '';
            
            const date = new Date(dateString);
            const now = new Date();
            
            if (date.toDateString() === now.toDateString()) {
                return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            }
            
            const yesterday = new Date(now);
            yesterday.setDate(yesterday.getDate() - 1);
            if (date.toDateString() === yesterday.toDateString()) {
                return 'Yesterday';
            }
            
            return date.toLocaleDateString([], { month: 'short', day: 'numeric' });
        }
        
        // Helper function to truncate text
        function truncate(text, length) {
            return text.length > length ? text.substring(0, length) + '...' : text;
        }
        
        // Initialize chat type filters
        chatTypes.forEach(type => {
            type.addEventListener('click', () => {
                chatTypes.forEach(t => t.classList.remove('active'));
                type.classList.add('active');
                
                tabContents.forEach(tab => tab.classList.remove('active'));
                
                currentFilter = type.dataset.type;
                const tabId = `${currentFilter}-chats`;
                document.getElementById(tabId).classList.add('active');
                
                renderChatList(currentFilter);
            });
        });
        
        // New group button functionality
        document.getElementById('new-group-btn').addEventListener('click', () => {
            renderGroupMembersSelector();
            newGroupModal.style.display = 'flex';
        });
        
        document.getElementById('close-group-modal').addEventListener('click', () => {
            newGroupModal.style.display = 'none';
        });
        
        document.getElementById('cancel-group').addEventListener('click', () => {
            newGroupModal.style.display = 'none';
        });
        
        document.getElementById('create-group').addEventListener('click', async () => {
            const name = document.getElementById('group-name').value.trim();
            const description = document.getElementById('group-description').value.trim();
            
            if (!name) {
                alert('Group name is required');
                return;
            }
            
            // Get selected members
            const memberCheckboxes = document.querySelectorAll('#group-members-selector input[type="checkbox"]:checked');
            const members = Array.from(memberCheckboxes).map(cb => parseInt(cb.value));
            
            const result = await createGroup(name, description, members);
            if (result.error) {
                alert(result.error);
            } else {
                newGroupModal.style.display = 'none';
                document.getElementById('group-name').value = '';
                document.getElementById('group-description').value = '';
                renderChatList(currentFilter);
                
                // Open the new group chat
                const chats = await fetchChats();
                const newGroup = chats.find(c => c.type === 'group' && c.group_id == result.group_id);
                if (newGroup) {
                    renderChat(newGroup);
                }
            }
        });
        
        // Edit group functionality
        document.getElementById('close-edit-group-modal').addEventListener('click', () => {
            editGroupModal.style.display = 'none';
        });
        
        document.getElementById('cancel-edit-group').addEventListener('click', () => {
            editGroupModal.style.display = 'none';
        });
        
        document.getElementById('save-group').addEventListener('click', async () => {
            if (!currentGroup) return;
            
            const name = document.getElementById('edit-group-name').value.trim();
            const description = document.getElementById('edit-group-description').value.trim();
            
            if (!name) {
                alert('Group name is required');
                return;
            }
            
            // Update group info
            const updateResult = await updateGroup(currentGroup.group_id, name, description);
            if (updateResult.error) {
                alert(updateResult.error);
                return;
            }
            
            // Add new members
            const memberCheckboxes = document.querySelectorAll('#edit-group-members-selector input[type="checkbox"]:checked');
            const newMembers = Array.from(memberCheckboxes).map(cb => parseInt(cb.value));
            
            if (newMembers.length > 0) {
                const addResult = await addGroupMembers(currentGroup.group_id, newMembers);
                if (addResult.error) {
                    alert(addResult.error);
                    return;
                }
            }
            
            editGroupModal.style.display = 'none';
            renderChatList(currentFilter);
            
            // Refresh current chat if it's this group
            if (currentChat && currentChat.type === 'group' && currentChat.group_id == currentGroup.group_id) {
                const chats = await fetchChats();
                const updatedGroup = chats.find(c => c.type === 'group' && c.group_id == currentGroup.group_id);
                if (updatedGroup) {
                    renderChat(updatedGroup);
                }
            }
        });
        
        // Contacts button functionality
        document.getElementById('contacts-btn').addEventListener('click', () => {
            renderContactsList();
            contactsModal.style.display = 'flex';
        });
        
        document.getElementById('close-contacts-modal').addEventListener('click', () => {
            contactsModal.style.display = 'none';
        });
        
        // Search functionality
        document.getElementById('search-chats').addEventListener('input', async (e) => {
            const query = e.target.value.trim();
            if (query.length < 2) {
                renderChatList(currentFilter);
                return;
            }
            
            const results = await searchMessages(query);
            const container = currentFilter === 'private' ? privateChatList : 
                            currentFilter === 'group' ? groupChatList : chatList;
            
            if (results.length === 0) {
                container.innerHTML = '<div style="padding: 20px; text-align: center; color: var(--light-text);">No results found</div>';
                return;
            }
            
            container.innerHTML = '';
            
            results.forEach(result => {
                const resultElement = document.createElement('div');
                resultElement.className = 'chat-item';
                resultElement.innerHTML = `
                    <div class="chat-avatar">
                        <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(result.chat_name)}&background=random" alt="${result.chat_name}">
                    </div>
                    <div class="chat-info">
                        <div class="chat-name">
                            ${result.chat_name}
                            <span class="chat-time">${formatTime(result.sent_at)}</span>
                        </div>
                        <div class="chat-preview">
                            ${truncate(result.content, 50)}
                        </div>
                    </div>
                `;
                
                resultElement.addEventListener('click', () => {
                    // Open the chat and highlight the message
                    const chat = {
                        type: result.type,
                        [result.type === 'private' ? 'conversation_id' : 'group_id']: 
                            result.type === 'private' ? result.conversation_id : result.group_id,
                        name: result.chat_name
                    };
                    
                    renderChat(chat).then(() => {
                        // Scroll to the message (would need to implement message highlighting)
                        const messageElement = document.querySelector(`.message[data-message-id="${result.message_id}"]`);
                        if (messageElement) {
                            messageElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            messageElement.style.backgroundColor = '#fff9e6';
                            setTimeout(() => {
                                messageElement.style.backgroundColor = '';
                            }, 2000);
                        }
                    });
                });
                
                container.appendChild(resultElement);
            });
        });
        
        // Initialize with all chats
        renderChatList();
        
        // Initialize WebSocket
        initWebSocket();
    </script>
</body>
</html>