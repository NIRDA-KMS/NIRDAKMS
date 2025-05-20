<?php
include('../SchedureEvent/connect.php');

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get POST data
    $projectName = isset($_POST['projectName']) ? mysqli_real_escape_string($connection, $_POST['projectName']) : '';
    $projectDescription = isset($_POST['projectDescription']) ? mysqli_real_escape_string($connection, $_POST['projectDescription']) : '';
    $startDate = isset($_POST['startDate']) ? mysqli_real_escape_string($connection, $_POST['startDate']) : '';
    $endDate = isset($_POST['endDate']) ? mysqli_real_escape_string($connection, $_POST['endDate']) : '';
    $notifyTeam = isset($_POST['notifyTeam']) ? 1 : 0;
    $template = isset($_POST['template']) ? mysqli_real_escape_string($connection, $_POST['template']) : '';

    // Process first goal (stored in projects table)
    $goalTitle = '';
    $goalDescription = '';
    if (isset($_POST['goals'][0]['title'])) {
        $goalTitle = mysqli_real_escape_string($connection, $_POST['goals'][0]['title']);
        $goalDescription = isset($_POST['goals'][0]['description']) ? mysqli_real_escape_string($connection, $_POST['goals'][0]['description']) : '';
    }

    // Process members
    $members = [];
    if (isset($_POST['members']) && is_array($_POST['members'])) {
        foreach ($_POST['members'] as $userId => $memberData) {
            $members[] = [
                'user_id' => mysqli_real_escape_string($connection, $userId),
                'role' => mysqli_real_escape_string($connection, $memberData['role'])
            ];
        }
    }

    // Validate required fields
    if (empty($projectName)) {
        $response['message'] = 'Project name is required';
        echo json_encode($response);
        exit;
    }

    if (empty($startDate) || empty($endDate)) {
        $response['message'] = 'Start and end dates are required';
        echo json_encode($response);
        exit;
    }

    if ($startDate > $endDate) {
        $response['message'] = 'End date must be after start date';
        echo json_encode($response);
        exit;
    }

    if (empty($members)) {
        $response['message'] = 'At least one team member is required';
        echo json_encode($response);
        exit;
    }

    // Begin transaction
    mysqli_begin_transaction($connection);

    try {
        // Insert project into projects table (including first goal)
        $projectQuery = "INSERT INTO projects (
                            project_name, 
                            description, 
                            project_template,
                            start_date, 
                            end_date,
                            goal_title,
                            goal_description,
                            created_at
                        ) VALUES (
                            '$projectName',
                            '$projectDescription',
                            '$template',
                            '$startDate',
                            '$endDate',
                            '$goalTitle',
                            '$goalDescription',
                            NOW()
                        )";
        
        $projectResult = mysqli_query($connection, $projectQuery);
        
        if (!$projectResult) {
            throw new Exception('Failed to create project: ' . mysqli_error($connection));
        }
        
        $projectId = mysqli_insert_id($connection);
        
        // Insert team members into project_members table
        foreach ($members as $member) {
            $memberQuery = "INSERT INTO project_members (
                               project_id, 
                               user_id, 
                               role, 
                               joined_at
                           ) VALUES (
                               '$projectId',
                               '{$member['user_id']}',
                               '{$member['role']}',
                               NOW()
                           )";
            
            $memberResult = mysqli_query($connection, $memberQuery);
            
            if (!$memberResult) {
                throw new Exception('Failed to add team member: ' . mysqli_error($connection));
            }
        }
    
        // Commit transaction
        mysqli_commit($connection);
        
        // Set success response
        // $response['success'] = true;
        // $response['project_id'] = $projectId;
        // $response['message'] = 'Project created successfully';
        header("Location:create_project.php");
        
        // Send notifications if requested
        if ($notifyTeam) {
            // Notification logic would go here
        }
        
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($connection);
        $response['success'] = false;
        $response['message'] = $e->getMessage();
    }

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Project | NIRDA Collaboration</title>
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
            padding: 20px;
        }
        
        .container {
            max-width: 1000px;
            margin-bottom: 50px;
            margin-right: 30px;
            margin-top: 100px;
            margin-left: 255px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding-left: 30px;
        }
        
        h1 {
            color: var(--primary);
            margin-top: 0;
            border-bottom: 2px solid var(--accent);
            padding-bottom: 10px;
        }
        
        .wizard-progress {
            display: flex;
            margin-bottom: 30px;
        }
        
        .wizard-step {
            flex: 1;
            text-align: center;
            position: relative;
        }
        
        .wizard-step .step-number {
            width: 30px;
            height: 30px;
            background-color: #ddd;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 5px;
            color: var(--text);
        }
        
        .wizard-step.active .step-number {
            background-color: var(--accent);
            color: white;
        }
        
        .wizard-step.completed .step-number {
            background-color: var(--primary);
            color: white;
        }
        
        .wizard-step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 15px;
            left: 50%;
            right: -50%;
            height: 2px;
            background-color: #ddd;
            z-index: -1;
        }
        
        .wizard-step.completed:not(:last-child)::after {
            background-color: var(--primary);
        }
        
        .wizard-content {
            display: none;
        }
        
        .wizard-content.active {
            display: block;
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
        input[type="date"],
        textarea,
        select {
    width: 90%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-family: 'Roboto', sans-serif;
    background-color: white;
    appearance: none; /* Removes default system styling */
    -webkit-appearance: none; /* For Safari */
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 10px center;
    background-size: 1em;
}

select:focus {
    outline: none;
    border-color: var(--accent);
    box-shadow: 0 0 0 2px rgba(0,160,223,0.2);
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
        
        .btn-outline {
            background-color: transparent;
            border: 1px solid var(--secondary);
            color: var(--secondary);
        }
        
        .btn-outline:hover {
            background-color: var(--secondary);
            color: white;
        }
        
        .action-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        
        .member-list {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 15px;
            min-height: 150px;
        }
        
        .member-item {
            display: flex;
            align-items: center;
            padding: 8px;
            background-color: var(--background);
            margin-bottom: 8px;
            border-radius: 4px;
        }
        
        .member-item:hover {
            background-color: #e0e5eb;
        }
        
        .member-role {
    margin-left: auto;
    width: 150px;
    padding: 8px; /* Slightly less padding than main selects */
    border: 1px solid #ddd;
    border-radius: 4px;
    font-family: 'Roboto', sans-serif;
    appearance: none;
    -webkit-appearance: none;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 8px center;
    background-size: 1em;
}
        
        .goal-item {
            margin-bottom: 15px;
            padding: 15px;
            background-color: var(--background);
            border-radius: 4px;
        }
        
        .goal-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .template-card {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .template-card:hover {
            border-color: var(--accent);
            box-shadow: 0 2px 8px rgba(0,160,223,0.2);
        }
        
        .template-card h3 {
            margin-top: 0;
            color: var(--primary);
        }
        
        .drag-handle {
            cursor: move;
            margin-right: 10px;
            color: var(--secondary);
        }
        
        .error-message {
            color: #e74c3c;
            font-size: 0.9em;
            margin-top: 5px;
        }
        
        .search-container {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap; /* Allows items to wrap on small screens */
}

.search-input, .member-role {
    flex: 1;
    min-width: 150px; /* Prevents them from becoming too small */
}

#addMemberBtn {
    white-space: nowrap; /* Prevents button text from wrapping */
}
    </style>
</head>
<body>
    <?php include('../Internees_task/header.php'); ?>
    <div class="container">
        <h1><i class="fas fa-project-diagram"></i> Create New Collaborative Project</h1>
        
        <!-- Wizard Progress -->
        <div class="wizard-progress">
            <div class="wizard-step active" id="step1">
                <div class="step-number">1</div>
                <div class="step-title">Basic Info</div>
            </div>
            <div class="wizard-step" id="step2">
                <div class="step-number">2</div>
                <div class="step-title">Team Setup</div>
            </div>
            <div class="wizard-step" id="step3">
                <div class="step-number">3</div>
                <div class="step-title">Goals</div>
            </div>
            <div class="wizard-step" id="step4">
                <div class="step-number">4</div>
                <div class="step-title">Review</div>
            </div>
        </div>
        
        <!-- Wizard Content -->
        <form id="projectForm" method="POST" action="">
            <!-- Step 1: Basic Info -->
            <div class="wizard-content active" id="content1">
                <div class="form-group">
                    <label for="projectName">Project Name <span class="required">*</span></label>
                    <input type="text" id="projectName" name="projectName" required>
                    <div class="error-message" id="nameError"></div>
                </div>
                
                <div class="form-group">
                    <label for="projectDescription">Description</label>
                    <textarea id="projectDescription"name="projectDescription" maxlength="10000"></textarea>
                </div>
                
                <div class="form-row" style="display: flex; gap: 20px;">
                    <div class="form-group" style="flex: 1;">
                        <label for="startDate">Start Date <span class="required">*</span></label>
                        <input type="date" id="startDate" name="startDate" required>
                        <div class="error-message" id="startDateError"></div>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label for="endDate">End Date <span class="required">*</span></label>
                        <input type="date" id="endDate" name="endDate" required>
                        <div class="error-message" id="endDateError"></div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Project Template (Optional)</label>
                    <div class="template-card">
                        <h3><i class="fas fa-file-alt"></i> Standard Project</h3>
                        <p>A blank project with basic structure</p>
                        <button type="button" class="btn btn-outline" onclick="selectTemplate(this, 'Standard Project')">Select</button>
                    </div>
                    <div class="template-card">
                        <h3><i class="fas fa-users"></i> Team Collaboration</h3>
                        <p>Pre-configured with multiple roles and standard goals</p>
                        <button type="button" class="btn btn-outline" onclick="selectTemplate(this, 'Team Collaboration')">Select</button>
                    </div>
                    <div class="template-card">
                        <h3><i class="fas fa-chart-line"></i> Research Project</h3>
                        <p>Includes research milestones and reporting structure</p>
                        <button type="button" class="btn btn-outline" onclick="selectTemplate(this, 'Research Project')">Select</button>
                    </div>
                    <input type="hidden" id="template" name="template" value="">
                </div>
            </div>
            
            <!-- Step 2: Team Setup -->
            <div class="wizard-content" id="content2">
                <div class="form-group">
                    <label>Add Team Members</label>
                    <div class="search-container">
                        <select id="userSelect" class="search-input">
                            <option value="">-- Select a User --</option>
                            <?php
                            $usersQuery = "SELECT user_id, full_name FROM users ORDER BY full_name";
                            $usersResult = mysqli_query($connection, $usersQuery);
                            
                            if ($usersResult && mysqli_num_rows($usersResult) > 0) {
                                while ($user = mysqli_fetch_assoc($usersResult)) {
                                    echo '<option value="' . htmlspecialchars($user['user_id']) . '">' . 
                                         htmlspecialchars($user['full_name']) . '</option>';
                                }
                            } else {
                                echo '<option value="">No users available</option>';
                            }
                            ?>
                        </select>
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
                        <button type="button" class="btn btn-accent" id="addMemberBtn"><i class="fas fa-plus"></i> Add</button>
                    </div>
                    <div class="error-message" id="memberError"></div>
                </div>
                
                <div class="form-group">
                    <label>Project Members</label>
                    <div class="member-list" id="memberList">
                        <!-- Members will be added here dynamically -->
                    </div>
                </div>
            </div>
            
            <!-- Step 3: Goals -->
            <div class="wizard-content" id="content3">
                <div class="form-group">
                    <label>Project Goals and Objectives</label>
                    <div id="goalsContainer">
                        <div class="goal-item">
                            <div class="goal-header">
                                <input type="text" name="goals[0][title]" placeholder="Goal title" required style="flex: 1; margin-right: 10px;">
                            </div>
                            <textarea name="goals[0][description]" placeholder="Goal description"></textarea>
                        </div>
                    </div>
                    <button type="button" class="btn btn-accent" id="addGoal"><i class="fas fa-plus"></i> Add Goal</button>
                </div>
            </div>
            
            <!-- Step 4: Review -->
            <div class="wizard-content" id="content4">
                <div class="form-group">
                    <h3 style="color: var(--primary);">Project Summary</h3>
                    <div style="background-color: var(--background); padding: 20px; border-radius: 4px;">
                        <h4 id="reviewProjectName">Project Name: </h4>
                        <p id="reviewProjectDescription">Description: </p>
                        <p id="reviewProjectDates">Dates: </p>
                        <p id="reviewProjectTemplate">Template: </p>
                        
                        <h4 style="margin-top: 20px;">Team Members</h4>
                        <ul id="reviewMembers" style="list-style-type: none; padding-left: 0;">
                            <!-- Team members will be listed here -->
                        </ul>
                        
                        <h4 style="margin-top: 20px;">Project Goals</h4>
                        <ul id="reviewGoals" style="list-style-type: none; padding-left: 0;">
                            <!-- Goals will be listed here -->
                        </ul>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="notifyTeam">Notify team members about this project?</label>
                    <input type="checkbox" id="notifyTeam" name="notifyTeam" checked>
                </div>
            </div>
            
            <!-- Navigation Buttons -->
            <div class="action-buttons">
                <button type="button" class="btn btn-secondary" id="prevBtn" disabled><i class="fas fa-arrow-left"></i> Previous</button>
                <button type="button" class="btn btn-primary" id="nextBtn">Next <i class="fas fa-arrow-right"></i></button>
            </div>
        </form>
    </div>

    <script>
    // Wizard Navigation
    let currentStep = 1;
    const totalSteps = 4;
    
    document.getElementById('nextBtn').addEventListener('click', function() {
        if (validateStep(currentStep)) {
            if (currentStep < totalSteps) {
                document.getElementById(`content${currentStep}`).classList.remove('active');
                document.getElementById(`step${currentStep}`).classList.remove('active');
                
                currentStep++;
                
                document.getElementById(`content${currentStep}`).classList.add('active');
                document.getElementById(`step${currentStep}`).classList.add('active');
                
                document.getElementById('prevBtn').disabled = false;
                
                if (currentStep === totalSteps) {
                    updateReviewSection();
                    this.textContent = 'Create Project';
                } else {
                    this.textContent = 'Next';
                }
            } else {
                // Submit form
                document.getElementById('projectForm').submit();
            }
        }
    });
    
    document.getElementById('prevBtn').addEventListener('click', function() {
        document.getElementById(`content${currentStep}`).classList.remove('active');
        document.getElementById(`step${currentStep}`).classList.remove('active');
        
        currentStep--;
        
        document.getElementById(`content${currentStep}`).classList.add('active');
        document.getElementById(`step${currentStep}`).classList.add('active');
        
        document.getElementById('nextBtn').textContent = 'Next';
        
        if (currentStep === 1) {
            this.disabled = true;
        }
    });
    
    // Add Member Button with role handling
    document.getElementById('addMemberBtn').addEventListener('click', function() {
        const userSelect = document.getElementById('userSelect');
        const roleSelect = document.getElementById('roleSelect');
        const userId = userSelect.value;
        const userName = userSelect.options[userSelect.selectedIndex].text;
        const role = roleSelect.value;
        const roleName = roleSelect.options[roleSelect.selectedIndex].text;
        
        if (!userId) {
            alert('Please select a user');
            return;
        }
        
        // Check if user already exists
        if (document.querySelector(`.member-item[data-user-id="${userId}"]`)) {
            alert('This user is already a team member');
            return;
        }
        
        // Create new member item
        const memberItem = document.createElement('div');
        memberItem.className = 'member-item';
        memberItem.dataset.userId = userId;
        
        // Clone the role select dropdown for this member
        const roleSelectClone = roleSelect.cloneNode(true);
        roleSelectClone.value = role;
        roleSelectClone.className = 'member-role';
        roleSelectClone.style.width = '150px';
        
        memberItem.innerHTML = `
            <i class="fas fa-grip-vertical drag-handle"></i>
            <span>${userName}</span>
            <input type="hidden" name="members[${userId}][user_id]" value="${userId}">
            <input type="hidden" name="members[${userId}][name]" value="${userName}">
            <input type="hidden" name="members[${userId}][role]" value="${role}">
            <input type="hidden" name="members[${userId}][role_name]" value="${roleName}">
            <button type="button" class="btn btn-outline" style="margin-left: 10px;">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        // Insert the cloned select before the button
        memberItem.insertBefore(roleSelectClone, memberItem.querySelector('button'));
        
        // Add remove functionality
        memberItem.querySelector('button').addEventListener('click', function() {
            memberItem.remove();
        });
        
        // Add role change handler
        roleSelectClone.addEventListener('change', function() {
            memberItem.querySelector('input[name$="[role]"]').value = this.value;
            memberItem.querySelector('input[name$="[role_name]"]').value = 
                this.options[this.selectedIndex].text;
        });
        
        document.getElementById('memberList').appendChild(memberItem);
        userSelect.selectedIndex = 0;
        roleSelect.value = 'contributor'; // Reset to default
    });
    
    // Add Goal Button
    document.getElementById('addGoal').addEventListener('click', function() {
        const goalCount = document.querySelectorAll('.goal-item').length;
        const goalItem = document.createElement('div');
        goalItem.className = 'goal-item';
        goalItem.innerHTML = `
            <div class="goal-header">
                <input type="text" name="goals[${goalCount}][title]" placeholder="Goal title" required style="flex: 1; margin-right: 10px;">
                <button type="button" class="btn btn-outline"><i class="fas fa-times"></i> Remove</button>
            </div>
            <textarea name="goals[${goalCount}][description]" placeholder="Goal description"></textarea>
        `;
        
        goalItem.querySelector('button').addEventListener('click', function() {
            goalItem.remove();
        });
        
        document.getElementById('goalsContainer').appendChild(goalItem);
    });
    
    // Form Validation
    function validateStep(step) {
        let isValid = true;
        
        if (step === 1) {
            const projectName = document.getElementById('projectName').value.trim();
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            
            if (projectName === '') {
                document.getElementById('nameError').textContent = 'Project name is required';
                isValid = false;
            } else {
                document.getElementById('nameError').textContent = '';
            }
            
            if (startDate === '') {
                document.getElementById('startDateError').textContent = 'Start date is required';
                isValid = false;
            } else {
                document.getElementById('startDateError').textContent = '';
            }
            
            if (endDate === '') {
                document.getElementById('endDateError').textContent = 'End date is required';
                isValid = false;
            } else if (startDate !== '' && endDate < startDate) {
                document.getElementById('endDateError').textContent = 'End date must be after start date';
                isValid = false;
            } else {
                document.getElementById('endDateError').textContent = '';
            }
        } else if (step === 2) {
            const memberCount = document.querySelectorAll('.member-item').length;
            if (memberCount === 0) {
                document.getElementById('memberError').textContent = 'At least one team member is required';
                isValid = false;
            } else {
                document.getElementById('memberError').textContent = '';
            }
        } else if (step === 3) {
            // Validate goals
            const goalInputs = document.querySelectorAll('input[name^="goals["][name$="][title]"]');
            let hasEmptyGoal = false;
            
            goalInputs.forEach(input => {
                if (input.value.trim() === '') {
                    hasEmptyGoal = true;
                    input.style.borderColor = '#e74c3c';
                } else {
                    input.style.borderColor = '#ddd';
                }
            });
            
            if (hasEmptyGoal) {
                alert('Please fill in all goal titles');
                isValid = false;
            }
        }
        
        return isValid;
    }
    
    // Update Review Section with role names
    function updateReviewSection() {
        // Basic project info
        document.getElementById('reviewProjectName').textContent = 'Project Name: ' + document.getElementById('projectName').value;
        document.getElementById('reviewProjectDescription').textContent = 'Description: ' + (document.getElementById('projectDescription').value || 'None provided');
        document.getElementById('reviewProjectDates').textContent = 'Dates: ' + document.getElementById('startDate').value + ' to ' + document.getElementById('endDate').value;
        document.getElementById('reviewProjectTemplate').textContent = 'Template: ' + (document.getElementById('template').value || 'None selected');
        
        // Team members
        const reviewMembersList = document.getElementById('reviewMembers');
        reviewMembersList.innerHTML = '';
        
        const memberItems = document.querySelectorAll('.member-item');
        memberItems.forEach(member => {
            const userName = member.querySelector('span').textContent;
            const roleName = member.querySelector('input[name$="[role_name]"]').value;
            
            const listItem = document.createElement('li');
            listItem.innerHTML = `<i class="fas fa-user-tie"></i> ${userName} (${roleName})`;
            reviewMembersList.appendChild(listItem);
        });
        
        // Goals
        const reviewGoalsList = document.getElementById('reviewGoals');
        reviewGoalsList.innerHTML = '';
        
        const goalInputs = document.querySelectorAll('input[name^="goals["][name$="][title]"]');
        goalInputs.forEach(input => {
            const title = input.value;
            if (title) {
                const listItem = document.createElement('li');
                listItem.innerHTML = `<i class="fas fa-check-circle"></i> ${title}`;
                reviewGoalsList.appendChild(listItem);
            }
        });
    }
    
    // Template selection
    function selectTemplate(button, templateName) {
        document.querySelectorAll('.template-card').forEach(card => {
            card.style.borderColor = '#ddd';
            card.querySelector('button').className = 'btn btn-outline';
            card.querySelector('button').textContent = 'Select';
        });
        
        button.closest('.template-card').style.borderColor = 'var(--accent)';
        button.className = 'btn btn-accent';
        button.textContent = 'Selected';
        document.getElementById('template').value = templateName;
    }
</script>
</body>
</html>