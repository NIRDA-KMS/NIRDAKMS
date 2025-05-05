<?php
session_start();
include('../SchedureEvent/connect.php');

// Database connection
if (!$connection) {
    die("Database connection failed");
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle task creation
    if (isset($_POST['create_task'])) {
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $priority = $_POST['priority'] ?? 'medium';
        $status = $_POST['status'] ?? 'backlog';
        $assignee_id = $_POST['assignee_id'] ?? null;
        $deadline = $_POST['deadline'] ?? null;

        if (!empty($title)) {
            $stmt = $connection->prepare("INSERT INTO tasks (title, description, status, priority, assignee_id, deadline) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssis", $title, $description, $status, $priority, $assignee_id, $deadline);
            
            if ($stmt->execute()) {
                header("Location: ".$_SERVER['HTTP_REFERER']);
                // echo json_encode(['success' => true, 'message' => 'Task created successfully!']);
            } else {
                echo json_encode(['success' => false, 'message' => "Error creating task: " . $stmt->error]);
            }
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => "Title is required"]);
        }
        exit();
    }




    // Handle file upload
    if (isset($_FILES['file'])) {
        header('Content-Type: application/json');
        
        try {
            $file = $_FILES['file'];
            $uploadDir = 'storage/';
            $allowedTypes = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'application/pdf' => 'pdf',
                'text/plain' => 'txt',
                'application/msword' => 'doc',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx'
            ];
            $maxFileSize = 10 * 1024 * 1024; // 10MB

            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Check for upload errors
            if ($file['error'] !== UPLOAD_ERR_OK) {
                throw new Exception(match($file['error']) {
                    UPLOAD_ERR_INI_SIZE => "File exceeds server's upload_max_filesize",
                    UPLOAD_ERR_FORM_SIZE => "File exceeds form's MAX_FILE_SIZE",
                    UPLOAD_ERR_PARTIAL => "File was only partially uploaded",
                    UPLOAD_ERR_NO_FILE => "No file was uploaded",
                    UPLOAD_ERR_NO_TMP_DIR => "Missing temporary folder",
                    UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk",
                    UPLOAD_ERR_EXTENSION => "File upload stopped by PHP extension",
                    default => "Unknown upload error"
                });
            }
            
            // Validate file type
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $fileType = $finfo->file($file['tmp_name']);
            if (!array_key_exists($fileType, $allowedTypes)) {
                throw new Exception("File type not allowed. Allowed types: JPG, PNG, PDF, TXT, DOC, DOCX");
            }
            
            // Validate file size
            if ($file['size'] > $maxFileSize) {
                throw new Exception("File size exceeds maximum allowed (10MB).");
            }
            
            // Sanitize filename
            $originalName = basename($file['name']);
            $extension = $allowedTypes[$fileType];
            $safeName = uniqid() . '.' . $extension;
            $destination = $uploadDir . $safeName;
            
            // // Move uploaded file to storage
            if (!move_uploaded_file($file['tmp_name'], $destination)) {
                throw new Exception("Failed to move uploaded file.");
            }
            
            // Store file info in database
            $stmt = $connection->prepare("INSERT INTO files (filename, filepath, size, type) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssis", $originalName, $destination, $file['size'], $fileType);
            
            if ($stmt->execute()) {
                $fileId = $stmt->insert_id;
                $stmt->close();
                
                // // Get the newly uploaded file to return as JSON
                // $stmt = $connection->prepare("SELECT id, filename, filepath, size, type FROM files WHERE id = ?");
                // $stmt->bind_param("i", $fileId);
                // $stmt->execute();
                // $result = $stmt->get_result();
                // $newFile = $result->fetch_assoc();
                // $stmt->close();
                
                // Format the file data
                $formattedFile = [
                    'id' => $newFile['id'],
                    'name' => $newFile['filename'],
                    'path' => $newFile['filepath'],
                    'size' => formatSizeUnits($newFile['size']),
                    'type' => $newFile['type'],
                    'uploaded' => date('M j, Y'),
                    'ext' => strtolower(pathinfo($newFile['filename'], PATHINFO_EXTENSION))
                ];
                
                // echo json_encode([
                //     'success' => true,
                //     'message' => 'File uploaded successfully!',
                //     'file' => $formattedFile
                // ]);
                header("Location: ".$_SERVER['HTTP_REFERER']);
            } else {
                throw new Exception("Failed to save file information to database.");
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'File upload error: ' . $e->getMessage()
            ]);
        }
        exit();
    }
}

