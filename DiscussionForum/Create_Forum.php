<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Forum</title>
    <style>
        /* Enhanced Inline CSS */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #e9ecef;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            width: 100%;
        }
        h2 {
            text-align: center;
            color: #444;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        label {
            font-weight: bold;
            color: #555;
        }
        input, textarea, select, button {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
        }
        input:focus, textarea:focus, select:focus {
            border-color: #007bff;
            outline: none;
        }
        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #0056b3;
        }
        .hidden {
            display: none;
        }
        #previewSection {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 6px;
        }
        #previewSection h3 {
            color: #444;
            margin-bottom: 10px;
        }
        #previewSection p {
            margin: 5px 0;
            color: #555;
        }
    </style>
    
</head>
<body>
<div class="container">
        <h2>Create a Forum Category</h2>
        <form id="createForumForm">
            <label for="categoryName">Category Name:</label>
            <input type="text" id="categoryName" name="categoryName" placeholder="Enter category name" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="4" placeholder="Enter description" required></textarea>

            <label for="accessPermissions">Access Permissions:</label>
            <select id="accessPermissions" name="accessPermissions">
                <option value="public">Public</option>
                <option value="private">Private</option>
                <option value="restricted">Restricted</option>
            </select>

            <div id="restrictedOptions" class="hidden">
                <label for="userGroups">Select Users/Groups:</label>
                <input type="text" id="userGroups" name="userGroups" placeholder="Enter users/groups">
            </div>

            <button type="button" onclick="previewCategory()">Preview</button>
            <button type="submit">Submit</button>
        </form>

        <div id="previewSection" class="hidden">
            <h3>Preview:</h3>
            <p><strong>Category Name:</strong> <span id="previewName"></span></p>
            <p><strong>Description:</strong> <span id="previewDescription"></span></p>
            <p><strong>Access Permissions:</strong> <span id="previewPermissions"></span></p>
        </div>
    </div>

    <script>
        // Enhanced Inline JavaScript
        document.getElementById('accessPermissions').addEventListener('change', function () {
            const selectedValue = this.value;
            const restrictedOptions = document.getElementById('restrictedOptions');
            if (selectedValue === 'private' || selectedValue === 'restricted') {
                restrictedOptions.classList.remove('hidden');
            } else {
                restrictedOptions.classList.add('hidden');
            }
        });

        function previewCategory() {
            const name = document.getElementById('categoryName').value;
            const description = document.getElementById('description').value;
            const permissions = document.getElementById('accessPermissions').value;

            document.getElementById('previewName').textContent = name;
            document.getElementById('previewDescription').textContent = description;
            document.getElementById('previewPermissions').textContent = permissions;

            document.getElementById('previewSection').classList.remove('hidden');
        }

        document.getElementById('createForumForm').addEventListener('submit', function (event) {
            event.preventDefault();
            alert('Forum category successfully created!');
        });


///////////////////////
// Sidebar Toggle Functionality
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebarCollapse');
    
    // Initialize from localStorage
    if(localStorage.getItem('sidebarState') === 'open') {
        sidebar.classList.add('active');
        document.body.classList.add('sidebar-open');
        document.querySelector('.main-content')?.classList.add('sidebar-active');
    }
    
    // Toggle sidebar
    if(toggleBtn) {
        toggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const isOpening = !sidebar.classList.contains('active');
            
            sidebar.classList.toggle('active');
            document.body.classList.toggle('sidebar-open');
            document.querySelector('.main-content')?.classList.toggle('sidebar-active');
            
            localStorage.setItem('sidebarState', isOpening ? 'open' : 'closed');
        });
    }
    
    // Highlight current page in sidebar
    const currentPage = window.location.pathname.split('/').pop() || 'index.php';
    document.querySelectorAll('.sidebar a').forEach(link => {
        if(link.getAttribute('href').includes(currentPage)) {
            link.classList.add('active');
        }
    });
});


    </script>
