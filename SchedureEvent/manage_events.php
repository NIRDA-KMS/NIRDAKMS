<<<<<<< Updated upstream
<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define WebSocket constants (set to false if not using WebSockets)
define('ENABLE_WEBSOCKETS', false);
define('WEBSOCKET_SERVER', 'http://localhost:3000');

// Database connection
include('connect.php');

// Check connection
if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'event_actions.php';
    exit;
}

// Pagination setup
$perPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

// Get total events count
$totalQuery = "SELECT COUNT(*) as total FROM schedule_events";
$totalResult = mysqli_query($connection, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalEvents = $totalRow['total'];
$totalPages = ceil($totalEvents / $perPage);

// Function to get paginated events with prepared statements
function getPaginatedEvents($connection, $offset, $perPage) {
    $events = [];
    $query = "SELECT * FROM schedule_events ORDER BY startDateTime DESC LIMIT ?, ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "ii", $offset, $perPage);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $events[] = $row;
        }
    }
    mysqli_stmt_close($stmt);
    return $events;
}

// Get paginated events
$events = getPaginatedEvents($connection, $offset, $perPage);

// Function to format date range
function formatDateRange($start, $end) {
    $startDate = new DateTime($start);
    $endDate = new DateTime($end);
    
    if ($startDate->format('H:i') === '00:00' && $endDate->format('H:i') === '23:59') {
        return $startDate->format('M j, Y') . ' - All Day';
    }
    
    if ($startDate->format('Y-m-d') === $endDate->format('Y-m-d')) {
        return $startDate->format('M j, Y - g:i A') . ' to ' . $endDate->format('g:i A');
    }
    
    return $startDate->format('M j, Y - g:i A') . ' to ' . $endDate->format('M j, Y - g:i A');
}

// Function to determine event status
function getEventStatus($start, $end, $isActive = 1) {
    if (!$isActive) {
        return ['text' => 'Inactive', 'class' => 'status-inactive'];
    }
    
    $now = new DateTime();
    $startDate = new DateTime($start);
    $endDate = new DateTime($end);
    
    if ($now < $startDate) {
        return ['text' => 'Upcoming', 'class' => 'status-upcoming'];
    } elseif ($now >= $startDate && $now <= $endDate) {
        return ['text' => 'Active', 'class' => 'status-active'];
    } else {
        return ['text' => 'Completed', 'class' => 'status-completed'];
    }
}
?>

=======
>>>>>>> Stashed changes
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Events | NIRDA Knowledge Management System</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
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
<<<<<<< Updated upstream
      --success-color: #4CAF50;
      --warning-color: #FFC107;
      --danger-color: #F44336;
      --info-color: #2196F3;
    } 
=======
    }
