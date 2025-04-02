

<?php
include('connect.php');

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Event ID not provided']);
    exit;
}

$eventId = intval($_GET['id']);
$query = "SELECT * FROM schedule_events WHERE event_id = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "i", $eventId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($event = mysqli_fetch_assoc($result)) {
    echo json_encode(['success' => true, 'event' => $event]);
} else {
    echo json_encode(['success' => false, 'message' => 'Event not found']);
}

mysqli_close($connection);
?>





<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Events | NIRDA Knowledge Management System</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
  <style>

    /* Color Variables */
    :root {
      --primary-color: #1a237e;
      --secondary-color: #2c3e50;
      --accent-color: #00A0DF;
      --background-color: #f0f2f5;
      --text-color: #333333;
      --light-text: #ffffff;
      --border-color: #d1d5db;
    } 

    /* Base Styles */
    body {
      font-family: 'Roboto', sans-serif;
      background-color: var(--background-color);
      color: var(--text-color);
      margin: 0;
      padding: 0;
      line-height: 1.6;
    }

    /* Main Content */
    .main-content {
      padding: 20px;
      margin-left: 0;
      transition: margin-left 0.3s;
    }

    .main-content.sidebar-active {
      margin-left: 250px;
    }

    /* Content Container */
    .content-container {
      max-width: 1200px;
      margin: 20px auto;
      background-color: white;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      padding: 100px;
    }

    /* View Tabs */
    .view-tabs {
      display: flex;
      border-bottom: 1px solid var(--border-color);
      margin-bottom: 20px;
    }

    .view-tab {
      padding: 10px 20px;
      cursor: pointer;
      border-bottom: 3px solid transparent;
      font-weight: 500;
    }

    .view-tab.active {
      border-bottom-color: var(--accent-color);
      color: var(--primary-color);
    }

    .view-tab:hover:not(.active) {
      background-color: var(--background-color);
    }

    /* View Containers */
    .view-container {
      display: none;
    }

    .view-container.active {
      display: block;
    }

    /* Data Table */
    .data-table {
      width: 100%;
      border-collapse: collapse;
    }

    .data-table th, .data-table td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid var(--border-color);
    }

    .data-table th {
      background-color: var(--primary-color);
      color: var(--light-text);
      font-weight: 500;
    }

    .data-table tr:hover {
      background-color: var(--background-color);
    }

    /* Status Badges */
    .status-badge {
      display: inline-block;
      padding: 4px 8px;
      border-radius: 4px;
      font-size: 0.8rem;
      font-weight: 500;
    }

    .status-active {
      background-color: #d4edda;
      color: #155724;
    }

    .status-inactive {
      background-color: #f8d7da;
      color: #721c24;
    }

    /* Action Buttons */
    .action-btn {
      padding: 5px 10px;
      border-radius: 4px;
      border: none;
      cursor: pointer;
      font-size: 0.85rem;
      margin-right: 5px;
      transition: all 0.2s;
    }

    .action-btn i {
      margin-right: 5px;
    }

    .btn-edit {
      background-color: #ffc107;
      color: #212529;
    }

    .btn-delete {
      background-color: #dc3545;
      color: white;
    }

    .btn-deactivate {
      background-color: #6c757d;
      color: white;
    }

    .btn-view {
      background-color: var(--accent-color);
      color: white;
    }

    .btn-remind {
      background-color: #28a745;
      color: white;
    }

    /* Calendar */
    #calendar {
      margin-top: 20px;
      background-color: white;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      padding: 20px;
    }

    /* Modal */
    .modal {
      display: none;
      position: fixed;
      z-index: 2000;
      left: 300px;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.5);
    }
    .manage-options {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.manage-options .action-btn {
  width: 80%;
  text-align: left;
}
    .modal-content {
      background-color: white;
      margin: 10% auto;
      padding: 20px;
      border-radius: 8px;
      width: 20%;
      max-width: 800px;
      box-shadow: 0 0px 10px rgba(0,0,0,0.1);
    }

    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px solid var(--border-color);
      padding-bottom: 15px;
      margin-bottom: 15px;
    }

    .modal-title {
      font-size: 1.25rem;
      color: var(--primary-color);
      margin: 0;
    }

    .close-modal {
      background: none;
      border: none;
      font-size: 1.5rem;
      cursor: pointer;
      color: var(--secondary-color);
    }

    /* Responsive Styles */
    @media (max-width: 992px) {
      .main-content.sidebar-active {
        margin-left: 0;
      }
    }

    @media (max-width: 768px) {
      .data-table {
        display: block;
        overflow-x: auto;
      }
      
      .modal-content {
        width: 95%;
        margin: 20% auto;
      }
    }


    
  </style>
</head>
<body>
<?php 
$page_title = "Manage Events";
include_once("../Internees' task/header.php"); 
?>

<div class="main-content">
  <div class="content-container">
    <h2><i class="fas fa-calendar-alt"></i> Manage Events</h2>
    
    <!-- View Tabs -->
    <div class="view-tabs">
      <div class="view-tab active" data-view="list-view">List View</div>
      <div class="view-tab" data-view="calendar-view">Calendar View</div>
    </div>
    
    <!-- List View -->
    <div class="view-container active" id="list-view">
      <table class="data-table">
        <thead>
          <tr>
            <th>Title</th>
            <th>Date & Time</th>
            <th>Location</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        
