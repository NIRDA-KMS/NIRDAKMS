<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum Management</title>
    <script src="https://cdn.tiny.cloud/1/yy21cxb9sz8dz5s1jswqcenpziyj0y4frg79dtifqqamfxbf/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
    <style>
        /* Enhanced CSS for Better Design */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
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
        h2, h3 {
            text-align: center;
            color: #333;
        }
        input, textarea, button, select {
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
        .topic {
            padding: 15px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 6px;
            background: #f1f1f1;
        }
        .replies {
            margin-left: 20px;
            border-left: 3px solid #ccc;
            padding-left: 10px;
        }
        .pagination {
            text-align: center;
            margin-top: 20px;
        }
        .hidden {
            display: none;
        }
        .flagged {
            background-color: #ffeeba;
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
        <div id="topicsList">
            <!-- Dynamic list of topics -->
        </div>
        <div class="pagination" id="pagination">
            <!-- Pagination Controls -->
        </div>
    </div>

    <div class="container hidden" id="adminPanel">
        <h3>Flagged Topics</h3>
        <div id="flaggedContent">
            <!-- Dynamic list of flagged content for admin -->
        </div>
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
        let flaggedTopics = [];
        const itemsPerPage = 5; // Pagination setup
        let currentPage = 1;

        // Create a new topic
        function createTopic() {
            const title = document.getElementById('topicTitle').value;
            const content = tinymce.get('topicContent').getContent();
            if (title && content) {
                const topic = {
                    id: Date.now(),
                    title,
                    content,
                    replies: [],
                    isPinned: false,
                    isActive: true,
                    isFlagged: false
                };
                topics.push(topic);
                renderTopics();
                document.getElementById('createTopicForm').reset();
                tinymce.get('topicContent').setContent('');
            } else {
                alert('Please complete all fields!');
            }
        }

        // Render topics with pagination
        function renderTopics() {
            const topicsList = document.getElementById('topicsList');
            topicsList.innerHTML = '';
            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;
            const paginatedTopics = topics.slice(startIndex, endIndex);

            paginatedTopics.forEach(topic => {
                const topicDiv = document.createElement('div');
                topicDiv.className = `topic ${topic.isFlagged ? 'flagged' : ''}`;
                const pinnedBadge = topic.isPinned ? '<strong>[Pinned]</strong> ' : '';

                topicDiv.innerHTML = `
                    <h4>${pinnedBadge}${topic.title}</h4>
                    <p>${topic.content}</p>
                    <div>
                        <button onclick="editTopic(${topic.id})">Edit</button>
                        <button onclick="deleteTopic(${topic.id})">Delete</button>
                        <button onclick="toggleActivate(${topic.id})">
                            ${topic.isActive ? 'Deactivate' : 'Activate'}
                        </button>
                        <button onclick="pinTopic(${topic.id})">${topic.isPinned ? 'Unpin' : 'Pin'}</button>
                        <button onclick="flagTopic(${topic.id})">Flag</button>
                    </div>
                    <div class="replies">${renderReplies(topic.replies)}</div>
                `;
                topicsList.appendChild(topicDiv);
            });

            renderPagination();
        }

        // Render replies (threaded view)
        function renderReplies(replies) {
            return replies.map(reply => `<p>${reply}</p>`).join('');
        }

        // Pagination rendering
        function renderPagination() {
            const pagination = document.getElementById('pagination');
            pagination.innerHTML = '';
            const totalPages = Math.ceil(topics.length / itemsPerPage);
            for (let i = 1; i <= totalPages; i++) {
                const button = document.createElement('button');
                button.textContent = i;
                button.onclick = () => {
                    currentPage = i;
                    renderTopics();
                };
                if (i === currentPage) {
                    button.style.backgroundColor = '#0056b3';
                    button.style.color = '#fff';
                }
                pagination.appendChild(button);
            }
        }

        // Search topics
        function searchTopics() {
            const query = document.getElementById('searchBar').value.toLowerCase();
            const filteredTopics = topics.filter(topic => 
                topic.title.toLowerCase().includes(query) || 
                topic.content.toLowerCase().includes(query)
            );
            renderFilteredTopics(filteredTopics);
        }

        // Render filtered topics
        function renderFilteredTopics(filteredTopics) {
            const topicsList = document.getElementById('topicsList');
            topicsList.innerHTML = '';
            filteredTopics.forEach(topic => {
                const topicDiv = document.createElement('div');
                topicDiv.className = 'topic';
                topicDiv.innerHTML = `<h4>${topic.title}</h4><p>${topic.content}</p>`;
                topicsList.appendChild(topicDiv);
            });
        }

        // Pin, Flag, Delete, Edit, and Activation logic...
    </script>
</body>
</html>









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