>>>>>>> Stashed changes

    /* Base Styles */
    body {
      font-family: 'Roboto', sans-serif;
      background-color: var(--background-color);
      color: var(--text-color);
      margin: 0;
      padding: 0;
      line-height: 1.6;
      transition: all 0.3s;
    }

    /* Sidebar Styles */
    .sidebar {
      min-width: 250px;
      max-width: 250px;
      background: var(--primary-color);
      color: white;
      transition: all 0.3s;
      position: fixed;
      height: 100vh;
      z-index: 1000;
      left: -250px;
    }

    .sidebar.active {
      left: 0;
    }

    .sidebar-header {
      padding: 20px;
      background: var(--secondary-color);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .sidebar-header h3 {
      margin: 0;
      color: white;
    }

    #sidebarCollapse {
      background: none;
      border: none;
      color: white;
      font-size: 1.5rem;
      cursor: pointer;
    }

    .sidebar ul.components {
      padding: 20px 0;
      list-style: none;
      margin: 0;
    }

    .sidebar ul li a {
      padding: 10px 20px;
      color: white;
      display: block;
      text-decoration: none;
      transition: all 0.3s;
    }

    .sidebar ul li a:hover {
      background: rgba(255, 255, 255, 0.1);
    }

    .sidebar ul li a.active {
      background: var(--accent-color);
    }

    .sidebar ul li a i {
      margin-right: 10px;
    }

    /* When sidebar is active, add padding to body */
    body.sidebar-open {
      padding-left: 250px;
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
    .container {
      max-width: 1200px;
      margin: 20px auto;
      background-color: white;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      padding: 20px;
    }

    h1 {
      color: var(--primary-color);
      margin-bottom: 20px;
    }

    /* View Switcher */
    .view-switcher {
      margin-bottom: 20px;
      display: flex;
      justify-content: space-between;
    }

    .view-btn {
      background-color: var(--accent-color);
      color: white;
      border: none;
      padding: 8px 16px;
      margin-right: 10px;
      border-radius: 4px;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .view-btn:hover {
      background-color: var(--primary-color);
    }

    .view-btn.active {
      background-color: var(--primary-color);
    }

    /* Status Badges */
    .status-badge {
      padding: 4px 8px;
      border-radius: 12px;
      font-size: 12px;
      font-weight: 500;
      color: white;
    }

    .status-upcoming {
      background-color: var(--info-color);
    }

    .status-active {
      background-color: var(--success-color);
    }

    .status-completed {
      background-color: var(--secondary-color);
    }

    .status-inactive {
      background-color: var(--danger-color);
    }

    /* Action Buttons */
    .action-btn {
      background-color: var(--accent-color);
      color: white;
      border: none;
      padding: 6px 12px;
      border-radius: 4px;
      cursor: pointer;
    }

    /* Dropdown */
    .dropdown {
      position: relative;
      display: inline-block;
    }

    .dropdown-content {
      display: none;
      position: absolute;
      background-color: white;
      min-width: 160px;
      box-shadow: 0 8px 16px rgba(0,0,0,0.2);
      z-index: 1;
      border-radius: 4px;
    }

    .dropdown-content a {
      color: var(--text-color);
      padding: 8px 12px;
      text-decoration: none;
      display: block;
    }

    .dropdown-content a:hover {
      background-color: var(--background-color);
    }

    .dropdown:hover .dropdown-content {
      display: block;
    }

    /* Pagination */
    .pagination {
      margin-top: 20px;
    }

    .pagination a {
      color: var(--primary-color);
      padding: 8px 16px;
      text-decoration: none;
      border: 1px solid #ddd;
      margin: 0 4px;
    }

    .pagination a.active {
      background-color: var(--primary-color);
      color: white;
      border: 1px solid var(--primary-color);
    }

    .pagination a:hover:not(.active) {
      background-color: #ddd;
    }

    /* Modal */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.5);
    }

    .modal-content {
      background-color: white;
      margin: 10% auto;
      padding: 20px;
      border-radius: 8px;
<<<<<<< Updated upstream
      width: 60%;
      max-width: 800px;
=======
      width: 80%;
      max-width: 700px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.2);
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
>>>>>>> Stashed changes
    }

    .close-modal {
      color: #aaa;
      float: right;
      font-size: 28px;
      font-weight: bold;
      cursor: pointer;
    }

    .close-modal:hover {
      color: black;
    }

    /* Calendar View */
    #calendarView {
      display: none;
      margin-top: 20px;
    }

    .fc-event-main {
      padding: 2px 5px;
      border-radius: 3px;
      color: white;
    }

    /* Attendee List */
    .attendee-item {
      display: flex;
      justify-content: space-between;
      padding: 8px;
      border-bottom: 1px solid #eee;
    }

    .rsvp-status {
      padding: 2px 8px;
      border-radius: 12px;
      font-size: 12px;
      color: white;
    }

    .rsvp-confirmed {
      background-color: var(--success-color);
    }

    .rsvp-declined {
      background-color: var(--danger-color);
    }

    .rsvp-pending {
      background-color: var(--warning-color);
    }

    /* Form Styles */
    .form-group {
      margin-bottom: 15px;
    }

    .form-control {
      width: 100%;
      padding: 8px;
      border: 1px solid #ddd;
      border-radius: 4px;
    }

    .form-row {
      display: flex;
      flex-wrap: wrap;
      margin-right: -15px;
      margin-left: -15px;
    }

    .form-group.col-md-6 {
      flex: 0 0 50%;
      max-width: 50%;
      padding-right: 15px;
      padding-left: 15px;
    }

    .btn-primary {
      background-color: var(--accent-color);
      color: white;
      border: none;
      padding: 8px 16px;
      border-radius: 4px;
      cursor: pointer;
    }

    /* Responsive Styles */
    @media (max-width: 992px) {
      .main-content.sidebar-active {
        margin-left: 0;
      }
      
      body.sidebar-open {
        padding-left: 0;
      }
    }

    @media (max-width: 768px) {
      .container {
        padding: 10px;
      }
      
      .modal-content {
        width: 90%;
        margin: 20% auto;
      }
      
      .form-group.col-md-6 {
        flex: 0 0 100%;
        max-width: 100%;
      }
    }
  </style>
