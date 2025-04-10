<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Management - Contacts</title>
    <style>
        /* Base Styles - Matching Header.php */
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
            padding-top: 130px; /* Adjusted for fixed header */
            min-height: 100vh;
        }

        .container {
            /* max-width: 1400px; */
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

        .search-box {
            position: sticky;
            top: 0;
            background: white;
            padding: 10px 0;
            z-index: 10;
        }

        .search-box input {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 20px;
            font-size: 14px;
        }

        .groups-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 20px 0 10px;
        }

        .groups-header h3 {
            color: var(--primary-color);
        }

        .add-group-btn {
            background: none;
            border: none;
            color: var(--accent-color);
            cursor: pointer;
            font-size: 20px;
        }

        .group-list {
            list-style: none;
            margin-bottom: 20px;
        }

        .group-item {
            padding: 8px 0;
            cursor: pointer;
            display: flex;
            align-items: center;
        }

        .group-item.active {
            font-weight: 600;
            color: var(--accent-color);
        }

        .group-item .count {
            margin-left: auto;
            background: var(--background-color);
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 12px;
        }

        /* Contacts List */
        .contacts-list {
            list-style: none;
        }

        .contact-item {
            display: flex;
            align-items: center;
            padding: 12px 10px;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .contact-item:hover {
            background-color: rgba(0, 160, 223, 0.05);
        }

        .contact-avatar {
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

        .status-offline {
            background-color: var(--offline-color);
        }

        .status-away {
            background-color: var(--away-color);
        }

        .status-dnd {
            background-color: var(--dnd-color);
        }

        .contact-info {
            flex-grow: 1;
        }

        .contact-name {
            font-weight: 500;
            margin-bottom: 3px;
        }

        .contact-status {
            font-size: 12px;
            color: var(--dark-gray);
        }

        .contact-actions {
            display: none;
        }

        .contact-item:hover .contact-actions {
            display: flex;
            gap: 8px;
        }

        .action-btn {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--dark-gray);
            font-size: 16px;
            transition: color 0.2s;
        }

        .action-btn:hover {
            color: var(--accent-color);
        }

        .action-btn.delete:hover {
            color: var(--danger-color);
        }

        /* Main Content Area */
        .contact-details {
            padding: 20px;
            height: calc(100vh - 170px);
            overflow-y: auto;
        }

        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: var(--dark-gray);
            text-align: center;
        }

        .empty-state i {
            font-size: 50px;
            margin-bottom: 20px;
            color: var(--background-color);
        }

        /* Modal Styles */
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
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-header h3 {
            color: var(--primary-color);
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--dark-gray);
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
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

            .contact-details {
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
            <div class="search-box">
                <input type="text" id="contactSearch" placeholder="Search contacts...">
            </div>
            
            <div class="groups-header">
                <h3>Groups</h3>
                <button class="add-group-btn" id="addGroupBtn">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            
            <ul class="group-list" id="groupList">
                <li class="group-item active" data-group="all">All Contacts <span class="count">42</span></li>
                <li class="group-item" data-group="friends">Friends <span class="count">15</span></li>
                <li class="group-item" data-group="colleagues">Colleagues <span class="count">12</span></li>
                <li class="group-item" data-group="family">Family <span class="count">5</span></li>
                <li class="group-item" data-group="blocked">Blocked <span class="count">3</span></li>
            </ul>
            
            <h3>Contacts</h3>
            <ul class="contacts-list" id="contactsList">
                <!-- Contacts will be loaded here -->
            </ul>
        </aside>
        
        <main class="contact-details">
            <div class="empty-state" id="emptyState">
                <i class="fas fa-user-friends"></i>
                <h3>No contact selected</h3>
                <p>Select a contact from the list to view details</p>
            </div>
            
            <div id="contactDetailView" style="display: none;">
                <!-- Contact details will be loaded here -->
            </div>
        </main>
    </div>
    
    <!-- Add Group Modal -->
    <div class="modal" id="addGroupModal">
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
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" id="cancelGroup">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Group</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Add Contact Modal -->
    <div class="modal" id="addContactModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add New Contact</h3>
                <button class="close-modal" id="closeContactModal">&times;</button>
            </div>
            <form id="contactForm">
                <div class="form-group">
                    <label for="contactEmail">Email Address</label>
                    <input type="email" id="contactEmail" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="contactGroup">Add to Group</label>
                    <select id="contactGroup" class="form-control">
                        <option value="">None</option>
                        <option value="friends">Friends</option>
                        <option value="colleagues">Colleagues</option>
                        <option value="family">Family</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" id="cancelContact">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Contact</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Sample data - in a real app, this would come from an API
        const contacts = [
            { id: 1, name: "John Doe", email: "john@example.com", status: "online", group: "friends", 
              avatar: "JD", lastSeen: "Online now", isBlocked: false },
            { id: 2, name: "Jane Smith", email: "jane@example.com", status: "away", group: "colleagues", 
              avatar: "JS", lastSeen: "Last seen 30 mins ago", isBlocked: false },
            { id: 3, name: "Bob Johnson", email: "bob@example.com", status: "offline", group: "colleagues", 
              avatar: "BJ", lastSeen: "Last seen 2 hours ago", isBlocked: false },
            { id: 4, name: "Alice Williams", email: "alice@example.com", status: "dnd", group: "friends", 
              avatar: "AW", lastSeen: "Do not disturb", isBlocked: false },
            { id: 5, name: "Mike Brown", email: "mike@example.com", status: "online", group: "family", 
              avatar: "MB", lastSeen: "Online now", isBlocked: false },
            { id: 6, name: "Sarah Davis", email: "sarah@example.com", status: "offline", group: "blocked", 
              avatar: "SD", lastSeen: "Last seen 1 day ago", isBlocked: true }
        ];

        // DOM Elements
        const contactsList = document.getElementById('contactsList');
        const groupList = document.getElementById('groupList');
        const emptyState = document.getElementById('emptyState');
        const contactDetailView = document.getElementById('contactDetailView');
        const contactSearch = document.getElementById('contactSearch');
        const addGroupBtn = document.getElementById('addGroupBtn');
        const addGroupModal = document.getElementById('addGroupModal');
        const closeGroupModal = document.getElementById('closeGroupModal');
        const cancelGroup = document.getElementById('cancelGroup');
        const groupForm = document.getElementById('groupForm');
        const addContactModal = document.getElementById('addContactModal');
        const closeContactModal = document.getElementById('closeContactModal');
        const cancelContact = document.getElementById('cancelContact');
        const contactForm = document.getElementById('contactForm');

        // Initialize the app
        document.addEventListener('DOMContentLoaded', function() {
            loadContacts();
            setupEventListeners();
            
            // In a real app, we'd initialize WebSocket connection here
            // initWebSocket();
        });

        // Load contacts into the list
        function loadContacts(filter = '', group = 'all') {
            contactsList.innerHTML = '';
            
            let filteredContacts = contacts;
            
            // Apply group filter
            if (group !== 'all') {
                filteredContacts = filteredContacts.filter(contact => contact.group === group);
            }
            
            // Apply search filter
            if (filter) {
                const searchTerm = filter.toLowerCase();
                filteredContacts = filteredContacts.filter(contact => 
                    contact.name.toLowerCase().includes(searchTerm) || 
                    contact.email.toLowerCase().includes(searchTerm)
                );
            }
            
            if (filteredContacts.length === 0) {
                contactsList.innerHTML = '<li class="no-contacts">No contacts found</li>';
                return;
            }
            
            filteredContacts.forEach(contact => {
                const contactItem = document.createElement('li');
                contactItem.className = 'contact-item';
                contactItem.dataset.id = contact.id;
                
                // Determine status class
                let statusClass = '';
                if (contact.status === 'online') statusClass = 'status-online';
                else if (contact.status === 'away') statusClass = 'status-away';
                else if (contact.status === 'dnd') statusClass = 'status-dnd';
                else statusClass = 'status-offline';
                
                contactItem.innerHTML = `
                    <div class="contact-avatar">${contact.avatar}
                        <span class="status-indicator ${statusClass}"></span>
                    </div>
                    <div class="contact-info">
                        <div class="contact-name">${contact.name} ${contact.isBlocked ? '<span style="color:var(--danger-color)">(Blocked)</span>' : ''}</div>
                        <div class="contact-status">${contact.lastSeen}</div>
                    </div>
                    <div class="contact-actions">
                        <button class="action-btn" title="Message"><i class="fas fa-comment"></i></button>
                        <button class="action-btn" title="Edit"><i class="fas fa-edit"></i></button>
                        ${contact.isBlocked ? 
                            `<button class="action-btn" title="Unblock" onclick="toggleBlock(${contact.id}, false)"><i class="fas fa-unlock"></i></button>` :
                            `<button class="action-btn" title="Block" onclick="toggleBlock(${contact.id}, true)"><i class="fas fa-ban"></i></button>`
                        }
                        <button class="action-btn delete" title="Remove" onclick="removeContact(${contact.id})"><i class="fas fa-trash"></i></button>
                    </div>
                `;
                
                contactsList.appendChild(contactItem);
            });
            
            // Add click event to each contact item
            document.querySelectorAll('.contact-item').forEach(item => {
                item.addEventListener('click', function(e) {
                    // Don't trigger if clicking on action buttons
                    if (!e.target.closest('.contact-actions')) {
                        const contactId = parseInt(this.dataset.id);
                        showContactDetails(contactId);
                    }
                });
            });
        }

        // Show contact details in the main view
        function showContactDetails(contactId) {
            const contact = contacts.find(c => c.id === contactId);
            if (!contact) return;
            
            emptyState.style.display = 'none';
            contactDetailView.style.display = 'block';
            
            contactDetailView.innerHTML = `
                <div class="contact-header" style="display:flex; align-items:center; margin-bottom:20px;">
                    <div class="contact-avatar" style="width:60px; height:60px; font-size:24px; margin-right:15px;">
                        ${contact.avatar}
                        <span class="status-indicator ${contact.status === 'online' ? 'status-online' : 
                            contact.status === 'away' ? 'status-away' : 
                            contact.status === 'dnd' ? 'status-dnd' : 'status-offline'}" 
                            style="width:15px; height:15px;"></span>
                    </div>
                    <div>
                        <h2 style="color:var(--primary-color); margin-bottom:5px;">${contact.name}</h2>
                        <p style="color:var(--dark-gray);">${contact.lastSeen}</p>
                    </div>
                </div>
                
                <div class="contact-info" style="margin-bottom:20px;">
                    <div style="margin-bottom:10px;">
                        <strong>Email:</strong> ${contact.email}
                    </div>
                    <div style="margin-bottom:10px;">
                        <strong>Group:</strong> ${contact.group.charAt(0).toUpperCase() + contact.group.slice(1)}
                    </div>
                    <div>
                        <strong>Status:</strong> ${contact.status.charAt(0).toUpperCase() + contact.status.slice(1)}
                    </div>
                </div>
                
                <div class="contact-actions" style="display:flex; gap:10px; margin-top:30px;">
                    <button class="btn btn-primary" style="flex:1;">
                        <i class="fas fa-comment"></i> Message
                    </button>
                    <button class="btn btn-secondary" style="flex:1;" onclick="editContact(${contact.id})">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    ${contact.isBlocked ? 
                        `<button class="btn btn-secondary" style="flex:1;" onclick="toggleBlock(${contact.id}, false)">
                            <i class="fas fa-unlock"></i> Unblock
                        </button>` :
                        `<button class="btn btn-secondary" style="flex:1;" onclick="toggleBlock(${contact.id}, true)">
                            <i class="fas fa-ban"></i> Block
                        </button>`
                    }
                </div>
            `;
        }

        // Setup event listeners
        function setupEventListeners() {
            // Group filtering
            groupList.addEventListener('click', function(e) {
                if (e.target.classList.contains('group-item')) {
                    document.querySelectorAll('.group-item').forEach(item => {
                        item.classList.remove('active');
                    });
                    e.target.classList.add('active');
                    loadContacts(contactSearch.value, e.target.dataset.group);
                }
            });
            
            // Search functionality
            contactSearch.addEventListener('input', function() {
                const activeGroup = document.querySelector('.group-item.active').dataset.group;
                loadContacts(this.value, activeGroup);
            });
            
            // Group modal
            addGroupBtn.addEventListener('click', function() {
                addGroupModal.style.display = 'flex';
            });
            
            closeGroupModal.addEventListener('click', function() {
                addGroupModal.style.display = 'none';
            });
            
            cancelGroup.addEventListener('click', function() {
                addGroupModal.style.display = 'none';
            });
            
            groupForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const groupName = document.getElementById('groupName').value;
                // In a real app, this would be an API call
                console.log('Creating new group:', groupName);
                addGroupModal.style.display = 'none';
                groupForm.reset();
            });
            
            // Contact modal
            // You would add an "Add Contact" button and set up similar event listeners
        }

        // Contact management functions
        function toggleBlock(contactId, block) {
            const contact = contacts.find(c => c.id === contactId);
            if (contact) {
                contact.isBlocked = block;
                contact.group = block ? 'blocked' : 'friends'; // Default group when unblocking
                loadContacts(contactSearch.value, document.querySelector('.group-item.active').dataset.group);
                
                if (contactDetailView.style.display === 'block') {
                    showContactDetails(contactId);
                }
                
                // In a real app, this would be an API call
                console.log(`${block ? 'Blocked' : 'Unblocked'} contact ${contactId}`);
            }
        }

        function removeContact(contactId) {
            if (confirm('Are you sure you want to remove this contact?')) {
                const index = contacts.findIndex(c => c.id === contactId);
                if (index !== -1) {
                    contacts.splice(index, 1);
                    loadContacts(contactSearch.value, document.querySelector('.group-item.active').dataset.group);
                    
                    if (contactDetailView.style.display === 'block') {
                        emptyState.style.display = 'flex';
                        contactDetailView.style.display = 'none';
                    }
                    
                    // In a real app, this would be an API call
                    console.log(`Removed contact ${contactId}`);
                }
            }
        }

        function editContact(contactId) {
            // In a real app, this would open an edit modal
            alert(`Edit functionality for contact ${contactId} would open an edit form`);
        }

        // Make functions available globally
        window.toggleBlock = toggleBlock;
        window.removeContact = removeContact;
        window.editContact = editContact;
    </script>
</body>
</html>