// Get all tasks
$tasks = [];
$result = $connection->query("SELECT * FROM tasks ORDER BY FIELD(status, 'backlog', 'todo', 'in_progress', 'done'), deadline");
if ($result) {
    $tasks = $result->fetch_all(MYSQLI_ASSOC);
    $result->free();
}

// Get team members
$team_members = [];
try {
    $query = "SELECT u.user_id, u.full_name, pm.role FROM users u JOIN project_members pm ON u.user_id = pm.user_id";
    $stmt = $connection->prepare($query);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $team_members[] = [
                'id' => $row['user_id'],
                'name' => $row['full_name'],
                'role' => $row['role']
            ];
        }
    }
    $stmt->close();
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
}

// Calculate project progress
$total_tasks = count($tasks);
$completed_tasks = 0;
foreach ($tasks as $task) {
    if ($task['status'] === 'done') {
        $completed_tasks++;
    }
}
$progress = $total_tasks > 0 ? round(($completed_tasks / $total_tasks) * 100) : 0;

// Get files from database
$files = [];
try {
    $query = "SELECT id, filename, filepath, size, type, uploaded_at FROM files ORDER BY uploaded_at DESC";
    $result = $connection->query($query);
    while ($row = $result->fetch_assoc()) {
        $files[] = [
            'id' => $row['id'],
            'name' => $row['filename'],
            'path' => $row['filepath'],
            'size' => formatSizeUnits($row['size']),
            'type' => $row['type'],
            'uploaded' => date('M j, Y', strtotime($row['uploaded_']))
        ];
    }
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
}

function formatSizeUnits($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        return $bytes . ' bytes';
    } elseif ($bytes == 1) {
        return $bytes . ' byte';
    }
    return '0 bytes';
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Management Dashboard</title>
    <!-- Chart.js for visualizations -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- SortableJS for drag and drop -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
    <style>
/* File Repository Styles */
.file-repository {
    background: white;
    border-radius: 8px;
    padding: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    margin-bottom: 30px;
}

.file-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    flex-wrap: wrap;
    gap: 15px;
}

.file-upload {
    display: flex;
    align-items: center;
    gap: 15px;
    background: #f8f9fa;
    padding: 12px 20px;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.file-upload:hover {
    background: #e9ecef;
}

.file-upload input[type="file"] {
    display: none;
}

.file-upload label {
    background: #007bff;
    color: white;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-block;
}

.file-upload label:hover {
    background: #0069d9;
}

.file-upload button {
    background: #28a745;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s;
}

.file-upload button:hover {
    background: #218838;
}

.file-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.file-card {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 18px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    display: flex;
    flex-direction: column;
}

.file-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    border-color: #007bff;
}

.file-icon {
    font-size: 42px;
    text-align: center;
    margin-bottom: 15px;
    color: #6c757d;
}

