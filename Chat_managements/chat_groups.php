<?php
session_start();
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "NIRDAKMS";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;  
}

$userId = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'User';

// Fetch user's private conversations
$privateChats = [];
$privateQuery = "SELECT 
    pc.conversation_id, 
    CASE 
        WHEN pc.user1_id = $userId THEN u2.user_id 
        ELSE u1.user_id 
    END as other_user_id,
    CASE 
        WHEN pc.user1_id = $userId THEN u2.username 
        ELSE u1.username 
    END as other_username,
    CASE 
        WHEN pc.user1_id = $userId THEN u2.full_name 
        ELSE u1.full_name 
    END as other_full_name,
    m.content as last_message,
    m.sent_at as last_message_time,
    us.is_online,
    us.last_seen
FROM private_conversations pc
LEFT JOIN users u1 ON pc.user1_id = u1.user_id
LEFT JOIN users u2 ON pc.user2_id = u2.user_id
LEFT JOIN messages m ON m.message_id = (
    SELECT message_id FROM messages 
    WHERE (conversation_id = pc.conversation_id)
    ORDER BY sent_at DESC LIMIT 1
)
LEFT JOIN user_status us ON us.user_id = CASE 
    WHEN pc.user1_id = $userId THEN pc.user2_id 
    ELSE pc.user1_id 
END
WHERE pc.user1_id = $userId OR pc.user2_id = $userId
ORDER BY m.sent_at DESC";
$privateResult = mysqli_query($conn, $privateQuery);
while ($row = mysqli_fetch_assoc($privateResult)) {
    $privateChats[] = $row;
}

// Fetch user's group chats
$groupChats = [];
$groupQuery = "SELECT 
    g.group_id,
    g.group_name,
    g.description,
    m.content as last_message,
    m.sent_at as last_message_time,
    (SELECT COUNT(*) FROM group_members WHERE group_id = g.group_id) as member_count
FROM group_members gm
JOIN chat_groups g ON gm.group_id = g.group_id
LEFT JOIN messages m ON m.message_id = (
    SELECT message_id FROM messages 
    WHERE (group_id = g.group_id)
    ORDER BY sent_at DESC LIMIT 1
)
WHERE gm.user_id = $userId AND g.is_active = 1
ORDER BY m.sent_at DESC";
$groupResult = mysqli_query($conn, $groupQuery);
while ($row = mysqli_fetch_assoc($groupResult)) {
    $groupChats[] = $row;
}

// Handle new message submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message_content'])) {
    $content = trim($_POST['message_content']);
    $chatType = $_POST['chat_type'];
    $chatId = $_POST['chat_id'];
    
    if (!empty($content)) {
        if ($chatType === 'private') {
            // Find the conversation ID between current user and the other user
            $convQuery = "SELECT conversation_id FROM private_conversations 
                          WHERE (user1_id = $userId AND user2_id = $chatId) 
                          OR (user2_id = $userId AND user1_id = $chatId)";
            $convResult = mysqli_query($conn, $convQuery);
            $convRow = mysqli_fetch_assoc($convResult);
            $conversationId = $convRow['conversation_id'];
            
            // Insert message
            $insertQuery = "INSERT INTO messages (conversation_id, sender_id, content) 
                            VALUES ($conversationId, $userId, '$content')";
            mysqli_query($conn, $insertQuery);
            
            // Update conversation last message time
            $updateQuery = "UPDATE private_conversations 
                            SET last_message_at = NOW() 
                            WHERE conversation_id = $conversationId";
            mysqli_query($conn, $updateQuery);
        } elseif ($chatType === 'group') {
            // Insert message to group
            $insertQuery = "INSERT INTO messages (group_id, sender_id, content) 
                            VALUES ($chatId, $userId, '$content')";
            mysqli_query($conn, $insertQuery);
            
            // Update group last updated time
            $updateQuery = "UPDATE chat_groups 
                            SET updated_at = NOW() 
                            WHERE group_id = $chatId";
            mysqli_query($conn, $updateQuery);
        }
        
        // Return success response
        echo json_encode(['status' => 'success']);
        exit;
    }
}

