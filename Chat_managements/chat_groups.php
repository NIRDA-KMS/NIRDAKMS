<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Application - Chat Types</title>
    <style>
        /* Internal CSS */
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
        }
        
        .chat-avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
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
        
        /* Responsive adjustments */
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
            <!-- Chat items will be populated by JavaScript -->
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
        // Sample data for chats
        const chats = [
            {
                id: 1,
                name: "John Doe",
                type: "private",
                avatar: "https://randomuser.me/api/portraits/men/1.jpg",
                online: true,
                starred: false,
                lastMessage: "Hey, how are you doing?",
                lastMessageTime: "10:30 AM",
                messages: [
                    {
                        id: 1,
                        sender: "John Doe",
                        content: "Hey there!",
                        time: "10:20 AM",
                        status: "read"
                    },
                    {
                        id: 2,
                        sender: "You",
                        content: "Hi John!",
                        time: "10:25 AM",
                        status: "read"
                    },
                    {
                        id: 3,
                        sender: "John Doe",
                        content: "Hey, how are you doing?",
                        time: "10:30 AM",
                        status: "delivered"
                    }
                ]
            },
            {
                id: 2,
                name: "Sarah Smith",
                type: "private",
                avatar: "https://randomuser.me/api/portraits/women/2.jpg",
                online: false,
                starred: true,
                lastMessage: "The meeting is at 2 PM",
                lastMessageTime: "Yesterday",
                messages: [
                    {
                        id: 1,
                        sender: "Sarah Smith",
                        content: "Don't forget about our meeting tomorrow",
                        time: "Yesterday, 5:30 PM",
                        status: "read"
                    },
                    {
                        id: 2,
                        sender: "You",
                        content: "What time is it?",
                        time: "Yesterday, 5:35 PM",
                        status: "read"
                    },
                    {
                        id: 3,
                        sender: "Sarah Smith",
                        content: "The meeting is at 2 PM",
                        time: "Yesterday, 5:36 PM",
                        status: "read"
                    }
                ]
            },
            {
                id: 3,
                name: "Team Project",
                type: "group",
                avatar: "https://randomuser.me/api/portraits/lego/3.jpg",
                online: false,
                starred: false,
                lastMessage: "Alice: I've finished the design",
                lastMessageTime: "Monday",
                members: ["You", "Alice", "Bob", "Charlie"],
                messages: [
                    {
                        id: 1,
                        sender: "Bob",
                        content: "How's everyone doing with their tasks?",
                        time: "Monday, 9:00 AM",
                        status: "read"
                    },
                    {
                        id: 2,
                        sender: "You",
                        content: "I'm almost done with the backend",
                        time: "Monday, 9:15 AM",
                        status: "read"
                    },
                    {
                        id: 3,
                        sender: "Alice",
                        content: "I've finished the design",
                        time: "Monday, 9:30 AM",
                        status: "read"
                    }
                ]
            },
            {
                id: 4,
                name: "Family Group",
                type: "group",
                avatar: "https://randomuser.me/api/portraits/lego/4.jpg",
                online: false,
                starred: true,
                lastMessage: "Mom: Don't forget Sunday dinner",
                lastMessageTime: "Sunday",
                members: ["You", "Mom", "Dad", "Sister"],
                messages: [
                    {
                        id: 1,
                        sender: "Mom",
                        content: "How is everyone?",
                        time: "Sunday, 12:00 PM",
                        status: "read"
                    },
                    {
                        id: 2,
                        sender: "You",
                        content: "I'm good!",
                        time: "Sunday, 12:05 PM",
                        status: "read"
                    },
                    {
                        id: 3,
                        sender: "Mom",
                        content: "Don't forget Sunday dinner",
                        time: "Sunday, 12:10 PM",
                        status: "read"
                    }
                ]
            }
        ];
        
        // DOM elements
        const chatList = document.getElementById('chat-list');
        const mainChat = document.getElementById('main-chat');
        const chatTypes = document.querySelectorAll('.chat-type');
        
        // Current active chat type filter
        let currentFilter = 'all';
        
        // Render chat list based on filter
        function renderChatList(filter = 'all') {
            chatList.innerHTML = '';
            
            const filteredChats = chats.filter(chat => {
                if (filter === 'all') return true;
                if (filter === 'starred') return chat.starred;
                return chat.type === filter;
            });
            
            if (filteredChats.length === 0) {
                chatList.innerHTML = '<div style="padding: 20px; text-align: center; color: var(--light-text);">No chats found</div>';
                return;
            }
            
            filteredChats.forEach(chat => {
                const chatItem = document.createElement('div');
                chatItem.className = 'chat-item';
                chatItem.dataset.id = chat.id;
                
                chatItem.innerHTML = `
                    <div class="chat-avatar">
                        <img src="${chat.avatar}" alt="${chat.name}">
                        ${chat.online ? '<div class="online-status"></div>' : ''}
                    </div>
                    <div class="chat-info">
                        <div class="chat-name">
                            ${chat.name}
                            <span class="chat-time">${chat.lastMessageTime}</span>
                        </div>
                        <div class="chat-preview">
                            ${chat.lastMessage}
                            ${chat.starred ? '<span class="star-icon">★</span>' : ''}
                        </div>
                    </div>
                `;
                
                chatItem.addEventListener('click', () => renderChat(chat.id));
                chatList.appendChild(chatItem);
            });
        }
        
        // Render a specific chat
        function renderChat(chatId) {
            const chat = chats.find(c => c.id == chatId);
            if (!chat) return;
            
            // Highlight selected chat in list
            document.querySelectorAll('.chat-item').forEach(item => {
                item.classList.remove('active');
                if (item.dataset.id == chatId) item.classList.add('active');
            });
            
            // Render chat header
            let headerHTML = `
                <div class="chat-avatar">
                    <img src="${chat.avatar}" alt="${chat.name}">
                    ${chat.online ? '<div class="online-status"></div>' : ''}
                </div>
                <div class="chat-header-info">
                    <div class="chat-header-name">${chat.name}</div>
                    <div class="chat-header-status">
                        ${chat.online ? 'Online' : (chat.type === 'private' ? 'Last seen today at 12:45 PM' : `${chat.members.length} members`)}
                    </div>
                </div>
            `;
            
            // Render messages
            let messagesHTML = '';
            chat.messages.forEach(message => {
                const isSent = message.sender === 'You';
                messagesHTML += `
                    <div class="message ${isSent ? 'sent' : 'received'}">
                        ${!isSent && chat.type === 'group' ? '<div style="font-size: 12px; margin-bottom: 2px;">' + message.sender + '</div>' : ''}
                        <div class="message-content">${message.content}</div>
                        <div class="message-time">
                            ${message.time}
                            ${isSent ? '<span class="message-status">' + (message.status === 'read' ? '✓✓' : '✓') + '</span>' : ''}
                        </div>
                    </div>
                `;
            });
            
            // Add typing indicator if online
            if (chat.online && chat.type === 'private') {
                messagesHTML += '<div class="typing-indicator">' + chat.name + ' is typing...</div>';
            }
            
            // Render input area
            let inputHTML = `
                <input type="text" placeholder="Type a message...">
                <button class="send-button">➤</button>
            `;
            
            // Update main chat area
            mainChat.innerHTML = `
                <div class="chat-header">
                    ${headerHTML}
                </div>
                <div class="chat-messages">
                    ${messagesHTML}
                </div>
                <div class="chat-input">
                    ${inputHTML}
                </div>
            `;
            
            // Scroll to bottom of messages
            const messagesContainer = mainChat.querySelector('.chat-messages');
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
            
            // Add event listener for sending messages
            const sendButton = mainChat.querySelector('.send-button');
            const inputField = mainChat.querySelector('input');
            
            sendButton.addEventListener('click', () => {
                const messageContent = inputField.value.trim();
                if (messageContent) {
                    // In a real app, this would be sent to the server via AJAX
                    const newMessage = {
                        id: chat.messages.length + 1,
                        sender: 'You',
                        content: messageContent,
                        time: new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}),
                        status: 'sent'
                    };
                    
                    chat.messages.push(newMessage);
                    chat.lastMessage = messageContent;
                    chat.lastMessageTime = 'Just now';
                    
                    // Update both the chat list and the main chat
                    renderChatList(currentFilter);
                    renderChat(chatId);
                    
                    inputField.value = '';
                }
            });
            
            // Also send on Enter key
            inputField.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    sendButton.click();
                }
            });
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
        
        // Initialize with all chats
        renderChatList();
        
        // New chat button functionality
        document.getElementById('new-chat-btn').addEventListener('click', () => {
            alert('In a real app, this would open a new chat dialog');
        });
    </script>
</body>
</html>