</head>
<body>
<?php include_once("../Internees' task/header.php"); ?>


<div class="main-content">
<<<<<<< Updated upstream
    <div class="container">
        <h1><i class="fas fa-calendar-alt"></i> Event Management</h1>
        
        <div class="view-switcher">
            <button id="tableViewBtn" class="view-btn active"><i class="fas fa-table"></i> List View</button>
            <button id="calendarViewBtn" class="view-btn"><i class="fas fa-calendar"></i> Calendar View</button>
            <button id="addEventBtn" class="view-btn" style="float: right;"><i class="fas fa-plus"></i> Add Event</button>
        </div>
        
        <!-- Table View -->
        <div id="tableView">
            <table id="eventsTable" class="display">
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
                    <?php foreach ($events as $event): ?>
                        <?php 
                        $dateTimeRange = formatDateRange($event['startDateTime'], $event['endingDateTime']);
                        $status = getEventStatus($event['startDateTime'], $event['endingDateTime'], $event['isActive'] ?? 1);
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($event['eventTitle']); ?></td>
                            <td><?php echo $dateTimeRange; ?></td>
                            <td><?php echo htmlspecialchars($event['eventLocation']); ?></td>
                            <td>
                                <span class="status-badge <?php echo $status['class']; ?>">
                                    <?php echo $status['text']; ?>
                                </span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="action-btn btn-manage">
                                        <i class="fas fa-cogs"></i> Manage <i class="fas fa-caret-down"></i>
                                    </button>
                                    <div class="dropdown-content">
                                        <a href="#" class="edit-event" data-event-id="<?php echo $event['event_id']; ?>">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="#" class="delete-event" data-event-id="<?php echo $event['event_id']; ?>">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                        <a href="#" class="toggle-active" data-event-id="<?php echo $event['event_id']; ?>" data-current-state="<?php echo ($event['isActive'] ?? 1) ? 'active' : 'inactive'; ?>">
                                            <i class="fas fa-eye<?php echo ($event['isActive'] ?? 1) ? '-slash' : ''; ?>"></i> 
                                            <?php echo ($event['isActive'] ?? 1) ? 'Deactivate' : 'Activate'; ?>
                                        </a>
                                        <a href="#" class="view-attendees" data-event-id="<?php echo $event['event_id']; ?>">
                                            <i class="fas fa-users"></i> View Attendees
                                        </a>
                                        <a href="#" class="send-reminders" data-event-id="<?php echo $event['event_id']; ?>">
                                            <i class="fas fa-bell"></i> Send Reminders
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <!-- Pagination -->
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>">&laquo;</a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>" <?php echo $i == $page ? 'class="active"' : ''; ?>>
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?php echo $page + 1; ?>">&raquo;</a>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Calendar View -->
        <div id="calendarView"></div>
    </div>
</div>

