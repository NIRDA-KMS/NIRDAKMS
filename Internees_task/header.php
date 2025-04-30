

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title style="color: white;">NIRDA Knowledge Management System</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
<!-- <link rel="stylesheet" href="../header.css"> -->


<style>
    /* header.css */





    :root {
    --primary-color: #1a237e;
    --secondary-color: #2c3e50;
    --accent-color: #e74c3c;
    --background-color: #ecf0f1;
    --text-color: #34495e;
}

body {
    font-family: 'Roboto', sans-serif;
    line-height: 1.6;
    margin: 0;
    padding: 0;
    background-color: #f0f2f5;
    color: #333;
    padding-top: 50px; 
}

.navbar {
    background-color: #2c3e50;
    padding: 5px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1100;
    height: 60px;
}

.navbar-brand {
    display: flex;
    align-items: center;
    color: white;
    text-decoration: none;
    font-size: 18px;
}

.navbar-title {
    color: white;
    font-size: 18px;
    font-weight: bold;
    margin-left: 20px;
}

.navbar-icons {
    display: flex;
    align-items: center;
}

.navbar-icons a {
    color: white;
    margin-left: 20px;
    font-size: 18px;
    position: relative;
}

.notification-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background-color: red;
    color: white;
    border-radius: 50%;
    padding: 1px 6px;
    font-size: 10px;
}

.dropdown {
    position: relative;
    display: inline-block;
}

.dropdown-content {
    display: none;
    position: absolute;
    background-color: #f9f9f9;
    min-width: 200px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    z-index: 1200;
    top: 100%; 
    right: 0;
}

.dropdown-content a {
    color: black;
    padding: 12px 16px;
    border-radius: 5px;
    text-decoration: none;
    display: block;
    font-size: 14px;
}

.dropdown-content a i {
    padding-right: 0px;
}

.dropdown-content a:hover {
    background-color: #f1f1f1;
}

.dropdown:hover .dropdown-content {
    display: block;
}

.user-profile {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #ddd;
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: hidden;
}

.user-profile img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.logo {
    text-align: center;
    margin-bottom: 0px;
    position: relative;
    z-index: 10;
}

.logo-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background-color: #2c3e50;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 0 auto;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    position: relative;
}

.logo img {
    max-width: 50px;
    max-height: 50px;
    border-radius: 10%;
    object-fit: contain;
}

.notice-bar {
display: none;
background-color: #f8d7da;
color: #721c24;
padding: -5px 0;
position: fixed;
top: 60px;
left: 0;
right: 0;
z-index: 1050;
white-space: nowrap;
overflow: hidden;
}

.notice-bar.active {
display: block;

}

.notice-content {
display: inline-block;
white-space: nowrap;
padding-left: 100%;
animation: scroll-left 45s linear infinite;
}
.notice-message {
display: inline-block;
padding-right: 50px; /* Space between messages */
}

@keyframes scroll-left {
0% { transform: translateX(0); }
100% { transform: translateX(-100%); }
}
nav {
background: #1a237e;
padding: 0.5rem 0;
position: fixed;
top: 60px; /* Default position when notice bar is not visible */
left: 0;
right: 0;
z-index: 1000;
transition: 0.3s;
}

nav.notice-active {
top: 85px; /* Position when notice bar is visible */
}

.main-nav {
    padding:0;
    padding-right:100px;
    padding-left:100px;
    list-style: none;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 0px;
    height: 20px;
}

.main-nav li {
    margin: 0 5px;
}

.main-nav a {
    color: #fff;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 400;
    transition: all 0.3s ease;
    padding: 5px 10px;
    border-radius: 5px;
    display: flex;
    align-items: center;
}

.main-nav a i {
    margin-right: 0px;
}

.main-nav a:hover, .main-nav a.active {
    background-color: #00A0DF;
    color: #fff;
}

.dropdown {
    position: relative;
    display: inline-block;
}

.dropdown-content {
    display: none;
    position: absolute;
    right: 10;
    background-color: var(--primary-color);
    min-width: 200px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    z-index: 1;
}

