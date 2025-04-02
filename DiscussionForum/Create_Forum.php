<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Forum</title>
    <style>
        /* Enhanced Inline CSS */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #e9ecef;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            width: 100%;
        }
        h2 {
            text-align: center;
            color: #444;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        label {
            font-weight: bold;
            color: #555;
        }
        input, textarea, select, button {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
        }
        input:focus, textarea:focus, select:focus {
            border-color: #007bff;
            outline: none;
        }
        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #0056b3;
        }
        .hidden {
            display: none;
        }
        #previewSection {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 6px;
        }
        #previewSection h3 {
            color: #444;
            margin-bottom: 10px;
        }
        #previewSection p {
            margin: 5px 0;
            color: #555;
        }
    </style>
    
</head>
<body>
<div class="container">
        <h2>Create a Forum Category</h2>
        <form id="createForumForm">
            <label for="categoryName">Category Name:</label>
            <input type="text" id="categoryName" name="categoryName" placeholder="Enter category name" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="4" placeholder="Enter description" required></textarea>

            <label for="accessPermissions">Access Permissions:</label>
            <select id="accessPermissions" name="accessPermissions">
                <option value="public">Public</option>
                <option value="private">Private</option>
                <option value="restricted">Restricted</option>
            </select>

            <div id="restrictedOptions" class="hidden">
                <label for="userGroups">Select Users/Groups:</label>
                <input type="text" id="userGroups" name="userGroups" placeholder="Enter users/groups">
            </div>

            <button type="button" onclick="previewCategory()">Preview</button>
            <button type="submit">Submit</button>
        </form>

        <div id="previewSection" class="hidden">
            <h3>Preview:</h3>
            <p><strong>Category Name:</strong> <span id="previewName"></span></p>
            <p><strong>Description:</strong> <span id="previewDescription"></span></p>
            <p><strong>Access Permissions:</strong> <span id="previewPermissions"></span></p>
        </div>
    </div>

    <script>
        // Enhanced Inline JavaScript
        document.getElementById('accessPermissions').addEventListener('change', function () {
            const selectedValue = this.value;
            const restrictedOptions = document.getElementById('restrictedOptions');
            if (selectedValue === 'private' || selectedValue === 'restricted') {
                restrictedOptions.classList.remove('hidden');
            } else {
                restrictedOptions.classList.add('hidden');
            }
        });

        function previewCategory() {
            const name = document.getElementById('categoryName').value;
            const description = document.getElementById('description').value;
            const permissions = document.getElementById('accessPermissions').value;

            document.getElementById('previewName').textContent = name;
            document.getElementById('previewDescription').textContent = description;
            document.getElementById('previewPermissions').textContent = permissions;

            document.getElementById('previewSection').classList.remove('hidden');
        }

        document.getElementById('createForumForm').addEventListener('submit', function (event) {
            event.preventDefault();
            alert('Forum category successfully created!');
        });


///////////////////////
// Sidebar Toggle Functionality
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebarCollapse');
    
    // Initialize from localStorage
    if(localStorage.getItem('sidebarState') === 'open') {
        sidebar.classList.add('active');
        document.body.classList.add('sidebar-open');
        document.querySelector('.main-content')?.classList.add('sidebar-active');
    }
    
    // Toggle sidebar
    if(toggleBtn) {
        toggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const isOpening = !sidebar.classList.contains('active');
            
            sidebar.classList.toggle('active');
            document.body.classList.toggle('sidebar-open');
            document.querySelector('.main-content')?.classList.toggle('sidebar-active');
            
            localStorage.setItem('sidebarState', isOpening ? 'open' : 'closed');
        });
    }
    
    // Highlight current page in sidebar
    const currentPage = window.location.pathname.split('/').pop() || 'index.php';
    document.querySelectorAll('.sidebar a').forEach(link => {
        if(link.getAttribute('href').includes(currentPage)) {
            link.classList.add('active');
        }
    });
});


    </script>
</body>
</html>
