<?php
// Database connection
$host = '127.0.0.1:3307';   // Database server
$user = 'root';          // Database username
$pass = '';              // Database password
$dbname = 'NIRDAKMS';      // Database name

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
                $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
                $stmt->execute([$_GET['user_id']]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                echo json_encode($user ?: ['error' => 'User not found']);
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
                           m.content as last_message
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
                    WHERE c.user1_id = ? OR c.user2_id = ?
                    ORDER BY c.last_message_at DESC
                ");
                $stmt->execute([$_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id']]);
                $privateChats = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Get group chats
                $stmt = $pdo->prepare("
                    SELECT g.group_id, g.group_name as name, g.description, 
                           'group' as type, g.created_at, g.updated_at,
                           m.content as last_message,
                           (SELECT COUNT(*) FROM group_members WHERE group_id = g.group_id) as member_count
                    FROM chat_groups g
                    JOIN group_members gm ON g.group_id = gm.group_id
                    LEFT JOIN messages m ON (
                        m.message_id = (
                            SELECT message_id FROM messages 
                            WHERE (group_id = g.group_id) 
                            ORDER BY sent_at DESC LIMIT 1
                        )
                    )
                    WHERE gm.user_id = ? AND g.is_active = 1
                    ORDER BY g.updated_at DESC
                ");
                $stmt->execute([$_SESSION['user_id']]);
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
                        SELECT m.*, u.full_name as sender_name
                        FROM messages m
                        JOIN users u ON m.sender_id = u.user_id
                        WHERE m.conversation_id = ?
                        ORDER BY m.sent_at ASC
                    ");
                    $stmt->execute([$_GET['conversation_id']]);
                } elseif (isset($_GET['group_id'])) {
                    // Group messages
                    $stmt = $pdo->prepare("
                        SELECT m.*, u.full_name as sender_name
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
                    
                    $pdo->commit();
                    echo json_encode(['success' => true, 'group_id' => $groupId]);
                } catch (Exception $e) {
                    $pdo->rollBack();
                    echo json_encode(['error' => 'Failed to create group: ' . $e->getMessage()]);
                }
                break;
                
            default:
                echo json_encode(['error' => 'Invalid action']);
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}

// Frontend HTML/JS
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat_types Application</title>
    <style>
        /* Your existing CSS styles */
        :root {
            --primary-color: #0084ff;
            --secondary-color: #f0f2f5;
            --text-color: #050505;
            --light-text: #65676b;
            --border-color: #dddfe2;
            --online-color: #31a24c;
            --star-color: #ffc107;
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
        }
        
        .online-status {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: var(--online-color);
            border: 2px solid white;
        }
        
        .chat-info {
            flex: 1;
        }
        
        .chat-name {
            font-weight: 600;
            display: flex;
            justify-content: space-between;
        }
        
        .chat-time {
            font-size: 12px;
            color: var(--light-text);
        }
        
        .chat-preview {
            font-size: 14px;
            color: var(--light-text);
            display: flex;
            justify-content: space-between;
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
        }
        
        .chat-header-info {
            margin-left: 10px;
            flex: 1;
        }
        
        .chat-header-name {
            font-weight: 600;
        }
        
        .chat-header-status {
            font-size: 13px;
            color: var(--light-text);
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
        }
        
        .message-status {
            margin-left: 5px;
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
    </style>
</head>
<body>
    <!-- Sidebar with chat list -->
    <div class="sidebar">
        <div class="header">
            <h2>Messages</h2>
            <button id="new-chat-btn">+ New Group</button>
            <button id="new-private-chat-btn">+ New Private Chat</button>
        </div>
        
        <div class="search-bar">
            <input type="text" placeholder="Search messages...">
        </div>
        
        <div class="chat-types">
            <div class="chat-type active" data-type="all">All</div>
            <div class="chat-type" data-type="private">Private</div>
            <div class="chat-type" data-type="group">Group</div>
        </div>
        
        <div class="chat-list" id="chat-list">
            <!-- Chat items will be populated by JavaScript -->
            <div class="loading">Loading chats...</div>
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
    <div id="new-group-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; justify-content:center; align-items:center;">
        <div style="background:white; padding:20px; border-radius:8px; width:400px; max-width:90%;">
            <h2>Create New Group</h2>
            <div style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:5px;">Group Name</label>
                <input type="text" id="group-name" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px;">
            </div>
            <div style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:5px;">Description (Optional)</label>
                <textarea id="group-description" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px; height:80px;"></textarea>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button id="cancel-group" style="padding:8px 15px; background:#ddd; border:none; border-radius:4px;">Cancel</button>
                <button id="create-group" style="padding:8px 15px; background:#0084ff; color:white; border:none; border-radius:4px;">Create</button>
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
        const mainChat = document.getElementById('main-chat');
        const chatTypes = document.querySelectorAll('.chat-type');
        const newGroupModal = document.getElementById('new-group-modal');
        
        // Current active chat and filter
        let currentChat = null;
        let currentFilter = 'all';
        
        // Fetch chats from server
        async function fetchChats() {
            try {
                const response = await fetch('chat_groups.php?action=get_chats');
                const chats = await response.json();
                
                if (chats.error) {
                    throw new Error(chats.error);
                }
                
                return chats;
            } catch (error) {
                console.error('Error fetching chats:', error);
                chatList.innerHTML = `<div class="error">${error.message}</div>`;
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
                
                return result;
            } catch (error) {
                console.error('Error sending message:', error);
                return { error: error.message };
            }
        }
        
        // Create a new group
        async function createGroup(name, description) {
            try {
                const formData = new FormData();
                formData.append('group_name', name);
                formData.append('description', description);
                
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


        // Add to your existing script
document.getElementById('new-private-chat-btn').addEventListener('click', async () => {
    const username = prompt("Enter username to chat with:");
    if (username) {
        try {
            const response = await fetch('chat_groups.php?action=start_private_chat', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username })
            });
            const result = await response.json();
            
            if (result.error) throw new Error(result.error);
            renderChatList(); // Refresh chat list
        } catch (error) {
            alert(error.message);
        }
    }
});
        
        // Render chat list
        async function renderChatList(filter = 'all') {
            chatList.innerHTML = '<div class="loading">Loading chats...</div>';
            
            try {
                const chats = await fetchChats();
                
                if (chats.length === 0) {
                    chatList.innerHTML = '<div style="padding: 20px; text-align: center; color: var(--light-text);">No chats found</div>';
                    return;
                }
                
                const filteredChats = chats.filter(chat => {
                    if (filter === 'all') return true;
                    return chat.type === filter;
                });
                
                chatList.innerHTML = '';
                
                filteredChats.forEach(chat => {
                    const chatItem = document.createElement('div');
                    chatItem.className = 'chat-item';
                    chatItem.dataset.id = chat.type === 'private' ? `private_${chat.conversation_id}` : `group_${chat.group_id}`;
                    
                    // Use default avatar if none provided
                    const avatar = `https://ui-avatars.com/api/?name=${encodeURIComponent(chat.name)}&background=random`;
                    
                    chatItem.innerHTML = `
                        <div class="chat-avatar">
                            <img src="${avatar}" alt="${chat.name}">
                        </div>
                        <div class="chat-info">
                            <div class="chat-name">
                                ${chat.name}
                                <span class="chat-time">${formatTime(chat.last_message_at || chat.updated_at || chat.created_at)}</span>
                            </div>
                            <div class="chat-preview">
                                ${chat.last_message ? truncate(chat.last_message, 30) : 'No messages yet'}
                                ${chat.type === 'group' ? `<small>(${chat.member_count || 0} members)</small>` : ''}
                            </div>
                        </div>
                    `;
                    
                    chatItem.addEventListener('click', () => renderChat(chat));
                    chatList.appendChild(chatItem);
                });
            } catch (error) {
                chatList.innerHTML = `<div class="error">${error.message}</div>`;
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
            
            let headerHTML = `
                <div class="chat-avatar">
                    <img src="${avatar}" alt="${chat.name}">
                </div>
                <div class="chat-header-info">
                    <div class="chat-header-name">${chat.name}</div>
                    <div class="chat-header-status">
                        ${chat.type === 'private' ? chat.email : `${chat.member_count || 0} members`}
                    </div>
                </div>
            `;
            
            // Render messages
            let messagesHTML = '';
            messages.forEach(message => {
                const isSent = message.sender_id == currentUserId;
                const senderName = isSent ? 'You' : (message.sender_name || 'Unknown');
                
                messagesHTML += `
                    <div class="message ${isSent ? 'sent' : 'received'}">
                        ${!isSent && chat.type === 'group' ? `<div style="font-size: 12px; margin-bottom: 2px;">${senderName}</div>` : ''}
                        <div class="message-content">${message.content}</div>
                        <div class="message-time">
                            ${formatTime(message.sent_at)}
                            ${isSent ? `<span class="message-status">${message.status === 'read' ? '✓✓' : '✓'}</span>` : ''}
                        </div>
                    </div>
                `;
            });
            
            // Render input area
            let inputHTML = `
                <input type="text" placeholder="Type a message..." id="message-input">
                <button class="send-button" id="send-button">➤</button>
            `;
            
            // Update main chat area
            mainChat.innerHTML = `
                <div class="chat-header">
                    ${headerHTML}
                </div>
                <div class="chat-messages" id="messages-container">
                    ${messagesHTML}
                </div>
                <div class="chat-input">
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
            
            inputField.focus();
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
                currentFilter = type.dataset.type;
                renderChatList(currentFilter);
            });
        });
        
        // New group button functionality
        document.getElementById('new-chat-btn').addEventListener('click', () => {
            newGroupModal.style.display = 'flex';
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
            
            const result = await createGroup(name, description);
            if (result.error) {
                alert(result.error);
            } else {
                newGroupModal.style.display = 'none';
                document.getElementById('group-name').value = '';
                document.getElementById('group-description').value = '';
                renderChatList(currentFilter);
            }
        });
        
        // Initialize with all chats
        renderChatList();
    </script>
</body>
</html>