.dropdown-content a, .nested-dropdown > a {
    color: #fff;
    padding: 8px 10px;
    text-decoration: none;
    display: block;
}

.dropdown-content a:hover, .nested-dropdown > a:hover {
    background-color: #00A0DF;
}

.dropdown:hover .dropdown-content {
    display: block;
}

.nested-dropdown {
    position: relative;
}

.nested-dropdown-content {
    display: none;
    position: absolute;
    right: 100%;
    top: 0;
    background-color: var(--primary-color);
    min-width: 210px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
}

.nested-dropdown:hover .nested-dropdown-content {
    display: block;
}

/* Sidebar Styles */
.sidebar {
    width: 250px;
    background-color: #1a237e; /* NIRDA primary color */
    color: white;
    height: calc(100vh - 60px); /* Full height minus header */
    position: fixed;
    left: 0;
    top: 60px; /* Below header */
    transform: translateX(-100%);
    transition: transform 0.3s ease;
    z-index: 1000;
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    overflow-y: auto;
    padding-top: 20px;
}

.sidebar.active {
    transform: translateX(0);
}

.sidebar a {
    color: white;
    padding: 12px 20px;
    text-decoration: none;
    display: block;
    transition: all 0.3s ease;
    margin: 5px 10px;
    border-radius: 4px;
    font-size: 0.95rem;
}

.sidebar a:hover {
    background-color: #00A0DF; /* NIRDA accent color */
    transform: translateX(5px);
}

.sidebar a i {
    margin-right: 10px;
    width: 20px;
    text-align: center;
}

/* Body adjustment when sidebar is open */
body.sidebar-open {
    overflow-x: hidden;
}

/* Main content adjustment */
.main-content {
    transition: margin-left 0.3s;
    margin-left: 0;
}

.main-content.sidebar-active {
    margin-left: 250px;
}

/* Responsive behavior */
@media (max-width: 768px) {
    .sidebar {
        width: 80%;
    }
    
    .sidebar.active {
        box-shadow: 5px 0 15px rgba(0, 0, 0, 0.2);
    }
    
    .main-content.sidebar-active {
        margin-left: 0;
        position: relative;
        left: 80%;
        width: 100%;
    }
    
    .sidebar a:hover {
        transform: none;
        background-color: rgba(0, 160, 223, 0.8);
    }
}

/* Animation for active menu item */
.sidebar a.active {
    background-color: #00A0DF;
    font-weight: 500;
    border-left: 3px solid white;
}
.main-content {
    margin-left: 0;
    transition: 0.3s;
    padding-top: 10px;
}

.main-content.active {
    margin-left: 220px;
}

@media (max-width: 768px) {
    .main-nav {
        flex-wrap: wrap;
    }
    .main-nav li {
        flex: 1 1 auto;
        text-align: center;
        margin-bottom: 5px;
    }
    .main-nav a {
        justify-content: center;
    }
    .dropdown {
        flex: 1 1 100%;
    }
    .dropdown-content, .nested-dropdown-content {
        width: 100%;
        position: static;
    }
    .nested-dropdown-content {
        padding-left: 20px;
    }
    nav.active {
        left: 0;
    }
}
nav:not(.navbar) {
transition: margin-left 0.3s;
margin-left: 0;
}

nav:not(.navbar).sidebar-active {
margin-left: 220px;
}

.main-content {
transition: margin-left 0.3s;
margin-left: 0;
}

.main-content.sidebar-active {
margin-left: 220px;
}

/* closing header.css */