<!-- Event Modal -->
<div id="eventModal" class="modal">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <div id="modalContent">
            <!-- Content will be loaded dynamically -->
        </div>
=======
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
              <button class="action-btn btn-edit"><i class="fas fa-edit"></i> Edit</button>
              <button class="action-btn btn-delete"><i class="fas fa-trash"></i> Delete</button>
              <button class="action-btn btn-deactivate"><i class="fas fa-eye-slash"></i> Deactivate</button>
              <button class="action-btn btn-view"><i class="fas fa-users"></i> Attendees</button>
              <button class="action-btn btn-remind"><i class="fas fa-bell"></i> Remind</button>
            </td>
          </tr>
          <tr>
            <td>Project Deadline</td>
            <td>Jun 30, 2023 - All Day</td>
            <td>Virtual</td>
            <td><span class="status-badge status-inactive">Inactive</span></td>
            <td>
              <button class="action-btn btn-edit"><i class="fas fa-edit"></i> Edit</button>
              <button class="action-btn btn-delete"><i class="fas fa-trash"></i> Delete</button>
              <button class="action-btn btn-deactivate"><i class="fas fa-eye"></i> Activate</button>
              <button class="action-btn btn-view"><i class="fas fa-users"></i> View Attendees</button>
              <button class="action-btn btn-remind"><i class="fas fa-bell"></i> Remind</button>
            </td>
          </tr>
        </tbody>
          <tr>
            <td>Project Deadline</td>
            <td>Jun 30, 2023 - All Day</td>
            <td>Virtual</td>
            <td><span class="status-badge status-inactive">Inactive</span></td>
            <td>
              <button class="action-btn btn-edit"><i class="fas fa-edit"></i> Edit</button>
              <button class="action-btn btn-delete"><i class="fas fa-trash"></i> Delete</button>
              <button class="action-btn btn-deactivate"><i class="fas fa-eye"></i> Activate</button>
              <button class="action-btn btn-view"><i class="fas fa-users"></i> View Attendees</button>
              <button class="action-btn btn-remind"><i class="fas fa-bell"></i> Remind</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    
    
    <!-- Calendar View -->
    <div class="view-container" id="calendar-view">
      <div id="calendar"></div>
>>>>>>> Stashed changes
    </div>
</div>

<!-- Attendees Modal -->
<div id="attendeesModal" class="modal">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <h2>Event Attendees</h2>
        <div id="attendeesContent">
            <!-- Content will be loaded dynamically -->
        </div>
    </div>
</div>

<!-- Add/Edit Event Modal -->
<div id="editEventModal" class="modal">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <h2 id="editModalTitle">Add New Event</h2>
        <form id="eventForm">
            <input type="hidden" id="eventId" name="eventId" value="">
            <div class="form-group">
                <label for="eventTitle">Event Title</label>
                <input type="text" id="eventTitle" name="eventTitle" required class="form-control">
            </div>
            <div class="form-group">
                <label for="eventDescription">Description</label>
                <textarea id="eventDescription" name="eventDescription" class="form-control" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label for="eventLocation">Location</label>
                <input type="text" id="eventLocation" name="eventLocation" class="form-control">
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="startDateTime">Start Date & Time</label>
                    <input type="datetime-local" id="startDateTime" name="startDateTime" required class="form-control">
                </div>
                <div class="form-group col-md-6">
                    <label for="endingDateTime">End Date & Time</label>
                    <input type="datetime-local" id="endingDateTime" name="endingDateTime" required class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label for="maxAttendees">Maximum Attendees (0 for unlimited)</label>
                <input type="number" id="maxAttendees" name="maxAttendees" min="0" class="form-control" value="0">
            </div>
            <div class="form-group">
                <label for="isActive">Status</label>
                <select id="isActive" name="isActive" class="form-control">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Save Event</button>
        </form>
    </div>
</div>

