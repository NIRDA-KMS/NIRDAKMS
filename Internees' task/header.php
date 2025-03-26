

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>NIRDA Knowledge Management System</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
<link href="css/style.css" rel="stylesheet">
</head>
<body>
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
            <span class="navbar-title"><h1>NIRDA Knowledge Hub - <?php echo isset($page_title) ? $page_title : 'Dashboard'; ?></h1></span>
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
                            <a href="#" class="nested-dropbtn"><i class="fas fa-calendar-alt"></i> Events</a>
                            <div class="nested-dropdown-content">
                                <a href="schedule_event.php" onclick="showContent('scheduleEvent')"><i class="fas fa-calendar-plus"></i> Schedule Event</a>
                                <a href="manage_events.php" onclick="showContent('manageEvents')"><i class="fas fa-calendar-check"></i> Manage Events</a>
                            </div>
                        </div>
                        <div class="nested-dropdown">
                            <a href="#" class="nested-dropbtn"><i class="fas fa-comments"></i> Discussion Forum</a>
                            <div class="nested-dropdown-content">
                                <a href="create_forum.php" onclick="showContent('createForum')"><i class="fas fa-plus-circle"></i> Create Forum</a>
                                <a href="manage_forums.php" onclick="showContent('manageForums')"><i class="fas fa-cogs"></i> Manage Forums</a>
                            </div>
                        </div>
                        <div class="nested-dropdown">
                            <a href="#" class="nested-dropbtn"><i class="fas fa-project-diagram"></i> Collaborative Projects</a>
                            <div class="nested-dropdown-content">
                                <a href="create_project.php" onclick="showContent('createProject')"><i class="fas fa-plus-circle"></i> Create Project</a>
                                <a href="manage_projects.php" onclick="showContent('manageProjects')"><i class="fas fa-tasks"></i> Manage Projects</a>
                                <a href="project_members.php" onclick="showContent('projectMembers')"><i class="fas fa-user-plus"></i> Manage Members</a>
                            </div>
                        </div>
                        <div class="nested-dropdown">
                            <a href="#" class="nested-dropbtn"><i class="fas fa-comment-dots"></i> Chat Management</a>
                            <div class="nested-dropdown-content">
                                <a href="chat_contacts.php" onclick="showContent('chatContacts')"><i class="fas fa-address-book"></i> Contacts</a>
                                <a href="online_users.php" onclick="showContent('onlineUsers')"><i class="fas fa-user-circle"></i> Online Users</a>
                                <a href="chat_groups.php" onclick="showContent('chatGroups')"><i class="fas fa-users"></i> Manage Groups</a>
                                <a href="manage_chats.php" onclick="showContent('manageChats')"><i class="fas fa-comments"></i> Manage Chats</a>
                            </div>
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
                        <a href="add_user.php" onclick="showContent('addUser')"><i class="fas fa-user-plus"></i> Add User</a>
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        var sidebar = $('#sidebar');
        var mainNav = $('nav:not(.navbar)');
        var mainContent = $('.main-content');
        var noticeBar = $('.notice-bar');
        var closeNotice = $('.close-notice');

        $('#sidebarCollapse').on('click', function() {
            sidebar.toggleClass('active');
            mainNav.toggleClass('sidebar-active');
            mainContent.toggleClass('sidebar-active');
        });

        function updateNavPosition() {
            if (noticeBar.hasClass('active')) {
                mainNav.addClass('notice-active');
            } else {
                mainNav.removeClass('notice-active');
            }
        }

        if (noticeBar.length && noticeBar.find('.notice-content').text().trim() !== '') {
            noticeBar.addClass('active');
            updateNavPosition();
        }

        closeNotice.on('click', function() {
            noticeBar.removeClass('active');
            updateNavPosition();
        });
    });
</script>
</body>
</html>