/* footer styles */
body, html {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

/* Wrapper for content */
.footer-content {
    flex: 1;  /* Pushes footer to the bottom */
    padding-bottom: 80px; /* Prevents overlap with footer */
}

/* Fixed Footer */
.footer {
    background-color: #00a1e4;
    color: white;
    text-align: center;
    position: fixed;
     bottom: 0;
    width: 100%;
    z-index: 1000;
}

/* Contact Info */
.footer .contact-info {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
}

.footer .contact-info div {
    margin: 10px 20px;
    font-size: 16px;
}

/* Icons */
.footer i {
    margin-right: 8px;
}

/* Responsive Design */
@media (max-width: 600px) {
    .footer .contact-info {
        flex-direction: column;
        text-align: center;
    }

    .footer .contact-info div {
        margin: 10px 0;
    }
}


</style>

</head>


<body>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebarCollapse');
    
    toggleBtn.addEventListener('click', function(e) {
        e.preventDefault();
        sidebar.classList.toggle('active');
        document.body.classList.toggle('sidebar-open');
        
        // Close when clicking outside (optional)
        if (sidebar.classList.contains('active')) {
            document.addEventListener('click', closeSidebar);
        } else {
            document.removeEventListener('click', closeSidebar);
        }
    });
    
    function closeSidebar(e) {
        if (!sidebar.contains(e.target) && e.target !== toggleBtn && !toggleBtn.contains(e.target)) {
            sidebar.classList.remove('active');
            document.body.classList.remove('sidebar-open');
            document.removeEventListener('click', closeSidebar);
        }
    }
});
</script>
    <nav class="navbar">
        <div class="navbar-brand">
            <a href="#" id="sidebarCollapse" style="padding-right: 40px;">
                <i class="fas fa-bars" style="color: white;"></i>
            </a>
            <div class="logo">
                <div class="logo-circle">
                    <img src="images/nirda_logo.png" alt="NIRDA Knowledge Management System">
                </div>
            </div>
            <span class="navbar-title"><h1 style="color:#fff;">NIRDA Knowledge Hub - <?php echo isset($page_title) ? $page_title : 'Dashboard'; ?></h1></span>
        </div>
        <div class="navbar-icons">
            <a href="#"><i class="fas fa-search"></i></a>
            <div class="dropdown">
                <a href="#"><i class="fas fa-globe"></i></a>
                <div class="dropdown-content">
                    <a href="#"><i class="fas fa-flag"></i> English</a>
                    <a href="#"><i class="fas fa-flag"></i> Français</a>
                    <a href="#"><i class="fas fa-flag"></i> Kinyarwanda</a>
                </div>
            </div>
            <a href="#"><i class="fas fa-bell"></i><span class="notification-badge">3</span></a>
            <a href="#"><i class="far fa-comment"></i><span class="notification-badge">2</span></a>
            <div class="dropdown">
                <a href="#" class="user-profile">
                    <img src="path/to/default-avatar.png" alt="User" id="userProfilePic">
                    <span class="dropdown-arrow"></span>
                </a>
                <div class="dropdown-content">
                    <a href="#"><i class="fas fa-user"></i> Profile</a>
                    <a href="#"><i class="fas fa-envelope"></i> Messages</a>
                    <a href="#"><i class="fas fa-exchange-alt"></i> Switch role</a>
                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Log out</a>
                </div>
            </div>
        </div>
    </nav>

<?php if (!empty($activeMessages)): ?>
<div class="notice-bar active">
    <div class="notice-content">
        <?php foreach ($activeMessages as $message): ?>
            <span class="notice-message"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></span>
        <?php endforeach; ?>
    </div>
    <button class="close-notice">&times;</button>
</div>
<?php endif; ?>

