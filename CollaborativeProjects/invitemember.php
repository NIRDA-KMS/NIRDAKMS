<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invite Members | NIRDA Collaboration</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
        :root {
            --primary: #1a237e;
            --secondary: #2c3e50;
            --accent: #00A0DF;
            --background: #f0f2f5;
            --text: #333333;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--background);
            color: var(--text);
            margin: 0;
            margin-right: 30px;
            padding: 0;
        }
        
        .container {
            width: 600px;
            margin: 100px auto 50px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
        }
        
        h1 {
            color: var(--primary);
            margin-top: 0;
            border-bottom: 2px solid var(--accent);
            padding-bottom: 10px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        input[type="text"],
        input[type="email"],
        select,
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: 'Roboto', sans-serif;
        }
        
        textarea {
            min-height: 100px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.3s;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #0f1769;
        }
        
        .btn-secondary {
            background-color: var(--secondary);
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #1e2b37;
        }
        
        .btn-accent {
            background-color: var(--accent);
            color: white;
        }
        
        .btn-accent:hover {
            background-color: #0088c6;
        }
        
        .member-role {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: 'Roboto', sans-serif;
        }
        
        .member-list {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 15px;
            margin-top: 15px;
        }
        
        .member-item {
            display: flex;
            align-items: center;
            padding: 10px;
            background-color: var(--background);
            margin-bottom: 8px;
            border-radius: 4px;
        }
        
        .member-item:hover {
            background-color: #e0e5eb;
        }
        
        .error-message {
            color: #e74c3c;
            font-size: 0.9em;
            margin-top: 5px;
        }
        
        .success-message {
            color: #27ae60;
            font-size: 0.9em;
            margin-top: 5px;
        }
        
        .search-container {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .search-input {
            flex: 1;
        }
    </style>
</head>
<body>
    <?php include('../Internees_task/header.php'); ?>
    
    <div class="container">
        <h1><i class="fas fa-user-plus"></i> Invite Members to Project</h1>
        
        <?php
        // Database connection
        include('../SchedureEvent/connect.php');
        
        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $project_id = isset($_POST['project_id']) ? mysqli_real_escape_string($connection, $_POST['project_id']) : '';
            $emails = isset($_POST['emails']) ? $_POST['emails'] : [];
            $roles = isset($_POST['roles']) ? $_POST['roles'] : [];
            
            $success_count = 0;
            $error_messages = [];
            
            // Validate project ID
            if (empty($project_id)) {
                $error_messages[] = "Project ID is required";
            }
            
            // Process each invitation
            if (empty($error_messages)) {
                foreach ($emails as $index => $email) {
                    $email = mysqli_real_escape_string($connection, trim($email));
                    $role = mysqli_real_escape_string($connection, $roles[$index]);
                    
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $error_messages[] = "Invalid email address: $email";
                        continue;
                    }
                    
                    // Check if user exists
                    $user_query = "SELECT user_id FROM users WHERE email = '$email'";
                    $user_result = mysqli_query($connection, $user_query);
                    
                    if (mysqli_num_rows($user_result) > 0) {
                        $user = mysqli_fetch_assoc($user_result);
                        $user_id = $user['user_id'];
                        
                        // Check if already a member
                        $member_query = "SELECT * FROM project_members WHERE project_id = '$project_id' AND user_id = '$user_id'";
                        $member_result = mysqli_query($connection, $member_query);
                        
                        if (mysqli_num_rows($member_result) > 0) {
                            $error_messages[] = "User $email is already a member of this project";
                        } else {
                            // Add to project
                            $insert_query = "INSERT INTO project_members (project_id, user_id, role, joined_at) 
                                           VALUES ('$project_id', '$user_id', '$role', NOW())";
                            
                            if (mysqli_query($connection, $insert_query)) {
                                $success_count++;
                            } else {
                                $error_messages[] = "Error adding $email: " . mysqli_error($connection);
                            }
                        }
                    } else {
                        // TODO: Send invitation email to non-existing users
                        $error_messages[] = "User $email not found in system (invitation email would be sent in production)";
                    }
                }
                
                if ($success_count > 0) {
                    echo '<div class="success-message">Successfully added ' . $success_count . ' member(s) to the project</div>';
                }
                
                if (!empty($error_messages)) {
                    foreach ($error_messages as $error) {
                        echo '<div class="error-message">' . htmlspecialchars($error) . '</div>';
                    }
                }
            } else {
                foreach ($error_messages as $error) {
                    echo '<div class="error-message">' . htmlspecialchars($error) . '</div>';
                }
            }
        }
        
        // Get projects for dropdown
        $projects_query = "SELECT project_id, project_name FROM projects ORDER BY project_name";
        $projects_result = mysqli_query($connection, $projects_query);
        ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="project_id">Select Project <span class="required">*</span></label>
                <select id="project_id" name="project_id" required>
                    <option value="">-- Select a Project --</option>
                    <?php
                    if ($projects_result && mysqli_num_rows($projects_result) > 0) {
                        while ($project = mysqli_fetch_assoc($projects_result)) {
                            echo '<option value="' . htmlspecialchars($project['project_id']) . '">' . 
                                 htmlspecialchars($project['project_name']) . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Invite Members</label>
                <div id="inviteContainer">
                    <div class="search-container">
                        <input type="email" name="emails[]" placeholder="Enter email address" class="search-input" required>
                        <select id="roleSelect" class="member-role" name="new_role">
    <?php
    // Assuming you have a database connection
    $query = "SELECT role_id, role_name FROM roles ORDER BY role_name";
    $result = mysqli_query($connection, $query);
    
    while ($row = mysqli_fetch_assoc($result)) {
        $selected = ($row['role_id'] == 'contributor') ? ' selected' : '';
        echo '<option value="' . htmlspecialchars($row['role_id']) . '"' . $selected . '>' 
             . htmlspecialchars($row['role_name']) . '</option>';
    }
    ?>
</select>
                        <button type="button" class="btn btn-accent" id="addInviteField"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label>Custom Message (Optional)</label>
                <textarea name="invite_message" placeholder="Add a personal message to include with the invitation"></textarea>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Send Invitations</button>
                <button type="button" class="btn btn-secondary" id="previewBtn"><i class="fas fa-eye"></i> Preview Invitation</button>
            </div>
        </form>
    </div>

    <script>
        // Add more invite fields
        document.getElementById('addInviteField').addEventListener('click', function() {
            const container = document.getElementById('inviteContainer');
            const newField = document.createElement('div');
            newField.className = 'search-container';
            newField.innerHTML = `
                <input type="email" name="emails[]" placeholder="Enter email address" class="search-input" required>
                <select name="roles[]" class="member-role" required>
                    <option value="contributor" selected>Contributor</option>
                    <option value="manager">Manager</option>
                    <option value="viewer">Viewer</option>
                </select>
                <button type="button" class="btn btn-secondary remove-field"><i class="fas fa-times"></i></button>
            `;
            
            container.appendChild(newField);
            
            // Add remove functionality
            newField.querySelector('.remove-field').addEventListener('click', function() {
                if (document.querySelectorAll('.search-container').length > 1) {
                    newField.remove();
                } else {
                    alert('At least one invite field is required');
                }
            });
        });
        
        // Preview button functionality
        document.getElementById('previewBtn').addEventListener('click', function() {
            const projectSelect = document.getElementById('project_id');
            const projectName = projectSelect.options[projectSelect.selectedIndex].text;
            
            if (!projectSelect.value) {
                alert('Please select a project first');
                return;
            }
            
            // Count invitees
            const inviteCount = document.querySelectorAll('input[name="emails[]"]').length;
            
            // Show preview
            alert(`You are about to send ${inviteCount} invitation(s) to join project: ${projectName}\n\nClick "Send Invitations" to confirm.`);
        });
    </script>
</body>
</html>