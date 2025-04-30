<?php
// Start session and include header

// Database connection
require_once('../SchedureEvent/connect.php');
$conn = $connection;

// Check connection


// Initialize variables
$error = '';
$success = '';
$users = [];
$groups = [];

// Check if user is logged in (add your own authentication logic)
require_once __DIR__ . '/../Internees_task/auth/auth_check.php'; 


// Get users and groups for access control
// $users_result = $conn->query("SELECT id, username FROM users");
// if ($users_result) {
//     $users = $users_result->fetch_all(MYSQLI_ASSOC);
// }

// Get active users for selection dropdown
$users_result = $conn->query("
    SELECT user_id, username, full_name 
    FROM users 
    WHERE is_active = 1
    ORDER BY full_name
");

$users = [];
if ($users_result && $users_result->num_rows > 0) {
    $users = $users_result->fetch_all(MYSQLI_ASSOC);
}

// $groups_result = $conn->query("SELECT id, name FROM groups");
// if ($groups_result) {
//     $groups = $groups_result->fetch_all(MYSQLI_ASSOC);
// }

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_name = $conn->real_escape_string($_POST['categoryName']);
    $description = $conn->real_escape_string($_POST['categoryDescription']);
    $permission = $conn->real_escape_string($_POST['access']);
    
    // Initialize empty arrays for selections
    $allowed_user_ids = [];
    $allowed_group_ids = [];
    
    // Process selected users if private access
    if ($permission === 'private' && isset($_POST['userSelect'])) {
        // Validate each selected user exists in database
        $valid_users = [];
        foreach ($_POST['userSelect'] as $user_id) {
            $user_id = (int)$user_id;
            $result = $conn->query("SELECT user_id FROM users WHERE user_id = $user_id AND is_active = 1");
            if ($result && $result->num_rows > 0) {
                $valid_users[] = $user_id;
            }
        }
        $allowed_user_ids = $valid_users;
    }
    
    // Process selected groups if restricted access
    if ($permission === 'restricted' && isset($_POST['groupSelect'])) {
        $allowed_group_ids = array_map('intval', $_POST['groupSelect']);
    }
    
    // Convert arrays to comma-separated strings for database storage
    $users_str = implode(",", $allowed_user_ids);
    $groups_str = implode(",", $allowed_group_ids);
// }else {
//         $groups_str = "";
//     }
//     if ($permission === 'private' && isset($_POST['userSelect'])) {
//     $allowed_user_ids = validateUserIds($conn, $_POST['userSelect']);
// }
    
    // Insert into database
    $sql = "INSERT INTO forum_categories (category_name, description, permission, allowed_user_ids, allowed_group_ids) 
            VALUES ('$category_name', '$description', '$permission', '$users_str', '$groups_str')";
    
    if ($conn->query($sql)) {
        $success = "Forum category created successfully!"; 
    } else {
        $error = "Error creating forum: " . $conn->error;
    }
}    

// Add User Validation Function

function validateUserIds($conn, $user_ids) {
    if (empty($user_ids)) return [];
    
    $ids = array_map('intval', $user_ids);
    $ids_str = implode(",", $ids);
    
    $result = $conn->query("SELECT user_id FROM users WHERE user_id IN ($ids_str) AND is_active = 1");
    $valid_ids = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $valid_ids[] = $row['user_id'];
        }
    }
    
    return $valid_ids;
}


?>

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
            padding-top: 80px;
            min-height: 100vh;
        }
        
        .container {
            max-width: 800px;
            margin: 100px auto;
            background: white;
            padding: 30px;
            padding-top: 50px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        h1 {
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
        }
        
        .success-message {
            padding: 15px;
            background: #2ecc71;
            color: white;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            body {
                padding-top: 60px;
            }
            
            .container {
                margin: 10px;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Create New Forum Category</h1>
        
        <?php if ($success): ?>
            <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form id="forumForm" method="POST" action="create_forum.php">
            <div class="form-group">
                <label for="categoryName">Category Name</label>
                <input type="text" id="categoryName" name="categoryName" required>
                <div class="error" id="nameError">Please enter a category name</div>
            </div>
            
            <div class="form-group">
                <label for="categoryDescription">Description</label>
                <textarea id="categoryDescription" name="categoryDescription" required></textarea>
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
    <select multiple class="multi-select" id="userSelect" name="userSelect[]">
        <?php foreach ($users as $user): ?>
            <option value="<?php echo htmlspecialchars($user['user_id']); ?>">
                <?php echo htmlspecialchars($user['full_name'] . ' (' . $user['username'] . ')'); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <div class="error" id="privateError">Please select at least one user</div>
</div>
                
                <div class="access-controls" id="restrictedControls">
                    <label>Select Groups</label>
                    <select multiple class="multi-select" id="groupSelect" name="groupSelect[]">
                        <?php foreach ($groups as $group): ?>
                            <option value="<?php echo $group['id']; ?>">
                                <?php echo htmlspecialchars($group['name']); ?>
                            </option>
                        <?php endforeach; ?>
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
            
            // Cancel button functionality
            document.getElementById('cancelBtn').addEventListener('click', function() {
                if (confirm('Are you sure you want to cancel? All unsaved changes will be lost.')) {
                    window.location.href = 'manage_forums.php';
                }
            });
        });
    </script>
</body>
</html>

<?php
// Close database connection
$conn->close();
?>