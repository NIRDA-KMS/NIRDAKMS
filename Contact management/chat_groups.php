<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Management - Messaging</title>
    <style>
        /* Base Styles */
        :root {
            --primary-color: #1a237e;
            --secondary-color: #2c3e50;
            --accent-color: #00A0DF;
            --background-color: #ecf0f1;
            --text-color: #34495e;
            --success-color: #2ecc71;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --light-gray: #f5f7fa;
            --dark-gray: #7f8c8d;
            --online-color: #2ecc71;
            --offline-color: #95a5a6;
            --away-color: #f39c12;
            --dnd-color: #e74c3c;
            --chat-bg: #f5f5f5;
            --message-bg: #ffffff;
            --received-bg: #e3f2fd;
            --sent-bg: #00A0DF;
            --sent-text: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Roboto', sans-serif;
        }

        body {
            background-color: var(--light-gray);
            color: var(--text-color);
            padding-top: 130px;
            min-height: 100vh;
        }

        .container {
            margin: 20px auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-top: 100px;
            padding-top: 50px;
            padding-left: 25px;
            display: grid;
            margin-bottom: 100px;
            grid-template-columns: 300px 1fr;
            gap: 20px;
        }

        /* Sidebar Styles */
        .contacts-sidebar {
            border-right: 1px solid #eee;
            padding-right: 20px;
            height: calc(100vh - 170px);
            overflow-y: auto;
            position: relative;
        }

        /* Chat Types Navigation */
        .chat-types {
            display: flex;
            border-bottom: 1px solid #eee;
            margin-bottom: 15px;
        }

        .chat-type-tab {
            padding: 10px 15px;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            transition: all 0.2s;
            font-weight: 500;
            color: var(--dark-gray);
        }

        .chat-type-tab.active {
            color: var(--accent-color);
            border-bottom-color: var(--accent-color);
        }

        .chat-type-tab:hover:not(.active) {
            color: var(--primary-color);
        }

        /* Chat List Styles */
        .chat-list {
            list-style: none;
        }

        .chat-item {
            display: flex;
            align-items: center;
            padding: 12px 10px;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.2s;
            position: relative;
        }

        .chat-item:hover {
            background-color: rgba(0, 160, 223, 0.05);
        }

        .chat-item.active {
            background-color: rgba(0, 160, 223, 0.1);
        }

        .chat-item.starred::before {
            content: "★";
            position: absolute;
            left: 5px;
            top: 10px;
            color: var(--warning-color);
            font-size: 12px;
        }

        .chat-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--background-color);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            position: relative;
        }

        .chat-info {
            flex-grow: 1;
            min-width: 0;
        }

        .chat-name {
            font-weight: 500;
            margin-bottom: 3px;
            display: flex;
            justify-content: space-between;
        }

        .chat-preview {
            font-size: 12px;
            color: var(--dark-gray);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .chat-time {
            font-size: 11px;
            color: var(--dark-gray);
        }

        .unread-count {
            background-color: var(--accent-color);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            margin-left: 10px;
        }

        /* Chat Area Styles */
        .chat-area {
            display: flex;
            flex-direction: column;
            height: calc(100vh - 170px);
            background-color: var(--chat-bg);
            border-radius: 8px;
            overflow: hidden;
        }

        .chat-header {
            padding: 15px;
            background-color: white;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
        }

        .chat-title {
            font-weight: 500;
            font-size: 18px;
            margin-left: 10px;
        }

        .chat-messages {
            flex-grow: 1;
            padding: 15px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        .message {
            max-width: 70%;
            margin-bottom: 15px;
            padding: 10px 15px;
            border-radius: 18px;
            position: relative;
            word-wrap: break-word;
        }

        .received {
            align-self: flex-start;
            background-color: var(--received-bg);
            border-top-left-radius: 4px;
        }

        .sent {
            align-self: flex-end;
            background-color: var(--sent-bg);
            color: var(--sent-text);
            border-top-right-radius: 4px;
        }

        .message-time {
            font-size: 11px;
            color: rgba(255,255,255,0.7);
            text-align: right;
            margin-top: 5px;
        }

        .received .message-time {
            color: rgba(0,0,0,0.5);
        }

        .chat-input-area {
            padding: 15px;
            background-color: white;
            border-top: 1px solid #eee;
            display: flex;
            align-items: center;
        }

        .chat-input {
            flex-grow: 1;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 20px;
            resize: none;
            outline: none;
            font-size: 14px;
            max-height: 120px;
        }

        .send-button {
            background-color: var(--accent-color);
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            margin-left: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Group Chat Specific Styles */
        .group-avatar {
            display: flex;
            flex-wrap: wrap;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
            margin-right: 12px;
        }

        .group-avatar span {
            width: 50%;
            height: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            background-color: var(--background-color);
        }

        /* Empty State */
        .empty-chat {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: var(--dark-gray);
            text-align: center;
        }

        .empty-chat i {
            font-size: 50px;
            margin-bottom: 20px;
            color: var(--background-color);
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .container {
                grid-template-columns: 1fr;
                padding: 10px;
            }

            .contacts-sidebar {
                height: auto;
                border-right: none;
                padding-right: 0;
                border-bottom: 1px solid #eee;
                padding-bottom: 20px;
            }

            .chat-area {
                height: auto;
                min-height: 300px;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include('../Internees_task/header.php') ?>
    
    <div class="container">
        <aside class="contacts-sidebar">
            <!-- Chat Types Navigation -->
            <div class="chat-types">
                <div class="chat-type-tab active" data-type="private">Private</div>
                <div class="chat-type-tab" data-type="group">Group</div>
                <div class="chat-type-tab" data-type="starred">Starred</div>
            </div>
            
            <!-- Chat List -->
            <ul class="chat-list" id="chatList">
                <!-- Chats will be loaded here based on type -->
            </ul>
        </aside>
        
        <main class="chat-area">
            <div class="empty-chat" id="emptyChat">
                <i class="fas fa-comments"></i>
                <h3>No chat selected</h3>
                <p>Select a chat from the list to start messaging</p>
            </div>
            
            <div id="activeChat" style="display: none;">
                <div class="chat-header">
                    <div class="chat-avatar" id="currentChatAvatar">
                        <span>JD</span>
                    </div>
                    <div class="chat-title" id="currentChatTitle">John Doe</div>
                </div>
                
                <div class="chat-messages" id="chatMessages">
                    <!-- Messages will be loaded here -->
                </div>
                
                <div class="chat-input-area">
                    <textarea class="chat-input" id="messageInput" placeholder="Type a message..." rows="1"></textarea>
                    <button class="send-button" id="sendButton">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </main>
    </div>

    <!-- Create Group Modal -->
    <div class="modal" id="createGroupModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Create New Group</h3>
                <button class="close-modal" id="closeGroupModal">&times;</button>
            </div>
            <form id="groupForm">
                <div class="form-group">
                    <label for="groupName">Group Name</label>
                    <input type="text" id="groupName" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Select Members</label>
                    <div id="groupMembersList" style="max-height: 200px; overflow-y: auto;">
                        <!-- Members will be listed here -->
                    </div>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" id="cancelGroup">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Group</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Sample data for different chat types
        const privateChats = [
            {
                id: 'p1',
                type: 'private',
                name: 'John Doe',
                avatar: 'JD',
                lastMessage: 'Hey, how are you doing?',
                time: '10:30 AM',
                unread: 2,
                starred: true,
                messages: [
                    { id: 'm1', sender: 'p1', text: 'Hi there!', time: '10:20 AM' },
                    { id: 'm2', sender: 'me', text: 'Hello! How are you?', time: '10:22 AM' },
                    { id: 'm3', sender: 'p1', text: 'Hey, how are you doing?', time: '10:30 AM' }
                ]
            },
            {
                id: 'p2',
                type: 'private',
                name: 'Jane Smith',
                avatar: 'JS',
                lastMessage: 'The meeting is at 2pm',
                time: '9:15 AM',
                unread: 0,
                starred: false,
                messages: [
                    { id: 'm1', sender: 'p2', text: 'Don\'t forget about our meeting', time: '9:10 AM' },
                    { id: 'm2', sender: 'me', text: 'What time was it again?', time: '9:12 AM' },
                    { id: 'm3', sender: 'p2', text: 'The meeting is at 2pm', time: '9:15 AM' }
                ]
            }
        ];

        const groupChats = [
            {
                id: 'g1',
                type: 'group',
                name: 'Project Team',
                members: ['JD', 'JS', 'MB'],
                lastMessage: 'Alice: I\'ll send the files shortly',
                time: 'Yesterday',
                unread: 5,
                starred: true,
                messages: [
                    { id: 'm1', sender: 'JD', text: 'Has everyone reviewed the proposal?', time: 'Yesterday, 4:30 PM' },
                    { id: 'm2', sender: 'JS', text: 'Yes, looks good to me', time: 'Yesterday, 4:45 PM' },
                    { id: 'm3', sender: 'MB', text: 'I\'ll send the files shortly', time: 'Yesterday, 5:20 PM' }
                ]
            },
            {
                id: 'g2',
                type: 'group',
                name: 'Family Group',
                members: ['DW', 'ET', 'CA'],
                lastMessage: 'Mom: Call me when you can',
                time: 'Monday',
                unread: 0,
                starred: false,
                messages: [
                    { id: 'm1', sender: 'DW', text: 'How is everyone doing?', time: 'Monday, 10:00 AM' },
                    { id: 'm2', sender: 'ET', text: 'All good here!', time: 'Monday, 10:15 AM' },
                    { id: 'm3', sender: 'CA', text: 'Call me when you can', time: 'Monday, 11:30 AM' }
                ]
            }
        ];

        // DOM Elements
        const chatList = document.getElementById('chatList');
        const emptyChat = document.getElementById('emptyChat');
        const activeChat = document.getElementById('activeChat');
        const chatMessages = document.getElementById('chatMessages');
        const messageInput = document.getElementById('messageInput');
        const sendButton = document.getElementById('sendButton');
        const currentChatAvatar = document.getElementById('currentChatAvatar');
        const currentChatTitle = document.getElementById('currentChatTitle');
        const chatTypeTabs = document.querySelectorAll('.chat-type-tab');
        const createGroupModal = document.getElementById('createGroupModal');
        const closeGroupModal = document.getElementById('closeGroupModal');
        const cancelGroup = document.getElementById('cancelGroup');
        const groupForm = document.getElementById('groupForm');
        const groupMembersList = document.getElementById('groupMembersList');

        // State variables
        let currentChatType = 'private';
        let currentChatId = null;
        let contacts = [
            { id: 'p1', name: 'John Doe', avatar: 'JD' },
            { id: 'p2', name: 'Jane Smith', avatar: 'JS' },
            { id: 'p3', name: 'Mike Brown', avatar: 'MB' },
            { id: 'p4', name: 'David Wilson', avatar: 'DW' },
            { id: 'p5', name: 'Emily Taylor', avatar: 'ET' },
            { id: 'p6', name: 'Chris Anderson', avatar: 'CA' }
        ];

        // Initialize the app
        document.addEventListener('DOMContentLoaded', function() {
            loadChats(currentChatType);
            setupEventListeners();
            
            // Simulate encryption setup
            initializeEncryption();
        });

        // Load chats based on type
        function loadChats(type) {
            chatList.innerHTML = '';
            currentChatType = type;
            
            let chats = [];
            if (type === 'private') {
                chats = privateChats;
            } else if (type === 'group') {
                chats = groupChats;
            } else if (type === 'starred') {
                chats = [...privateChats, ...groupChats].filter(chat => chat.starred);
            }
            
            if (chats.length === 0) {
                chatList.innerHTML = '<li style="padding:15px; text-align:center; color:var(--dark-gray);">No chats found</li>';
                return;
            }
            
            chats.forEach(chat => {
                const chatItem = document.createElement('li');
                chatItem.className = `chat-item ${chat.id === currentChatId ? 'active' : ''} ${chat.starred ? 'starred' : ''}`;
                chatItem.dataset.id = chat.id;
                chatItem.dataset.type = chat.type;
                
                let avatarHtml = '';
                if (chat.type === 'private') {
                    avatarHtml = `<div class="chat-avatar"><span>${chat.avatar}</span></div>`;
                } else {
                    // Group avatar shows first 4 members
                    avatarHtml = `<div class="group-avatar">`;
                    chat.members.slice(0, 4).forEach(member => {
                        avatarHtml += `<span>${member}</span>`;
                    });
                    avatarHtml += `</div>`;
                }
                
                chatItem.innerHTML = `
                    ${avatarHtml}
                    <div class="chat-info">
                        <div class="chat-name">
                            ${chat.name}
                            <span class="chat-time">${chat.time}</span>
                        </div>
                        <div class="chat-preview">${chat.lastMessage}</div>
                    </div>
                    ${chat.unread > 0 ? `<div class="unread-count">${chat.unread}</div>` : ''}
                `;
                
                chatList.appendChild(chatItem);
            });
            
            // Add click event to each chat item
            document.querySelectorAll('.chat-item').forEach(item => {
                item.addEventListener('click', function() {
                    const chatId = this.dataset.id;
                    const chatType = this.dataset.type;
                    openChat(chatId, chatType);
                });
            });
        }

        // Open a chat
        function openChat(chatId, chatType) {
            const chat = findChat(chatId, chatType);
            if (!chat) return;
            
            currentChatId = chatId;
            
            // Update active state in list
            document.querySelectorAll('.chat-item').forEach(item => {
                item.classList.remove('active');
                if (item.dataset.id === chatId) {
                    item.classList.add('active');
                }
            });
            
            // Update chat header
            if (chat.type === 'private') {
                currentChatAvatar.innerHTML = `<span>${chat.avatar}</span>`;
            } else {
                currentChatAvatar.innerHTML = '';
                chat.members.slice(0, 4).forEach(member => {
                    currentChatAvatar.innerHTML += `<span>${member}</span>`;
                });
            }
            currentChatTitle.textContent = chat.name;
            
            // Load messages
            loadMessages(chat.messages);
            
            // Mark as read
            chat.unread = 0;
            
            // Show chat area
            emptyChat.style.display = 'none';
            activeChat.style.display = 'flex';
            
            // Scroll to bottom
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        // Load messages into chat area
        function loadMessages(messages) {
            chatMessages.innerHTML = '';
            
            messages.forEach(message => {
                const messageDiv = document.createElement('div');
                messageDiv.className = `message ${message.sender === 'me' ? 'sent' : 'received'}`;
                
                messageDiv.innerHTML = `
                    <div class="message-text">${message.text}</div>
                    <div class="message-time">${message.time}</div>
                `;
                
                chatMessages.appendChild(messageDiv);
            });
        }

        // Find chat by ID and type
        function findChat(chatId, chatType) {
            if (chatType === 'private') {
                return privateChats.find(chat => chat.id === chatId);
            } else if (chatType === 'group') {
                return groupChats.find(chat => chat.id === chatId);
            }
            return null;
        }

        // Initialize encryption (simulated)
        function initializeEncryption() {
            console.log("End-to-end encryption initialized");
            // In a real app, this would set up encryption keys
        }

        // Encrypt message (simulated)
        function encryptMessage(message) {
            console.log("Encrypting message:", message);
            return message; // In real app, would return encrypted message
        }

        // Decrypt message (simulated)
        function decryptMessage(message) {
            console.log("Decrypting message:", message);
            return message; // In real app, would return decrypted message
        }

        // Setup event listeners
        function setupEventListeners() {
            // Chat type tabs
            chatTypeTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    chatTypeTabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    loadChats(this.dataset.type);
                });
            });
            
            // Send message
            sendButton.addEventListener('click', sendMessage);
            messageInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });
            
            // Auto-resize textarea
            messageInput.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
            
            // Group modal
            document.getElementById('addGroupBtn')?.addEventListener('click', function() {
                // Populate members list
                groupMembersList.innerHTML = '';
                contacts.forEach(contact => {
                    const memberDiv = document.createElement('div');
                    memberDiv.className = 'form-check';
                    memberDiv.innerHTML = `
                        <input type="checkbox" id="member-${contact.id}" class="form-check-input" value="${contact.id}">
                        <label for="member-${contact.id}" class="form-check-label">
                            <div class="chat-avatar" style="display:inline-flex; width:20px; height:20px; font-size:10px; margin-right:5px;">
                                <span>${contact.avatar}</span>
                            </div>
                            ${contact.name}
                        </label>
                    `;
                    groupMembersList.appendChild(memberDiv);
                });
                
                createGroupModal.style.display = 'flex';
            });
            
            closeGroupModal.addEventListener('click', function() {
                createGroupModal.style.display = 'none';
            });
            
            cancelGroup.addEventListener('click', function() {
                createGroupModal.style.display = 'none';
            });
            
            groupForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const groupName = document.getElementById('groupName').value;
                const selectedMembers = Array.from(document.querySelectorAll('#groupMembersList input:checked'))
                    .map(input => input.value);
                
                if (selectedMembers.length < 2) {
                    alert('Please select at least 2 members for the group');
                    return;
                }
                
                // Create new group (in a real app, this would be an API call)
                const newGroup = {
                    id: 'g' + (groupChats.length + 1),
                    type: 'group',
                    name: groupName,
                    members: selectedMembers.map(id => {
                        const contact = contacts.find(c => c.id === id);
                        return contact.avatar;
                    }),
                    lastMessage: 'Group created',
                    time: 'Just now',
                    unread: 0,
                    starred: false,
                    messages: []
                };
                
                groupChats.push(newGroup);
                createGroupModal.style.display = 'none';
                groupForm.reset();
                
                // Switch to group view and open new chat
                document.querySelector('.chat-type-tab[data-type="group"]').click();
                setTimeout(() => {
                    const newChatItem = document.querySelector(`.chat-item[data-id="${newGroup.id}"]`);
                    if (newChatItem) newChatItem.click();
                }, 100);
                
                console.log('Created new group:', newGroup);
            });
        }

        // Send a new message
        function sendMessage() {
            const messageText = messageInput.value.trim();
            if (!messageText || !currentChatId) return;
            
            // Encrypt message (simulated)
            const encryptedMessage = encryptMessage(messageText);
            
            // Find current chat
            const chat = findChat(currentChatId, currentChatType);
            if (!chat) return;
            
            // Create new message
            const newMessage = {
                id: 'm' + (chat.messages.length + 1),
                sender: 'me',
                text: messageText,
                time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
            };
            
            // Add to chat
            chat.messages.push(newMessage);
            chat.lastMessage = `You: ${messageText}`;
            chat.time = 'Just now';
            
            // Update UI
            loadMessages(chat.messages);
            loadChats(currentChatType);
            
            // Clear input
            messageInput.value = '';
            messageInput.style.height = 'auto';
            
            // Scroll to bottom
            chatMessages.scrollTop = chatMessages.scrollHeight;
            
            // In a real app, would send to server here
            console.log("Message sent (encrypted):", encryptedMessage);
        }

        // Toggle star on a chat
        function toggleStar(chatId, chatType) {
            const chat = findChat(chatId, chatType);
            if (chat) {
                chat.starred = !chat.starred;
                loadChats(currentChatType);
                
                if (chatId === currentChatId) {
                    const chatItem = document.querySelector(`.chat-item[data-id="${chatId}"]`);
                    if (chatItem) {
                        chatItem.classList.toggle('starred', chat.starred);
                    }
                }
            }
        }

        // Make functions available globally
        window.toggleStar = toggleStar;
    </script>
</body>
</html>