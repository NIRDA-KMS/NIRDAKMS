<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum Management System</title>
    <script src="https://cdn.tiny.cloud/1/yy21cxb9sz8dz5s1jswqcenpziyj0y4frg79dtifqqamfxbf/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    <style>
           /* Reset and Base Styles */
           * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            
            width: 100%;
            padding-top: 60px; /* Adjust based on your header's fixed height */
            
        }

        /*Layout Styles */
         .main-container {
            
            margin: 80px auto 20px auto;   /*Adjust top margin to be greater than padding-top  */
            padding: 0 15px;
            padding-left: 255px;
        }   

        .forum-layout {
            display: inline-block;
            width: 1000px;
            grid-template-columns: 250px 1fr;
            gap: 20px;
            bottom: -20 px;
        }

        /* Sidebar Styles */
        .sidebar {
            background-color: #fff;
            border-radius: 5px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .category-list h3 {
            padding: 8px;
            background: #2c3e50;
            color: #fff;
            border-radius: 4px;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .category-list ul {
            list-style: none;
        }

        .category-list li {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }

        .category-list a {
            color: #3498db;
            text-decoration: none;
            transition: color 0.3s;
        }

        .category-list a:hover {
            color: #1a6ea0;
        }

        .forum-stats {
            margin-top: 20px;
        }

        .forum-stats h3 {
            padding: 8px;
            background: #2c3e50;
            color: #fff;
            border-radius: 4px;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .forum-stats p {
            margin-bottom: 5px;
            font-size: 14px;
        }

        /* Main Content Styles */
        .main-content {
            background-color: #fff;
            border-radius: 5px;
            padding: 90px;
             
            
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .topic-list-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .topic-list-header h2 {
            font-size: 20px;
            color: #2c3e50;
        }

        /* Button Styles */
        .btn {
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            border: none;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-primary {
            background-color: #3498db;
            color: #fff;
        }

        .btn-primary:hover {
            background-color: #2980b9;
        }

        .btn-secondary {
            background-color: #95a5a6;
            color: #fff;
        }

        .btn-secondary:hover {
            background-color: #7f8c8d;
        }

        .btn-danger {
            background-color: #e74c3c;
            color: #fff;
        }

        .btn-danger:hover {
            background-color: #c0392b;
        }

        .create-topic-btn,
        .review-report-btn {
            /* You might need to adjust this if your header has specific button styles */
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            border: none;
            font-weight: 500;
            transition: all 0.3s;
            background-color: #3498db;
            color: #fff;
        }

        .review-report-btn {
            margin-top: 20px;
        }

        /* Topic Item Styles */
        .topic-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
            position: relative;
            transition: background-color 0.3s;
        }

        .topic-item:hover {
            background-color: #f9f9f9;
        }

        .topic-title {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .topic-meta {
            font-size: 13px;
            color: #7f8c8d;
            margin-bottom: 5px;
        }

        .topic-stats {
            display: flex;
            gap: 15px;
            font-size: 13px;
            color: #7f8c8d;
        }

        .pinned-badge {
            background-color: #2ecc71;
            color: #fff;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 12px;
            margin-left: 10px;
            display: inline-block;
        }

        .mod-controls {
            position: absolute;
            top: 15px;
            right: 15px;
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .mod-btn {
            background: none;
            border: none;
            color: #7f8c8d;
            cursor: pointer;
            font-size: 14px;
            transition: color 0.3s;
        }

        .mod-btn:hover {
            color: #3498db;
        }

        /* Status Toggle Styles */
        .status-toggle {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 40px;
            height: 20px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.4s;
            border-radius: 20px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 2px;
            bottom: 2px;
            background-color: #fff;
            transition: 0.4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: #2ecc71;
        }

        input:checked + .slider:before {
            transform: translateX(20px);
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000; /* Ensure it's below the header's z-index if the header is fixed and has a higher z-index */
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            width: 90%;
            max-width: 800px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal h2 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 22px;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #2c3e50;
        }

        .form-group input[type="text"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        /* Reply Styles */
        .reply {
            margin-left: 30px;
            padding: 10px;
            border-left: 2px solid #eee;
            margin-bottom: 15px;
        }

        .reply-content {
            margin-bottom: 10px;
            font-size: 14px;
        }

        .reply-meta {
            font-size: 12px;
            color: #7f8c8d;
            margin-bottom: 5px;
        }

        .flag-btn {
            background: none;
            border: none;
            color: #e74c3c;
            cursor: pointer;
            font-size: 12px;
            margin-left: 10px;
            transition: color 0.3s;
        }

        .flag-btn:hover {
            color: #c0392b;
        }

        .flag-form {
            display: none;
            padding: 10px;
            background: #fff5f5;
            margin-top: 5px;
            border-radius: 4px;
        }

        /* State Styles */
        .flagged {
            border-left: 3px solid #e74c3c;
            background-color: #fff5f5;
        }

        .deactivated {
            opacity: 0.6;
            background-color: #f9f9f9;
            border-left: 3px solid #95a5a6;
        }

        /* Admin Panel Styles */
        .admin-tabs {
            display: flex;
            border-bottom: 1px solid #ddd;
            margin-bottom: 15px;
        }

        .admin-tab {
            padding: 8px 15px;
            background: none;
            border: none;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            font-weight: 500;
            color: #7f8c8d;
            transition: all 0.3s;
        }

        .admin-tab.active {
            color: #3498db;
            border-bottom-color: #3498db;
        }

        .admin-tab-content {
            display: none;
        }

        .admin-tab-content.active {
            display: block;
        }

        .flagged-item {
            padding: 15px;
            margin-bottom: 15px;
            border-left: 3px solid #e74c3c;
            background-color: #fff5f5;
            border-radius: 4px;
        }

        .flagged-item p {
            margin-bottom: 8px;
        }

        .admin-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .forum-layout {
                grid-template-columns: 1fr;
            }

            .mod-controls {
                position: static;
                margin-top: 10px;
                justify-content: flex-end;
            }

            .reply {
                margin-left: 15px;
            }

            .admin-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <?php include '../Internees_task/header.php'; ?>
    
    <div class="main-container">
        <div class="forum-layout">
            <aside class="sidebar">
                <div class="category-list">
                    <h3>Categories</h3>
                    <ul>
                        <li><a href="#">General Discussion</a></li>
                        <li><a href="#">Technical Support</a></li>
                        <li><a href="#">Feature Requests</a></li>
                        <li><a href="#">Announcements</a></li>
                    </ul>
                </div>
                
                <div class="forum-stats">
                    <h3>Forum Statistics</h3>
                    <p>Topics: 124</p>
                    <p>Posts: 892</p>
                    <p>Members: 342</p>
                </div>
            </aside>
            
            <main class="main-content">
                <div class="topic-list-header">
                    <h2 style="padding-left: 200px;">Recent Topics</h2>
                    <button class="btn btn-primary" id="createTopicBtn">Create New Topic</button>
                </div>
                
                <div class="topic-list" id="topicList">
                    <!-- Topics will be loaded here -->
                </div>

                <button class="btn btn-primary review-report-btn" id="reviewReports">
                    Review Reports
                </button>
            </main>
        </div>
    </div>
    
    <!-- Create Topic Modal -->
    <div class="modal" id="createTopicModal">
        <div class="modal-content">
            <h2>Create New Topic</h2>
            <form id="topicForm">
                <div class="form-group">
                    <label for="topicCategory">Category</label>
                    <select id="topicCategory" required>
                        <option value="">Select a category</option>
                        <option value="1">General Discussion</option>
                        <option value="2">Technical Support</option>
                        <option value="3">Feature Requests</option>
                        <option value="4">Announcements</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="topicTitle">Title</label>
                    <input type="text" id="topicTitle" required>
                </div>
                
                <div class="form-group">
                    <label for="topicContent">Content</label>
                    <textarea id="topicContent" style="height: 300px;"></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" id="cancelTopic">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Topic</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Topic View Modal -->
    <div class="modal" id="topicViewModal">
        <div class="modal-content">
            <div class="topic-header">
                <h2 id="viewTopicTitle"></h2>
                <div class="topic-meta" id="viewTopicMeta"></div>
            </div>
            
            <div class="topic-content" id="viewTopicContent" style="margin: 20px 0;"></div>
            
            <div class="topic-replies" id="topicReplies">
                <h3>Replies</h3>
                <!-- Replies will be loaded here -->
            </div>
            
            <div class="reply-form" style="margin-top: 20px;">
                <h3>Post a Reply</h3>
                <textarea id="replyContent" style="width: 100%; height: 150px; margin-bottom: 10px;"></textarea>
                <button class="btn btn-primary" id="submitReply">Post Reply</button>
            </div>
        </div>
    </div>
    
    <!-- Admin Panel Modal -->
    <div class="modal" id="reviewReportsModal">
        <div class="modal-content">
            <h2>Admin Review Reports</h2>
            
            <div class="admin-tabs">
                <button class="admin-tab active" data-tab="flagged">Flagged Content</button>
                <button class="admin-tab" data-tab="users">User Management</button>
                <button class="admin-tab" data-tab="categories">Categories</button>
            </div>
            
            <div class="admin-tab-content active" id="flaggedContent" style="margin-top: 20px;">
                <h3>Flagged Posts <span id="flag-count">(0)</span></h3>
                <div class="flagged-posts-list" id="flaggedPostsList">
                    <!-- Flagged posts will be loaded here -->
                </div>
            </div>
            
            <div class="admin-tab-content" id="usersContent" style="margin-top: 20px;">
                <h3>User Management</h3>
                <p>User management content goes here</p>
            </div>
            
            <div class="admin-tab-content" id="categoriesContent" style="margin-top: 20px;">
                <h3>Category Management</h3>
                <p>Category management content goes here</p>
            </div>
        </div>
    </div>
    
    <script>
        // Document Ready Function
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize TinyMCE
            initTinyMCE();
            
            // Initialize the application
            initApp();
        });

        /**
         * Initialize TinyMCE editor
         */
        function initTinyMCE() {
            tinymce.init({
                selector: '#topicContent',
                plugins: 'link lists code',
                toolbar: 'bold italic | bullist numlist | link code',
                menubar: false,
                content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; }'
            });
        }

        /**
         * Main application initialization
         */
        function initApp() {
            // DOM Elements
            const createTopicBtn = document.getElementById('createTopicBtn');
            const createTopicModal = document.getElementById('createTopicModal');
            const cancelTopicBtn = document.getElementById('cancelTopic');
            const topicForm = document.getElementById('topicForm');
            const topicList = document.getElementById('topicList');
            const reviewReportsBtn = document.getElementById('reviewReports');
            const reviewReportsModal = document.getElementById('reviewReportsModal');
            const topicViewModal = document.getElementById('topicViewModal');
            
            // Sample Data (in a real app, this would come from an API)
            const topics = [
                {
                    id: 1,
                    title: "How to use the new forum features?",
                    category: "General Discussion",
                    author: "admin",
                    date: "2023-05-15",
                    replies: 5,
                    views: 124,
                    pinned: true,
                    active: true,
                    content: "<p>Welcome to our new forum! Here's how to use the new features...</p>",
                    repliesList: [
                        {
                            id: 101,
                            author: "user1",
                            date: "2023-05-16",
                            content: "Thanks for the guide! Very helpful.",
                            flagged: false
                        },
                        {
                            id: 102,
                            author: "user2",
                            date: "2023-05-17",
                            content: "I'm having trouble with the editor. Can you help?",
                            flagged: true,
                            flagReason: "Request for help"
                        }
                    ]
                },
                {
                    id: 2,
                    title: "Bug report: Login issues",
                    category: "Technical Support",
                    author: "user3",
                    date: "2023-05-14",
                    replies: 3,
                    views: 87,
                    pinned: false,
                    active: true,
                    content: "<p>I'm experiencing issues when trying to log in...</p>",
                    repliesList: [
                        {
                            id: 201,
                            author: "admin",
                            date: "2023-05-14",
                            content: "We're looking into this issue. Thanks for reporting.",
                            flagged: false
                        }
                    ]
                }
            ];
            
            // Event Listeners
            createTopicBtn.addEventListener('click', showCreateTopicModal);
            cancelTopicBtn.addEventListener('click', hideCreateTopicModal);
            reviewReportsBtn.addEventListener('click', showReviewReportsModal);
            topicForm.addEventListener('submit', handleTopicFormSubmit);
            
            // Close modals when clicking outside
            document.addEventListener('click', handleModalOutsideClick);
            
            // Admin tab switching
            setupAdminTabs();
            
            // Initialize the page
            loadTopics();
            
            /**
             * Show create topic modal
             */
            function showCreateTopicModal() {
                createTopicModal.style.display = 'flex';
            }

            /**
             * Hide create topic modal
             */
            function hideCreateTopicModal() {
                createTopicModal.style.display = 'none';
            }

            /**
             * Show review reports modal
             */
            function showReviewReportsModal(e) {
                e.preventDefault();
                reviewReportsModal.style.display = 'flex';
                loadFlaggedContent();
            }

            /**
             * Handle topic form submission
             */
            function handleTopicFormSubmit(e) {
                e.preventDefault();
                createNewTopic();
            }

            /**
             * Handle modal outside click
             */
            function handleModalOutsideClick(e) {
                if (e.target.classList.contains('modal')) {
                    e.target.style.display = 'none';
                }
            }

            /**
             * Setup admin tabs functionality
             */
            function setupAdminTabs() {
                document.querySelectorAll('.admin-tab').forEach(tab => {
                    tab.addEventListener('click', () => {
                        // Update tab styles
                        document.querySelectorAll('.admin-tab').forEach(t => t.classList.remove('active'));
                        tab.classList.add('active');
                        
                        // Update content visibility
                        document.querySelectorAll('.admin-tab-content').forEach(c => c.classList.remove('active'));
                        document.getElementById(`${tab.dataset.tab}Content`).classList.add('active');
                    });
                });
            }

            /**
             * Load topics into the topic list
             */
            function loadTopics() {
                topicList.innerHTML = '';
                
                if (topics.length === 0) {
                    topicList.innerHTML = '<p>No topics found.</p>';
                    return;
                }
                
                topics.forEach(topic => {
                    const topicElement = document.createElement('div');
                    topicElement.className = `topic-item ${topic.active ? '' : 'deactivated'}`;
                    topicElement.innerHTML = `
                        <h3 class="topic-title">${topic.title} 
                            ${topic.pinned ? '<span class="pinned-badge">Pinned</span>' : ''}
                            ${!topic.active ? '<span class="pinned-badge" style="background:#95a5a6">Hidden</span>' : ''}
                        </h3>
                        <div class="topic-meta">Posted by ${topic.author} in ${topic.category} on ${topic.date}</div>
                        <div class="topic-stats">
                            <span>${topic.replies} replies</span>
                            <span>${topic.views} views</span>
                        </div>
                        <div class="mod-controls">
                            <button class="mod-btn" onclick="viewTopic(${topic.id})">View</button>
                            <button class="mod-btn" onclick="editTopic(${topic.id})">Edit</button>
                            <button class="mod-btn" onclick="confirmDelete(${topic.id}, true)">Delete</button>
                            <button class="mod-btn" onclick="togglePin(${topic.id}, ${topic.pinned})">
                                ${topic.pinned ? 'Unpin' : 'Pin'}
                            </button>
                            <div class="status-toggle">
                                <label class="switch">
                                    <input type="checkbox" ${topic.active ? 'checked' : ''} onchange="toggleTopicStatus(${topic.id}, this.checked)">
                                    <span class="slider"></span>
                                </label>
                                <span>${topic.active ? 'Active' : 'Hidden'}</span>
                            </div>
                        </div>
                    `;
                    
                    topicList.appendChild(topicElement);
                });
            }

            /**
             * View a specific topic
             */
            function viewTopic(topicId) {
                const topic = topics.find(t => t.id === topicId);
                if (!topic) {
                    alert('Topic not found');
                    return;
                }
                
                document.getElementById('viewTopicTitle').textContent = topic.title;
                document.getElementById('viewTopicTitle').dataset.topicId = topic.id;
                document.getElementById('viewTopicMeta').innerHTML = `
                    Posted by ${topic.author} in ${topic.category} on ${topic.date}
                `;
                document.getElementById('viewTopicContent').innerHTML = topic.content;
                
                // Load replies
                const repliesContainer = document.getElementById('topicReplies');
                repliesContainer.innerHTML = '<h3>Replies</h3>';
                
                if (topic.repliesList.length === 0) {
                    repliesContainer.innerHTML += '<p>No replies yet.</p>';
                } else {
                    topic.repliesList.forEach(reply => {
                        const replyElement = document.createElement('div');
                        replyElement.className = `reply ${reply.flagged ? 'flagged' : ''}`;
                        replyElement.innerHTML = `
                            <div class="reply-content">${reply.content}</div>
                            <div class="reply-meta">
                                Posted by ${reply.author} on ${reply.date}
                                <button class="flag-btn" onclick="showFlagForm(${reply.id})">Report</button>
                            </div>
                            <div class="flag-form" id="flag-form-${reply.id}">
                                <select id="flag-reason-${reply.id}" style="margin-bottom:5px; width:100%">
                                    <option value="spam">Spam</option>
                                    <option value="inappropriate">Inappropriate Content</option>
                                    <option value="offensive">Offensive Language</option>
                                    <option value="other">Other</option>
                                </select>
                                <button class="btn btn-primary" onclick="submitFlag(${reply.id}, ${topicId})">Submit Report</button>
                            </div>
                            <div class="mod-controls">
                                <button class="mod-btn" onclick="editReply(${reply.id}, ${topicId})">Edit</button>
                                <button class="mod-btn" onclick="confirmDelete(${reply.id}, false)">Delete</button>
                            </div>
                        `;
                        repliesContainer.appendChild(replyElement);
                    });
                }
                
                // Set up reply submission
                document.getElementById('submitReply').onclick = () => {
                    const replyContent = document.getElementById('replyContent').value;
                    if (replyContent.trim()) {
                        // In a real app, this would be an API call
                        topic.repliesList.push({
                            id: Math.floor(Math.random() * 1000),
                            author: "current_user",
                            date: new Date().toISOString().split('T')[0],
                            content: replyContent,
                            flagged: false
                        });
                        
                        document.getElementById('replyContent').value = '';
                        viewTopic(topicId); // Refresh the view
                    }
                };
                
                topicViewModal.style.display = 'flex';
            }

            /**
             * Create a new topic
             */
            function createNewTopic() {
                const category = document.getElementById('topicCategory').value;
                const title = document.getElementById('topicTitle').value;
                const content = tinymce.get('topicContent').getContent();
                
                if (!category || !title || !content) {
                    alert('Please fill in all fields');
                    return;
                }
                
                // In a real app, this would be an API call
                const newTopic = {
                    id: topics.length + 1,
                    title,
                    category: document.getElementById('topicCategory').options[document.getElementById('topicCategory').selectedIndex].text,
                    author: "current_user",
                    date: new Date().toISOString().split('T')[0],
                    replies: 0,
                    views: 0,
                    pinned: false,
                    active: true,
                    content,
                    repliesList: []
                };
                
                topics.unshift(newTopic);
                loadTopics();
                
                // Reset form
                document.getElementById('topicCategory').value = '';
                document.getElementById('topicTitle').value = '';
                tinymce.get('topicContent').setContent('');
                
                createTopicModal.style.display = 'none';
            }

            /**
             * Edit a topic
             */
            function editTopic(topicId) {
                const topic = topics.find(t => t.id === topicId);
                if (!topic) return;
                
                // In a real app, this would open an edit form
                alert(`Edit functionality for topic ${topicId} would open an edit form`);
            }

            /**
             * Edit a reply
             */
            function editReply(replyId, topicId) {
                // In a real app, this would open an edit form
                alert(`Edit functionality for reply ${replyId} in topic ${topicId}`);
            }

            /**
             * Confirm deletion of a topic or reply
             */
            function confirmDelete(id, isTopic) {
                if (confirm(`Are you sure you want to delete this ${isTopic ? 'topic' : 'reply'}?`)) {
                    // In a real app, this would be an API call
                    if (isTopic) {
                        const index = topics.findIndex(t => t.id === id);
                        if (index !== -1) {
                            topics.splice(index, 1);
                            loadTopics();
                        }
                    } else {
                        // Find the reply in any topic and delete it
                        for (const topic of topics) {
                            const index = topic.repliesList.findIndex(r => r.id === id);
                            if (index !== -1) {
                                topic.repliesList.splice(index, 1);
                                // Refresh if we're viewing this topic
                                if (topicViewModal.style.display === 'flex') {
                                    const currentTopicId = parseInt(document.getElementById('viewTopicTitle').dataset.topicId);
                                    if (currentTopicId === topic.id) {
                                        viewTopic(topic.id);
                                    }
                                }
                                break;
                            }
                        }
                    }
                }
            }

            /**
             * Toggle pin status of a topic
             */
            function togglePin(topicId, currentlyPinned) {
                const topic = topics.find(t => t.id === topicId);
                if (topic) {
                    topic.pinned = !currentlyPinned;
                    loadTopics();
                }
            }

            /**
             * Toggle active status of a topic
             */
            function toggleTopicStatus(topicId, isActive) {
                const topic = topics.find(t => t.id === topicId);
                if (topic) {
                    topic.active = isActive;
                    loadTopics();
                }
            }

            /**
             * Show flag form for a reply
             */
            function showFlagForm(replyId) {
                document.querySelectorAll('.flag-form').forEach(form => form.style.display = 'none');
                document.getElementById(`flag-form-${replyId}`).style.display = 'block';
            }

            /**
             * Submit a flag for a reply
             */
            function submitFlag(replyId, topicId) {
                const reason = document.getElementById(`flag-reason-${replyId}`).value;
                const topic = topics.find(t => t.id === topicId);
                if (topic) {
                    const reply = topic.repliesList.find(r => r.id === replyId);
                    if (reply) {
                        reply.flagged = true;
                        reply.flagReason = reason;
                        alert('Thank you for reporting. Moderators will review this content.');
                        // Refresh flagged content list if admin panel is open
                        if (reviewReportsModal.style.display === 'flex') {
                            loadFlaggedContent();
                        }
                    }
                }
            }

            /**
             * Load flagged content for admin review
             */
            function loadFlaggedContent() {
                let flaggedCount = 0;
                const flaggedPostsList = document.getElementById('flaggedPostsList');
                flaggedPostsList.innerHTML = '';
                
                topics.forEach(topic => {
                    topic.repliesList.forEach(reply => {
                        if (reply.flagged) {
                            flaggedCount++;
                            const flaggedItem = document.createElement('div');
                            flaggedItem.className = 'flagged-item';
                            flaggedItem.innerHTML = `
                                <p><strong>Topic:</strong> <a href="#" onclick="viewTopic(${topic.id}); return false">${topic.title}</a></p>
                                <p><strong>Author:</strong> ${reply.author} | <strong>Reported for:</strong> ${reply.flagReason || 'No reason provided'}</p>
                                <div class="flagged-content-preview" style="padding:10px;background:#f9f9f9;margin:5px 0">
                                    ${reply.content}
                                </div>
                                <div class="admin-actions">
                                    <button class="btn btn-primary" onclick="resolveFlag(${reply.id}, ${topic.id}, 'keep')">Keep Content</button>
                                    <button class="btn btn-secondary" onclick="resolveFlag(${reply.id}, ${topic.id}, 'warn')">Warn User</button>
                                    <button class="btn btn-danger" onclick="resolveFlag(${reply.id}, ${topic.id}, 'delete')">Delete Post</button>
                                </div>
                            `;
                            flaggedPostsList.appendChild(flaggedItem);
                        }
                    });
                });
                
                document.getElementById('flag-count').textContent = `(${flaggedCount})`;
                if (flaggedCount === 0) {
                    flaggedPostsList.innerHTML = '<p>No flagged content to review.</p>';
                }
            }

            /**
             * Resolve a flag (admin action)
             */
            function resolveFlag(replyId, topicId, action) {
                const topic = topics.find(t => t.id === topicId);
                if (topic) {
                    const reply = topic.repliesList.find(r => r.id === replyId);
                    if (reply) {
                        if (action === 'delete') {
                            topic.repliesList = topic.repliesList.filter(r => r.id !== replyId);
                        } else {
                            reply.flagged = false;
                            if (action === 'warn') {
                                console.log(`Warning sent to user ${reply.author}`);
                            }
                        }
                        loadFlaggedContent();
                        // Refresh topic view if open
                        if (document.getElementById('viewTopicTitle').textContent === topic.title) {
                            viewTopic(topicId);
                        }
                    }
                }
            }

            // Make functions available globally
            window.viewTopic = viewTopic;
            window.editTopic = editTopic;
            window.editReply = editReply;
            window.confirmDelete = confirmDelete;
            window.togglePin = togglePin;
            window.toggleTopicStatus = toggleTopicStatus;
            window.showFlagForm = showFlagForm;
            window.submitFlag = submitFlag;
            window.loadFlaggedContent = loadFlaggedContent;
            window.resolveFlag = resolveFlag;
        }
    </script>
</body>
</html>