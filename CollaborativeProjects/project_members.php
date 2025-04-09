<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Members Management</title>
    <style>
        /* Internal CSS Styles */
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
            --success-color: #2ecc71;
            --warning-color: #f39c12;
            --light-gray: #ecf0f1;
            --dark-gray: #7f8c8d;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 25px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .project-title {
            font-size: 24px;
            color: var(--secondary-color);
        }
        
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background: #2980b9;
        }
        
        .btn-danger {
            background: var(--accent-color);
            color: white;
        }
        
        .btn-danger:hover {
            background: #c0392b;
        }
        
        .btn-secondary {
            background: var(--light-gray);
            color: var(--secondary-color);
        }
        
        .btn-secondary:hover {
            background: #ddd;
        }
        
        /* Members Table */
        .members-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        
        .members-table th {
            text-align: left;
            padding: 12px 15px;
            background: var(--light-gray);
            color: var(--secondary-color);
            font-weight: 600;
        }
        
        .members-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }
        
        .members-table tr:hover {
            background-color: #f9f9f9;
        }
        
        .member-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #ddd;
            margin-right: 10px;
            display: inline-block;
            vertical-align: middle;
        }
        
        .member-name {
            display: inline-block;
            vertical-align: middle;
        }
        
        .role-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .role-manager {
            background: rgba(52, 152, 219, 0.1);
            color: var(--primary-color);
            border: 1px solid rgba(52, 152, 219, 0.3);
        }
        
        .role-contributor {
            background: rgba(46, 204, 113, 0.1);
            color: var(--success-color);
            border: 1px solid rgba(46, 204, 113, 0.3);
        }
        
        .role-viewer {
            background: rgba(155, 155, 155, 0.1);
            color: var(--dark-gray);
            border: 1px solid rgba(155, 155, 155, 0.3);
        }
        
        .action-btn {
            background: none;
            border: none;
            color: var(--primary-color);
            cursor: pointer;
            margin-right: 10px;
            font-size: 14px;
        }
        
        .action-btn:hover {
            text-decoration: underline;
        }
        
        .action-btn.delete {
            color: var(--accent-color);
        }
        
        /* Activity Log */
        .activity-log {
            margin-top: 30px;
        }
        
        .activity-log h3 {
            margin-bottom: 15px;
            color: var(--secondary-color);
        }
        
        .activity-item {
            display: flex;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .activity-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #ddd;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .activity-content {
            flex-grow: 1;
        }
        
        .activity-meta {
            display: flex;
            justify-content: space-between;
            margin-top: 5px;
            font-size: 13px;
            color: var(--dark-gray);
        }
        
        /* Modals */
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
            max-height: 90vh;
            overflow-y: auto;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
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
            color: var(--secondary-color);
        }
        
        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }
        
        /* Bulk Actions */
        .bulk-actions {
            display: none;
            margin-bottom: 15px;
            padding: 10px;
            background: var(--light-gray);
            border-radius: 4px;
        }
        
        .bulk-actions.active {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .select-all {
            margin-right: 15px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .members-table {
                display: block;
                overflow-x: auto;
            }
            
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
<?php include("../Internees_task/header.php"); ?>
    <div class="container">
        <div class="header">
            <h1 class="project-title">Website Redesign Project - Team Management</h1>
            <button class="btn btn-primary" onclick="openInviteModal()">Invite Members</button>
        </div>
        
        <!-- Bulk Actions (hidden by default) -->
        <div class="bulk-actions" id="bulkActions">
            <div class="select-all">
                <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                <label for="selectAll">Select all</label>
            </div>
            <span id="selectedCount">0 selected</span>
            <select class="form-control" style="width: 150px;" id="bulkRoleChange">
                <option value="">Change role to...</option>
                <option value="manager">Manager</option>
                <option value="contributor">Contributor</option>
                <option value="viewer">Viewer</option>
            </select>
            <button class="btn btn-danger" onclick="removeSelectedMembers()">Remove</button>
            <button class="btn btn-secondary" onclick="cancelBulkActions()">Cancel</button>
        </div>
        
        <!-- Members Table -->
        <table class="members-table">
            <thead>
                <tr>
                    <th width="40"><input type="checkbox" id="toggleBulk" onchange="toggleBulkActions()"></th>
                    <th>Member</th>
                    <th>Role</th>
                    <th>Joined</th>
                    <th>Last Active</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="membersList">
                <!-- Members will be loaded here -->
            </tbody>
        </table>
        
        <!-- Activity Log -->
        <div class="activity-log">
            <h3>Recent Activity</h3>
            <div id="activityFeed">
                <!-- Activity items will be loaded here -->
            </div>
        </div>
    </div>
    
    <!-- Invite Member Modal -->
    <div class="modal" id="inviteModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Invite New Members</h3>
                <button class="close-modal" onclick="closeModal()">×</button>
            </div>
            
            <form id="inviteForm">
                <div class="form-group">
                    <label for="inviteEmails">Email Addresses</label>
                    <textarea id="inviteEmails" class="form-control" rows="3" 
                              placeholder="Enter email addresses, separated by commas"></textarea>
                    <small>You can enter multiple email addresses separated by commas</small>
                </div>
                
                <div class="form-group">
                    <label for="inviteRole">Assign Role</label>
                    <select id="inviteRole" class="form-control">
                        <option value="contributor">Contributor</option>
                        <option value="manager">Project Manager</option>
                        <option value="viewer">Viewer</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="inviteMessage">Personal Message (Optional)</label>
                    <textarea id="inviteMessage" class="form-control" rows="3" 
                              placeholder="Add a personal message to the invitation"></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Send Invitations</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Edit Role Modal -->
    <div class="modal" id="roleModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Change Member Role</h3>
                <button class="close-modal" onclick="closeModal()">×</button>
            </div>
            
            <form id="roleForm">
                <input type="hidden" id="editMemberId">
                <div class="form-group">
                    <label>Member</label>
                    <p id="editMemberName" style="padding: 8px 0; font-weight: 500;"></p>
                </div>
                
                <div class="form-group">
                    <label for="newRole">New Role</label>
                    <select id="newRole" class="form-control">
                        <option value="manager">Project Manager</option>
                        <option value="contributor">Contributor</option>
                        <option value="viewer">Viewer</option>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Role</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Confirmation Modal -->
    <div class="modal" id="confirmModal">
        <div class="modal-content" style="max-width: 400px;">
            <div class="modal-header">
                <h3>Confirm Action</h3>
                <button class="close-modal" onclick="closeModal()">×</button>
            </div>
            
            <div class="modal-body">
                <p id="confirmMessage">Are you sure you want to remove this member?</p>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmActionBtn">Confirm</button>
            </div>
        </div>
    </div>

    <script>
        // Sample data - in a real app, this would come from an API
        const members = [
            { id: 1, name: "Sarah Johnson", email: "sarah@example.com", role: "manager", 
              joined: "2023-06-15", lastActive: "2023-07-20", avatar: "SJ" },
            { id: 2, name: "John Smith", email: "john@example.com", role: "contributor", 
              joined: "2023-06-18", lastActive: "2023-07-19", avatar: "JS" },
            { id: 3, name: "Emily Davis", email: "emily@example.com", role: "contributor", 
              joined: "2023-06-20", lastActive: "2023-07-18", avatar: "ED" },
            { id: 4, name: "Michael Brown", email: "michael@example.com", role: "viewer", 
              joined: "2023-06-22", lastActive: "2023-07-15", avatar: "MB" }
        ];
        
        const activityLog = [
            { id: 1, memberId: 2, action: "completed task 'Homepage layout'", timestamp: "2023-07-20T14:30:00" },
            { id: 2, memberId: 3, action: "uploaded file 'design-specs.pdf'", timestamp: "2023-07-20T11:15:00" },
            { id: 3, memberId: 1, action: "updated project timeline", timestamp: "2023-07-19T16:45:00" },
            { id: 4, memberId: 2, action: "commented on task 'User authentication'", timestamp: "2023-07-19T10:20:00" },
            { id: 5, memberId: 4, action: "viewed project dashboard", timestamp: "2023-07-18T09:10:00" }
        ];
        
        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            loadMembers();
            loadActivityLog();
            
            // Form submissions
            document.getElementById('inviteForm').addEventListener('submit', function(e) {
                e.preventDefault();
                inviteMembers();
            });
            
            document.getElementById('roleForm').addEventListener('submit', function(e) {
                e.preventDefault();
                updateMemberRole();
            });
        });
        
        // Load members into the table
        function loadMembers() {
            const membersList = document.getElementById('membersList');
            membersList.innerHTML = '';
            
            members.forEach(member => {
                const roleClass = `role-${member.role}`;
                const roleName = member.role === 'manager' ? 'Project Manager' : 
                                member.role === 'contributor' ? 'Contributor' : 'Viewer';
                
                const row = document.createElement('tr');
                row.dataset.id = member.id;
                row.innerHTML = `
                    <td><input type="checkbox" class="member-checkbox" onchange="updateSelectedCount()"></td>
                    <td>
                        <div class="member-avatar">${member.avatar}</div>
                        <div class="member-name">
                            <div>${member.name}</div>
                            <small style="color: var(--dark-gray);">${member.email}</small>
                        </div>
                    </td>
                    <td><span class="role-badge ${roleClass}">${roleName}</span></td>
                    <td>${formatDate(member.joined)}</td>
                    <td>${formatDate(member.lastActive)}</td>
                    <td>
                        <button class="action-btn" onclick="openRoleModal(${member.id}, '${member.name}', '${member.role}')">
                            Edit Role
                        </button>
                        ${member.role !== 'manager' ? 
                          `<button class="action-btn delete" onclick="confirmRemoveMember(${member.id}, '${member.name}')">
                            Remove
                          </button>` : ''
                        }
                    </td>
                `;
                
                membersList.appendChild(row);
            });
        }
        
        // Load activity log
        function loadActivityLog() {
            const activityFeed = document.getElementById('activityFeed');
            activityFeed.innerHTML = '';
            
            activityLog.forEach(activity => {
                const member = members.find(m => m.id === activity.memberId);
                if (!member) return;
                
                const activityItem = document.createElement('div');
                activityItem.className = 'activity-item';
                activityItem.innerHTML = `
                    <div class="activity-avatar">${member.avatar}</div>
                    <div class="activity-content">
                        <div><strong>${member.name}</strong> ${activity.action}</div>
                        <div class="activity-meta">
                            <span>${formatDateTime(activity.timestamp)}</span>
                        </div>
                    </div>
                `;
                
                activityFeed.appendChild(activityItem);
            });
        }
        
        // Modal functions
        function openInviteModal() {
            document.getElementById('inviteModal').style.display = 'flex';
        }
        
        function openRoleModal(memberId, memberName, currentRole) {
            document.getElementById('editMemberId').value = memberId;
            document.getElementById('editMemberName').textContent = memberName;
            document.getElementById('newRole').value = currentRole;
            document.getElementById('roleModal').style.display = 'flex';
        }
        
        function closeModal() {
            document.querySelectorAll('.modal').forEach(modal => {
                modal.style.display = 'none';
            });
        }
        
        // Invite members function
        function inviteMembers() {
            const emails = document.getElementById('inviteEmails').value;
            const role = document.getElementById('inviteRole').value;
            const message = document.getElementById('inviteMessage').value;
            
            // Validate emails
            const emailList = emails.split(',').map(email => email.trim()).filter(email => email);
            if (emailList.length === 0) {
                alert('Please enter at least one email address');
                return;
            }
            
            // In a real app, this would be an AJAX call to your backend
            console.log('Inviting:', emailList, 'with role:', role);
            
            // Simulate API call
            setTimeout(() => {
                alert(`Invitations sent to ${emailList.length} ${emailList.length === 1 ? 'person' : 'people'}`);
                closeModal();
                document.getElementById('inviteForm').reset();
            }, 1000);
        }
        
        // Update member role
        function updateMemberRole() {
            const memberId = parseInt(document.getElementById('editMemberId').value);
            const newRole = document.getElementById('newRole').value;
            
            // In a real app, this would be an AJAX call
            const memberIndex = members.findIndex(m => m.id === memberId);
            if (memberIndex !== -1) {
                members[memberIndex].role = newRole;
                loadMembers();
                closeModal();
            }
        }
        
        // Confirm member removal
        function confirmRemoveMember(memberId, memberName) {
            const confirmModal = document.getElementById('confirmModal');
            document.getElementById('confirmMessage').textContent = 
                `Are you sure you want to remove ${memberName} from the project?`;
            
            // Set up confirm button
            const confirmBtn = document.getElementById('confirmActionBtn');
            confirmBtn.onclick = function() {
                removeMember(memberId);
                closeModal();
            };
            
            confirmModal.style.display = 'flex';
        }
        
        // Remove member function
        function removeMember(memberId) {
            // In a real app, this would be an AJAX call
            const memberIndex = members.findIndex(m => m.id === memberId);
            if (memberIndex !== -1) {
                members.splice(memberIndex, 1);
                loadMembers();
            }
        }
        
        // Bulk actions
        function toggleBulkActions() {
            const bulkToggle = document.getElementById('toggleBulk');
            const bulkActions = document.getElementById('bulkActions');
            
            if (bulkToggle.checked) {
                bulkActions.classList.add('active');
                document.querySelectorAll('.member-checkbox').forEach(checkbox => {
                    checkbox.checked = true;
                });
                updateSelectedCount();
            } else {
                bulkActions.classList.remove('active');
                document.querySelectorAll('.member-checkbox').forEach(checkbox => {
                    checkbox.checked = false;
                });
            }
        }
        
        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            document.querySelectorAll('.member-checkbox').forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
            updateSelectedCount();
        }
        
        function updateSelectedCount() {
            const selectedCount = document.querySelectorAll('.member-checkbox:checked').length;
            document.getElementById('selectedCount').textContent = `${selectedCount} selected`;
        }
        
        function removeSelectedMembers() {
            const selectedCheckboxes = document.querySelectorAll('.member-checkbox:checked');
            const selectedIds = Array.from(selectedCheckboxes).map(checkbox => 
                parseInt(checkbox.closest('tr').dataset.id)
            );
            
            if (selectedIds.length === 0) {
                alert('Please select at least one member');
                return;
            }
            
            // In a real app, this would be an AJAX call
            selectedIds.forEach(id => {
                const memberIndex = members.findIndex(m => m.id === id);
                if (memberIndex !== -1 && members[memberIndex].role !== 'manager') {
                    members.splice(memberIndex, 1);
                }
            });
            
            loadMembers();
            cancelBulkActions();
        }
        
        function cancelBulkActions() {
            document.getElementById('toggleBulk').checked = false;
            document.getElementById('bulkActions').classList.remove('active');
            document.querySelectorAll('.member-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
        }
        
        // Helper functions
        function formatDate(dateString) {
            const options = { year: 'numeric', month: 'short', day: 'numeric' };
            return new Date(dateString).toLocaleDateString('en-US', options);
        }
        
        function formatDateTime(dateTimeString) {
            const options = { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            return new Date(dateTimeString).toLocaleDateString('en-US', options);
        }
    </script>
</body>
</html>