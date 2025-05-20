<?php
include('../SchedureEvent/connect.php');

// Initialize variables
$projects = [];
$all_members = [];
$all_activities = [];

// Function to get all projects
function getAllProjects($connection) {
    $projects = [];
    $query = "SELECT project_id, project_name FROM projects ORDER BY project_name";
    $result = mysqli_query($connection, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $projects[] = $row;
    }
    return $projects;
}

// Function to get project members (modified to return all members)
function getProjectMembers($connection, $project_id = null) {
    $members = [];
    $query = "SELECT 
                pm.project_id,
                u.user_id, 
                u.full_name, 
                u.email, 
                u.last_login,
                pm.role, 
                pm.joined_at
              FROM project_members pm
              JOIN users u ON pm.user_id = u.user_id";
    
    if ($project_id !== null) {
        $query .= " WHERE pm.project_id = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = mysqli_query($connection, $query);
    }
    
    while ($row = ($project_id !== null ? $result->fetch_assoc() : mysqli_fetch_assoc($result))) {
        $members[] = $row;
    }
    
    if ($project_id !== null) {
        $stmt->close();
    }
    
    return $members;
}

function getRecentActivities($connection, $assignee_id = null, $limit = 10) {
    $activities = [];
    
    $query = "SELECT 
                u.user_id,
                u.full_name,
                t.id,
                t.title,
                t.description,
                t.deadline,
                t.created_at AS timestamp,
                t.status
              FROM tasks t
              JOIN users u ON t.assignee_id = u.user_id";
    
    if ($assignee_id !== null) {
        $query .= " WHERE t.assignee_id = ?";
    }
    
    $query .= " ORDER BY t.created_at DESC LIMIT ?";
    
    $stmt = $connection->prepare($query);
    
    if ($assignee_id !== null) {
        $stmt->bind_param("ii", $assignee_id, $limit);
    } else {
        $stmt->bind_param("i", $limit);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $activities[] = $row;
    }
    
    $stmt->close();
    return $activities;
}




// Helper function to generate activity description
function generateTaskDescription($task) {
    $description = "Task '".htmlspecialchars($task['title'])."' was ";
    
    if ($task['status'] === 'completed') {
        $description .= "completed";
    } elseif ($task['status'] === 'in_progress') {
        $description .= "started working on";
    } else {
        $description .= "created";
    }
    
    return $description;
}

// Get all data from database
$projects = getAllProjects($connection);
$all_members = getProjectMembers($connection); // Get all members for all projects
$recent_activities = isset($connection) ? getRecentActivities($connection, $_SESSION['user_id'] ?? null, 10) : [];

// Helper functions with improved error handling
function getInitials($full_name) {
    if (empty($full_name)) {
        return '';
    }
    
    $initials = '';
    $parts = explode(' ', trim($full_name));
    foreach ($parts as $part) {
        if (!empty($part)) {
            $initials .= strtoupper(substr($part, 0, 1));
        }
    }
    return substr($initials, 0, 2);
}

function formatDate($dateString) {
    if (empty($dateString) || $dateString === '0000-00-00') {
        return 'No date';
    }
    
    try {
        $date = new DateTime($dateString);
        return $date->format('M j, Y');
    } catch (Exception $e) {
        return 'Invalid date';
    }
}

function formatDateTime($dateTimeString) {
    if (empty($dateTimeString) || $dateTimeString === '0000-00-00 00:00:00') {
        return 'No date/time';
    }
    
    try {
        $date = new DateTime($dateTimeString);
        return $date->format('M j, Y g:i a');
    } catch (Exception $e) {
        return 'Invalid date/time';
    }
}















