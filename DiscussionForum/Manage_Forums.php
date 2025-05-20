<?php
require_once('../SchedureEvent/connect.php');
require_once __DIR__ . '/../Internees_task/auth/auth_check.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum Management System</title>
    <script src="https://cdn.tiny.cloud/1/yy21cxb9sz8dz5s1jswqcenpziyj0y4frg79dtifqqamfxbf/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
             padding-left: 25px;
            margin-left: 255px;
            margin-right: 100px;
            padding-right: 100px;
            margin-bottom: 100px;
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
        
         /* Add to your existing CSS */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    justify-content: center;
    align-items: center;
}

.modal-content {
    background-color: #fff;
    padding: 20px;
    border-radius: 5px;
    width: 80%;
    max-width: 800px;
    max-height: 90vh;
    overflow-y: auto;
}

.modal h2 {
    color: #2c3e50;
    margin-bottom: 20px;
    font-size: 22px;
}

/* Edit form styles */
.edit-form {
    margin-top: 20px;
}

.edit-form .form-group {
    margin-bottom: 15px;
}

.edit-form label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
    color: #2c3e50;
}

.edit-form input[type="text"],
.edit-form textarea {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.edit-form textarea {
    min-height: 150px;
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

        .subscription-controls {
            margin-top: 15px;
            display: flex;
            gap: 10px;
        }
        .subscribe-btn, .unsubscribe-btn {
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        .subscribe-btn {
            background-color: #2ecc71;
            color: white;
            border: none;
        }
        .unsubscribe-btn {
            background-color: #e74c3c;
            color: white;
            border: none;
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

        /* Custom Alert Styles */
        .custom-alert {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 5px;
            color: white;
            font-weight: 500;
            z-index: 9999;
            animation: slideIn 0.5s ease-out;
            display: none;
        }

        .alert-success {
            background-color: #2ecc71;
        }

        .alert-error {
            background-color: #e74c3c;
        }

        .alert-info {
            background-color: #3498db;
        }

        @keyframes slideIn {
            from { transform: translateX(100%); }
            to { transform: translateX(0); }
        }

        /* Form Validation Styles */
        .form-error {
            color: #e74c3c;
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }

        .error-field {
            border-color: #e74c3c !important;
        }

        .header-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn i {
            margin-right: 5px;
        }
    </style>
</head>
<body>
<?php include('../Internees_task/header.php') ?>
    
    <div class="main-container">
        <div class="forum-layout">
            <aside class="sidebar">
                <div class="category-list">
                    <h3>Categories</h3>
                    <ul id="categoryList">
                        <!-- Categories will be loaded via API -->
                    </ul>
                </div>
                
                <div class="forum-stats">
                    <h3>Forum Statistics</h3>
                    <p>Topics: <span id="topicCount">0</span></p>
                    <p>Posts: <span id="postCount">0</span></p>
                    <p>Members: <span id="memberCount">0</span></p>
                </div>
            </aside>
            
            <main class="main-content">
                <div class="topic-list-header">
                    <h2 style="padding-left: 200px;">Manage Forums</h2>
                    <div class="header-controls">
                        
                     
                        <button class="btn btn-primary" id="createTopicBtn">Create New Topic</button>
                    </div>
                    <div class="search-filters" style="margin-bottom: 20px;">
                        <input type="text" id="searchInput" placeholder="Search topics..." style="padding: 8px; margin-right: 10px;">
                        <select id="dateFilter" style="padding: 8px; margin-right: 10px;">
                            <option value="">All Time</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                        </select>
                        <select id="userFilter" style="padding: 8px; margin-right: 10px;">
                            <option value="">All Users</option>
                        </select>
                        <button class="btn btn-secondary" onclick="applyFilters()">Apply Filters</button>
                    </div>
                </div>
                
                <div class="topic-list" id="topicList">
                    <!-- Topics will be loaded via API -->
                </div>

                <div class="pagination" style="margin-top: 20px; text-align: center;">
                    <button class="btn btn-secondary" onclick="changePage('prev')" id="prevPage">Previous</button>
                    <span id="pageInfo" style="margin: 0 15px;">Page 1</span>
                    <button class="btn btn-secondary" onclick="changePage('next')" id="nextPage">Next</button>
                </div>

                <div id="moderationControls" style="display: none;">
                    <button class="btn btn-primary review-report-btn" id="reviewReports">
                        Review Reports
                    </button>
                </div>
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
                        <!-- Categories will be loaded via API -->
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
                <!-- Replies will be loaded via API -->
            </div>
            
            <div class="reply-form" style="margin-top: 20px;">
                <h3>Post a Reply</h3>
                <textarea id="replyContent" style="width: 100%; height: 150px; margin-bottom: 10px;"></textarea>
                <button class="btn btn-primary" id="submitReply">Post Reply</button>
            </div>
        </div>
    </div>
    
    <!-- Edit Topic Modal -->
    <div class="modal" id="editTopicModal" style="display: none;">
        <div class="modal-content">
            <!-- Content will be loaded dynamically -->
        </div>
    </div>
    
    <!-- Edit Reply Modal -->
    <div class="modal" id="editReplyModal" style="display: none;">
        <div class="modal-content">
            <!-- Content will be loaded dynamically -->
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
                    <!-- Flagged posts will be loaded via API -->
                </div>
            </div>
            
            <div class="admin-tab-content" id="usersContent" style="margin-top: 20px;">
                <h3>User Management</h3>
                <div id="userManagementContent">
                    <!-- User management content will be loaded here -->
                </div>
            </div>
            
            <div class="admin-tab-content" id="categoriesContent" style="margin-top: 20px;">
                <h3>Category Management</h3>
                <div id="categoryManagementContent">
                    <!-- Category management content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
    
    <div id="customAlert" class="custom-alert"></div>
    
    <script>
        // Global variables
        let currentUserRole = <?php echo isset($_SESSION['role_id']) ? $_SESSION['role_id'] : 0; ?>;
        let currentUserId = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0; ?>;
        let currentTopicId = null;
        let currentTopicSubscribed = false;
        const API_BASE_URL = 'forum_api.php';
        let currentPage = 1;
        let totalPages = 1;

        // Document Ready Function
        document.addEventListener('DOMContentLoaded', function() {
            // Get current user role from server and update UI accordingly
            fetch(`${API_BASE_URL}?action=get_topics`)
                .then(response => response.json())
                .then(data => {
                    if (data.current_user_role) {
                        currentUserRole = data.current_user_role;
                        console.log('Updated currentUserRole:', currentUserRole);
                        // Update UI based on role
                        updateUIBasedOnRole();
                    }
                })
                .catch(error => console.error('Error getting user role:', error));

            // Initialize TinyMCE for create topic
            tinymce.init({
                selector: '#topicContent',
                plugins: 'link lists code',
                toolbar: 'bold italic | bullist numlist | link code',
                menubar: false,
                content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; }'
            });

            // Initialize the application
            initApp();
            
            // Load initial data
            loadForumStats();
            loadCategories();
            loadTopics();
            
            // Show moderation controls if user is admin/moderator
            if (currentUserRole === 1 || currentUserRole === 2 || currentUserRole === 3) {
                document.getElementById('moderationControls').style.display = 'block';
            }
            
            // Load users for filter
            loadUserFilter();
            
            // Add search input event listener
            const searchInput = document.getElementById('searchInput');
            let searchTimeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(applyFilters, 500); // Debounce search
            });
            
            // Add filter change listeners
            document.getElementById('dateFilter').addEventListener('change', applyFilters);
            document.getElementById('userFilter').addEventListener('change', applyFilters);
        });

        // function updateUIBasedOnRole() {
        //     const headerControls = document.querySelector('.header-controls');
        //     if (headerControls) {
        //         if (currentUserRole === 1 || currentUserRole === 2 || currentUserRole === 3) {
        //             const adminButton = `<button class="btn btn-primary" onclick="showReviewReportsModal(event)" style="margin-right: 10px;">
        //                 <i class="fas fa-flag"></i> Review Reports
        //             </button>`;
        //             headerControls.insertAdjacentHTML('afterbegin', adminButton);
        //         }
        //     }
        // }

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
            const submitReplyBtn = document.getElementById('submitReply');
            
            // Event Listeners
            createTopicBtn.addEventListener('click', showCreateTopicModal);
            cancelTopicBtn.addEventListener('click', hideCreateTopicModal);
            reviewReportsBtn.addEventListener('click', showReviewReportsModal);
            topicForm.addEventListener('submit', handleTopicFormSubmit);
            submitReplyBtn.addEventListener('click', handleReplySubmit);
            
            // Close modals when clicking outside
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('modal')) {
                    e.target.style.display = 'none';
                }
            });
            
            // Admin tab switching
            setupAdminTabs();
        }

        /**
         * Load forum statistics
         */
        function loadForumStats() {
            fetch(`${API_BASE_URL}?action=get_stats`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error(data.error);
                        return;
                    }
                    
                    document.getElementById('topicCount').textContent = data.topics || 0;
                    document.getElementById('postCount').textContent = data.posts || 0;
                    document.getElementById('memberCount').textContent = data.members || 0;
                })
                .catch(error => console.error('Error loading forum stats:', error));
        }

        /**
         * Load categories for sidebar and dropdown
         */
        function loadCategories() {
            fetch(`${API_BASE_URL}?action=get_categories`)
                .then(response => response.json())
                .then(categories => {
                    const categoryList = document.getElementById('categoryList');
                    const categoryDropdown = document.getElementById('topicCategory');
                    
                    // Clear existing options except the first one
                    while (categoryDropdown.options.length > 1) {
                        categoryDropdown.remove(1);
                    }
                    
                    categoryList.innerHTML = '';
                    
                    if (categories.length === 0) {
                        categoryList.innerHTML = '<li>No categories found</li>';
                        return;
                    }
                    
                    categories.forEach(category => {
                        // Add to sidebar
                        const listItem = document.createElement('li');
                        listItem.innerHTML = `<a href="#" onclick="loadTopics(${category.id}); return false">${category.name}</a>`;
                        categoryList.appendChild(listItem);
                        
                        // Add to dropdown
                        const option = document.createElement('option');
                        option.value = category.id;
                        option.textContent = category.name;
                        categoryDropdown.appendChild(option);
                    });
                })
                .catch(error => console.error('Error loading categories:', error));
        }

        /**
         * Load topics for the current view
         */
        function loadTopics(categoryId = null) {
            let url = `${API_BASE_URL}?action=get_topics&page=${currentPage}`;
            if (categoryId) {
                url += `&category_id=${categoryId}`;
            }
            
            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        throw new Error(data.error);
                    }

                    const topicList = document.getElementById('topicList');
                    topicList.innerHTML = '';
                    
                    if (!data.topics || data.topics.length === 0) {
                        topicList.innerHTML = '<p>No topics found matching your criteria.</p>';
                        return;
                    }
                    
                    data.topics.forEach(topic => {
                        const topicElement = document.createElement('div');
                        topicElement.className = `topic-item ${topic.active ? '' : 'deactivated'}`;
                        topicElement.innerHTML = `
                            <h3 class="topic-title">${topic.title} 
                                ${topic.pinned ? '<span class="pinned-badge">Pinned</span>' : ''}
                                ${!topic.active ? '<span class="pinned-badge" style="background:#95a5a6">Hidden</span>' : ''}
                            </h3>
                            <div class="topic-meta">Posted by ${topic.author.name} in ${topic.category.name} on ${new Date(topic.created_at).toLocaleDateString()}</div>
                            <div class="topic-stats">
                                <span>${topic.reply_count} replies</span>
                                <button class="btn ${topic.is_subscribed ? 'btn-danger' : 'btn-primary'} btn-sm" 
                                        onclick="${topic.is_subscribed ? 'unsubscribeFromTopic' : 'subscribeToTopic'}(${topic.id})">
                                    ${topic.is_subscribed ? 'Unsubscribe' : 'Subscribe'}
                                </button>
                            </div>
                            <div class="mod-controls">
                                <button class="mod-btn" onclick="viewTopic(${topic.id})">View</button>
                                ${topic.can_edit ? 
                                    `<button class="mod-btn" onclick="editTopic(${topic.id})">Edit</button>
                                     <button class="mod-btn" onclick="confirmDelete(${topic.id}, true)">Delete</button>` : ''}
                                ${(currentUserRole === 1 || currentUserRole === 2 || currentUserRole === 3) ? 
                                    `<button class="mod-btn" onclick="togglePin(${topic.id}, ${topic.pinned})">
                                        ${topic.pinned ? 'Unpin' : 'Pin'}
                                    </button>
                                    <div class="status-toggle">
                                        <label class="switch">
                                            <input type="checkbox" ${topic.active ? 'checked' : ''} onchange="toggleTopicStatus(${topic.id}, this.checked)">
                                            <span class="slider"></span>
                                        </label>
                                        <span>${topic.active ? 'Active' : 'Hidden'}</span>
                                    </div>` : ''}
                            </div>
                        `;
                        topicList.appendChild(topicElement);
                    });
                })
                .catch(error => {
                    console.error('Error loading topics:', error);
                    showAlert('Failed to load topics. Please try again.', 'error');
                });
        }

        /**
         * View a specific topic
         */
        function viewTopic(topicId) {
            currentTopicId = topicId;
            
            fetch(`${API_BASE_URL}?action=get_topic&topic_id=${topicId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }
                    
                    const topic = data.topic;
                    if (!topic) {
                        alert('Topic not found');
                        return;
                    }

                    document.getElementById('viewTopicTitle').textContent = topic.title;
                    document.getElementById('viewTopicMeta').innerHTML = `
                        Posted by ${topic.author.name} in ${topic.category.name} on ${new Date(topic.created_at).toLocaleDateString()}
                    `;
                    document.getElementById('viewTopicContent').innerHTML = topic.content;

                    // Check subscription status
                    currentTopicSubscribed = data.is_subscribed;
                    updateSubscriptionButton();
                    
                    // Load replies
                    const repliesContainer = document.getElementById('topicReplies');
                    repliesContainer.innerHTML = '<h3>Replies</h3>';

                    // Add subscription controls if they don't exist
                    if (!document.getElementById('subscriptionControls')) {
                        const subscriptionControls = document.createElement('div');
                        subscriptionControls.id = 'subscriptionControls';
                        subscriptionControls.className = 'subscription-controls';
                        subscriptionControls.innerHTML = `
                            <button class="subscribe-btn" id="subscribeBtn" style="display: none;">Subscribe</button>
                            <button class="unsubscribe-btn" id="unsubscribeBtn" style="display: none;">Unsubscribe</button>`;
                        repliesContainer.appendChild(subscriptionControls);
                        
                        // Add event listeners for subscription buttons
                        document.getElementById('subscribeBtn').addEventListener('click', () => subscribeToTopic(topicId));
                        document.getElementById('unsubscribeBtn').addEventListener('click', () => unsubscribeFromTopic(topicId));
                    }
                    updateSubscriptionButton();

                    // Display replies
                    if (data.replies && data.replies.length > 0) {
                        data.replies.forEach(reply => {
                            const replyElement = document.createElement('div');
                            replyElement.className = `reply ${reply.flagged ? 'flagged' : ''}`;
                            replyElement.innerHTML = `
                                <div class="reply-content">${reply.content}</div>
                                <div class="reply-meta">
                                    Posted by ${reply.author.name} on ${new Date(reply.created_at).toLocaleDateString()}
                                    ${reply.author.id !== currentUserId ? 
                                        `<button class="flag-btn" onclick="showFlagForm(${reply.id})">Report</button>` : ''}
                                </div>
                                <div class="flag-form" id="flag-form-${reply.id}" style="display: none;">
                                    <select id="flag-reason-${reply.id}" style="margin-bottom:5px; width:100%">
                                        <option value="spam">Spam</option>
                                        <option value="inappropriate">Inappropriate Content</option>
                                        <option value="offensive">Offensive Language</option>
                                        <option value="other">Other</option>
                                    </select>
                                    <button class="btn btn-primary" onclick="submitFlag(${reply.id}, ${topicId})">Submit Report</button>
                                </div>
                                ${(currentUserRole === 1 || currentUserRole === 2 || reply.author.id === currentUserId) ? 
                                    `<div class="mod-controls">
                                        <button class="mod-btn" onclick="editReply(${reply.id}, ${topicId})">Edit</button>
                                        <button class="mod-btn" onclick="confirmDelete(${reply.id}, false)">Delete</button>
                                    </div>` : ''}
                            `;
                            repliesContainer.appendChild(replyElement);
                        });
                    } else {
                        repliesContainer.innerHTML += '<p>No replies yet.</p>';
                    }

                    // Show the modal
                    document.getElementById('topicViewModal').style.display = 'flex';
                })
                .catch(error => {
                    console.error('Error loading topic:', error);
                    alert('Failed to load topic. Please try again.');
                });
        }

        /**
         * Update subscription button visibility
         */
        function updateSubscriptionButton() {
            const subscribeBtn = document.getElementById('subscribeBtn');
            const unsubscribeBtn = document.getElementById('unsubscribeBtn');
            
            if (subscribeBtn && unsubscribeBtn) {
                subscribeBtn.style.display = currentTopicSubscribed ? 'none' : 'block';
                unsubscribeBtn.style.display = currentTopicSubscribed ? 'block' : 'none';
            }
        }

        /**
         * Subscribe to a topic
         */
        function subscribeToTopic(topicId) {
            if (!currentUserId) {
                alert('Please log in to subscribe to topics');
                return;
            }

            fetch(API_BASE_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'subscribe_topic',
                    topic_id: topicId,
                    user_id: currentUserId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    showAlert(data.error, 'error');
                    return;
                }
                
                showAlert('Successfully subscribed to topic', 'success');
                // Update the subscription button
                const subscriptionBtn = document.querySelector(`button[onclick="subscribeToTopic(${topicId})"]`);
                if (subscriptionBtn) {
                    subscriptionBtn.innerHTML = 'Unsubscribe';
                    subscriptionBtn.onclick = () => unsubscribeFromTopic(topicId);
                    subscriptionBtn.classList.remove('btn-primary');
                    subscriptionBtn.classList.add('btn-danger');
                }
            })
            .catch(error => {
                console.error('Error subscribing to topic:', error);
                showAlert('Failed to subscribe to topic', 'error');
            });
        }

        /**
         * Unsubscribe from a topic
         */
        function unsubscribeFromTopic(topicId) {
            if (!currentUserId) {
                alert('Please log in to manage subscriptions');
                return;
            }

            fetch(API_BASE_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'unsubscribe_topic',
                    topic_id: topicId,
                    user_id: currentUserId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    showAlert(data.error, 'error');
                    return;
                }
                
                showAlert('Successfully unsubscribed from topic', 'success');
                // Update the subscription button
                const subscriptionBtn = document.querySelector(`button[onclick="unsubscribeFromTopic(${topicId})"]`);
                if (subscriptionBtn) {
                    subscriptionBtn.innerHTML = 'Subscribe';
                    subscriptionBtn.onclick = () => subscribeToTopic(topicId);
                    subscriptionBtn.classList.remove('btn-danger');
                    subscriptionBtn.classList.add('btn-primary');
                }
            })
            .catch(error => {
                console.error('Error unsubscribing from topic:', error);
                showAlert('Failed to unsubscribe from topic', 'error');
            });
        }

        /**
         * Show create topic modal
         */
        function showCreateTopicModal() {
            document.getElementById('createTopicModal').style.display = 'flex';
        }

        /**
         * Hide create topic modal
         */
        function hideCreateTopicModal() {
            document.getElementById('createTopicModal').style.display = 'none';
        }

        /**
         * Handle topic form submission
         */
        function handleTopicFormSubmit(e) {
            e.preventDefault();
            
            const categoryId = document.getElementById('topicCategory').value;
            const title = document.getElementById('topicTitle').value;
            const content = tinymce.get('topicContent').getContent();
            
            if (!categoryId || !title || !content) {
                alert('Please fill in all fields');
                return;
            }
            
            fetch(API_BASE_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'create_topic',
                    category_id: categoryId,
                    title: title,
                    content: content
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }
                
                alert('Topic created successfully!');
                hideCreateTopicModal();
                loadTopics();
                
                // Reset form
                document.getElementById('topicCategory').value = '';
                document.getElementById('topicTitle').value = '';
                tinymce.get('topicContent').setContent('');
            })
            .catch(error => {
                console.error('Error creating topic:', error);
                alert('Failed to create topic');
            });
        }

        /**
         * Handle reply submission
         */
        function handleReplySubmit() {
            if (!currentTopicId || currentTopicId === 0) {
                alert('No topic selected. Please view a topic before replying.');
                return;
            }
            
            const content = document.getElementById('replyContent').value;
            
            if (!content.trim()) {
                alert('Please enter reply content');
                return;
            }
            
            fetch(API_BASE_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'create_reply',
                    topic_id: currentTopicId,
                    content: content,
                    user_id: currentUserId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }
                
                document.getElementById('replyContent').value = '';
                viewTopic(currentTopicId); // Refresh the view
                loadTopics(); // Update topic list counts
            })
            .catch(error => {
                console.error('Error creating reply:', error);
                alert('Failed to create reply');
            });
        }

        /**
         * Show review reports modal
         */
        function showReviewReportsModal(e) {
            e.preventDefault();
            document.getElementById('reviewReportsModal').style.display = 'flex';
            loadFlaggedContent();
            showAlert('Loading admin panel...', 'info');
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
                    
                    // Load content if needed
                    if (tab.dataset.tab === 'flagged') {
                        loadFlaggedContent();
                    } else if (tab.dataset.tab === 'users') {
                        loadUserManagement();
                    } else if (tab.dataset.tab === 'categories') {
                        loadCategoryManagement();
                    }
                });
            });
        }

        /**
         * Edit a topic
         */
        function editTopic(topicId) {
            fetch(`${API_BASE_URL}?action=get_topic&topic_id=${topicId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        showAlert(data.error, 'error');
                        return;
                    }
                    
                    const topic = data.topic;
                    if (!topic) {
                        showAlert('Topic not found', 'error');
                        return;
                    }

                    // Initialize TinyMCE if not already initialized
                    if (!tinymce.get('editTopicContent')) {
                        tinymce.init({
                            selector: '#editTopicContent',
                            plugins: 'link lists code',
                            toolbar: 'bold italic | bullist numlist | link code',
                            menubar: false,
                            content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; }'
                        });
                    }
                    
                    const modalContent = `
                        <h2>Edit Topic</h2>
                        <form id="editTopicForm">
                            <input type="hidden" id="editTopicId" value="${topic.id}">
                            <div class="form-group">
                                <label for="editTopicTitle">Title</label>
                                <input type="text" id="editTopicTitle" value="${topic.title}" required>
                                <div id="editTitleError" class="form-error">Please enter a title</div>
                            </div>
                            <div class="form-group">
                                <label for="editTopicContent">Content</label>
                                <textarea id="editTopicContent">${topic.content}</textarea>
                                <div id="editContentError" class="form-error">Please enter content</div>
                            </div>
                            <div class="form-actions">
                                <button type="button" class="btn btn-secondary" onclick="document.getElementById('editTopicModal').style.display='none'">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    `;
                    
                    document.getElementById('editTopicModal').querySelector('.modal-content').innerHTML = modalContent;
                    document.getElementById('editTopicModal').style.display = 'flex';
                    
                    // Handle form submission
                    document.getElementById('editTopicForm').addEventListener('submit', function(e) {
                        e.preventDefault();
                        if (validateEditForm()) {
                            saveTopicChanges(topicId);
                        }
                    });
                })
                .catch(error => {
                    console.error('Error loading topic for edit:', error);
                    showAlert('Failed to load topic for editing', 'error');
                });
        }

        /**
         * Save edited topic
         */
        function saveTopicChanges(topicId) {
            const title = document.getElementById('editTopicTitle').value.trim();
            const content = tinymce.get('editTopicContent').getContent().trim();
            
            if (!title || !content) {
                alert('Please fill in all fields');
                return;
            }
            
            fetch(API_BASE_URL, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'update_topic',
                    topic_id: topicId,
                    title: title,
                    content: content
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }
                
                alert('Topic updated successfully!');
                document.getElementById('editTopicModal').style.display = 'none';
                viewTopic(topicId); // Refresh the view
                loadTopics(); // Update topic list
            })
            .catch(error => {
                console.error('Error updating topic:', error);
                alert('Failed to update topic');
            });
        }

        /**
         * Edit a reply
         */
        function editReply(replyId, topicId) {
            fetch(`${API_BASE_URL}?action=get_topic&topic_id=${topicId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }
                    
                    // Find the reply
                    const reply = data.replies.find(r => r.id == replyId);
                    if (!reply) {
                        alert('Reply not found');
                        return;
                    }

                    // Initialize TinyMCE if not already initialized
                    if (!tinymce.get('editReplyContent')) {
                        tinymce.init({
                            selector: '#editReplyContent',
                            plugins: 'link lists code',
                            toolbar: 'bold italic | bullist numlist | link code',
                            menubar: false,
                            content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; }'
                        });
                    }
                    
                    const modalContent = `
                        <h2>Edit Reply</h2>
                        <form id="editReplyForm">
                            <input type="hidden" id="editReplyId" value="${replyId}">
                            <input type="hidden" id="editReplyTopicId" value="${topicId}">
                            <div class="form-group">
                                <label for="editReplyContent">Content</label>
                                <textarea id="editReplyContent">${reply.content}</textarea>
                            </div>
                            <div class="form-actions">
                                <button type="button" class="btn btn-secondary" onclick="document.getElementById('editReplyModal').style.display='none'">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    `;
                    
                    document.getElementById('editReplyModal').querySelector('.modal-content').innerHTML = modalContent;
                    document.getElementById('editReplyModal').style.display = 'flex';
                    
                    // Handle form submission
                    document.getElementById('editReplyForm').addEventListener('submit', function(e) {
                        e.preventDefault();
                        saveReplyChanges(replyId, topicId);
                    });
                })
                .catch(error => {
                    console.error('Error loading reply for edit:', error);
                    alert('Failed to load reply for editing');
                });
        }

        /**
         * Save edited reply
         */
        function saveReplyChanges(replyId, topicId) {
            const content = tinymce.get('editReplyContent').getContent().trim();
            
            if (!content) {
                alert('Please enter reply content');
                return;
            }
            
            fetch(API_BASE_URL, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'update_reply',
                    reply_id: replyId,
                    content: content
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }
                
                alert('Reply updated successfully!');
                document.getElementById('editReplyModal').style.display = 'none';
                viewTopic(topicId); // Refresh the current topic view
            })
            .catch(error => {
                console.error('Error updating reply:', error);
                alert('Failed to update reply');
            });
        }

        /**
         * Confirm deletion of a topic or reply
         */
        function confirmDelete(id, isTopic) {
            if (!confirm(`Are you sure you want to delete this ${isTopic ? 'topic' : 'reply'}? This action cannot be undone.`)) {
                return;
            }
            
            const action = isTopic ? 'delete_topic' : 'delete_reply';
            const idField = isTopic ? 'topic_id' : 'reply_id';
            
            fetch(API_BASE_URL, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: action,
                    [idField]: id
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }
                
                alert(`${isTopic ? 'Topic' : 'Reply'} deleted successfully`);
                
                if (isTopic) {
                    loadTopics(); // Refresh topic list
                    document.getElementById('topicViewModal').style.display = 'none';
                } else {
                    viewTopic(currentTopicId); // Refresh the current topic view
                }
            })
            .catch(error => {
                console.error(`Error deleting ${isTopic ? 'topic' : 'reply'}:`, error);
                alert(`Failed to delete ${isTopic ? 'topic' : 'reply'}. Please try again.`);
            });
        }

        /**
         * Toggle pin status of a topic
         */
        function togglePin(topicId, currentlyPinned) {
            fetch(API_BASE_URL, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'toggle_pin',
                    topic_id: topicId,
                    pinned: !currentlyPinned
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }
                
                loadTopics(); // Refresh the topic list
                if (currentTopicId === topicId) {
                    viewTopic(topicId); // Refresh the current topic view if it's open
                }
            })
            .catch(error => {
                console.error('Error toggling pin status:', error);
                alert('Failed to update pin status. Please try again.');
            });
        }

        /**
         * Toggle active status of a topic
         */
        function toggleTopicStatus(topicId, isActive) {
            fetch(API_BASE_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'toggle_active',
                    topic_id: topicId,
                    active: isActive
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }
                
                loadTopics();
            })
            .catch(error => {
                console.error('Error toggling topic status:', error);
                alert('Failed to update topic status');
            });
        }

        /**
         * Show flag form for a reply
         */
        function showFlagForm(replyId) {
            // Hide all other flag forms first
            document.querySelectorAll('.flag-form').forEach(form => form.style.display = 'none');
            // Show the selected flag form
            document.getElementById(`flag-form-${replyId}`).style.display = 'block';
        }

        /**
         * Submit a flag for a reply
         */
        function submitFlag(replyId, topicId) {
            const reason = document.getElementById(`flag-reason-${replyId}`).value;
            
            if (!reason) {
                alert('Please select a reason for reporting');
                return;
            }
            
            fetch(API_BASE_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'flag_reply',
                    reply_id: replyId,
                    reason: reason
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }
                
                alert('Thank you for reporting. Moderators will review this content.');
                document.getElementById(`flag-form-${replyId}`).style.display = 'none';
                
                // Refresh flagged content list if admin panel is open
                if (document.getElementById('reviewReportsModal').style.display === 'flex') {
                    loadFlaggedContent();
                }
                
                // Refresh the topic view to show the flagged status
                viewTopic(topicId);
            })
            .catch(error => {
                console.error('Error flagging reply:', error);
                alert('Failed to submit report. Please try again.');
            });
        }

        /**
         * Load flagged content for admin review
         */
        function loadFlaggedContent() {
            fetch(`${API_BASE_URL}?action=get_flagged_content`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(flaggedContent => {
                    const flaggedPostsList = document.getElementById('flaggedPostsList');
                    flaggedPostsList.innerHTML = '';
                    
                    document.getElementById('flag-count').textContent = `(${flaggedContent.length})`;
                    
                    if (flaggedContent.length === 0) {
                        flaggedPostsList.innerHTML = '<p>No flagged content to review.</p>';
                        return;
                    }
                    
                    flaggedContent.forEach(item => {
                        const flaggedItem = document.createElement('div');
                        flaggedItem.className = 'flagged-item';
                        flaggedItem.innerHTML = `
                            <p><strong>Topic:</strong> <a href="#" onclick="viewTopic(${item.topic_id}); return false">${item.topic_title}</a></p>
                            <p><strong>Author:</strong> ${item.author_name} | <strong>Reported for:</strong> ${item.flag_reason || 'No reason provided'}</p>
                            <div class="flagged-content-preview" style="padding:10px;background:#f9f9f9;margin:5px 0">
                                ${item.content}
                            </div>
                            <div class="admin-actions">
                                <button class="btn btn-primary" onclick="resolveFlagAdmin(${item.reply_id}, 'keep')">Keep Content</button>
                                <button class="btn btn-secondary" onclick="resolveFlagAdmin(${item.reply_id}, 'warn')">Warn User</button>
                                <button class="btn btn-danger" onclick="resolveFlagAdmin(${item.reply_id}, 'delete')">Delete Post</button>
                            </div>
                        `;
                        flaggedPostsList.appendChild(flaggedItem);
                    });
                })
                .catch(error => {
                    console.error('Error loading flagged content:', error);
                    alert('Failed to load flagged content. Please try again.');
                });
        }

        /**
         * Resolve a flag (admin action)
         */
        function resolveFlagAdmin(replyId, action) {
            if (!confirm(`Are you sure you want to ${action} this content?`)) {
                return;
            }
            
            fetch(API_BASE_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'resolve_flag',
                    reply_id: replyId,
                    resolution: action
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }
                
                alert(`Action "${action}" completed successfully`);
                loadFlaggedContent(); // Refresh the flagged content list
                
                // Refresh topic view if open
                if (document.getElementById('topicViewModal').style.display === 'flex') {
                    viewTopic(currentTopicId);
                }
            })
            .catch(error => {
                console.error('Error resolving flag:', error);
                alert('Failed to complete action. Please try again.');
            });
        }

        /**
         * Load user management content
         */
        function loadUserManagement() {
            fetch(`${API_BASE_URL}?action=get_users`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(users => {
                    const userContent = document.getElementById('userManagementContent');
                    userContent.innerHTML = `
                        <div class="user-list">
                            <table class="admin-table" style="width:100%; border-collapse: collapse;">
                                <thead>
                                    <tr>
                                        <th style="padding: 10px; text-align: left; border-bottom: 2px solid #eee;">Name</th>
                                        <th style="padding: 10px; text-align: left; border-bottom: 2px solid #eee;">Role</th>
                                        <th style="padding: 10px; text-align: left; border-bottom: 2px solid #eee;">Status</th>
                                        <th style="padding: 10px; text-align: left; border-bottom: 2px solid #eee;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${users.map(user => `
                                        <tr>
                                            <td style="padding: 10px; border-bottom: 1px solid #eee;">${user.full_name}</td>
                                            <td style="padding: 10px; border-bottom: 1px solid #eee;">${user.role_name}</td>
                                            <td style="padding: 10px; border-bottom: 1px solid #eee;">
                                                <span class="status-badge ${user.is_active ? 'active' : 'inactive'}">
                                                    ${user.is_active ? 'Active' : 'Inactive'}
                                                </span>
                                            </td>
                                            <td style="padding: 10px; border-bottom: 1px solid #eee;">
                                                <button class="btn btn-secondary btn-sm" onclick="toggleUserStatus(${user.user_id}, ${!user.is_active})">
                                                    ${user.is_active ? 'Deactivate' : 'Activate'}
                                                </button>
                                                <button class="btn btn-primary btn-sm" onclick="editUserRole(${user.user_id})">
                                                    Change Role
                                                </button>
                                            </td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    `;
                })
                .catch(error => {
                    console.error('Error loading users:', error);
                    document.getElementById('userManagementContent').innerHTML = 
                        '<p class="error">Failed to load user management content. Please try again.</p>';
                });
        }

        /**
         * Load category management content
         */
        function loadCategoryManagement() {
            fetch(`${API_BASE_URL}?action=get_categories`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(categories => {
                    const categoryContent = document.getElementById('categoryManagementContent');
                    categoryContent.innerHTML = `
                        <div class="category-controls" style="margin-bottom: 20px;">
                            <button class="btn btn-primary" onclick="showCreateCategoryModal()">
                                Create New Category
                            </button>
                        </div>
                        <div class="category-list">
                            <table class="admin-table" style="width:100%; border-collapse: collapse;">
                                <thead>
                                    <tr>
                                        <th style="padding: 10px; text-align: left; border-bottom: 2px solid #eee;">Name</th>
                                        <th style="padding: 10px; text-align: left; border-bottom: 2px solid #eee;">Description</th>
                                        <th style="padding: 10px; text-align: left; border-bottom: 2px solid #eee;">Permission</th>
                                        <th style="padding: 10px; text-align: left; border-bottom: 2px solid #eee;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${categories.map(category => `
                                        <tr>
                                            <td style="padding: 10px; border-bottom: 1px solid #eee;">${category.name}</td>
                                            <td style="padding: 10px; border-bottom: 1px solid #eee;">${category.description}</td>
                                            <td style="padding: 10px; border-bottom: 1px solid #eee;">${category.permission}</td>
                                            <td style="padding: 10px; border-bottom: 1px solid #eee;">
                                                <button class="btn btn-primary btn-sm" onclick="editCategory(${category.id})">
                                                    Edit
                                                </button>
                                                <button class="btn btn-danger btn-sm" onclick="deleteCategory(${category.id})">
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    `;
                })
                .catch(error => {
                    console.error('Error loading categories:', error);
                    document.getElementById('categoryManagementContent').innerHTML = 
                        '<p class="error">Failed to load category management content. Please try again.</p>';
                });
        }

        /**
         * Apply search and filters to topics
         */
        function applyFilters() {
            const searchQuery = document.getElementById('searchInput').value;
            const dateFilter = document.getElementById('dateFilter').value;
            const userFilter = document.getElementById('userFilter').value;
            
            let url = `${API_BASE_URL}?action=get_topics&page=${currentPage}`;
            
            if (searchQuery) {
                url += `&search=${encodeURIComponent(searchQuery)}`;
            }
            
            if (dateFilter) {
                url += `&date_filter=${dateFilter}`;
            }
            
            if (userFilter) {
                url += `&user_id=${userFilter}`;
            }
            
            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    const topicList = document.getElementById('topicList');
                    topicList.innerHTML = '';
                    
                    if (!data.topics || data.topics.length === 0) {
                        topicList.innerHTML = '<p>No topics found matching your criteria.</p>';
                        return;
                    }
                    
                    totalPages = Math.ceil(data.total_count / data.per_page);
                    document.getElementById('pageInfo').textContent = `Page ${currentPage} of ${totalPages}`;
                    document.getElementById('prevPage').disabled = currentPage === 1;
                    document.getElementById('nextPage').disabled = currentPage === totalPages;
                    
                    data.topics.forEach(topic => {
                        const topicElement = document.createElement('div');
                        topicElement.className = `topic-item ${topic.active ? '' : 'deactivated'}`;
                        topicElement.innerHTML = `
                            <h3 class="topic-title">${topic.title} 
                                ${topic.pinned ? '<span class="pinned-badge">Pinned</span>' : ''}
                                ${!topic.active ? '<span class="pinned-badge" style="background:#95a5a6">Hidden</span>' : ''}
                            </h3>
                            <div class="topic-meta">Posted by ${topic.author.name} in ${topic.category.name} on ${new Date(topic.created_at).toLocaleDateString()}</div>
                            <div class="topic-stats">
                                <span>${topic.reply_count} replies</span>
                                <button class="btn ${topic.is_subscribed ? 'btn-danger' : 'btn-primary'} btn-sm" 
                                        onclick="${topic.is_subscribed ? 'unsubscribeFromTopic' : 'subscribeToTopic'}(${topic.id})">
                                    ${topic.is_subscribed ? 'Unsubscribe' : 'Subscribe'}
                                </button>
                            </div>
                            <div class="mod-controls">
                                <button class="mod-btn" onclick="viewTopic(${topic.id})">View</button>
                                ${topic.can_edit ? 
                                    `<button class="mod-btn" onclick="editTopic(${topic.id})">Edit</button>
                                     <button class="mod-btn" onclick="confirmDelete(${topic.id}, true)">Delete</button>` : ''}
                                ${(currentUserRole === 1 || currentUserRole === 2 || currentUserRole === 3) ? 
                                    `<button class="mod-btn" onclick="togglePin(${topic.id}, ${topic.pinned})">
                                        ${topic.pinned ? 'Unpin' : 'Pin'}
                                    </button>
                                    <div class="status-toggle">
                                        <label class="switch">
                                            <input type="checkbox" ${topic.active ? 'checked' : ''} onchange="toggleTopicStatus(${topic.id}, this.checked)">
                                            <span class="slider"></span>
                                        </label>
                                        <span>${topic.active ? 'Active' : 'Hidden'}</span>
                                    </div>` : ''}
                            </div>
                        `;
                        topicList.appendChild(topicElement);
                    });
                })
                .catch(error => {
                    console.error('Error applying filters:', error);
                    alert('Failed to apply filters. Please try again.');
                });
        }

        /**
         * Load users for the user filter dropdown
         */
        function loadUserFilter() {
            fetch(`${API_BASE_URL}?action=get_users`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(users => {
                    const userFilter = document.getElementById('userFilter');
                    users.forEach(user => {
                        const option = document.createElement('option');
                        option.value = user.user_id;
                        option.textContent = user.full_name;
                        userFilter.appendChild(option);
                    });
                })
                .catch(error => console.error('Error loading users for filter:', error));
        }

        function changePage(direction) {
            if (direction === 'prev' && currentPage > 1) {
                currentPage--;
            } else if (direction === 'next' && currentPage < totalPages) {
                currentPage++;
            }
            
            document.getElementById('pageInfo').textContent = `Page ${currentPage}`;
            applyFilters();
        }

        function showAlert(message, type = 'info') {
            const alert = document.getElementById('customAlert');
            alert.textContent = message;
            alert.className = `custom-alert alert-${type}`;
            alert.style.display = 'block';
            
            setTimeout(() => {
                alert.style.display = 'none';
            }, 3000);
        }

        function validateEditForm() {
            const title = document.getElementById('editTopicTitle');
            const content = tinymce.get('editTopicContent').getContent();
            let isValid = true;
            
            // Reset validation states
            title.classList.remove('error-field');
            document.querySelectorAll('.form-error').forEach(err => err.style.display = 'none');
            
            if (!title.value.trim()) {
                title.classList.add('error-field');
                document.getElementById('editTitleError').style.display = 'block';
                isValid = false;
            }
            
            if (!content.trim()) {
                document.getElementById('editContentError').style.display = 'block';
                isValid = false;
            }
            
            return isValid;
        }

        // Add missing functions for user management
        function toggleUserStatus(userId, newStatus) {
            fetch(API_BASE_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'toggle_user_status',
                    user_id: userId,
                    is_active: newStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    showAlert(data.error, 'error');
                    return;
                }
                showAlert('User status updated successfully', 'success');
                loadUserManagement();
            })
            .catch(error => {
                console.error('Error updating user status:', error);
                showAlert('Failed to update user status', 'error');
            });
        }

        function editUserRole(userId) {
            fetch(`${API_BASE_URL}?action=get_roles`)
                .then(response => response.json())
                .then(roles => {
                    const roleOptions = roles.map(role => 
                        `<option value="${role.id}">${role.name}</option>`
                    ).join('');

                    const modalContent = `
                        <h2>Change User Role</h2>
                        <form id="editRoleForm">
                            <div class="form-group">
                                <label for="roleSelect">Select New Role</label>
                                <select id="roleSelect" required>
                                    ${roleOptions}
                                </select>
                            </div>
                            <div class="form-actions">
                                <button type="button" class="btn btn-secondary" onclick="document.getElementById('editTopicModal').style.display='none'">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    `;

                    document.getElementById('editTopicModal').querySelector('.modal-content').innerHTML = modalContent;
                    document.getElementById('editTopicModal').style.display = 'flex';

                    document.getElementById('editRoleForm').addEventListener('submit', function(e) {
                        e.preventDefault();
                        const roleId = document.getElementById('roleSelect').value;
                        updateUserRole(userId, roleId);
                    });
                })
                .catch(error => {
                    console.error('Error loading roles:', error);
                    showAlert('Failed to load roles', 'error');
                });
        }

        function updateUserRole(userId, roleId) {
            fetch(API_BASE_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'update_user_role',
                    user_id: userId,
                    role_id: roleId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    showAlert(data.error, 'error');
                    return;
                }
                document.getElementById('editTopicModal').style.display = 'none';
                showAlert('User role updated successfully', 'success');
                loadUserManagement();
            })
            .catch(error => {
                console.error('Error updating user role:', error);
                showAlert('Failed to update user role', 'error');
            });
        }

        // Add missing functions for category management
        function showCreateCategoryModal() {
            const modalContent = `
                <h2>Create New Category</h2>
                <form id="createCategoryForm">
                    <div class="form-group">
                        <label for="categoryName">Category Name</label>
                        <input type="text" id="categoryName" required>
                    </div>
                    <div class="form-group">
                        <label for="categoryDescription">Description</label>
                        <textarea id="categoryDescription" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="categoryPermission">Permission</label>
                        <select id="categoryPermission" required>
                            <option value="public">Public</option>
                            <option value="private">Private</option>
                            <option value="restricted">Restricted</option>
                        </select>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="document.getElementById('editTopicModal').style.display='none'">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Category</button>
                    </div>
                </form>
            `;

            document.getElementById('editTopicModal').querySelector('.modal-content').innerHTML = modalContent;
            document.getElementById('editTopicModal').style.display = 'flex';

            document.getElementById('createCategoryForm').addEventListener('submit', function(e) {
                e.preventDefault();
                createCategory();
            });
        }

        function createCategory() {
            const name = document.getElementById('categoryName').value;
            const description = document.getElementById('categoryDescription').value;
            const permission = document.getElementById('categoryPermission').value;

            fetch(API_BASE_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'create_category',
                    name: name,
                    description: description,
                    permission: permission
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    showAlert(data.error, 'error');
                    return;
                }
                document.getElementById('editTopicModal').style.display = 'none';
                showAlert('Category created successfully', 'success');
                loadCategoryManagement();
            })
            .catch(error => {
                console.error('Error creating category:', error);
                showAlert('Failed to create category', 'error');
            });
        }

        function editCategory(categoryId) {
            fetch(`${API_BASE_URL}?action=get_categories`)
                .then(response => response.json())
                .then(categories => {
                    const category = categories.find(c => c.category_id === categoryId);
                    if (!category) {
                        showAlert('Category not found', 'error');
                        return;
                    }

                    const modalContent = `
                        <h2>Edit Category</h2>
                        <form id="editCategoryForm">
                            <div class="form-group">
                                <label for="categoryName">Category Name</label>
                                <input type="text" id="categoryName" value="${category.name}" required>
                            </div>
                            <div class="form-group">
                                <label for="categoryDescription">Description</label>
                                <textarea id="categoryDescription" required>${category.description}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="categoryPermission">Permission</label>
                                <select id="categoryPermission" required>
                                    <option value="public" ${category.permission === 'public' ? 'selected' : ''}>Public</option>
                                    <option value="private" ${category.permission === 'private' ? 'selected' : ''}>Private</option>
                                    <option value="restricted" ${category.permission === 'restricted' ? 'selected' : ''}>Restricted</option>
                                </select>
                            </div>
                            <div class="form-actions">
                                <button type="button" class="btn btn-secondary" onclick="document.getElementById('editTopicModal').style.display='none'">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    `;

                    document.getElementById('editTopicModal').querySelector('.modal-content').innerHTML = modalContent;
                    document.getElementById('editTopicModal').style.display = 'flex';

                    document.getElementById('editCategoryForm').addEventListener('submit', function(e) {
                        e.preventDefault();
                        updateCategory(categoryId);
                    });
                })
                .catch(error => {
                    console.error('Error loading category:', error);
                    showAlert('Failed to load category', 'error');
                });
        }

        function updateCategory(categoryId) {
            const name = document.getElementById('categoryName').value;
            const description = document.getElementById('categoryDescription').value;
            const permission = document.getElementById('categoryPermission').value;

            fetch(API_BASE_URL, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'update_category',
                    category_id: categoryId,
                    name: name,
                    description: description,
                    permission: permission
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    showAlert(data.error, 'error');
                    return;
                }
                document.getElementById('editTopicModal').style.display = 'none';
                showAlert('Category updated successfully', 'success');
                loadCategoryManagement();
            })
            .catch(error => {
                console.error('Error updating category:', error);
                showAlert('Failed to update category', 'error');
            });
        }

        function deleteCategory(categoryId) {
            if (!confirm('Are you sure you want to delete this category? This action cannot be undone.')) {
                return;
            }

            fetch(API_BASE_URL, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'delete_category',
                    category_id: categoryId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    showAlert(data.error, 'error');
                    return;
                }
                showAlert('Category deleted successfully', 'success');
                loadCategoryManagement();
            })
            .catch(error => {
                console.error('Error deleting category:', error);
                showAlert('Failed to delete category', 'error');
            });
        }
    </script>
</body>
</html>