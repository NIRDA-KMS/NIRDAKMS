<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Project</title>
    <!-- TinyMCE for rich text editor -->
    <script src="https://cdn.tiny.cloud/1/yy21cxb9sz8dz5s1jswqcenpziyj0y4frg79dtifqqamfxbf/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
    <style>
    /* Updated to match header.css variables */
    :root {
        --primary-color: #1a237e;
        --secondary-color: #2c3e50;
        --accent-color: #00A0DF;
        --background-color: #ecf0f1;
        --text-color: #34495e;
    }

    body {
        font-family: 'Roboto', sans-serif;
        line-height: 1.6;
        background-color: #f0f2f5;
        color: #333;
        padding-top: 110px; /* Adjusted for fixed header (50px) + nav (60px) */
    }

    .container {
        width: 1000px;
        margin-top: 100px;
        background: white;
        margin-left: 255px;
        
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin-bottom: 100px; /* Space for footer */
    }

    /* Wizard Header - Matched to nav styling */
    .wizard-header {
        display: flex;
        background: #1a237e;
        padding: 0;
    }

    .wizard-step {
        flex: 1;
        text-align: center;
        padding: 15px;
        position: relative;
        color: rgba(255, 255, 255, 0.8);
        font-weight: 400;
        transition: all 0.3s ease;
    }

    .wizard-step.active {
        color: white;
        font-weight: 500;
        background-color: rgba(0, 160, 223, 0.2);
    }

    .wizard-step.completed {
        color: #2ecc71;
    }

    .wizard-step::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        width: 0;
        height: 3px;
        background: var(--accent-color);
        transition: width 0.3s ease;
    }

    .wizard-step.active::after {
        width: 100%;
        left: 0;
    }

    /* Wizard Content */
    .wizard-content {
        padding: 30px;
    }

    .step-panel {
        display: none;
    }

    .step-panel.active {
        display: block;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    /* Form Styles - Matched to header theme */
    .form-group {
        margin-bottom: 20px;
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
        transition: border-color 0.3s;
        font-family: 'Roboto', sans-serif;
    }

    .form-control:focus {
        border-color: var(--accent-color);
        outline: none;
        box-shadow: 0 0 0 2px rgba(0, 160, 223, 0.2);
    }

    textarea.form-control {
        min-height: 120px;
        resize: vertical;
    }

    /* Buttons - Matched to header button styles */
    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s;
        font-size: 14px;
        font-family: 'Roboto', sans-serif;
    }

    .btn-primary {
        background: var(--accent-color);
        color: white;
    }

    .btn-primary:hover {
        background: #0088cc;
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .btn-secondary {
        background: var(--background-color);
        color: var(--secondary-color);
    }

    .btn-secondary:hover {
        background: #d5dbdb;
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .btn-outline {
        background: transparent;
        border: 1px solid var(--accent-color);
        color: var(--accent-color);
    }

    .btn-outline:hover {
        background: rgba(0, 160, 223, 0.1);
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    /* Member Selection - Updated colors */
    .member-selection {
        display: flex;
        gap: 20px;
        margin-top: 20px;
    }

    .available-members, .selected-members {
        flex: 1;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 15px;
        background: #f9f9f9;
        min-height: 300px;
    }

    .member-item:hover {
        background: rgba(0, 160, 223, 0.1);
    }

    /* Goals Section */
    .goal-item:hover {
        border-color: var(--accent-color);
    }

    /* Templates */
    .template-card:hover {
        border-color: var(--accent-color);
    }

    .template-card.selected {
        border: 2px solid var(--accent-color);
        background: rgba(0, 160, 223, 0.1);
    }

    /* Review Section */
    .review-section {
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid #eee;
    }

    .review-section h3 {
        color: var(--primary-color);
        margin-bottom: 15px;
        padding-bottom: 5px;
        border-bottom: 1px solid #eee;
    }

    .review-item {
        margin-bottom: 10px;
    }

    .review-text {
        background: #f9f9f9;
        padding: 10px;
        border-radius: 4px;
        border-left: 3px solid var(--accent-color);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        body {
            padding-top: 135px; /* Adjusted for mobile header height */
        }
        
        .container {
           margin-top: 200px;
            padding-top: 100px;
            border-radius: 0;
        }
        
        .wizard-content {
            padding: 15px;
        }
        
        .member-selection {
            flex-direction: column;
        }
        
        .date-picker-group {
            flex-direction: column;
            gap: 10px;
        }
        
        .wizard-step {
            font-size: 14px;
            padding: 10px 5px;
        }
        
        .btn {
            padding: 8px 15px;
        }
    }

    @media (max-width: 480px) {
        body {
            padding-top: 160px; /* Extra space for mobile navigation */
        }
    }
</style>
</head>
<body>
<?php include("../Internees_task/header.php"); ?>
    <div class="container">
        <!-- Wizard Header -->
        <div class="wizard-header">
            <div class="wizard-step active" data-step="1">
                <span>1. Basic Info</span>
            </div>
            <div class="wizard-step" data-step="2">
                <span>2. Team Setup</span>
            </div>
            <div class="wizard-step" data-step="3">
                <span>3. Goals</span>
            </div>
            <div class="wizard-step" data-step="4">
                <span>4. Review</span>
            </div>
        </div>
        
        <!-- Wizard Content -->
        <div class="wizard-content">
            <!-- Step 1: Basic Info -->
            <div class="step-panel active" id="step1">
                <h2>Project Information</h2>
                <p class="subtitle">Enter basic details about your project</p>
                
                <div class="form-group">
                    <label for="projectName">Project Name</label>
                    <input type="text" id="projectName" class="form-control" placeholder="e.g. Website Redesign Project">
                </div>
                
                <div class="form-group">
                    <label for="projectDescription">Description</label>
                    <textarea id="projectDescription" class="form-control" placeholder="Describe your project in detail..."></textarea>
                </div>
                
                <div class="form-group">
                    <label>Project Timeline</label>
                    <div class="date-picker-group">
                        <div class="date-picker">
                            <label for="startDate">Start Date</label>
                            <input type="date" id="startDate" class="form-control">
                        </div>
                        <div class="date-picker">
                            <label for="endDate">End Date</label>
                            <input type="date" id="endDate" class="form-control">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Project Template (Optional)</label>
                    <div class="template-options">
                        <div class="template-card" data-template="web-dev">
                            <h4>Web Development</h4>
                            <p>Standard phases for website projects</p>
                        </div>
                        <div class="template-card" data-template="marketing">
                            <h4>Marketing Campaign</h4>
                            <p>Template for marketing initiatives</p>
                        </div>
                        <div class="template-card" data-template="product">
                            <h4>Product Launch</h4>
                            <p>Product development workflow</p>
                        </div>
                        <div class="template-card" data-template="research">
                            <h4>Research Project</h4>
                            <p>Academic research framework</p>
                        </div>
                    </div>
                </div>
                
                <div class="wizard-actions">
                    <div></div> <!-- Empty div for spacing -->
                    <button class="btn btn-primary" onclick="nextStep()">Next: Team Setup</button>
                </div>
            </div>
            
            <!-- Step 2: Team Setup -->
            <div class="step-panel" id="step2">
                <h2>Team Members</h2>
                <p class="subtitle">Add collaborators and assign roles</p>
                
                <div class="member-selection">
                    <div class="available-members">
                        <h3>Available Members</h3>
                        <ul class="member-list" id="availableMembers">
                            <li class="member-item" draggable="true" data-id="1">
                                <span>John Smith</span>
                                <span class="member-role">Not assigned</span>
                            </li>
                            <li class="member-item" draggable="true" data-id="2">
                                <span>Sarah Johnson</span>
                                <span class="member-role">Not assigned</span>
                            </li>
                            <li class="member-item" draggable="true" data-id="3">
                                <span>Michael Brown</span>
                                <span class="member-role">Not assigned</span>
                            </li>
                            <li class="member-item" draggable="true" data-id="4">
                                <span>Emily Davis</span>
                                <span class="member-role">Not assigned</span>
                            </li>
                            <li class="member-item" draggable="true" data-id="5">
                                <span>David Wilson</span>
                                <span class="member-role">Not assigned</span>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="selected-members">
                        <h3>Project Team</h3>
                        <div class="role-tabs">
                            <button class="role-tab active" data-role="manager">Managers</button>
                            <button class="role-tab" data-role="contributor">Contributors</button>
                            <button class="role-tab" data-role="viewer">Viewers</button>
                        </div>
                        <ul class="member-list" id="projectTeam">
                            <!-- Members will be added here via drag and drop -->
                        </ul>
                    </div>
                </div>
                
                <div class="wizard-actions">
                    <button class="btn btn-secondary" onclick="prevStep()">Back</button>
                    <button class="btn btn-primary" onclick="nextStep()">Next: Goals</button>
                </div>
            </div>
            
            <!-- Step 3: Goals -->
            <div class="step-panel" id="step3">
                <h2>Project Goals</h2>
                <p class="subtitle">Define your project objectives and milestones</p>
                
                <div id="goalsContainer">
                    <div class="goal-item">
                        <button class="remove-goal" onclick="removeGoal(this)">×</button>
                        <div class="form-group">
                            <label>Goal Title</label>
                            <input type="text" class="form-control" placeholder="e.g. Complete homepage design">
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" placeholder="Describe this goal in detail..."></textarea>
                        </div>
                        <div class="form-group">
                            <label>Target Date</label>
                            <input type="date" class="form-control">
                        </div>
                    </div>
                </div>
                
                <button class="btn btn-outline" onclick="addGoal()">+ Add Another Goal</button>
                
                <div class="wizard-actions">
                    <button class="btn btn-secondary" onclick="prevStep()">Back</button>
                    <button class="btn btn-primary" onclick="nextStep()">Next: Review</button>
                </div>
            </div>
            
            <!-- Step 4: Review -->
            <div class="step-panel" id="step4">
                <h2>Review Project</h2>
                <p class="subtitle">Verify all details before creating your project</p>
                
                <div class="review-section">
                    <h3>Project Information</h3>
                    <div class="review-item">
                        <strong>Project Name:</strong>
                        <span id="reviewName">Not specified</span>
                    </div>
                    <div class="review-item">
                        <strong>Description:</strong>
                        <div id="reviewDescription" class="review-text">Not specified</div>
                    </div>
                    <div class="review-item">
                        <strong>Timeline:</strong>
                        <span id="reviewTimeline">Not specified</span>
                    </div>
                </div>
                
                <div class="review-section">
                    <h3>Project Team</h3>
                    <div id="reviewTeam">
                        <p>No team members added yet</p>
                    </div>
                </div>
                
                <div class="review-section">
                    <h3>Project Goals</h3>
                    <div id="reviewGoals">
                        <p>No goals defined yet</p>
                    </div>
                </div>
                
                <div class="wizard-actions">
                    <button class="btn btn-secondary" onclick="prevStep()">Back</button>
                    <button class="btn btn-primary" onclick="submitProject()">Create Project</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialize TinyMCE for rich text editor
        tinymce.init({
            selector: '#projectDescription',
            plugins: 'lists link',
            toolbar: 'bold italic | bullist numlist | link',
            menubar: false,
            height: 200
        });
        
        // Wizard Navigation
        let currentStep = 1;
        
        function nextStep() {
            if (validateCurrentStep()) {
                document.getElementById(`step${currentStep}`).classList.remove('active');
                document.querySelector(`.wizard-step[data-step="${currentStep}"]`).classList.remove('active');
                
                currentStep++;
                
                document.getElementById(`step${currentStep}`).classList.add('active');
                document.querySelector(`.wizard-step[data-step="${currentStep}"]`).classList.add('active');
                
                if (currentStep === 4) {
                    updateReviewSection();
                }
            }
        }
        
        function prevStep() {
            document.getElementById(`step${currentStep}`).classList.remove('active');
            document.querySelector(`.wizard-step[data-step="${currentStep}"]`).classList.remove('active');
            
            currentStep--;
            
            document.getElementById(`step${currentStep}`).classList.add('active');
            document.querySelector(`.wizard-step[data-step="${currentStep}"]`).classList.add('active');
        }
        
        function validateCurrentStep() {
            if (currentStep === 1) {
                const projectName = document.getElementById('projectName').value.trim();
                if (!projectName) {
                    alert('Please enter a project name');
                    return false;
                }
            }
            return true;
        }
        
        // Template Selection
        document.querySelectorAll('.template-card').forEach(card => {
            card.addEventListener('click', function() {
                document.querySelectorAll('.template-card').forEach(c => c.classList.remove('selected'));
                this.classList.add('selected');
            });
        });
        
        // Drag and Drop for Team Members
        let draggedItem = null;
        
        document.querySelectorAll('.member-item').forEach(item => {
            item.addEventListener('dragstart', function() {
                draggedItem = this;
                setTimeout(() => this.classList.add('dragging'), 0);
            });
            
            item.addEventListener('dragend', function() {
                this.classList.remove('dragging');
            });
        });
        
        document.querySelectorAll('.member-list').forEach(list => {
            list.addEventListener('dragover', function(e) {
                e.preventDefault();
                const afterElement = getDragAfterElement(this, e.clientY);
                if (afterElement == null) {
                    this.appendChild(draggedItem);
                } else {
                    this.insertBefore(draggedItem, afterElement);
                }
            });
        });
        
        function getDragAfterElement(container, y) {
            const draggableElements = [...container.querySelectorAll('.member-item:not(.dragging)')];
            
            return draggableElements.reduce((closest, child) => {
                const box = child.getBoundingClientRect();
                const offset = y - box.top - box.height / 2;
                if (offset < 0 && offset > closest.offset) {
                    return { offset: offset, element: child };
                } else {
                    return closest;
                }
            }, { offset: Number.NEGATIVE_INFINITY }).element;
        }
        
        // Role Assignment
        document.querySelectorAll('.role-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.role-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                const role = this.dataset.role;
                if (draggedItem) {
                    const roleSpan = draggedItem.querySelector('.member-role');
                    roleSpan.textContent = role.charAt(0).toUpperCase() + role.slice(1);
                    roleSpan.style.background = getRoleColor(role);
                }
            });
        });
        
        function getRoleColor(role) {
            const colors = {
                manager: '#3498db',
                contributor: '#2ecc71',
                viewer: '#95a5a6'
            };
            return colors[role] || '#eee';
        }
        
        // Goals Management
        function addGoal() {
            const goalsContainer = document.getElementById('goalsContainer');
            const goalId = Date.now();
            
            const goalHtml = `
                <div class="goal-item">
                    <button class="remove-goal" onclick="removeGoal(this)">×</button>
                    <div class="form-group">
                        <label>Goal Title</label>
                        <input type="text" class="form-control" placeholder="e.g. Complete homepage design">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" placeholder="Describe this goal in detail..."></textarea>
                    </div>
                    <div class="form-group">
                        <label>Target Date</label>
                        <input type="date" class="form-control">
                    </div>
                </div>
            `;
            
            goalsContainer.insertAdjacentHTML('beforeend', goalHtml);
        }
        
        function removeGoal(button) {
            if (document.querySelectorAll('.goal-item').length > 1) {
                button.closest('.goal-item').remove();
            } else {
                alert('A project must have at least one goal');
            }
        }
        
        // Review Section Update
        function updateReviewSection() {
            // Project Info
            document.getElementById('reviewName').textContent = 
                document.getElementById('projectName').value || 'Not specified';
            
            document.getElementById('reviewDescription').innerHTML = 
                tinymce.get('projectDescription').getContent() || 'Not specified';
            
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            document.getElementById('reviewTimeline').textContent = 
                startDate && endDate ? `${formatDate(startDate)} to ${formatDate(endDate)}` : 'Not specified';
            
            // Team Members
            const teamMembers = document.getElementById('projectTeam').children;
            if (teamMembers.length > 0) {
                let teamHtml = '<ul>';
                Array.from(teamMembers).forEach(member => {
                    const name = member.querySelector('span:first-child').textContent;
                    const role = member.querySelector('.member-role').textContent;
                    teamHtml += `<li><strong>${name}</strong> (${role})</li>`;
                });
                teamHtml += '</ul>';
                document.getElementById('reviewTeam').innerHTML = teamHtml;
            }
            
            // Goals
            const goals = document.querySelectorAll('.goal-item');
            if (goals.length > 0) {
                let goalsHtml = '<ul>';
                goals.forEach(goal => {
                    const title = goal.querySelector('input[type="text"]').value || 'Untitled goal';
                    const description = goal.querySelector('textarea').value || 'No description';
                    const date = goal.querySelector('input[type="date"]').value;
                    
                    goalsHtml += `
                        <li>
                            <strong>${title}</strong>
                            <p>${description}</p>
                            ${date ? `<small>Target: ${formatDate(date)}</small>` : ''}
                        </li>
                    `;
                });
                goalsHtml += '</ul>';
                document.getElementById('reviewGoals').innerHTML = goalsHtml;
            }
        }
        
        function formatDate(dateString) {
            if (!dateString) return '';
            const options = { year: 'numeric', month: 'short', day: 'numeric' };
            return new Date(dateString).toLocaleDateString('en-US', options);
        }
        
        // Form Submission
        function submitProject() {
            // Collect all form data
            const projectData = {
                name: document.getElementById('projectName').value,
                description: tinymce.get('projectDescription').getContent(),
                startDate: document.getElementById('startDate').value,
                endDate: document.getElementById('endDate').value,
                template: document.querySelector('.template-card.selected')?.dataset.template,
                members: [],
                goals: []
            };
            
            // Get team members
            const teamMembers = document.getElementById('projectTeam').children;
            Array.from(teamMembers).forEach(member => {
                projectData.members.push({
                    id: member.dataset.id,
                    name: member.querySelector('span:first-child').textContent,
                    role: member.querySelector('.member-role').textContent.toLowerCase()
                });
            });
            
            // Get goals
            const goals = document.querySelectorAll('.goal-item');
            goals.forEach(goal => {
                projectData.goals.push({
                    title: goal.querySelector('input[type="text"]').value,
                    description: goal.querySelector('textarea').value,
                    targetDate: goal.querySelector('input[type="date"]').value
                });
            });
            
            console.log('Project data to submit:', projectData);
            
            // In a real app, you would use AJAX to submit the data
            /*
            fetch('/api/projects', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(projectData)
            })
            .then(response => response.json())
            .then(data => {
                alert('Project created successfully!');
                window.location.href = `/projects/${data.id}`;
            })
            .catch(error => {
                console.error('Error:', error);
                alert('There was an error creating the project');
            });
            */
            
            // For demo purposes, just show an alert
            alert('Project creation functionality would submit to your backend API\nCheck console for collected data');
        }
    </script>
</body>
</html>