<tbody>
  <tr>
    <td>Quarterly Review Meeting</td>
    <td>Jun 15, 2023 - 10:00 AM to 12:00 PM</td>
    <td>Conference Room A</td>
    <td><span class="status-badge status-active">Active</span></td>
    <td>
      <button class="action-btn btn-manage" data-event-id="1"><i class="fas fa-cogs"></i> Manage</button>
    </td>
  </tr>
  <tr>
    <td>Project Deadline</td>
    <td>Jun 30, 2023 - All Day</td>
    <td>Virtual</td>
    <td><span class="status-badge status-inactive">Inactive</span></td>
    <td>
      <button class="action-btn btn-manage" data-event-id="2"><i class="fas fa-cogs"></i> Manage</button>
    </td>
  </tr>
</tbody>
      </table>
    </div>
<!-- manage Modal -->
    
<div class="modal" id="manageModal">
  <div class="modal-content">
    <div class="modal-header">
      <h3 class="modal-title"><i class="fas fa-cogs"></i> Manage Event</h3>
      <button class="close-modal">&times;</button>
    </div>
    <div class="modal-body">
      <p id="event-title"></p>
      <div class="manage-options">
        <button class="action-btn btn-edit"><i class="fas fa-edit"></i> Edit</button>
        <button class="action-btn btn-deactivate"><i class="fas fa-eye-slash"></i> Deactivate</button>
        <button class="action-btn btn-delete"><i class="fas fa-trash"></i> Delete</button>
        <button class="action-btn btn-view"><i class="fas fa-users"></i> Attendees</button>
        <button class="action-btn btn-remind"><i class="fas fa-bell"></i> Remind</button>
      </div>
    </div>
  </div>
</div>
    
    <!-- Calendar View -->
    <div class="view-container" id="calendar-view">
      <div id="calendar"></div>
    </div>
  </div>
</div>

<!-- Attendees Modal -->
<div class="modal" id="attendeesModal">
  <div class="modal-content">
    <div class="modal-header">
      <h3 class="modal-title"><i class="fas fa-users"></i> Event Attendees</h3>
      <button class="close-modal">&times;</button>
    </div>
    <div class="modal-body">
      <table class="data-table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Department</th>
            <th>RSVP Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>John Doe</td>
            <td>Marketing</td>
            <td><span class="status-badge status-active">Confirmed</span></td>
            <td>
              <button class="action-btn btn-remind"><i class="fas fa-envelope"></i> Message</button>
            </td>
          </tr>
          <tr>
            <td>Jane Smith</td>
            <td>Development</td>
            <td><span class="status-badge status-active">Confirmed</span></td>
            <td>
              <button class="action-btn btn-remind"><i class="fas fa-envelope"></i> Message</button>
            </td>
          </tr>
          <tr>
            <td>Mike Johnson</td>
            <td>HR</td>
            <td><span class="status-badge status-inactive">Pending</span></td>
            <td>
              <button class="action-btn btn-remind"><i class="fas fa-envelope"></i> Remind</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script>
  $(document).ready(function() {
    // Initialize sidebar toggle
    $('#sidebarCollapse').on('click', function(e) {
      e.preventDefault();
      $('#sidebar').toggleClass('active');
      $('nav:not(.navbar)').toggleClass('sidebar-active');
      $('.main-content').toggleClass('sidebar-active');
    });
    
    // View tab switching
    $('.view-tab').on('click', function() {
      $('.view-tab').removeClass('active');
      $(this).addClass('active');
      
      const view = $(this).data('view');
      $('.view-container').removeClass('active');
      $('#' + view).addClass('active');
    });
    // Manage botton
    $(document).ready(function () {
    // Handle "Manage" button click
    $('.btn-manage').on('click', function () {
      const eventId = $(this).data('event-id');
      const eventTitle = $(this).closest('tr').find('td:first').text();

      // Populate modal with event details
      $('#event-title').text(`Manage Options for: ${eventTitle}`);
      $('#manageModal').show();
    });

    // Close modal
    $('.close-modal').on('click', function () {
      $('#manageModal').hide();
    });
    $(window).on('click', function (e) {
      if ($(e.target).is('.modal')) {
        $('.modal').hide();
      }
    });
  });
    // Initialize calendar
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
      },
      events: [
        {
          title: 'Quarterly Review Meeting',
          start: '2023-06-15T10:00:00',
          end: '2023-06-15T12:00:00',
          color: '#1a237e'
        },
        {
          title: 'Team Building Workshop',
          start: '2023-06-20T09:00:00',
          end: '2023-06-20T17:00:00',
          color: '#00A0DF'
        },
        {
          title: 'Project Deadline',
          start: '2023-06-30',
          color: '#2c3e50',
          allDay: true
        }
      ]
    });
    calendar.render();
    
    // Modal functionality
    $('.btn-view').on('click', function() {
      $('#attendeesModal').show();
    });
    
    $('.close-modal').on('click', function() {
      $('#attendeesModal').hide();
    });
    
    $(window).on('click', function(e) {
      if ($(e.target).is('.modal')) {
        $('#attendeesModal').hide();
      }
    });
    
    // Auto-adjust for footer height
    function adjustForFooter() {
      const footerHeight = $('.footer').outerHeight();
      $('.main-content').css('padding-bottom', footerHeight + 20);
    }
    
    adjustForFooter();
    $(window).resize(adjustForFooter);
  });


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