<nav>
        <ul class="main-nav">
                <li><a href="sample_home.php" onclick="showContent('home')"><i class="fas fa-home"></i> Home</a></li>
                <li class="dropdown">
                    <a href="#" class="dropbtn"><i class="fas fa-archive"></i> Repository</a>
                    <div class="dropdown-content">
                        <a href="upload_document.php" onclick="showContent('uploadFile')"><i class="fas fa-upload"></i> Upload File</a>
                        <a href="create_folder.php" onclick="showContent('createFolder')"><i class="fas fa-folder-plus"></i> Create Folder</a>
                        <a href="manage_folders1.php" onclick="showContent('versionHistory')"><i class="fas fa-history"></i> Version History</a>
                        <a href="Add_knowledge_Area.php" onclick="showContent('addKnowledgeArea')"><i class="fas fa-plus"></i> Add Knowledge Area</a>
                        <a href="ticket.php" onclick="showContent('updateKnowledgeArea')"><i class="fas fa-edit"></i> Update Knowledge Area</a>
                        <a href="#" onclick="showContent('deleteKnowledgeArea')"><i class="fas fa-trash"></i> Delete Knowledge Area</a>
                        <a href="Add_Document.php" onclick="showContent('addDocument')"><i class="fas fa-plus"></i> Add Document</a>
                        <a href="#" onclick="showContent('updateDocument')"><i class="fas fa-edit"></i> Update Document</a>
                        <a href="#" onclick="showContent('deleteDocument')"><i class="fas fa-trash"></i> Delete Document</a>
                        <a href="#" onclick="showContent('manageUsers')"><i class="fas fa-users-cog"></i> Manage Users</a>
                        <a href="#" onclick="showContent('generateReport')"><i class="fas fa-chart-bar"></i> Generate Report</a>
                    </div>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropbtn"><i class="fas fa-search"></i> Search</a>
                    <div class="dropdown-content">
                        <a href="#" onclick="showContent('addRecentSearch')"><i class="fas fa-plus"></i> Add Recent Search</a>
                        <a href="#" onclick="showContent('updateRecentSearch')"><i class="fas fa-edit"></i> Update Recent Search</a>
                        <a href="#" onclick="showContent('deleteRecentSearch')"><i class="fas fa-trash"></i> Delete Recent Search</a>
                        <a href="#" onclick="showContent('saveSearch')"><i class="fas fa-save"></i> Save Search</a>
                        <a href="#" onclick="showContent('exportResults')"><i class="fas fa-file-export"></i> Export Results</a>
                    </div>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropbtn"><i class="fas fa-users"></i> Collaboration</a>
                    <div class="dropdown-content">
                        <div class="nested-dropdown">
                        <a href="../SchedureEvent/schedule_event.php" class="nested-dropbtn">
  <i class="fas fa-calendar-alt"></i> Events