// Handle Role Update
if (isset($_POST['update_role'])) {
    $member_id = $_POST['member_id'];
    $new_role = $_POST['new_role'];
    
    // List of all valid roles from your dropdown
    $valid_roles = [
        // Leadership & Management
        'executive_director', 'deputy_director', 'department_head', 'division_chief',
        'program_manager', 'project_manager', 'team_lead', 'unit_supervisor',
        'regional_coordinator', 'strategic_advisor', 'governance_officer', 'operations_manager',
        
        // Technical & Research
        'chief_scientist', 'senior_researcher', 'research_fellow', 'data_scientist',
        'statistician', 'technical_advisor', 'innovation_specialist', 'lab_manager',
        'field_researcher', 'evaluation_specialist', 'technology_architect', 'systems_analyst',
        'ai_specialist', 'gis_specialist', 'technical_writer',
        
        // Administration & Support
        'admin_officer', 'hr_manager', 'finance_officer', 'procurement_specialist',
        'logistics_coordinator', 'facilities_manager', 'executive_assistant', 'records_manager',
        'legal_advisor', 'compliance_officer', 'internal_auditor', 'security_officer',
        
        // Knowledge Management
        'km_strategist', 'knowledge_curator', 'information_specialist', 'content_manager',
        'documentation_specialist', 'taxonomy_expert', 'metadata_specialist', 'community_manager',
        'learning_developer', 'knowledge_analyst',
        
        // ICT & Digital
        'cio', 'systems_admin', 'database_admin', 'network_engineer',
        'software_developer', 'webmaster', 'cybersecurity_specialist', 'digital_transformation_lead',
        
        // Communication & Outreach
        'communications_director', 'public_relations_officer', 'media_specialist',
        'graphic_designer', 'multimedia_producer'
    ];
    
    // Validate inputs
    if (!empty($member_id) && in_array($new_role, $valid_roles)) {
        $stmt = $connection->prepare("UPDATE project_members SET role = ? WHERE user_id = ?");
        $stmt->bind_param("si", $new_role, $member_id);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "Role updated successfully to " . ucwords(str_replace('_', ' ', $new_role));
        } else {
            $_SESSION['error'] = "Error updating role: " . $connection->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Invalid role or member ID";
    }
    
    // Redirect back with project_id if it exists in URL
    $redirect_url = $_SERVER['PHP_SELF'];
    if (isset($_GET['project_id'])) {
        $redirect_url .= "?project_id=" . $_GET['project_id'];
    }
    header("Location: " . $redirect_url);
    exit();
}
// Handle Member Removal
if (isset($_POST['remove_member'])) {
    $member_id = $_POST['member_id'];
    
    if (!empty($member_id)) {
        $stmt = $connection->prepare("DELETE FROM project_members WHERE user_id = ?");
        $stmt->bind_param("i", $member_id);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "Member removed successfully!";
        } else {
            $_SESSION['error'] = "Error removing member: " . $connection->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Invalid member ID";
    }
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// Display messages if any
if (isset($_SESSION['message'])) {
    echo '<div class="alert success">'.htmlspecialchars($_SESSION['message']).'</div>';
    unset($_SESSION['message']);
}
if (isset($_SESSION['error'])) {
    echo '<div class="alert error">'.htmlspecialchars($_SESSION['error']).'</div>';
    unset($_SESSION['error']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Members Management</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f7fa;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin-left: 255px;
            margin-right: 30px;
            
            padding: 30px;
            padding-top: 100px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .project-title {
            margin: 0;
            color: #2c3e50;
        }
        .btn {
            padding: 8px 16px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-weight: 500;
        }
        .btn-primary {
            background-color: #3498db;
            color: white;
        }
        .btn-primary:hover {
            background-color: #2980b9;
        }
        .btn-danger {
            background-color: #e74c3c;
            color: white;
        }
        .btn-secondary {
            background-color: #95a5a6;
            color: white;
        }
        .members-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .members-table th, .members-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .members-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        .member-avatar {
            display: inline-block;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: #3498db;
            color: white;
            text-align: center;
            line-height: 36px;
            margin-right: 10px;
            font-weight: bold;
        }
        .member-name {
            display: inline-block;
            vertical-align: middle;
        }
        .role-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        .role-manager {
            background-color: #e3f2fd;
            color: #1976d2;
        }
        .role-contributor {
            background-color: #e8f5e9;
            color: #388e3c;
        }
        .role-viewer {
            background-color: #f3e5f5;
            color: #8e24aa;
        }
        .activity-log {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        .activity-item {
            display: flex;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .activity-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: #7f8c8d;
            color: white;
            text-align: center;
            line-height: 32px;
            margin-right: 15px;
            font-weight: bold;
            font-size: 12px;
        }
        .activity-content {
            flex: 1;
        }
        .activity-meta {
            font-size: 12px;
            color: #7f8c8d;
            margin-top: 4px;
        }
        .bulk-actions {
            display: none;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
            margin-bottom: 15px;
            align-items: center;
        }
        .bulk-actions.show {
            display: flex;
            gap: 10px;
        }
        .action-btn {
            padding: 4px 8px;
            border-radius: 4px;
            background-color: transparent;
            border: 1px solid #ddd;
            cursor: pointer;
            margin-right: 5px;
        }
        .action-btn.delete {
            color: #e74c3c;
            border-color: #e74c3c;
        }
        .action-btn.delete:hover {
            background-color: #fdecea;
        }
    </style>
</head>
<body>
<?php include("../Internees_task/header.php"); ?>
    <div class="container">
    <?php foreach ($projects as $project): ?>
        <?php 
        // Filter members for this project
        $project_members = array_filter($all_members, function($m) use ($project) {
            return $m['project_id'] == $project['project_id'];
        });
        
        // Filter activities for this project
        $project_activities = array_filter($all_activities, function($a) use ($project) {
            return $a['project_id'] == $project['project_id'];
        });
        ?>
        
        <div class="header">
            <h1 class="project-title"><?= htmlspecialchars($project['project_name']) ?> - Team Management</h1>
            <!-- <p>Project ID: <?= $project['project_id'] ?></p> -->
            <button class="btn btn-primary"><a href="../CollaborativeProjects/invitemember.php" style="text-decoration: none; color: white;">Invite Members</a></button>
        </div>
        
        <!-- Bulk Actions (hidden by default) -->
        <div class="bulk-actions" id="bulkActions">
            <div class="select-all">
                <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                <label for="selectAll">Select all</label>
            </div>
            <span id="selectedCount">0 selected</span>
           
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
                <?php foreach ($project_members as $member): ?>
                <tr data-id="<?= $member['user_id'] ?>">
                    <td><input type="checkbox" class="member-checkbox" onchange="updateSelectedCount()"></td>
                    <td>
                        <div class="member-avatar"><?= getInitials($member['full_name']) ?></div>
                        <div class="member-name">
                            <div><?= htmlspecialchars($member['full_name']) ?></div>
                            <small style="color: #7f8c8d;"><?= htmlspecialchars($member['email']) ?></small>
                        </div>
                    </td>
                    <td>
                        <span class="role-badge role-<?= $member['role'] ?>">
                            <?= ucfirst($member['role']) ?>
                        </span>
                    </td>
                    <td><?= formatDate($member['joined_at']) ?></td>
                    <td><?= $member['last_login'] ? formatDateTime($member['last_login']) : 'Never' ?></td>
                    <td>
    <?php if (isset($_GET['edit_role']) && $_GET['edit_role'] == $member['user_id']): ?>
        <!-- Role Edit Form -->
        <form method="post" style="display: inline;">
            <input type="hidden" name="member_id" value="<?= $member['user_id'] ?>">
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
            <button type="submit" name="update_role" class="action-btn">Save</button>
            <a href="?cancel=1" class="action-btn">Cancel</a>
        </form>
    <?php else: ?>
        <!-- Normal View -->
        <a href="?edit_role=<?= $member['user_id'] ?>" class="action-btn">Edit Role</a>
        
        <?php if ($member['role'] !== 'manager'): ?>
            <?php if (isset($_GET['confirm_remove']) && $_GET['confirm_remove'] == $member['user_id']): ?>
                <!-- Remove Confirmation -->
                <form method="post" style="display: inline;">
                    <input type="hidden" name="member_id" value="<?= $member['user_id'] ?>">
                    <span>Confirm?</span>
                    <button type="submit" name="remove_member" class="action-btn delete">Yes</button>
                    <a href="?cancel=1" class="action-btn">No</a>
                </form>
            <?php else: ?>
                <a href="?confirm_remove=<?= $member['user_id'] ?>" class="action-btn delete">Remove</a>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>
</td>

<?php
// Handle cancel action
if (isset($_GET['cancel'])) {
    header("Location: ".str_replace(['&edit_role='.$_GET['edit_role'], '&confirm_remove='.$_GET['confirm_remove']], '', $_SERVER['REQUEST_URI']));
    exit();
}
?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
   <!-- Activity Log -->
<div class="activity-log">
    <h3>Recent Activity</h3>
    <div id="activityFeed">
        <?php 
        // Get recent tasks (activities)
        $recent_tasks = getRecentActivities($connection, null, 10);
        
        if (empty($recent_tasks)): ?>
            <p>No recent activity found.</p>
        <?php else: ?>
            <?php foreach ($recent_tasks as $task): ?>
            <div class="activity-item">
                <div class="activity-avatar"><?= getInitials($task['full_name']) ?></div>
                <div class="activity-content">
                    <div>
                        <strong><?= htmlspecialchars($task['full_name']) ?></strong>
                        <?php if ($task['status'] == 'completed'): ?>
                            completed task:
                        <?php else: ?>
                            worked on task:
                        <?php endif; ?>
                        <strong><?= htmlspecialchars($task['title']) ?></strong>
                        <?php if (!empty($task['description'])): ?>
                            - <?= htmlspecialchars($task['description']) ?>
                        <?php endif; ?>
                    </div>
                    <div class="activity-meta">
                        <?php if (!empty($task['deadline'])): ?>
                            <span>Due: <?= formatDateTime($task['deadline']) ?></span>
                            <span>•</span>
                        <?php endif; ?>
                        <span><?= formatDateTime($task['timestamp']) ?></span>
                        <span>•</span>
                        <span class="status-<?= $task['status'] ?>"><?= ucfirst($task['status']) ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
        
        <hr style="margin: 40px 0; border: 0; border-top: 1px solid #eee;">
        
    <?php endforeach; ?>
    </div>
    
    <script>
     
    </script>
</body>
</html>