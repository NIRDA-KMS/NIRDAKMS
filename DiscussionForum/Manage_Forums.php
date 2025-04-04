<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum Management</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.tiny.cloud/1/yy21cxb9sz8dz5s1jswqcenpziyj0y4frg79dtifqqamfxbf/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background: white;
            padding: 20px;
            margin: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 800px;
        }
        input, textarea, button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background: #007bff;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        button:hover {
            background: #0056b3;
        }
        .topic, .reply {
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background: #f1f1f1;
            margin-bottom: 10px;
        }
        .reply {
            margin-left: 20px;
            background: #e9ecef;
        }
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Forum Management</h2>
        <h3>Create New Topic</h3>
        <form id="createTopicForm">
            <input type="text" id="topicTitle" placeholder="Enter topic title" required>
            <textarea id="topicContent"></textarea>
            <button type="button" onclick="createTopic()">Create Topic</button>
        </form>
    </div>

    <div class="container">
        <h3>Topics</h3>
        <input type="text" id="searchBar" placeholder="Search topics..." oninput="searchTopics()">
        <div id="topicsList"></div>
    </div>

    <script>
       tinymce.init({
        selector: 'textarea',
        plugins: [
          // Core editing features
          'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'image', 'link', 'lists', 'media', 'searchreplace', 'table', 'visualblocks', 'wordcount',
          // Your account includes a free trial of TinyMCE premium features
          // Try the most popular premium features until Apr 16, 2025:
          'checklist', 'mediaembed', 'casechange', 'formatpainter', 'pageembed', 'a11ychecker', 'tinymcespellchecker', 'permanentpen', 'powerpaste', 'advtable', 'advcode', 'editimage', 'advtemplate', 'ai', 'mentions', 'tinycomments', 'tableofcontents', 'footnotes', 'mergetags', 'autocorrect', 'typography', 'inlinecss', 'markdown','importword', 'exportword', 'exportpdf'
        ],
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
        tinycomments_mode: 'embedded',
        tinycomments_author: 'Author name',
        mergetags_list: [
          { value: 'First.Name', title: 'First Name' },
          { value: 'Email', title: 'Email' },
        ],
        ai_request: (request, respondWith) => respondWith.string(() => Promise.reject('See docs to implement AI Assistant')),
      });


        let topics = [];

        function createTopic() {
            const title = document.getElementById('topicTitle').value;
            const content = tinymce.get('topicContent').getContent();
            if (title && content) {
                const topic = {
                    id: Date.now(),
                    title,
                    content,
                    replies: [],
                    pinned: false,
                    deactivated: false,
                    flagged: false
                };
                topics.push(topic);
                renderTopics();
                document.getElementById('createTopicForm').reset();
                tinymce.get('topicContent').setContent('');
            } else {
                alert('Please complete all fields!');
            }
        }

        function renderTopics() {
            const topicsList = document.getElementById('topicsList');
            topicsList.innerHTML = '';
            topics.forEach(topic => {
                const topicDiv = document.createElement('div');
                topicDiv.className = 'topic';
                topicDiv.innerHTML = `
                    <h4>${topic.title}</h4>
                    <p>${topic.content}</p>
                    <button onclick="editTopic(${topic.id})">Edit</button>
                    <button onclick="deleteTopic(${topic.id})">Delete</button>
                    <button onclick="toggleDeactivate(${topic.id})">
                        ${topic.deactivated ? 'Activate' : 'Deactivate'}
                    </button>
                    <button onclick="flagTopic(${topic.id})">Flag</button>
                    <button onclick="pinTopic(${topic.id})">
                        ${topic.pinned ? 'Unpin' : 'Pin'}
                    </button>
                    <button onclick="replyToTopic(${topic.id})">Reply</button>
                    <div id="replies-${topic.id}"></div>
                `;
                topicsList.appendChild(topicDiv);
                renderReplies(topic.id);
            });
        }

        function replyToTopic(topicId) {
            const replyText = prompt('Enter your reply:');
            if (replyText) {
                const topic = topics.find(t => t.id === topicId);
                topic.replies.push({ id: Date.now(), text: replyText });
                renderReplies(topicId);
            }
        }

        function renderReplies(topicId) {
            const topic = topics.find(t => t.id === topicId);
            const repliesDiv = document.getElementById(`replies-${topicId}`);
            repliesDiv.innerHTML = '';
            topic.replies.forEach(reply => {
                const replyDiv = document.createElement('div');
                replyDiv.className = 'reply';
                replyDiv.innerHTML = `<p>${reply.text}</p>`;
                repliesDiv.appendChild(replyDiv);
            });
        }

        function searchTopics() {
            const query = document.getElementById('searchBar').value.toLowerCase();
            const filteredTopics = topics.filter(topic => 
                topic.title.toLowerCase().includes(query) || 
                topic.content.toLowerCase().includes(query)
            );
            renderFilteredTopics(filteredTopics);
        }

        function renderFilteredTopics(filteredTopics) {
            const topicsList = document.getElementById('topicsList');
            topicsList.innerHTML = '';
            filteredTopics.forEach(topic => {
                const topicDiv = document.createElement('div');
                topicDiv.className = 'topic';
                topicDiv.innerHTML = `
                    <h4>${topic.title}</h4>
                    <p>${topic.content}</p>
                `;
                topicsList.appendChild(topicDiv);
            });
        }
    </script>