</body>
</html> -->



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Forum Category</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        h1 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 24px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
        }
        
        input[type="text"], textarea, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .radio-group {
            margin: 15px 0;
        }
        
        .radio-option {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .radio-option input {
            margin-right: 10px;
        }
        
        .access-controls {
            display: none;
            margin-top: 15px;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 4px;
            border: 1px solid #eee;
        }
        
        .multi-select {
            width: 100%;
            height: 150px;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px;
            overflow-y: auto;
        }
        
        .multi-select option {
            padding: 8px;
            margin: 2px 0;
            background: white;
            border-bottom: 1px solid #f5f5f5;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            margin-right: 10px;
        }
        
        .btn-primary {
            background: #3498db;
            color: white;
        }
        
        .btn-secondary {
            background: #95a5a6;
            color: white;
        }
        
        .preview-section {
            margin-top: 30px;
            padding: 20px;
            border: 1px dashed #ccc;
            border-radius: 4px;
            display: none;
        }
        
        .preview-title {
            font-size: 20px;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .preview-description {
            color: #7f8c8d;
            margin-bottom: 15px;
        }
        
        .preview-access {
            font-size: 14px;
            color: #3498db;
        }
        
        .error {
            color: #e74c3c;
            font-size: 14px;
            margin-top: 5px;
            display: none;
        }
        
        .success-message {
            display: none;
            padding: 15px;
            background: #2ecc71;
            color: white;
            border-radius: 4px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include('../Internees_task/header.php') ?>
    <div class="container">
        <h1>Create New Forum Category</h1>
        
        <div class="success-message" id="successMessage">
            Forum category created successfully!
        </div>
        
        <form id="forumForm">
            <div class="form-group">
                <label for="categoryName">Category Name</label>
                <input type="text" id="categoryName" required>
                <div class="error" id="nameError">Please enter a category name</div>
            </div>
            
            <div class="form-group">
                <label for="categoryDescription">Description</label>
                <textarea id="categoryDescription" required></textarea>
                <div class="error" id="descError">Please enter a description</div>
            </div>
            
            <div class="form-group">
                <label>Access Permissions</label>
                <div class="radio-group">
                    <div class="radio-option">
                        <input type="radio" id="accessPublic" name="access" value="public" checked>
                        <label for="accessPublic">Public (Visible to all users)</label>
                    </div>
                    <div class="radio-option">
                        <input type="radio" id="accessPrivate" name="access" value="private">
                        <label for="accessPrivate">Private (Visible only to selected users)</label>
                    </div>
                    <div class="radio-option">
                        <input type="radio" id="accessRestricted" name="access" value="restricted">
                        <label for="accessRestricted">Restricted (Visible to specific groups)</label>
                    </div>
                </div>
                
                <div class="access-controls" id="privateControls">
                    <label>Select Users</label>
                    <select multiple class="multi-select" id="userSelect">
                        <option value="user1">John Doe (Admin)</option>
                        <option value="user2">Jane Smith (Moderator)</option>
                        <option value="user3">Bob Johnson (Member)</option>
                        <option value="user4">Alice Williams (Member)</option>
                    </select>
                    <div class="error" id="privateError">Please select at least one user</div>
                </div>
                
                <div class="access-controls" id="restrictedControls">
                    <label>Select Groups</label>
                    <select multiple class="multi-select" id="groupSelect">
                        <option value="group1">Administrators</option>
                        <option value="group2">Moderators</option>
                        <option value="group3">Engineering</option>
                        <option value="group4">Marketing</option>
                        <option value="group5">Sales</option>
                    </select>
                    <div class="error" id="restrictedError">Please select at least one group</div>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn btn-primary" id="previewBtn">Preview</button>
                <button type="submit" class="btn btn-primary">Create Forum</button>
                <button type="button" class="btn btn-secondary" id="cancelBtn">Cancel</button>
            </div>
        </form>
        
        <div class="preview-section" id="previewSection">
            <h2 class="preview-title" id="previewTitle">Category Name</h2>
            <p class="preview-description" id="previewDescription">Category description will appear here</p>
            <p class="preview-access" id="previewAccess">Access: Public</p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // DOM Elements
            const form = document.getElementById('forumForm');
            const accessRadios = document.querySelectorAll('input[name="access"]');
            const privateControls = document.getElementById('privateControls');
            const restrictedControls = document.getElementById('restrictedControls');
            const previewBtn = document.getElementById('previewBtn');
            const previewSection = document.getElementById('previewSection');
            const successMessage = document.getElementById('successMessage');
            
            // Access control visibility
            accessRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    privateControls.style.display = 'none';
                    restrictedControls.style.display = 'none';
                    
                    if (this.value === 'private') {
                        privateControls.style.display = 'block';
                    } else if (this.value === 'restricted') {
                        restrictedControls.style.display = 'block';
                    }
                });
            });
            
            // Preview functionality
            previewBtn.addEventListener('click', function() {
                if (validateForm(true)) {
                    updatePreview();
                    previewSection.style.display = 'block';
                }
            });
            
            // Form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (validateForm()) {
                    submitForm();
                }
            });
            
            // Form validation
            function validateForm(isPreview = false) {
                let isValid = true;
                const name = document.getElementById('categoryName').value.trim();
                const description = document.getElementById('categoryDescription').value.trim();
                const accessType = document.querySelector('input[name="access"]:checked').value;
                
                // Reset errors
                document.querySelectorAll('.error').forEach(el => el.style.display = 'none');
                
                if (!name) {
                    document.getElementById('nameError').style.display = 'block';
                    isValid = false;
                }
                
                if (!description) {
                    document.getElementById('descError').style.display = 'block';
                    isValid = false;
                }
                
                if (accessType === 'private' && !isPreview) {
                    const selectedUsers = document.getElementById('userSelect').selectedOptions.length;
                    if (selectedUsers === 0) {
                        document.getElementById('privateError').style.display = 'block';
                        isValid = false;
                    }
                }
                
                if (accessType === 'restricted' && !isPreview) {
                    const selectedGroups = document.getElementById('groupSelect').selectedOptions.length;
                    if (selectedGroups === 0) {
                        document.getElementById('restrictedError').style.display = 'block';
                        isValid = false;
                    }
                }
                
                return isValid;
            }
            
            // Update preview
            function updatePreview() {
                const name = document.getElementById('categoryName').value;
                const description = document.getElementById('categoryDescription').value;
                const accessType = document.querySelector('input[name="access"]:checked').value;
                
                document.getElementById('previewTitle').textContent = name;
                document.getElementById('previewDescription').textContent = description;
                
                let accessText = '';
                if (accessType === 'public') {
                    accessText = 'Access: Public (Visible to all users)';
                } else if (accessType === 'private') {
                    const selectedUsers = Array.from(document.getElementById('userSelect').selectedOptions)
                        .map(opt => opt.text.split(' ')[0]).join(', ');
                    accessText = `Access: Private (Visible to: ${selectedUsers})`;
                } else {
                    const selectedGroups = Array.from(document.getElementById('groupSelect').selectedOptions)
                        .map(opt => opt.text).join(', ');
                    accessText = `Access: Restricted (Visible to: ${selectedGroups})`;
                }
                
                document.getElementById('previewAccess').textContent = accessText;
            }
            
            // AJAX Form submission
            function submitForm() {
                const formData = {
                    name: document.getElementById('categoryName').value.trim(),
                    description: document.getElementById('categoryDescription').value.trim(),
                    access: document.querySelector('input[name="access"]:checked').value,
                    users: [],
                    groups: []
                };
                
                if (formData.access === 'private') {
                    formData.users = Array.from(document.getElementById('userSelect').selectedOptions)
                        .map(opt => opt.value);
                } else if (formData.access === 'restricted') {
                    formData.groups = Array.from(document.getElementById('groupSelect').selectedOptions)
                        .map(opt => opt.value);
                }
                
                // Simulate AJAX call
                console.log('Submitting form data:', formData);
                
                // Show success message (in real app, this would be after successful AJAX response)
                successMessage.style.display = 'block';
                form.reset();
                previewSection.style.display = 'none';
                privateControls.style.display = 'none';
                restrictedControls.style.display = 'none';
                
                // Hide success message after 3 seconds
                setTimeout(() => {
                    successMessage.style.display = 'none';
                }, 3000);
                
                // In a real implementation, you would use:
                /*
                fetch('/api/forums', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.json())
                .then(data => {
                    successMessage.style.display = 'block';
                    form.reset();
                    previewSection.style.display = 'none';
                    
                    setTimeout(() => {
                        successMessage.style.display = 'none';
                    }, 3000);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('There was an error creating the forum');
                });
                */
            }
        });
    </script>
</body>
</html>