<!-- JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php if (ENABLE_WEBSOCKETS): ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/4.5.1/socket.io.js"></script>
<?php endif; ?>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#eventsTable').DataTable({
        paging: false,
        searching: true,
        info: false,
        responsive: true
    });
    
    // View switcher
    $('#tableViewBtn').click(function() {
        $('#tableView').show();
        $('#calendarView').hide();
        $(this).addClass('active');
        $('#calendarViewBtn').removeClass('active');
    });
    
<<<<<<< Updated upstream
    $('#calendarViewBtn').click(function() {
        $('#tableView').hide();
        $('#calendarView').show();
        $(this).addClass('active');
        $('#tableViewBtn').removeClass('active');
        initCalendar();
    });
    
    // Initialize FullCalendar
    function initCalendar() {
        const calendarEl = document.getElementById('calendarView');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: 'get_events_for_calendar.php',
            eventClick: function(info) {
                showEventDetails(info.event.id);
            },
            eventContent: function(arg) {
                const statusClass = arg.event.extendedProps.isActive ? 
                    (arg.event.extendedProps.status === 'active' ? 'status-active' : 
                     arg.event.extendedProps.status === 'upcoming' ? 'status-upcoming' : 'status-completed') : 
                    'status-inactive';
                
                return {
                    html: `
                        <div class="fc-event-main ${statusClass}" style="padding: 2px 5px; border-radius: 3px;">
                            <strong>${arg.event.title}</strong>
                            <div>${arg.timeText}</div>
                        </div>
                    `
                };
            }
        });
        calendar.render();
    }
    
    // Modal handling
    const eventModal = $('#eventModal');
    const attendeesModal = $('#attendeesModal');
    const editEventModal = $('#editEventModal');
    const modalContent = $('#modalContent');
    const attendeesContent = $('#attendeesContent');
    
    $('.close-modal').click(function() {
        $(this).closest('.modal').hide();
    });
    
    $(window).click(function(event) {
        if ($(event.target).hasClass('modal')) {
            $('.modal').hide();
=======
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
>>>>>>> Stashed changes
        }
    });
    
    // WebSocket for real-time updates
    <?php if (ENABLE_WEBSOCKETS): ?>
    const socket = io('<?php echo WEBSOCKET_SERVER; ?>');
    socket.on('rsvp_update', function(data) {
        if (data.eventId == currentEventId) {
            updateAttendeeList(data.eventId);
        }
    });
    
    socket.on('event_update', function(data) {
        if (data.type === 'event_change') {
            location.reload();
        }
    });
    <?php endif; ?>
    
    // Add new event
    $('#addEventBtn').click(function() {
        $('#editModalTitle').text('Add New Event');
        $('#eventId').val('');
        $('#eventForm')[0].reset();
        editEventModal.show();
    });
    
    // Edit event
    $('body').on('click', '.edit-event', function(e) {
        e.preventDefault();
        const eventId = $(this).data('event-id');
        
        $.get('get_event.php', { id: eventId }, function(response) {
            if (response.success) {
                $('#editModalTitle').text('Edit Event');
                $('#eventId').val(response.event.event_id);
                $('#eventTitle').val(response.event.eventTitle);
                $('#eventDescription').val(response.event.eventDescription);
                $('#eventLocation').val(response.event.eventLocation);
                $('#startDateTime').val(response.event.startDateTime.replace(' ', 'T'));
                $('#endingDateTime').val(response.event.endingDateTime.replace(' ', 'T'));
                $('#maxAttendees').val(response.event.maxAttendees || 0);
                $('#isActive').val(response.event.isActive);
                editEventModal.show();
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        }).fail(function() {
            Swal.fire('Error', 'Failed to load event data', 'error');
        });
    });
    
    // Save event form
    $('#eventForm').submit(function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        const url = $('#eventId').val() ? 'update_event.php' : 'add_event.php';
        
        $.post(url, formData, function(response) {
            if (response.success) {
                Swal.fire({
                    title: 'Success',
                    text: $('#eventId').val() ? 'Event updated successfully!' : 'Event added successfully!',
                    icon: 'success'
                }).then(() => {
                    editEventModal.hide();
                    location.reload();
                });
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        }).fail(function() {
            Swal.fire('Error', 'Failed to save event', 'error');
        });
    });
    
    // Delete event
    $('body').on('click', '.delete-event', function(e) {
        e.preventDefault();
        const eventId = $(this).data('event-id');
        
        Swal.fire({
            title: 'Delete Event',
            text: 'Are you sure you want to permanently delete this event?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Delete'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('delete_event.php', { id: eventId }, function(response) {
                    if (response.success) {
                        Swal.fire('Deleted!', 'The event has been deleted.', 'success')
                            .then(() => location.reload());
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                }).fail(function() {
                    Swal.fire('Error', 'Failed to delete event', 'error');
                });
            }
        });
    });
    
    // Toggle event active status
    $('body').on('click', '.toggle-active', function(e) {
        e.preventDefault();
        const eventId = $(this).data('event-id');
        const currentState = $(this).data('current-state');
        const newState = currentState === 'active' ? 0 : 1;
        
        $.post('toggle_event_status.php', { 
            id: eventId, 
            isActive: newState 
        }, function(response) {
            if (response.success) {
                location.reload();
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        }).fail(function() {
            Swal.fire('Error', 'Failed to update event status', 'error');
        });
    });
    
    let currentEventId = null;
    
    // View attendees
    $('body').on('click', '.view-attendees', function(e) {
        e.preventDefault();
        currentEventId = $(this).data('event-id');
        updateAttendeeList(currentEventId);
        attendeesModal.show();
    });
    
    function updateAttendeeList(eventId) {
        attendeesContent.html('<p>Loading attendees...</p>');
        
        $.get(`get_attendees.php?id=${eventId}`, function(data) {
            if (data.success) {
                if (data.attendees && data.attendees.length > 0) {
                    let html = '<div class="attendee-list">';
                    html += `<div class="attendee-header">
                                <span>Name</span>
                                <span>RSVP Status</span>
                                <span>Actions</span>
                            </div>`;
                    
                    data.attendees.forEach(attendee => {
                        html += `
                            <div class="attendee-item">
                                <span>${attendee.name || attendee.email}</span>
                                <span class="rsvp-status rsvp-${attendee.rsvp_status || 'pending'}">
                                    ${(attendee.rsvp_status || 'pending').charAt(0).toUpperCase() + (attendee.rsvp_status || 'pending').slice(1)}
                                </span>
                                <span>
                                    <button class="btn-rsvp" data-attendee-id="${attendee.id}" data-rsvp-status="confirmed">Confirm</button>
                                    <button class="btn-rsvp" data-attendee-id="${attendee.id}" data-rsvp-status="declined">Decline</button>
                                </span>
                            </div>
                        `;
                    });
                    html += '</div>';
                    attendeesContent.html(html);
                } else {
                    attendeesContent.html('<p>No attendees found for this event.</p>');
                }
            } else {
                attendeesContent.html(`<p>Error: ${data.message}</p>`);
            }
        }).fail(function() {
            attendeesContent.html('<p>Failed to load attendees</p>');
        });
    }
    
    // Update RSVP status
    $('body').on('click', '.btn-rsvp', function(e) {
        e.preventDefault();
        const attendeeId = $(this).data('attendee-id');
        const rsvpStatus = $(this).data('rsvp-status');
        
        $.post('update_rsvp.php', {
            attendeeId: attendeeId,
            status: rsvpStatus
        }, function(response) {
            if (response.success) {
                updateAttendeeList(currentEventId);
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        }).fail(function() {
            Swal.fire('Error', 'Failed to update RSVP status', 'error');
        });
    });
    
    // Send reminders
    $('body').on('click', '.send-reminders', function(e) {
        e.preventDefault();
        const eventId = $(this).data('event-id');
        
        Swal.fire({
            title: 'Send Reminders',
            text: 'Send reminder notifications to all attendees?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Send'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('send_reminders.php', { id: eventId }, function(response) {
                    if (response.success) {
                        Swal.fire('Sent!', 'Reminders sent successfully!', 'success');
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                }).fail(function() {
                    Swal.fire('Error', 'Failed to send reminders', 'error');
                });
            }
        });
    });
    
    // Show event details
    function showEventDetails(eventId) {
        $.get('get_event.php', { id: eventId }, function(response) {
            if (response.success) {
                const event = response.event;
                const status = getEventStatus(event.startDateTime, event.endingDateTime, event.isActive);
                
                let html = `
                    <h2>${event.eventTitle}</h2>
                    <p><strong>Description:</strong> ${event.eventDescription || 'N/A'}</p>
                    <p><strong>Location:</strong> ${event.eventLocation || 'N/A'}</p>
                    <p><strong>Date & Time:</strong> ${formatDateRange(event.startDateTime, event.endingDateTime)}</p>
                    <p><strong>Status:</strong> <span class="status-badge ${status.class}">${status.text}</span></p>
                    <p><strong>Maximum Attendees:</strong> ${event.maxAttendees || 'Unlimited'}</p>
                `;
                
                modalContent.html(html);
                eventModal.show();
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        }).fail(function() {
            Swal.fire('Error', 'Failed to load event details', 'error');
        });
    }
    
    // Helper function to format date range (similar to PHP function)
    function formatDateRange(start, end) {
        const startDate = new Date(start);
        const endDate = new Date(end);
        
        if (startDate.getHours() === 0 && startDate.getMinutes() === 0 && 
            endDate.getHours() === 23 && endDate.getMinutes() === 59) {
            return startDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) + ' - All Day';
        }
        
        if (startDate.toDateString() === endDate.toDateString()) {
            return startDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) + ' - ' + 
                   startDate.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' }) + ' to ' + 
                   endDate.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
        }
        
        return startDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) + ' - ' + 
               startDate.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' }) + ' to ' + 
               endDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) + ' - ' + 
               endDate.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
    }
    
    // Helper function to get event status (similar to PHP function)
    function getEventStatus(start, end, isActive = 1) {
        if (!isActive) {
            return { text: 'Inactive', class: 'status-inactive' };
        }
        
        const now = new Date();
        const startDate = new Date(start);
        const endDate = new Date(end);
        
        if (now < startDate) {
            return { text: 'Upcoming', class: 'status-upcoming' };
        } else if (now >= startDate && now <= endDate) {
            return { text: 'Active', class: 'status-active' };
        } else {
            return { text: 'Completed', class: 'status-completed' };
        }
    }
});