</a>
                            <div class="nested-dropdown-content">
                                <a href="../SchedureEvent/schedule_event.php" onclick="showContent('scheduleEvent')"><i class="fas fa-calendar-plus"></i> Schedule Event</a>
                                <a href="../SchedureEvent/manage_events.php" onclick="showContent('manageEvents')"><i class="fas fa-calendar-check"></i> Manage Events</a>
                            </div>
                        </div>
                        <div class="nested-dropdown">
                            <a href="#" class="nested-dropbtn"><i class="fas fa-comments"></i> Discussion Forum</a>
                            <div class="nested-dropdown-content">
                                <a href="../DiscussionForum/Create_Forum.php" onclick="showContent('createForum')"><i class="fas fa-plus-circle"></i> Create Forum</a>
                                <a href="../DiscussionForum/Manage_Forums.php" onclick="showContent('manageForums')"><i class="fas fa-cogs"></i> Manage Forums</a>
                            </div>
                        </div>
                        <div class="nested-dropdown">
                            <a href="#" class="nested-dropbtn"><i class="fas fa-project-diagram"></i> Collaborative Projects</a>
                            <div class="nested-dropdown-content">
                                <a href="../CollaborativeProjects/create_project.php" onclick="showContent('createProject')"><i class="fas fa-plus-circle"></i> Create Project</a>
                                <a href="../CollaborativeProjects/manage_projects.php" onclick="showContent('manageProjects')"><i class="fas fa-tasks"></i> Manage Projects</a>
                                <a href="../CollaborativeProjects/project_members.php" onclick="showContent('projectMembers')"><i class="fas fa-user-plus"></i> Manage Members</a>
                            </div>
                        </div>
                        <div class="nested-dropdown">
                            <!-- <a href="#" class="nested-dropbtn"><i class="fas fa-comment-dots"></i> Chat Management</a> -->
                            <!-- <div class="nested-dropdown-content"> -->
                                <!-- <a href="../Chat_managements/chat_contacts.php" onclick="showContent('chatContacts')"><i class="fas fa-address-book"></i> Contacts</a> -->
                                <!-- <a href="../Chat_managements/online_users.php" onclick="showContent('onlineUsers')"><i class="fas fa-user-circle"></i> Online Users</a> -->
                                <a href="../Chat_managements/chat_groups.php" onclick="showContent('chatGroups')"><i class="fas fa-users"></i> chat application</a>
                                <!-- <a href="../Chat_managements/manage_chats.php" onclick="showContent('manageChats')"><i class="fas fa-comments"></i> Manage Chats</a> -->
                            <!-- </div> -->
                        </div>
                        </div>

                    
                </li>
                <li class="dropdown">
                    <a href="#" class="dropbtn"><i class="fas fa-chart-bar"></i> Analytics</a>
                    <div class="dropdown-content">
                        <a href="#" onclick="showContent('usageReport')"><i class="fas fa-chart-line"></i> Usage Report</a>
                        <a href="#" onclick="showContent('dashboard')"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                        <a href="#" onclick="showContent('customReport')"><i class="fas fa-file-export"></i> Custom Report</a>
                        <a href="#" onclick="showContent('addReport')"><i class="fas fa-plus"></i> Add Report</a>
                        <a href="#" onclick="showContent('updateReport')"><i class="fas fa-edit"></i> Update Report</a>
                        <a href="#" onclick="showContent('deleteReport')"><i class="fas fa-trash"></i> Delete Report</a>
                    </div>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropbtn"><i class="fas fa-fire"></i> Trending</a>
                    <div class="dropdown-content">
                        <a href="#" onclick="showContent('addTrendingItem')"><i class="fas fa-plus"></i> Add Trending Item</a>
                        <a href="#" onclick="showContent('updateTrendingItem')"><i class="fas fa-edit"></i> Update Trending Item</a>
                        <a href="#" onclick="showContent('deleteTrendingItem')"><i class="fas fa-trash"></i> Delete Trending Item</a>
                        <a href="#" onclick="showContent('shareTrendingItem')"><i class="fas fa-share"></i> Share Trending Item</a>
                        <a href="#" onclick="showContent('subscribeTrends')"><i class="fas fa-bell"></i> Subscribe to Trends</a>
                    </div>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropbtn"><i class="fas fa-user-cog"></i> Users</a>
                    <div class="dropdown-content">
                        <a href="" onclick="showContent('addUser')"><i class="fas fa-user-plus"></i> Add User</a>
                        <a href="add_role.php" onclick="showContent('addRole')"><i class="fas fa-user-plus"></i> Add Role</a>
                        <a href="manage_users.php" onclick="showContent('manageUser')"><i class="fas fa-user-cog"></i> Manage Users</a>
                        <a href="manage_Roles.php" onclick="showContent('manageRoles')"><i class="fas fa-users-cog"></i> Manage Roles</a>
                        <a href="manage_Permissions.php" onclick="showContent('managePermissions')"><i class="fas fa-lock"></i> Manage Permissions</a>
                    </div>
                </li>

                <li class="dropdown">
                    <a href="#" class="dropbtn"><i class="fas fa-cogs"></i> Config</a>
                    <div class="dropdown-content">
                        <a href="#" onclick="showContent('configureGeneral')"><i class="fas fa-cog"></i> General Settings</a>
                        <a href="#" onclick="showContent('configureEmail')"><i class="fas fa-envelope"></i> Email Settings</a>
                        <a href="#" onclick="showContent('configureSecurity')"><i class="fas fa-shield-alt"></i> Security Settings</a>
                        <a href="#" onclick="showContent('manageBackups')"><i class="fas fa-database"></i> Manage Backups</a>
                    </div>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropbtn"><i class="fas fa-ellipsis-h"></i> More</a>
                    <div class="dropdown-content">
                        <div class="nested-dropdown">
                            <a href="#" class="nested-dropbtn"><i class="fas fa-sitemap"></i> Taxonomy</a>
                            <div class="nested-dropdown-content">
                                <a href="create_taxonomy.php" onclick="showContent('addTaxonomy')"><i class="fas fa-plus"></i> Add Taxonomy</a>
                                <a href="view_taxonomies.php" onclick="showContent('editTaxonomy')"><i class="fas fa-edit"></i> Edit Taxonomy</a>
                                <a href="create_tag.php" onclick="showContent('addTag')"><i class="fas fa-plus"></i> Add Tag</a>
                                <a href="view_tags.php" onclick="showContent('manageTags')"><i class="fas fa-tags"></i> Manage Tags</a>
                                <a href="#" onclick="showContent('deleteTaxonomy')"><i class="fas fa-trash"></i> Delete Taxonomy</a>
                                <a href="manage_metadata.php" onclick="showContent('manageMetadata')"><i class="fas fa-tags"></i> Manage Metadata</a>
                            </div>
                        </div>
                        <div class="nested-dropdown">
                            <a href="#" class="nested-dropbtn"><i class="fas fa-shield-alt"></i> Moderation</a>
                            <div class="nested-dropdown-content">
                                <a href="#" onclick="showContent('reviewContent')"><i class="fas fa-eye"></i> Review Content</a>
                                <a href="#" onclick="showContent('manageFlags')"><i class="fas fa-flag"></i> Manage Flags</a>
                                <a href="#" onclick="showContent('configureAutoModeration')"><i class="fas fa-robot"></i> Auto-Moderation Settings</a>
                            </div>
                        </div>
                        <div class="nested-dropdown">
                            <a href="#" class="nested-dropbtn"><i class="fas fa-plug"></i> Integration</a>
                            <div class="nested-dropdown-content">
                                <a href="#" onclick="showContent('configureAPI')"><i class="fas fa-plug"></i> Configure API</a>
                                <a href="#" onclick="showContent('manageWebhooks')"><i class="fas fa-link"></i> Manage Webhooks</a>
                                <a href="#" onclick="showContent('configureSSO')"><i class="fas fa-sign-in-alt"></i> Configure SSO</a>
                            </div>
                        </div>
                        <div class="nested-dropdown">
                            <a href="#" class="nested-dropbtn"><i class="fas fa-history"></i> Audit Logs</a>
                            <div class="nested-dropdown-content">
                                <a href="#" onclick="showContent('filterLogs')"><i class="fas fa-filter"></i> Filter Logs</a>
                                <a href="#" onclick="showContent('exportLogs')"><i class="fas fa-file-export"></i> Export Logs</a>
                                <a href="#" onclick="showContent('configureLogRetention')"><i class="fas fa-history"></i> Log Retention Settings</a>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
    </nav>

    <div class="sidebar" id="sidebar">
        <a href="#"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="#"><i class="fas fa-home"></i> NIRDA home</a>
        <a href="#"><i class="fas fa-calendar"></i> Calendar</a>
        <a href="#"><i class="fas fa-certificate"></i> Privileges</a>
        <a href="#"><i class="fas fa-file-alt"></i> Private files</a>
        <a href="#"><i class="fas fa-database"></i> Content bank</a>
        <a href="#"><i class="fas fa-book"></i> My Creations</a>
        <a href="#"><i class="fas fa-cogs"></i> Site administration</a>
    </div>

    <div class="main-content">
        <!-- Your main content goes here -->
    </div>
    
<footer class="footer">
        <div class="contact-info">
            <div><i class="fas fa-map-marker-alt"></i> Kigali City Tower, 13th Floor</div>
            <div><i class="fas fa-envelope"></i> 273 Kigali</div>
            <div><i class="fas fa-phone-alt"></i> +250 255104465</div>
            <div><i class="fas fa-envelope-open"></i> info@nirda.gov.rw</div>
            <div><i class="fas fa-globe"></i> www.nirda.gov.rw</div>
        </div>
    </footer>

    <script>
        console.log("Fixed footer loaded successfully");
    </script>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
 
</script>

</body>
</html>