// Handle fetching messages for a chat
if (isset($_GET['fetch_messages'])) {
    $chatType = $_GET['chat_type'];
    $chatId = $_GET['chat_id'];
    $messages = [];
    
    if ($chatType === 'private') {
        // Find conversation ID
        $convQuery = "SELECT conversation_id FROM private_conversations 
                      WHERE (user1_id = $userId AND user2_id = $chatId) 
                      OR (user2_id = $userId AND user1_id = $chatId)";
        $convResult = mysqli_query($conn, $convQuery);
        $convRow = mysqli_fetch_assoc($convResult);
        $conversationId = $convRow['conversation_id'];
        
        // Fetch messages
        $messageQuery = "SELECT m.*, u.username, u.full_name 
                         FROM messages m
                         JOIN users u ON m.sender_id = u.user_id
                         WHERE m.conversation_id = $conversationId
                         ORDER BY m.sent_at ASC";
        $messageResult = mysqli_query($conn, $messageQuery);
        while ($row = mysqli_fetch_assoc($messageResult)) {
            $messages[] = [
                'id' => $row['message_id'],
                'sender_id' => $row['sender_id'],
                'sender_name' => $row['full_name'],
                'username' => $row['username'],
                'content' => $row['content'],
                'time' => date('h:i A', strtotime($row['sent_at'])),
                'is_sent' => ($row['sender_id'] == $userId)
            ];
        }
    } elseif ($chatType === 'group') {
        // Fetch group messages
        $messageQuery = "SELECT m.*, u.username, u.full_name 
                         FROM messages m
                         JOIN users u ON m.sender_id = u.user_id
                         WHERE m.group_id = $chatId
                         ORDER BY m.sent_at ASC";
        $messageResult = mysqli_query($conn, $messageQuery);
        while ($row = mysqli_fetch_assoc($messageResult)) {
            $messages[] = [
                'id' => $row['message_id'],
                'sender_id' => $row['sender_id'],
                'sender_name' => $row['full_name'],
                'username' => $row['username'],
                'content' => $row['content'],
                'time' => date('h:i A', strtotime($row['sent_at'])),
                'is_sent' => ($row['sender_id'] == $userId)
            ];
        }
    }
    
    // Return messages as JSON
    echo json_encode($messages);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Application - Messages</title>
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
            <button id="new-chat-btn">+ New</button>
        </div>
        
        <div class="search-bar">
            <input type="text" placeholder="Search messages...">
        </div>
        
        <div class="chat-types">
            <div class="chat-type active" data-type="all">All</div>
            <div class="chat-type" data-type="private">Private</div>
            <div class="chat-type" data-type="group">Group</div>
            <div class="chat-type" data-type="starred">Starred</div>
        </div>
        
        <div class="chat-list" id="chat-list">
            <!-- Private chats -->
            <?php foreach ($privateChats as $chat): ?>
                <div class="chat-item" data-type="private" data-id="<?= $chat['other_user_id'] ?>">
                    <div class="chat-avatar">
                        <?= substr($chat['other_full_name'], 0, 1) ?>
                        <?php if ($chat['is_online']): ?>
                            <div class="online-status"></div>
                        <?php endif; ?>
                    </div>
                    <div class="chat-info">
                        <div class="chat-name">
                            <?= htmlspecialchars($chat['other_full_name']) ?>
                            <span class="chat-time">
                                <?= $chat['last_message_time'] ? date('h:i A', strtotime($chat['last_message_time'])) : '' ?>
                            </span>
                        </div>
                        <div class="chat-preview">
                            <?= htmlspecialchars($chat['last_message'] ?? 'No messages yet') ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <!-- Group chats -->
            <?php foreach ($groupChats as $chat): ?>
                <div class="chat-item" data-type="group" data-id="<?= $chat['group_id'] ?>">
                    <div class="chat-avatar group-avatar">
                        <?= substr($chat['group_name'], 0, 1) ?>
                    </div>
                    <div class="chat-info">
                        <div class="chat-name">
                            <?= htmlspecialchars($chat['group_name']) ?>
                            <span class="chat-time">
                                <?= $chat['last_message_time'] ? date('h:i A', strtotime($chat['last_message_time'])) : '' ?>
                            </span>
                        </div>
                        <div class="chat-preview">
                            <?= htmlspecialchars($chat['last_message'] ?? 'No messages yet') ?>
                            <span class="chat-members"><?= $chat['member_count'] ?> members</span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Main chat area -->
    <div class="main-chat" id="main-chat">
        <div class="empty-chat">
            <div class="empty-chat-icon">💬</div>
            <h3>Select a chat to start messaging</h3>
        </div>
    </div>
    
    <script>
        // DOM elements
        const chatList = document.getElementById('chat-list');
        const mainChat = document.getElementById('main-chat');
        const chatTypes = document.querySelectorAll('.chat-type');
        
        // Current active chat
        let currentChat = null;
        
        // Initialize chat type filters
        chatTypes.forEach(type => {
            type.addEventListener('click', () => {
                chatTypes.forEach(t => t.classList.remove('active'));
                type.classList.add('active');
                filterChats(type.dataset.type);
            });
        });
        
        // Filter chats by type
        function filterChats(type) {
            const allChats = document.querySelectorAll('.chat-item');
            
            allChats.forEach(chat => {
                if (type === 'all') {
                    chat.style.display = 'flex';
                } else if (type === 'starred') {
                    // Implement starred functionality if needed
                    chat.style.display = 'none';
                } else {
                    chat.style.display = chat.dataset.type === type ? 'flex' : 'none';
                }
            });
        }
        
        // Handle chat selection
        chatList.addEventListener('click', (e) => {
            const chatItem = e.target.closest('.chat-item');
            if (!chatItem) return;
            
            // Highlight selected chat
            document.querySelectorAll('.chat-item').forEach(item => {
                item.classList.remove('active');
            });
            chatItem.classList.add('active');
            
            // Set current chat
            currentChat = {
                type: chatItem.dataset.type,
                id: chatItem.dataset.id
            };
            
            // Load chat messages
            loadChatMessages(currentChat.type, currentChat.id);
        });
        
        // Load messages for a specific chat
        function loadChatMessages(chatType, chatId) {
            fetch(`chat_groups.php?fetch_messages=1&chat_type=${chatType}&chat_id=${chatId}`)
                .then(response => response.json())
                .then(messages => {
                    renderChat(chatType, chatId, messages);
                })
                .catch(error => {
                    console.error('Error loading messages:', error);
                });
        }
        
        // Render chat with messages
        function renderChat(chatType, chatId, messages) {
            // Get chat info from the clicked item
            const chatItem = document.querySelector(`.chat-item[data-id="${chatId}"]`);
            if (!chatItem) return;
            
            const chatName = chatItem.querySelector('.chat-name').textContent.split('\n')[0].trim();
            const isOnline = chatItem.querySelector('.online-status') !== null;
            const memberCount = chatItem.querySelector('.chat-members')?.textContent || '';
            
            // Render chat header
            let headerHTML = `
                <div class="chat-avatar">
                    ${chatItem.querySelector('.chat-avatar').innerHTML}
                </div>
                <div class="chat-header-info">
                    <div class="chat-header-name">${chatName}</div>
                    <div class="chat-header-status">
                        ${chatType === 'private' ? 
                            (isOnline ? 'Online' : 'Offline') : 
                            memberCount}
                    </div>
                </div>
            `;
            
            // Render messages
            let messagesHTML = '';
            messages.forEach(message => {
                messagesHTML += `
                    <div class="message ${message.is_sent ? 'sent' : 'received'}">
                        ${chatType === 'group' && !message.is_sent ? 
                            `<div style="font-size: 12px; margin-bottom: 2px;">${message.sender_name}</div>` : ''}
                        <div class="message-content">${message.content}</div>
                        <div class="message-time">
                            ${message.time}
                            ${message.is_sent ? '<span class="message-status">✓✓</span>' : ''}
                        </div>
                    </div>
                `;
            });
            
            // Add typing indicator if online
            if (isOnline && chatType === 'private') {
                messagesHTML += `<div class="typing-indicator">${chatName} is typing...</div>`;
            }
            
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
            
            function sendMessage() {
                const messageContent = inputField.value.trim();
                if (messageContent && currentChat) {
                    fetch('chat_groups.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `message_content=${encodeURIComponent(messageContent)}&chat_type=${currentChat.type}&chat_id=${currentChat.id}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            inputField.value = '';
                            loadChatMessages(currentChat.type, currentChat.id);
                        }
                    })
                    .catch(error => {
                        console.error('Error sending message:', error);
                    });
                }
            }
            
            sendButton.addEventListener('click', sendMessage);
            
            inputField.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    sendMessage();
                }
            });
        }
        
        // New chat button functionality
        document.getElementById('new-chat-btn').addEventListener('click', () => {
            alert('New chat functionality would be implemented here');
        });
    </script>
</body>
</html>