// // Sidebar Toggle Functionality
// document.addEventListener('DOMContentLoaded', function() {
//     const sidebar = document.getElementById('sidebar');
//     const toggleBtn = document.getElementById('sidebarCollapse');
    
//     // Initialize from localStorage
//     if(localStorage.getItem('sidebarState') === 'open') {
//         sidebar.classList.add('active');
//         document.body.classList.add('sidebar-open');
//         document.querySelector('.main-content').classList.add('sidebar-active');
//     }
    
//     // Toggle sidebar
//     if(toggleBtn) {
//         toggleBtn.addEventListener('click', function(e) {
//             e.preventDefault();
//             const isOpening = !sidebar.classList.contains('active');
            
//             sidebar.classList.toggle('active');
//             document.body.classList.toggle('sidebar-open');
//             document.querySelector('.main-content').classList.toggle('sidebar-active');
            
//             localStorage.setItem('sidebarState', isOpening ? 'open' : 'closed');
//         });
//     }
    
//     // Highlight current page in sidebar
//     const currentPage = window.location.pathname.split('/').pop() || 'index.php';
//     document.querySelectorAll('.sidebar a').forEach(link => {
//         const linkHref = link.getAttribute('href').split('/').pop();
//         if (linkHref === currentPage) {
//             link.classList.add('active');
//         }
//     });
// });
</script>
</body>
</html>