<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Application - Manage Chats</title>
    <style>
        /* Internal CSS - Consistent with chat types page */
        :root {
            --primary-color: #0084ff;
            --secondary-color: #f0f2f5;
            --text-color: #050505;
            --light-text: #65676b;
            --border-color: #dddfe2;
            --online-color: #31a24c;
            --star-color: #ffc107;
            --danger-color: #ff4d4f;
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
        }
        
        .container {
            max-width: 1200px;
            margin-top:90px;
            margin-left:240px;
            padding: 20px;
            margin-bottom: 40px;
            
        } 
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .header h1 {
            font-size: 28px;
            font-weight: 600;
        }
        
        .back-button {
            padding: 8px 15px;
            background-color: var(--secondary-color);
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            align-items: center;
        }
        
        .back-button i {
            margin-right: 5px;
        }
        
        .management-sections {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
        }
        
        .management-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 20px;
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .card-header h2 {
            font-size: 18px;
            font-weight: 600;
        }
        
        .card-header button {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .group-list, .settings-list {
            list-style: none;
        }
        
        .group-item, .setting-item {
            padding: 10px 0;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .group-item:last-child, .setting-item:last-child {
            border-bottom: none;
        }
        
        .group-info {
            display: flex;
            align-items: center;
        }
        
        .group-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            overflow: hidden;
        }
        
        .group-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .group-name {
            font-weight: 500;
        }
        
        .group-members {
            font-size: 12px;
            color: var(--light-text);
        }
        
        .group-actions button {
            background: none;
            border: none;
            cursor: pointer;
            margin-left: 10px;
            color: var(--light-text);
        }
        
        .group-actions button.edit {
            color: var(--primary-color);
        }
        
        .group-actions button.delete {
            color: var(--danger-color);
        }
        
        .setting-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .setting-label {
            font-weight: 500;
        }
        
        .setting-description {
            font-size: 13px;
            color: var(--light-text);
            margin-top: 3px;
        }
        
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }
        
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }
        
        .slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        
        input:checked + .slider {
            background-color: var(--primary-color);
        }
        
        input:checked + .slider:before {
            transform: translateX(26px);
        }
        
        .search-messages {
            margin-bottom: 15px;
        }
        
        .search-messages input {
            width: 100%;
            padding: 8px 15px;
            border-radius: 20px;
            border: 1px solid var(--border-color);
            outline: none;
        }
        
        .message-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .message-actions button {
            padding: 5px 10px;
            border-radius: 5px;
            border: 1px solid var(--border-color);
            background-color: white;
            cursor: pointer;
        }
        
        .message-actions button.delete {
            color: var(--danger-color);
            border-color: var(--danger-color);
        }
        
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .modal-header h3 {
            font-size: 20px;
        }
        
        .close-modal {
            background: none;
            border: none;
            font-size: 24px;
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
            padding: 8px 10px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            outline: none;
        }
        
        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }
        
        .form-actions button {
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .cancel-btn {
            background-color: white;
            border: 1px solid var(--border-color);
        }
        
        .save-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
        }
        
        /* Member selection styles */
        .member-selection {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            padding: 10px;
            margin-top: 10px;
        }
        
        .member-item {
            display: flex;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid var(--border-color);
        }
        
        .member-item:last-child {
            border-bottom: none;
        }
        
        .member-item input {
            margin-right: 10px;
        }
        
        .member-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-right: 10px;
            overflow: hidden;
        }
        
        .member-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <?php include("../internees_task/header.php"); ?>
    <div class="container">
        <div class="header">
            <h1>Manage Chats</h1>
            <button class="back-button" onclick="window.location.href='chat_groups.php'">
                <i>←</i> Back to Chats
            </button>
        </div>
        
        <div class="management-sections">
            <!-- Group Management Section -->
            <div class="management-card">
                <div class="card-header">
                    <h2>Group Chats</h2>
                    <button id="create-group-btn">Create Group</button>
                </div>
                <ul class="group-list" id="group-list">
                    <!-- Groups will be populated by JavaScript -->
                </ul>
            </div>
            
            <!-- Message Management Section -->
            <div class="management-card">
                <div class="card-header">
                    <h2>Message Management</h2>
                </div>
                <div class="search-messages">
                    <input type="text" placeholder="Search messages...">
                </div>
                <div>
                    <p>Select a message to edit or delete it.</p>
                    <div class="message-actions">
                        <button id="edit-message-btn" disabled>Edit</button>
                        <button id="delete-message-btn" disabled class="delete">Delete</button>
                    </div>
                </div>
            </div>
            
            <!-- Chat Settings Section -->
            <div class="management-card">
                <div class="card-header">
                    <h2>Chat Settings</h2>
                </div>
                <ul class="settings-list">
                    <li class="setting-item">
                        <div>
                            <div class="setting-label">Read Receipts</div>
                            <div class="setting-description">Show when others have read your messages</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" checked>
                            <span class="slider"></span>
                        </label>
                    </li>
                    <li class="setting-item">
                        <div>
                            <div class="setting-label">Typing Indicators</div>
                            <div class="setting-description">Show when others are typing</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" checked>
                            <span class="slider"></span>
                        </label>
                    </li>
                    <li class="setting-item">
                        <div>
                            <div class="setting-label">Message Notifications</div>
                            <div class="setting-description">Receive notifications for new messages</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" checked>
                            <span class="slider"></span>
                        </label>
                    </li>
                    <li class="setting-item">
                        <div>
                            <div class="setting-label">Mute Group Notifications</div>
                            <div class="setting-description">Disable notifications from group chats</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox">
                            <span class="slider"></span>
                        </label>
                    </li>
                    <li class="setting-item">
                        <div>
                            <div class="setting-label">Media Auto-Download</div>
                            <div class="setting-description">Automatically download images and videos</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox">
                            <span class="slider"></span>
                        </label>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Create/Edit Group Modal -->
    <div class="modal" id="group-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modal-title">Create New Group</h3>
                <button class="close-modal">&times;</button>
            </div>
            <form id="group-form">
                <input type="hidden" id="group-id">
                <div class="form-group">
                    <label for="group-name">Group Name</label>
                    <input type="text" id="group-name" required>
                </div>
                <div class="form-group">
                    <label for="group-description">Description (Optional)</label>
                    <textarea id="group-description"></textarea>
                </div>
                <div class="form-group">
                    <label>Group Members</label>
                    <div class="member-selection" id="member-selection">
                        <!-- Members will be populated by JavaScript -->
                    </div>
                </div>
                <div class="form-group">
                    <label for="group-avatar">Group Avatar</label>
                    <input type="file" id="group-avatar" accept="image/*">
                </div>
                <div class="form-actions">
                    <button type="button" class="cancel-btn">Cancel</button>
                    <button type="submit" class="save-btn">Save</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Sample data for groups
        const groups = [
            {
                id: 1,
                name: "Team Project",
                description: "Group for the current project team",
                avatar: "https://randomuser.me/api/portraits/lego/1.jpg",
                members: [
                    { id: 1, name: "John Doe", avatar: "https://randomuser.me/api/portraits/men/1.jpg", selected: true },
                    { id: 2, name: "Sarah Smith", avatar: "https://randomuser.me/api/portraits/women/1.jpg", selected: true },
                    { id: 3, name: "Mike Johnson", avatar: "https://randomuser.me/api/portraits/men/2.jpg", selected: true },
                    { id: 4, name: "Emily Davis", avatar: "https://randomuser.me/api/portraits/women/2.jpg", selected: false }
                ]
            },
            {
                id: 2,
                name: "Family Group",
                description: "Our family chat group",
                avatar: "https://randomuser.me/api/portraits/lego/2.jpg",
                members: [
                    { id: 5, name: "Mom", avatar: "https://randomuser.me/api/portraits/women/3.jpg", selected: true },
                    { id: 6, name: "Dad", avatar: "https://randomuser.me/api/portraits/men/3.jpg", selected: true },
                    { id: 7, name: "Sister", avatar: "https://randomuser.me/api/portraits/women/4.jpg", selected: true }
                ]
            }
        ];
        
        // Sample data for contacts (potential group members)
        const contacts = [
            { id: 1, name: "John Doe", avatar: "https://randomuser.me/api/portraits/men/1.jpg" },
            { id: 2, name: "Sarah Smith", avatar: "https://randomuser.me/api/portraits/women/1.jpg" },
            { id: 3, name: "Mike Johnson", avatar: "https://randomuser.me/api/portraits/men/2.jpg" },
            { id: 4, name: "Emily Davis", avatar: "https://randomuser.me/api/portraits/women/2.jpg" },
            { id: 5, name: "Mom", avatar: "https://randomuser.me/api/portraits/women/3.jpg" },
            { id: 6, name: "Dad", avatar: "https://randomuser.me/api/portraits/men/3.jpg" },
            { id: 7, name: "Sister", avatar: "https://randomuser.me/api/portraits/women/4.jpg" },
            { id: 8, name: "Alex Brown", avatar: "https://randomuser.me/api/portraits/men/4.jpg" },
            { id: 9, name: "Lisa Wilson", avatar: "https://randomuser.me/api/portraits/women/5.jpg" }
        ];
        
        // DOM elements
        const groupList = document.getElementById('group-list');
        const groupModal = document.getElementById('group-modal');
        const modalTitle = document.getElementById('modal-title');
        const groupForm = document.getElementById('group-form');
        const groupIdInput = document.getElementById('group-id');
        const groupNameInput = document.getElementById('group-name');
        const groupDescriptionInput = document.getElementById('group-description');
        const memberSelection = document.getElementById('member-selection');
        const createGroupBtn = document.getElementById('create-group-btn');
        const closeModalBtn = document.querySelector('.close-modal');
        const cancelModalBtn = document.querySelector('.cancel-btn');
        
        // Render group list
        function renderGroupList() {
            groupList.innerHTML = '';
            
            if (groups.length === 0) {
                groupList.innerHTML = '<li style="padding: 10px; text-align: center; color: var(--light-text);">No groups found</li>';
                return;
            }
            
            groups.forEach(group => {
                const groupItem = document.createElement('li');
                groupItem.className = 'group-item';
                groupItem.dataset.id = group.id;
                
                groupItem.innerHTML = `
                    <div class="group-info">
                        <div class="group-avatar">
                            <img src="${group.avatar}" alt="${group.name}">
                        </div>
                        <div>
                            <div class="group-name">${group.name}</div>
                            <div class="group-members">${group.members.filter(m => m.selected).length} members</div>
                        </div>
                    </div>
                    <div class="group-actions">
                        <button class="edit" data-id="${group.id}">Edit</button>
                        <button class="delete" data-id="${group.id}">Delete</button>
                    </div>
                `;
                
                groupList.appendChild(groupItem);
            });
            
            // Add event listeners to edit and delete buttons
            document.querySelectorAll('.group-actions .edit').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const groupId = btn.dataset.id;
                    openEditGroupModal(groupId);
                });
            });
            
            document.querySelectorAll('.group-actions .delete').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const groupId = btn.dataset.id;
                    if (confirm('Are you sure you want to delete this group?')) {
                        deleteGroup(groupId);
                    }
                });
            });
        }
        
        // Render member selection for group creation/editing
        function renderMemberSelection(currentMembers = []) {
            memberSelection.innerHTML = '';
            
            contacts.forEach(contact => {
                const isSelected = currentMembers.some(m => m.id === contact.id && m.selected);
                
                const memberItem = document.createElement('div');
                memberItem.className = 'member-item';
                
                memberItem.innerHTML = `
                    <input type="checkbox" id="member-${contact.id}" ${isSelected ? 'checked' : ''}>
                    <div class="member-avatar">
                        <img src="${contact.avatar}" alt="${contact.name}">
                    </div>
                    <label for="member-${contact.id}">${contact.name}</label>
                `;
                
                memberSelection.appendChild(memberItem);
            });
        }
        
        // Open modal for creating a new group
        function openNewGroupModal() {
            modalTitle.textContent = 'Create New Group';
            groupIdInput.value = '';
            groupNameInput.value = '';
            groupDescriptionInput.value = '';
            renderMemberSelection();
            groupModal.style.display = 'flex';
        }
        
        // Open modal for editing an existing group
        function openEditGroupModal(groupId) {
            const group = groups.find(g => g.id == groupId);
            if (!group) return;
            
            modalTitle.textContent = 'Edit Group';
            groupIdInput.value = group.id;
            groupNameInput.value = group.name;
            groupDescriptionInput.value = group.description || '';
            renderMemberSelection(group.members);
            groupModal.style.display = 'flex';
        }
        
        // Save group (create or update)
        function saveGroup(e) {
            e.preventDefault();
            
            const groupId = groupIdInput.value;
            const name = groupNameInput.value;
            const description = groupDescriptionInput.value;
            
            // Get selected members
            const selectedMembers = [];
            document.querySelectorAll('#member-selection input[type="checkbox"]:checked').forEach(checkbox => {
                const memberId = parseInt(checkbox.id.replace('member-', ''));
                const contact = contacts.find(c => c.id === memberId);
                if (contact) {
                    selectedMembers.push({
                        id: contact.id,
                        name: contact.name,
                        avatar: contact.avatar,
                        selected: true
                    });
                }
            });
            
            if (groupId) {
                // Update existing group
                const groupIndex = groups.findIndex(g => g.id == groupId);
                if (groupIndex !== -1) {
                    groups[groupIndex] = {
                        ...groups[groupIndex],
                        name,
                        description,
                        members: selectedMembers
                    };
                }
            } else {
                // Create new group
                const newGroup = {
                    id: groups.length > 0 ? Math.max(...groups.map(g => g.id)) + 1 : 1,
                    name,
                    description,
                    avatar: "https://randomuser.me/api/portraits/lego/" + (groups.length + 1) + ".jpg",
                    members: selectedMembers
                };
                groups.push(newGroup);
            }
            
            // Handle file upload (in a real app, this would be sent to the server)
            const avatarFile = document.getElementById('group-avatar').files[0];
            if (avatarFile) {
                // In a real app, you would upload the file and get the URL
                console.log('Avatar file selected:', avatarFile.name);
            }
            
            renderGroupList();
            closeModal();
        }
        
        // Delete a group
        function deleteGroup(groupId) {
            const groupIndex = groups.findIndex(g => g.id == groupId);
            if (groupIndex !== -1) {
                groups.splice(groupIndex, 1);
                renderGroupList();
            }
        }
        
        // Close modal
        function closeModal() {
            groupModal.style.display = 'none';
            groupForm.reset();
        }
        
        // Event listeners
        createGroupBtn.addEventListener('click', openNewGroupModal);
        closeModalBtn.addEventListener('click', closeModal);
        cancelModalBtn.addEventListener('click', closeModal);
        groupForm.addEventListener('submit', saveGroup);
        
        // Click outside modal to close
        window.addEventListener('click', (e) => {
            if (e.target === groupModal) {
                closeModal();
            }
        });
        
        // Initialize
        renderGroupList();
        
        // Message management functionality
        const editMessageBtn = document.getElementById('edit-message-btn');
        const deleteMessageBtn = document.getElementById('delete-message-btn');
        
        // In a real app, these would be connected to actual message selection
        editMessageBtn.addEventListener('click', () => {
            alert('In a real app, this would open a message editor');
        });
        
        deleteMessageBtn.addEventListener('click', () => {
            if (confirm('Are you sure you want to delete this message?')) {
                alert('In a real app, this would delete the selected message');
            }
        });
        
        // Toggle switches for settings
        document.querySelectorAll('.toggle-switch input').forEach(switchInput => {
            switchInput.addEventListener('change', function() {
                const settingName = this.closest('.setting-item').querySelector('.setting-label').textContent;
                console.log(`${settingName} is now ${this.checked ? 'enabled' : 'disabled'}`);
                // In a real app, this would save the setting to the server
            });
        });
    </script>
</body>
</html>