.file-icon.pdf { color: #e74c3c; }
.file-icon.image { color: #3498db; }
.file-icon.doc { color: #2c3e50; }
.file-icon.xls { color: #27ae60; }
.file-icon.zip { color: #f39c12; }
.file-icon.other { color: #9b59b6; }

.file-name {
    font-weight: 600;
    margin-bottom: 8px;
    font-size: 15px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.file-meta {
    margin-top: auto;
    font-size: 13px;
    color: #6c757d;
}

.file-meta div {
    margin-bottom: 5px;
    display: flex;
    align-items: center;
}

.file-meta i {
    margin-right: 8px;
    width: 18px;
    text-align: center;
    color: #adb5bd;
}

.file-actions-bottom {
    display: flex;
    justify-content: space-between;
    margin-top: 15px;
    padding-top: 10px;
    border-top: 1px solid #eee;
}

.file-download {
    color: #007bff;
    text-decoration: none;
    font-size: 13px;
    display: inline-flex;
    align-items: center;
    transition: all 0.3s;
}

.file-download:hover {
    color: #0056b3;
    text-decoration: underline;
}

.file-delete {
    color: #dc3545;
    cursor: pointer;
    font-size: 13px;
    display: inline-flex;
    align-items: center;
    transition: all 0.3s;
}

.file-delete:hover {
    color: #c82333;
}

.upload-message {
    padding: 12px 15px;
    border-radius: 4px;
    margin: 10px 0;
    font-size: 14px;
}

.upload-message.success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.upload-message.error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .file-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    }
    
    .file-actions {
        flex-direction: column;
        align-items: flex-start;
    }
}

@media (max-width: 480px) {
    .file-grid {
        grid-template-columns: 1fr;
    }
    
    .file-upload {
        width: 100%;
        flex-direction: column;
        align-items: flex-start;
    }
}


        /* Your existing CSS styles here */


         /* Base Styles */
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
            padding: 0%px;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            background-color: #f5f7fa;
            color: #333;
            padding-top: 1500px;
        }
        
        .container {
            max-width: 750px;
            margin: 0 auto;
            padding: 20px;
            padding-top: 100px;
            margin-right: 200px;
            margin-bottom: 50px;
        }
        
        /* Header */
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #ddd;
        }
        
        .project-title {
            font-size: 24px;
            color: var(--secondary-color);
        }
        
        .project-actions {
            display: flex;
            gap: 10px;
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
        
        .btn-secondary {
            background: var(--light-gray);
            color: var(--secondary-color);
        }
        
        .btn-secondary:hover {
            background: #ddd;
        }
        
        /* Dashboard Layout */
        .dashboard-grid {
            display: flex;
            /* grid-template-columns: 300px 1fr; */
            gap: 20px;
        }
        
        /* Sidebar */
        .sidebar {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .sidebar-section {
            margin-bottom: 25px;
        }
        
        .sidebar-title {
            font-size: 18px;
            color: var(--secondary-color);
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 1px solid #eee;
        }
        
        /* Project Overview */
        .project-meta {
            margin-bottom: 15px;
        }
        
        .meta-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .meta-label {
            color: var(--dark-gray);
            font-weight: 500;
        }
        
        .progress-container {
            margin: 15px 0;
        }
        
        .progress-bar {
            height: 8px;
            background: #eee;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 5px;
        }
        
        .progress-fill {
            height: 100%;
            background: var(--primary-color);
            width: 65%; /* Dynamic value */
        }
        
        /* Activity Feed */
        .activity-item {
            display: flex;
            margin-bottom: 15px;
            font-size: 14px;
        }
        
        .activity-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #ddd;
            margin-right: 10px;
            flex-shrink: 0;
        }
        
        .activity-content {
            flex-grow: 1;
        }
        
        .activity-time {
            color: var(--dark-gray);
            font-size: 12px;
            margin-top: 3px;
        }
        
        /* Main Content */
        .main-content {
            display: grid;
            grid-template-rows: auto auto 1fr;
            gap: 20px;
        }
        
        /* Kanban Board */
        .kanban-board {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .kanban-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .kanban-columns {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }
        
        .kanban-column {
            background: var(--light-gray);
            border-radius: 6px;
            padding: 15px;
            min-height: 400px;
        }
        
        .column-header {
            font-weight: 600;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .task-count {
            background: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 12px;
        }
        
        .task-list {
            min-height: 300px;
        }
        
        .task-card {
            background: white;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            cursor: pointer;
        }
        
        .task-card:hover {
            box-shadow: 0 2px 5px rgba(0,0,0,0.15);
        }
        
        .task-priority {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 5px;
        }
        
        .priority-high {
            background: var(--accent-color);
        }
        
        .priority-medium {
            background: var(--warning-color);
        }
        
        .priority-low {
            background: var(--success-color);
        }
        
        .task-title {
            font-weight: 500;
            margin: 5px 0;
        }
        
        .task-meta {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: var(--dark-gray);
            margin-top: 8px;
        }
        
        .task-assignee {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #ddd;
        }
        
        /* File Repository */
        .file-repository {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .file-actions {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        
        .file-upload {
            position: relative;
            overflow: hidden;
            display: inline-block;
        }
        
        .file-upload input {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        
        .file-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .file-card {
            border: 1px solid #eee;
            border-radius: 6px;
            padding: 15px;
            transition: all 0.3s;
        }
        
        .file-card:hover {
            border-color: var(--primary-color);
        }
        
        .file-icon {
            font-size: 36px;
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 10px;
        }
        
        .file-name {
            font-weight: 500;
            margin-bottom: 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .file-meta {
            font-size: 12px;
            color: var(--dark-gray);
        }
        
        /* Project Timeline */
        .project-timeline {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .gantt-chart {
            width: 100%;
            height: 400px;
            background: #f9f9f9;
            border: 1px solid #eee;
            border-radius: 4px;
            margin-top: 15px;
            position: relative;
            overflow-x: auto;
        }
        
        /* Reporting */
        .reporting-section {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .chart-container {
            position: relative;
            height: 300px;
            margin-top: 15px;
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
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            padding: 25px;
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
        
        /* Responsive */
        @media (max-width: 1200px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            
            .kanban-columns {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 768px) {
            .kanban-columns {
                grid-template-columns: 1fr;
            }
            
            .file-grid {
                grid-template-columns: 1fr 1fr;
            }
        }



















        .file-table-container {
    overflow-x: auto;
    margin-top: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.file-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
}

.file-table th {
    background-color: #f5f5f5;
    padding: 12px 15px;
    text-align: left;
    font-weight: 600;
    border-bottom: 2px solid #ddd;
}

.file-table td {
    padding: 12px 15px;
    border-bottom: 1px solid #eee;
    vertical-align: middle;
}

.file-table tr:last-child td {
    border-bottom: none;
}

.file-table tr:hover {
    background-color: #f9f9f9;
}

.file-type {
    width: 50px;
    text-align: center;
    color: #555;
    font-size: 1.2em;
}

.file-name {
    font-weight: 500;
    word-break: break-word;
}

.file-size, .file-date {
    white-space: nowrap;
    color: #666;
}

.file-actions {
    white-space: nowrap;
    text-align: right;
}

.btn-download, .btn-delete {
    display: inline-block;
    padding: 5px 8px;
    border-radius: 4px;
    color: white;
    text-decoration: none;
    margin-left: 5px;
    transition: all 0.2s;
}

.btn-download {
    background: #4CAF50;
}

.btn-download:hover {
    background: #3e8e41;
}

.btn-delete {
    background: #f44336;
}

.btn-delete:hover {
    background: #d32f2f;
}

.no-files {
    text-align: center;
    color: #777;
    font-style: italic;
    padding: 20px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .file-table th, .file-table td {
        padding: 8px 10px;
    }
    
    .file-actions {
        text-align: center;
    }
}
    </style>
</head>
<body>
<?php include("../Internees_task/header.php"); ?>

<!-- Display messages -->
<?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<div class="container">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <h1 class="project-title">Website Redesign Project</h1>
        <!-- <div class="project-actions">
            <button class="btn btn-secondary">Export Report</button>
            <button class="btn btn-primary">Share Project</button>
        </div> -->
    </div>
    
    <!-- Dashboard Grid -->
    <div class="dashboard-grid">
        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Project Overview -->
            <div class="sidebar-section">
                <h3 class="sidebar-title">Project Overview</h3>
                <div class="project-meta">
                    <div class="meta-item">
                        <span class="meta-label">Status:</span>
                        <span>In Progress</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Start Date:</span>
                        <span>Jun 15, 2023</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">End Date:</span>
                        <span>Sep 30, 2023</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Completion:</span>
                        <span><?php echo $progress; ?>%</span>
                    </div>
                </div>
                
                <div class="progress-container">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo $progress; ?>%"></div>
                    </div>
                </div>
            </div>
            
            <!-- Team Members -->
            <div class="sidebar-section">
                <h3 class="sidebar-title">Team Members</h3>
                <div class="team-members">
                    <?php foreach ($team_members as $member): ?>
                        <div class="activity-item">
                            <div class="activity-avatar" style="background-color: #<?php echo substr(md5($member['id']), 0, 6); ?>"></div>
                            <div class="activity-content">
                                <strong><?php echo htmlspecialchars($member['name']); ?></strong> (<?php echo htmlspecialchars($member['role']); ?>)
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Recent Activity -->
            <div class="sidebar-section">
                <h3 class="sidebar-title">Recent Activity</h3>
                <div class="activity-feed">
                    <div class="activity-item">
                        <div class="activity-avatar" style="background-color: #<?php echo substr(md5(2), 0, 6); ?>"></div>
                        <div class="activity-content">
                            <strong>John Smith</strong> completed task "Homepage layout"
                            <div class="activity-time">2 hours ago</div>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-avatar" style="background-color: #<?php echo substr(md5(3), 0, 6); ?>"></div>
                        <div class="activity-content">
                            <strong>Emily Davis</strong> uploaded new file "design-specs.pdf"
                            <div class="activity-time">5 hours ago</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Kanban Board -->
            <div class="kanban-board">
                <div class="kanban-header">
                    <h2>Task Management</h2>
                    <button class="btn btn-primary" onclick="openTaskModal()">+ New Task</button>
                </div>
                
                <div class="kanban-columns" id="kanbanBoard">
                    <!-- Backlog Column -->
                    <div class="kanban-column">
                        <div class="column-header">
                            <span>Backlog</span>
                            <span class="task-count"><?php echo count(array_filter($tasks, function($t) { return $t['status'] == 'backlog'; })); ?></span>
                        </div>
                        <div class="task-list" id="backlog">
                            <?php foreach ($tasks as $task): ?>
                                <?php if ($task['status'] == 'backlog'): ?>
                                    <div class="task-card" draggable="true" data-task-id="<?php echo $task['id']; ?>">
                                        <div>
                                            <span class="task-priority priority-<?php echo $task['priority']; ?>"></span>
                                            <span class="task-priority-text"><?php echo ucfirst($task['priority']); ?></span>
                                        </div>
                                        <h4 class="task-title"><?php echo htmlspecialchars($task['title']); ?></h4>
                                        <div class="task-meta">
                                            <span>Due: <?php echo htmlspecialchars($task['deadline']); ?></span>
                                            <div class="task-assignee" style="background-color: #<?php echo substr(md5($task['assignee_id']), 0, 6); ?>"></div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- To Do Column -->
                    <div class="kanban-column">
                        <div class="column-header">
                            <span>To Do</span>
                            <span class="task-count"><?php echo count(array_filter($tasks, function($t) { return $t['status'] == 'todo'; })); ?></span>
                        </div>
                        <div class="task-list" id="todo">
                            <?php foreach ($tasks as $task): ?>
                                <?php if ($task['status'] == 'todo'): ?>
                                    <div class="task-card" draggable="true" data-task-id="<?php echo $task['id']; ?>">
                                        <div>
                                            <span class="task-priority priority-<?php echo $task['priority']; ?>"></span>
                                            <span class="task-priority-text"><?php echo ucfirst($task['priority']); ?></span>
                                        </div>
                                        <h4 class="task-title"><?php echo htmlspecialchars($task['title']); ?></h4>
                                        <div class="task-meta">
                                            <span>Due: <?php echo htmlspecialchars($task['deadline']); ?></span>
                                            <div class="task-assignee" style="background-color: #<?php echo substr(md5($task['assignee_id']), 0, 6); ?>"></div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- In Progress Column -->
                    <div class="kanban-column">
                        <div class="column-header">
                            <span>In Progress</span>
                            <span class="task-count"><?php echo count(array_filter($tasks, function($t) { return $t['status'] == 'in_progress'; })); ?></span>
                        </div>
                        <div class="task-list" id="inProgress">
                            <?php foreach ($tasks as $task): ?>
                                <?php if ($task['status'] == 'in_progress'): ?>
                                    <div class="task-card" draggable="true" data-task-id="<?php echo $task['id']; ?>">
                                        <div>
                                            <span class="task-priority priority-<?php echo $task['priority']; ?>"></span>
                                            <span class="task-priority-text"><?php echo ucfirst($task['priority']); ?></span>
                                        </div>
                                        <h4 class="task-title"><?php echo htmlspecialchars($task['title']); ?></h4>
                                        <div class="task-meta">
                                            <span>Due: <?php echo htmlspecialchars($task['deadline']); ?></span>
                                            <div class="task-assignee" style="background-color: #<?php echo substr(md5($task['assignee_id']), 0, 6); ?>"></div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Done Column -->
                    <div class="kanban-column">
                        <div class="column-header">
                            <span>Done</span>
                            <span class="task-count"><?php echo count(array_filter($tasks, function($t) { return $t['status'] == 'done'; })); ?></span>
                        </div>
                        <div class="task-list" id="done">
                            <?php foreach ($tasks as $task): ?>
                                <?php if ($task['status'] == 'done'): ?>
                                    <div class="task-card" draggable="true" data-task-id="<?php echo $task['id']; ?>">
                                        <div>
                                            <span class="task-priority priority-<?php echo $task['priority']; ?>"></span>
                                            <span class="task-priority-text"><?php echo ucfirst($task['priority']); ?></span>
                                        </div>
                                        <h4 class="task-title"><?php echo htmlspecialchars($task['title']); ?></h4>
                                        <div class="task-meta">
                                            <span>Completed: <?php echo date('M j, Y', strtotime($task['updated_at'] ?? $task['created_at'])); ?></span>
                                            <div class="task-assignee" style="background-color: #<?php echo substr(md5($task['assignee_id']), 0, 6); ?>"></div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>


 <!--file repository -->


            <div class="file-repository">
    <div class="file-actions">
        <h2>File Repository</h2>
        
       
        
        <!-- File Upload Form -->
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data" class="file-upload-form">
            <div class="file-upload">
                <input type="file" name="file" id="file-upload-input" required>
                <label for="file-upload-input">Choose File</label>
                <span id="file-name-display">No file chosen</span>
                <button type="submit" name="upload" class="btn-upload">Upload</button>
                <button type="button" id="view-files-btn" class="btn-upload">View Files</button>
            </div>
        </form>
    </div>
    
   <!-- File Display Section -->
<div class="file-display" id="file-display-section" style="display: none;">
    <h3>Uploaded Files</h3>
    <div class="file-table-container">
        <table class="file-table">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>File Name</th>
                    <th>Size</th>
                    <th>Uploaded</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Query all files from database
                $query = "SELECT id, filename, filepath, size, type, uploaded FROM files ORDER BY uploaded DESC";
                $result = $connection->query($query);
                
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $fileExt = strtolower(pathinfo($row['filename'], PATHINFO_EXTENSION));
                        echo '<tr class="file-row">';
                        
                        // File Type Icon
                        echo '<td class="file-type">';
                        switch($fileExt) {
                            case 'pdf':
                                echo '<i class="fas fa-file-pdf" title="PDF Document"></i>';
                                break;
                            case 'doc':
                            case 'docx':
                                echo '<i class="fas fa-file-word" title="Word Document"></i>';
                                break;
                            case 'jpg':
                            case 'jpeg':
                            case 'png':
                                echo '<i class="fas fa-file-image" title="Image File"></i>';
                                break;
                            case 'txt':
                                echo '<i class="fas fa-file-alt" title="Text File"></i>';
                                break;
                            default:
                                echo '<i class="fas fa-file" title="File"></i>';
                        }
                        echo '</td>';
                        
                        // File Name
                        echo '<td class="file-name">' . htmlspecialchars($row['filename']) . '</td>';
                        
                        // File Size
                        echo '<td class="file-size">' . formatSizeUnits($row['size']) . '</td>';
                        
                        // Upload Date
                        echo '<td class="file-date">' . date('M j, Y', strtotime($row['uploaded'])) . '</td>';
                        
                        // Actions
                        echo '<td class="file-actions">';
                        echo '<a href="download.php?id=' . $row['id'] . '" class="btn-download" title="Download"><i class="fas fa-download"></i></a>';
                        echo '<a href="delete.php?id=' . $row['id'] . '" class="btn-delete" title="Delete" onclick="return confirm(\'Are you sure you want to delete this file?\')"><i class="fas fa-trash"></i></a>';
                        echo '</td>';
                        
                        echo '</tr>';
                    }
                    $result->free();
                } else {
                    echo '<tr><td colspan="5" class="no-files">No files uploaded yet.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
</div>

    

            
            <!-- Project Timeline & Reporting -->
            <div class="project-timeline">
                <h2>Project Timeline</h2>
                <div class="gantt-chart" id="ganttChart">
                    <div style="display: flex; height: 100%; padding: 20px; flex-direction: column; gap: 15px;">
                        <div style="margin-bottom: 5px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                <span>Planning</span>
                                <span>Jun 15 - Jun 25</span>
                            </div>
                            <div style="height: 20px; background: #eee; border-radius: 10px; overflow: hidden;">
                                <div style="height: 100%; width: 100%; background: #2ecc71;"></div>
                            </div>
                        </div>
                        <div style="margin-bottom: 5px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                <span>Design</span>
                                <span>Jun 20 - Jul 10</span>
                            </div>
                            <div style="height: 20px; background: #eee; border-radius: 10px; overflow: hidden;">
                                <div style="height: 100%; width: 85%; background: #3498db;"></div>
                            </div>
                        </div>
                        <div style="margin-bottom: 5px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                <span>Development</span>
                                <span>Jul 1 - Aug 15</span>
                            </div>
                            <div style="height: 20px; background: #eee; border-radius: 10px; overflow: hidden;">
                                <div style="height: 100%; width: 65%; background: #3498db;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="reporting-section">
                <h2>Project Reports</h2>
                <div class="chart-container">
                    <canvas id="progressChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Task Modal -->
<div class="modal" id="taskModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Create New Task</h3>
            <button class="close-modal" onclick="closeModal()">×</button>
        </div>
        
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
            <input type="hidden" name="create_task" value="1">
            
            <div class="form-group">
                <label for="taskTitle">Task Title</label>
                <input type="text" id="taskTitle" class="form-control" name="title" required>
            </div>
            
            <div class="form-group">
                <label for="taskDescription">Description</label>
                <textarea id="taskDescription" name="description" class="form-control" rows="4"></textarea>
            </div>
            
            <div class="form-group">
                <label for="taskPriority">Priority</label>
                <select id="taskPriority" class="form-control" name="priority">
                    <option value="high">High</option>
                    <option value="medium" selected>Medium</option>
                    <option value="low">Low</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="taskStatus">Status</label>
                <select id="taskStatus" class="form-control" name="status">
                    <option value="backlog" selected>Backlog</option>
                    <option value="todo">To Do</option>
                    <option value="in_progress">In Progress</option>
                    <option value="done">Done</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="taskAssignee">Assignee</label>
                <select id="taskAssignee" class="form-control" name="assignee_id">
                    <option value="">Unassigned</option>
                    <?php foreach ($team_members as $member): ?>
                        <option value="<?php echo $member['id']; ?>"><?php echo htmlspecialchars($member['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="taskDeadline">Deadline</label>
                <input type="date" id="taskDeadline" name="deadline" class="form-control">
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Task</button>
            </div>
        </form>
    </div>
</div>

<script>
// Initialize Kanban Board Drag and Drop
document.addEventListener('DOMContentLoaded', function() {
    // Initialize SortableJS for kanban columns
    new Sortable(document.getElementById('backlog'), {
        group: 'tasks',
        animation: 150,
        ghostClass: 'dragging-task',
        onEnd: function(evt) {
            updateTaskStatus(evt.item.dataset.taskId, 'backlog');
        }
    });
    
    new Sortable(document.getElementById('todo'), {
        group: 'tasks',
        animation: 150,
        ghostClass: 'dragging-task',
        onEnd: function(evt) {
            updateTaskStatus(evt.item.dataset.taskId, 'todo');
        }
    });
    
    new Sortable(document.getElementById('inProgress'), {
        group: 'tasks',
        animation: 150,
        ghostClass: 'dragging-task',
        onEnd: function(evt) {
            updateTaskStatus(evt.item.dataset.taskId, 'in_progress');
        }
    });
    
    new Sortable(document.getElementById('done'), {
        group: 'tasks',
        animation: 150,
        ghostClass: 'dragging-task',
        onEnd: function(evt) {
            updateTaskStatus(evt.item.dataset.taskId, 'done');
        }
    });
    
    // Initialize Charts
    initProgressChart();
});

function updateTaskStatus(taskId, newStatus) {
    fetch('update_task.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'task_id=' + taskId + '&status=' + newStatus
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            alert('Failed to update task status');
        }
    });
}

function initProgressChart() {
    const ctx = document.getElementById('progressChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Planning', 'Design', 'Development', 'Testing', 'Deployment'],
            datasets: [{
                label: 'Completion %',
                data: [100, 85, 65, 30, 5],
                backgroundColor: [
                    '#2ecc71',
                    '#3498db',
                    '#3498db',
                    '#f39c12',
                    '#e74c3c'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
}

// Modal Functions
function openTaskModal() {
    document.getElementById('taskModal').style.display = 'flex';
}

function closeModal() {
    document.querySelectorAll('.modal').forEach(modal => {
        modal.style.display = 'none';
    });
}







document.addEventListener('DOMContentLoaded', function() {
    // Show selected filename
    const fileInput = document.getElementById('file-upload-input');
    const fileNameDisplay = document.getElementById('file-name-display');
    
    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            fileNameDisplay.textContent = this.files[0].name;
        } else {
            fileNameDisplay.textContent = 'No file chosen';
        }
    });

    // Toggle file display section
    const viewFilesBtn = document.getElementById('view-files-btn');
    const fileDisplaySection = document.getElementById('file-display-section');
    
    viewFilesBtn.addEventListener('click', function() {
        if (fileDisplaySection.style.display === 'none') {
            fileDisplaySection.style.display = 'block';
            this.textContent = 'Hide Files';
        } else {
            fileDisplaySection.style.display = 'none';
            this.textContent = 'View Files';
        }
    });
});



</script>
</body>
</html>