</body>
</html> -->



<!--  -->


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum Management System</title>
    <script src="https://cdn.tiny.cloud/1/yy21cxb9sz8dz5s1jswqcenpziyj0y4frg79dtifqqamfxbf/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }
        .container {
            
            /* margin-top:2px; */
            background: white;
            padding: 5px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
        }
        .header-container{
            background: white;
            padding: 5px;
            /* margin-left: 24rem; */


        }
        
        
        h1 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 24px;
        }
        
        .forum-layout {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 20px;
        }
        
        .sidebar {
            background-color: white;
            border-radius: 5px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .category-list h3 {
            
             padding:6px; 
            background:#2c3e50; 
            color:white; 
            border:none; 
            border-radius:4px;
        }

        
        
        .category-list ul {
            list-style: none;
        }
        
        .category-list li {
            padding: 8px 0;
            border-bottom: 1px solid #f5f5f5;
        }
        
        .category-list a {
            color: #3498db;
            text-decoration: none;
        }
        
        .main-content {
            margin: 0 5.5rem;
            background-color: white;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .topic-list-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .create-topic-btn , .review-report-btn {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        .review-report-btn{
            /* margin-left: 45rem; */
            margin-top: 5rem;

        }
      
        
        .topic-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
            position: relative;
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
        }
        
        .topic-stats {
            display: flex;
            gap: 15px;
        }
        
        .pinned-badge {
            background-color: #2ecc71;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 12px;
            margin-left: 10px;
        }
        
        .mod-controls {
            position: absolute;
            top: 15px;
            right: 15px;
            display: flex;
            gap: 10px;
        }
        
        .mod-btn {
            background: none;
            border: none;
            color: #7f8c8d;
            cursor: pointer;
            font-size: 14px;
        }
        
        .mod-btn:hover {
            color: #3498db;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            width: 80%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .form-group input[type="text"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }
        
        .btn {
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .btn-primary {
            background-color: #3498db;
            color: white;
            border: none;
        }
        
        .btn-secondary {
            background-color: #95a5a6;
            color: white;
            border: none;
        }
        
        .reply {
            margin-left: 30px;
            padding: 10px;
            border-left: 2px solid #eee;
            margin-bottom: 15px;
        }
        
        .reply-content {
            margin-bottom: 10px;
        }
        
        .reply-meta {
            font-size: 12px;
            color: #7f8c8d;
        }
        
        .flagged {
            border-left: 3px solid #e74c3c;
            background-color: #fff5f5;
        }
        
       
        
        .deactivated {
            opacity: 0.6;
            background-color: #f9f9f9;
            border-left: 3px solid #95a5a6;
        }
        
        .flag-btn {
            background: none;
            border: none;
            color: #e74c3c;
            cursor: pointer;
            font-size: 12px;
            margin-left: 10px;
        }
        
        .flag-form {
            display: none;
            padding: 10px;
            background: #fff5f5;
            margin-top: 5px;
            border-radius: 4px;
        }
        
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
            transition: .4s;
            border-radius: 20px;
        }
        
        .slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 2px;
            bottom: 2px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        
        input:checked + .slider {
            background-color: #2ecc71;
        }
        
        input:checked + .slider:before {
            transform: translateX(20px);
        }
        
        .flagged-item {
            padding: 10px;
            margin-bottom: 10px;
            border-left: 3px solid #e74c3c;
            background-color: #fff5f5;
        }
        
        .admin-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        
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
        }
    </style>
</head>
<body>
        <div class="header-container">
            <h1>Discussion Forum</h1>       
        </div>
    
    <div class="container">
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
                
                <div class="forum-stats" style="margin-top: 20px;">
                    <h3>Forum Statistics</h3>
                    <p>Topics: 124</p>
                    <p>Posts: 892</p>
                    <p>Members: 342</p>
                </div>
            </aside>
            
            <main class="main-content">
                <div class="topic-list-header">
                    <h2>Recent Topics</h2>
                    <button class="create-topic-btn" id="createTopicBtn">Create New Topic</button>
                </div>
               

              
                
                <div class="topic-list" id="topicList">
                    <!-- Topics will be loaded here -->
                     
                </div>

                 <button class="review-report-btn" id="reviewReports">
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
            
            <div class="admin-tab-content" id="flaggedContent" style="margin-top: 20px;">
                <h3>Flagged Posts <span id="flag-count">(0)</span></h3>
                <div class="flagged-posts-list" id="flaggedPostsList">
                    <!-- Flagged posts will be loaded here -->
                </div>
            </div>
            
            <div class="admin-tab-content" id="usersContent" style="display: none; margin-top: 20px;">
                <h3>User Management</h3>
                <p>User management content goes here</p>
            </div>
            
            <div class="admin-tab-content" id="categoriesContent" style="display: none; margin-top: 20px;">
                <h3>Category Management</h3>
                <p>Category management content goes here</p>
            </div>
        </div>
    </div>
    
    <script>
        // Initialize TinyMCE
        tinymce.init({
            selector: '#topicContent',
            plugins: 'link lists code',
            toolbar: 'bold italic | bullist numlist | link code',
            menubar: false,
            content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; }'
        });
        
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
        createTopicBtn.addEventListener('click', () => {
            createTopicModal.style.display = 'flex';
        });
        
        cancelTopicBtn.addEventListener('click', () => {
            createTopicModal.style.display = 'none';
        });
        
        reviewReportsBtn.addEventListener('click', (e) => {
            e.preventDefault();
            reviewReportsModal.style.display = 'flex';
            loadFlaggedContent();
        });
        
        topicForm.addEventListener('submit', (e) => {
            e.preventDefault();
            createNewTopic();
        });
        
        // Close modals when clicking outside
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal')) {
                e.target.style.display = 'none';
            }
        });
        
        // Admin tab switching
        document.querySelectorAll('.admin-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('.admin-tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.admin-tab-content').forEach(c => c.style.display = 'none');
                
                tab.classList.add('active');
                document.getElementById(`${tab.dataset.tab}Content`).style.display = 'block';
            });
        });
        
        // Functions
        function loadTopics() {
            topicList.innerHTML = '';
            
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
        
        function viewTopic(topicId) {
            const topic = topics.find(t => t.id === topicId);
            if (!topic) return;
            
            document.getElementById('viewTopicTitle').textContent = topic.title;
            document.getElementById('viewTopicMeta').innerHTML = `
                Posted by ${topic.author} in ${topic.category} on ${topic.date}
            `;
            document.getElementById('viewTopicContent').innerHTML = topic.content;
            
            // Load replies
            const repliesContainer = document.getElementById('topicReplies');
            repliesContainer.innerHTML = '<h3>Replies</h3>';
            
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
        
        function editTopic(topicId) {
            const topic = topics.find(t => t.id === topicId);
            if (!topic) return;
            
            // In a real app, this would open an edit form
            alert(`Edit functionality for topic ${topicId} would open an edit form`);
        }
        
        function editReply(replyId, topicId) {
            // In a real app, this would open an edit form
            alert(`Edit functionality for reply ${replyId} in topic ${topicId}`);
        }
        
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
        
        function togglePin(topicId, currentlyPinned) {
            const topic = topics.find(t => t.id === topicId);
            if (topic) {
                topic.pinned = !currentlyPinned;
                loadTopics();
            }
        }
        
        function toggleTopicStatus(topicId, isActive) {
            const topic = topics.find(t => t.id === topicId);
            if (topic) {
                topic.active = isActive;
                loadTopics();
            }
        }
        
        function showFlagForm(replyId) {
            document.querySelectorAll('.flag-form').forEach(form => form.style.display = 'none');
            document.getElementById(`flag-form-${replyId}`).style.display = 'block';
        }
        
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
                            <div class="admin-actions" style="margin-top:10px">
                                <button class="btn btn-primary" onclick="resolveFlag(${reply.id}, ${topic.id}, 'keep')">Keep Content</button>
                                <button class="btn btn-secondary" onclick="resolveFlag(${reply.id}, ${topic.id}, 'warn')">Warn User</button>
                                <button class="btn" style="background:#e74c3c;color:white" onclick="resolveFlag(${reply.id}, ${topic.id}, 'delete')">Delete Post</button>
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
        
        // Initialize the page
        loadTopics();
    </script>
</body>
</html>

<!--  -->




































<!-- <!DOCTYPE html> -->
<!-- <html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>hel</h1>
</body>
</html> -->
<!-- Place the first <script> tag in your HTML's <head> -->
    <!-- <script src="https://cdn.tiny.cloud/1/yy21cxb9sz8dz5s1jswqcenpziyj0y4frg79dtifqqamfxbf/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script> -->

    <!-- Place the following <script> and <textarea> tags your HTML's <body> -->
    <!-- <script>
      tinymce.init({
        selector: 'textarea',
        plugins: [
          // Core editing features
          'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'image', 'link', 'lists', 'media', 'searchreplace', 'table', 'visualblocks', 'wordcount',
          // Your account includes a free trial of TinyMCE premium features
          // Try the most popular premium features until Apr 16, 2025:
          'checklist', 'mediaembed', 'casechange', 'formatpainter', 'pageembed', 'a11ychecker', 'tinymcespellchecker', 'permanentpen', 'powerpaste', 'advtable', 'advcode', 'editimage', 'advtemplate', 'ai', 'mentions', 'tinycomments', 'tableofcontents', 'footnotes', 'mergetags', 'autocorrect', 'typography', 'inlinecss', 'markdown','importword', 'exportword', 'exportpdf'
        ],
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
        tinycomments_mode: 'embedded',
        tinycomments_author: 'Author name',
        mergetags_list: [
          { value: 'First.Name', title: 'First Name' },
          { value: 'Email', title: 'Email' },
        ],
        ai_request: (request, respondWith) => respondWith.string(() => Promise.reject('See docs to implement AI Assistant')),
      });
    </script>
    <textarea>
      Welcome to TinyMCE!
    </textarea> -->