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
            margin-right: 100px;
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
    </style>
</head>
<body>
<?php include("../Internees_task/header.php"); ?>
    <div class="container">
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <h1 class="project-title">Website Redesign Project</h1>
            <div class="project-actions">
                <button class="btn btn-secondary">Export Report</button>
                <button class="btn btn-primary">Share Project</button>
            </div>
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
                            <span class="meta-label">Project Manager:</span>
                            <span>Sarah Johnson</span>
                        </div>
                    </div>
                    
                    <div class="progress-container">
                        <div class="meta-item">
                            <span class="meta-label">Completion:</span>
                            <span>65%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 65%"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Team Members -->
                <div class="sidebar-section">
                    <h3 class="sidebar-title">Team Members</h3>
                    <div class="team-members">
                        <div class="activity-item">
                            <div class="activity-avatar"></div>
                            <div class="activity-content">
                                <strong>Sarah Johnson</strong> (Manager)
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-avatar"></div>
                            <div class="activity-content">
                                <strong>John Smith</strong> (Developer)
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-avatar"></div>
                            <div class="activity-content">
                                <strong>Emily Davis</strong> (Designer)
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-avatar"></div>
                            <div class="activity-content">
                                <strong>Michael Brown</strong> (QA)
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Activity -->
                <div class="sidebar-section">
                    <h3 class="sidebar-title">Recent Activity</h3>
                    <div class="activity-feed">
                        <div class="activity-item">
                            <div class="activity-avatar"></div>
                            <div class="activity-content">
                                <strong>John Smith</strong> completed task "Homepage layout"
                                <div class="activity-time">2 hours ago</div>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-avatar"></div>
                            <div class="activity-content">
                                <strong>Emily Davis</strong> uploaded new file "design-specs.pdf"
                                <div class="activity-time">5 hours ago</div>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-avatar"></div>
                            <div class="activity-content">
                                <strong>Sarah Johnson</strong> updated project timeline
                                <div class="activity-time">Yesterday</div>
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
                                <span class="task-count">3</span>
                            </div>
                            <div class="task-list" id="backlog">
                                <div class="task-card" draggable="true">
                                    <div>
                                        <span class="task-priority priority-high"></span>
                                        <span class="task-priority-text">High</span>
                                    </div>
                                    <h4 class="task-title">Implement user authentication</h4>
                                    <div class="task-meta">
                                        <span>Due: Jul 15</span>
                                        <div class="task-assignee"></div>
                                    </div>
                                </div>
                                <div class="task-card" draggable="true">
                                    <div>
                                        <span class="task-priority priority-medium"></span>
                                        <span class="task-priority-text">Medium</span>
                                    </div>
                                    <h4 class="task-title">Create database schema</h4>
                                    <div class="task-meta">
                                        <span>Due: Jul 20</span>
                                        <div class="task-assignee"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- To Do Column -->
                        <div class="kanban-column">
                            <div class="column-header">
                                <span>To Do</span>
                                <span class="task-count">5</span>
                            </div>
                            <div class="task-list" id="todo">
                                <div class="task-card" draggable="true">
                                    <div>
                                        <span class="task-priority priority-high"></span>
                                        <span class="task-priority-text">High</span>
                                    </div>
                                    <h4 class="task-title">Design homepage layout</h4>
                                    <div class="task-meta">
                                        <span>Due: Jul 10</span>
                                        <div class="task-assignee"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- In Progress Column -->
                        <div class="kanban-column">
                            <div class="column-header">
                                <span>In Progress</span>
                                <span class="task-count">2</span>
                            </div>
                            <div class="task-list" id="inProgress">
                                <div class="task-card" draggable="true">
                                    <div>
                                        <span class="task-priority priority-medium"></span>
                                        <span class="task-priority-text">Medium</span>
                                    </div>
                                    <h4 class="task-title">Develop product API</h4>
                                    <div class="task-meta">
                                        <span>Due: Jul 25</span>
                                        <div class="task-assignee"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Done Column -->
                        <div class="kanban-column">
                            <div class="column-header">
                                <span>Done</span>
                                <span class="task-count">4</span>
                            </div>
                            <div class="task-list" id="done">
                                <div class="task-card" draggable="true">
                                    <div>
                                        <span class="task-priority priority-low"></span>
                                        <span class="task-priority-text">Low</span>
                                    </div>
                                    <h4 class="task-title">Project setup</h4>
                                    <div class="task-meta">
                                        <span>Completed: Jun 20</span>
                                        <div class="task-assignee"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- File Repository -->
                <div class="file-repository">
                    <div class="file-actions">
                        <h2>File Repository</h2>
                        <div class="file-upload">
                            <button class="btn btn-primary">Upload File</button>
                            <input type="file" multiple>
                        </div>
                    </div>
                    
                    <div class="file-grid">
                        <div class="file-card">
                            <div class="file-icon">📄</div>
                            <h4 class="file-name">Project Requirements.pdf</h4>
                            <div class="file-meta">
                                <div>Uploaded: Jun 10, 2023</div>
                                <div>Version: 1.2</div>
                                <div>Size: 2.4 MB</div>
                            </div>
                        </div>
                        <div class="file-card">
                            <div class="file-icon">🎨</div>
                            <h4 class="file-name">Design Mockups.sketch</h4>
                            <div class="file-meta">
                                <div>Uploaded: Jun 15, 2023</div>
                                <div>Version: 3.1</div>
                                <div>Size: 8.7 MB</div>
                            </div>
                        </div>
                        <div class="file-card">
                            <div class="file-icon">📊</div>
                            <h4 class="file-name">Project Timeline.xlsx</h4>
                            <div class="file-meta">
                                <div>Uploaded: Jun 18, 2023</div>
                                <div>Version: 2.0</div>
                                <div>Size: 1.1 MB</div>
                            </div>
                        </div>
                        <div class="file-card">
                            <div class="file-icon">📝</div>
                            <h4 class="file-name">Meeting Notes.docx</h4>
                            <div class="file-meta">
                                <div>Uploaded: Jun 22, 2023</div>
                                <div>Version: 1.0</div>
                                <div>Size: 0.5 MB</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Project Timeline & Reporting -->
                <div class="project-timeline">
                    <h2>Project Timeline</h2>
                    <div class="gantt-chart" id="ganttChart">
                        <!-- Gantt chart will be rendered here -->
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
            
            <form id="taskForm">
                <div class="form-group">
                    <label for="taskTitle">Task Title</label>
                    <input type="text" id="taskTitle" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="taskDescription">Description</label>
                    <textarea id="taskDescription" class="form-control" rows="4"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="taskPriority">Priority</label>
                    <select id="taskPriority" class="form-control">
                        <option value="high">High</option>
                        <option value="medium" selected>Medium</option>
                        <option value="low">Low</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="taskAssignee">Assignee</label>
                    <select id="taskAssignee" class="form-control">
                        <option value="">Unassigned</option>
                        <option value="1">Sarah Johnson</option>
                        <option value="2">John Smith</option>
                        <option value="3">Emily Davis</option>
                        <option value="4">Michael Brown</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="taskDeadline">Deadline</label>
                    <input type="date" id="taskDeadline" class="form-control">
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Task</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- File Preview Modal -->
    <div class="modal" id="fileModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>File Preview</h3>
                <button class="close-modal" onclick="closeModal()">×</button>
            </div>
            
            <div class="file-preview-content">
                <div class="file-preview-header">
                    <div class="file-icon-lg">📄</div>
                    <div>
                        <h4 id="fileName">Document.pdf</h4>
                        <div class="file-meta">
                            <span id="fileVersion">Version: 1.0</span>
                            <span id="fileSize">Size: 2.4 MB</span>
                            <span id="fileUploadDate">Uploaded: Jun 10, 2023</span>
                        </div>
                    </div>
                </div>
                
                <div class="version-history">
                    <h5>Version History</h5>
                    <ul class="version-list">
                        <li>
                            <span>Version 1.2</span>
                            <span>Jun 15, 2023</span>
                            <span>2.4 MB</span>
                            <button class="btn btn-sm">Download</button>
                        </li>
                        <li>
                            <span>Version 1.1</span>
                            <span>Jun 12, 2023</span>
                            <span>2.1 MB</span>
                            <button class="btn btn-sm">Download</button>
                        </li>
                        <li>
                            <span>Version 1.0</span>
                            <span>Jun 10, 2023</span>
                            <span>2.0 MB</span>
                            <button class="btn btn-sm">Download</button>
                        </li>
                    </ul>
                </div>
                
                <div class="file-actions">
                    <button class="btn btn-primary">Download Current</button>
                    <button class="btn btn-secondary">Upload New Version</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialize Kanban Board Drag and Drop
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize SortableJS for kanban columns
            new Sortable(document.getElementById('backlog'), {
                group: 'tasks',
                animation: 150,
                ghostClass: 'dragging-task'
            });
            
            new Sortable(document.getElementById('todo'), {
                group: 'tasks',
                animation: 150,
                ghostClass: 'dragging-task'
            });
            
            new Sortable(document.getElementById('inProgress'), {
                group: 'tasks',
                animation: 150,
                ghostClass: 'dragging-task'
            });
            
            new Sortable(document.getElementById('done'), {
                group: 'tasks',
                animation: 150,
                ghostClass: 'dragging-task'
            });
            
            // Initialize Charts
            initProgressChart();
            initGanttChart();
            
            // File card click event
            document.querySelectorAll('.file-card').forEach(card => {
                card.addEventListener('click', function() {
                    document.getElementById('fileModal').style.display = 'flex';
                });
            });
            
            // Task form submission
            document.getElementById('taskForm').addEventListener('submit', function(e) {
                e.preventDefault();
                createNewTask();
                closeModal();
            });
        });
        
        // Modal Functions
        function openTaskModal() {
            document.getElementById('taskModal').style.display = 'flex';
        }
        
        function closeModal() {
            document.querySelectorAll('.modal').forEach(modal => {
                modal.style.display = 'none';
            });
        }
        
        // Create New Task
        function createNewTask() {
            const title = document.getElementById('taskTitle').value;
            const description = document.getElementById('taskDescription').value;
            const priority = document.getElementById('taskPriority').value;
            const assigneeId = document.getElementById('taskAssignee').value;
            const deadline = document.getElementById('taskDeadline').value;
            
            // In a real app, you would add to your data model and update UI
            const taskHtml = `
                <div class="task-card" draggable="true">
                    <div>
                        <span class="task-priority priority-${priority}"></span>
                        <span class="task-priority-text">${priority.charAt(0).toUpperCase() + priority.slice(1)}</span>
                    </div>
                    <h4 class="task-title">${title}</h4>
                    <div class="task-meta">
                        <span>${deadline ? 'Due: ' + formatDate(deadline) : 'No deadline'}</span>
                        <div class="task-assignee"></div>
                    </div>
                </div>
            `;
            
            // Add to backlog column
            document.getElementById('backlog').insertAdjacentHTML('beforeend', taskHtml);
            
            // Update task count
            updateTaskCounts();
            
            // Reset form
            document.getElementById('taskForm').reset();
        }
        
        function updateTaskCounts() {
            const columns = ['backlog', 'todo', 'inProgress', 'done'];
            columns.forEach(col => {
                const count = document.getElementById(col).children.length;
                document.querySelector(`#${col} + .column-header .task-count`).textContent = count;
            });
        }
        
        // Chart Initialization
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
        
        function initGanttChart() {
            // In a real app, you would use a proper Gantt chart library
            // This is a simplified visualization
            const ganttData = [
                { task: 'Planning', start: '2023-06-15', end: '2023-06-25', progress: 100 },
                { task: 'Design', start: '2023-06-20', end: '2023-07-10', progress: 85 },
                { task: 'Development', start: '2023-07-01', end: '2023-08-15', progress: 65 },
                { task: 'Testing', start: '2023-08-10', end: '2023-09-01', progress: 30 },
                { task: 'Deployment', start: '2023-09-01', end: '2023-09-15', progress: 5 }
            ];
            
            // Simplified rendering for demo
            const ganttChart = document.getElementById('ganttChart');
            ganttChart.innerHTML = `
                <div style="display: flex; height: 100%; padding: 20px; flex-direction: column; gap: 15px;">
                    ${ganttData.map(item => `
                        <div style="margin-bottom: 5px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                <span>${item.task}</span>
                                <span>${formatDate(item.start)} - ${formatDate(item.end)}</span>
                            </div>
                            <div style="height: 20px; background: #eee; border-radius: 10px; overflow: hidden;">
                                <div style="height: 100%; width: ${item.progress}%; background: ${getProgressColor(item.progress)};"></div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            `;
        }
        
        function getProgressColor(progress) {
            if (progress > 80) return '#2ecc71';
            if (progress > 50) return '#3498db';
            if (progress > 20) return '#f39c12';
            return '#e74c3c';
        }
        
        function formatDate(dateString) {
            const options = { year: 'numeric', month: 'short', day: 'numeric' };
            return new Date(dateString).toLocaleDateString('en-US', options);
        }
    </script>
</body>
</html>