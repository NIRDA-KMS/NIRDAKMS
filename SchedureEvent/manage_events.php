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
      --success-color: #4CAF50;
      --warning-color: #FFC107;
      --danger-color: #F44336;
      --info-color: #2196F3;
    } 

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
      width: 60%;
      max-width: 800px;
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
<?php include("../Internees_task/header.php"); ?>


<div class="main-content">
    <div class="container">
        <h1><i class="fas fa-calendar-alt"></i> Event Management</h1>
        
        <div class="view-switcher">
            <button id="tableViewBtn" class="view-btn active"><i class="fas fa-table"></i> List View</button>
            <button id="calendarViewBtn" class="view-btn"><i class="fas fa-calendar"></i> Calendar View</button>
            <button id="addEventBtn" class="view-btn" style="float: right;"> <i class="fas fa-plus"></i><a href="./schedule_event.php" style="text-decoration: none;">Add Event</a> </button>
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
                                        <a href="../SchedureEvent/edit.php" class="edit-event" data-event-id="<?php echo $event['event_id']; ?>">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="delete.php?event_id=<?php echo $event['event_id']; ?>" class="delete-event" data-event-id="<?php echo $event['event_id']; ?>">
    <i class="fas fa-trash"></i> Delete
</a>

                                        <a href="../SchedureEvent/deactivate.ph" class="toggle-active" data-event-id="<?php echo $event['event_id']; ?>" data-current-state="<?php echo ($event['isActive'] ?? 1) ? 'active' : 'inactive'; ?>">
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
        }
    });
    

   
    
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





// Toggle event active/inactive status
$(document).on('click', '.toggle-active', function(e) {
    e.preventDefault();
    
    const eventId = $(this).data('event-id');
    const currentState = $(this).data('current-state');
    
    Swal.fire({
        title: 'Are you sure?',
        text: `You are about to ${currentState === 'active' ? 'deactivate' : 'activate'} this event`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, proceed'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'deactivate.php',
                type: 'POST',
                data: {
                    event_id: eventId,
                    current_state: currentState
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire(
                            'Success!',
                            `Event has been ${response.new_state === 'active' ? 'activated' : 'deactivated'}.`,
                            'success'
                        ).then(() => {
                            // Update the button text and data attributes
                            const button = $(`[data-event-id="${eventId}"].toggle-active`);
                            button.data('current-state', response.new_state);
                            button.html(`<i class="fas ${response.icon_class}"></i> ${response.action_text}`);
                            
                            // Reload the page to reflect changes
                            location.reload();
                        });
                    } else {
                        Swal.fire(
                            'Error!',
                            response.message || 'Failed to update event status',
                            'error'
                        );
                    }
                },
                error: function() {
                    Swal.fire(
                        'Error!',
                        'An error occurred while processing your request',
                        'error'
                    );
                }
            });
        }
    });
});




</script>
</body>
</html>
