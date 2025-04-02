<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
include('connect.php');

// Check connection
if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
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

// Function to get paginated events
function getPaginatedEvents($connection, $offset, $perPage) {
    $events = [];
    $query = "SELECT * FROM schedule_events ORDER BY startDateTime DESC LIMIT $offset, $perPage";
    $result = mysqli_query($connection, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $events[] = $row;
        }
    }
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
    <title>Event Management | NIRDA KMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
    <style>
        :root {
            --primary-color: #1a237e;
            --secondary-color: #2c3e50;
            --accent-color: #00A0DF;
            --background-color: #f0f2f5;
            --text-color: #333333;
            --light-text: #ffffff;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            margin: 0;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
        }
        
        h1 {
            color: var(--primary-color);
            margin-bottom: 20px;
        }
        
        .view-switcher {
            margin-bottom: 20px;
        }
        
        .view-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.875rem;
            margin-right: 10px;
            background-color: var(--background-color);
            color: var(--text-color);
        }
        
        .view-btn.active {
            background-color: var(--accent-color);
            color: white;
        }
        
        /* Table View Styles */
        #eventsTable {
            width: 100%;
            border-collapse: collapse;
        }
        
        #eventsTable th {
            background-color: var(--primary-color);
            color: var(--light-text);
            padding: 12px 15px;
            text-align: left;
        }
        
        #eventsTable td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }
        
        #eventsTable tr:nth-child(even) {
            background-color: var(--background-color);
        }
        
        /* Calendar View Styles */
        #calendarView {
            display: none;
            margin-top: 20px;
        }
        
        .fc-header-toolbar {
            margin-bottom: 1em;
        }
        
        .fc-event {
            cursor: pointer;
        }
        
        /* Status Badges */
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
            display: inline-block;
        }
        
        .status-upcoming {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-active {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-completed {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .status-inactive {
            background-color: #e2e3e5;
            color: #383d41;
        }
        
        /* Action Buttons */
        .action-btn {
            padding: 6px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.875rem;
            margin-right: 5px;
            transition: all 0.3s;
        }
        
        .btn-manage {
            background-color: var(--accent-color);
            color: white;
        }
        
        .btn-manage:hover {
            background-color: #0088cc;
        }
        
        /* Dropdown Menu */
        .dropdown {
            position: relative;
            display: inline-block;
        }
        
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: white;
            min-width: 200px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            z-index: 1;
            border-radius: 4px;
            padding: 5px 0;
        }
        
        .dropdown-content a {
            color: var(--text-color);
            padding: 8px 16px;
            text-decoration: none;
            display: block;
            transition: background-color 0.3s;
        }
        
        .dropdown-content a:hover {
            background-color: var(--background-color);
        }
        
        .dropdown:hover .dropdown-content {
            display: block;
        }
        
        /* Pagination Styles */
        .pagination {
            margin-top: 20px;
            display: flex;
            justify-content: center;
        }
        
        .pagination a {
            color: var(--text-color);
            padding: 8px 16px;
            text-decoration: none;
            border: 1px solid #ddd;
            margin: 0 4px;
            border-radius: 4px;
        }
        
        .pagination a.active {
            background-color: var(--accent-color);
            color: white;
            border: 1px solid var(--accent-color);
        }
        
        .pagination a:hover:not(.active) {
            background-color: #ddd;
        }
        
        /* Modal Styles */
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
            margin: 5% auto;
            padding: 20px;
            border-radius: 8px;
            width: 60%;
            max-width: 700px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }
        
        .close-modal {
            float: right;
            font-size: 1.5rem;
            font-weight: bold;
            cursor: pointer;
            color: var(--secondary-color);
        }
        
        /* Attendee List Styles */
        .attendee-list {
            max-height: 300px;
            overflow-y: auto;
            margin: 15px 0;
        }
        
        .attendee-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
        }
        
        .rsvp-status {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
        }
        
        .rsvp-yes {
            background-color: #d4edda;
            color: #155724;
        }
        
        .rsvp-no {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .rsvp-maybe {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .rsvp-pending {
            background-color: #e2e3e5;
            color: #383d41;
        }
        
        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .modal-content {
                width: 90%;
            }
            
            .dropdown-content {
                min-width: 160px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-calendar-alt"></i> Event Management</h1>
        
        <div class="view-switcher">
            <button id="tableViewBtn" class="view-btn active"><i class="fas fa-table"></i> Table View</button>
            <button id="calendarViewBtn" class="view-btn"><i class="fas fa-calendar"></i> Calendar View</button>
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

    <!-- Modal for event details -->
    <div id="eventModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h2 id="modalTitle"><i class="fas fa-calendar-check"></i> Event Details</h2>
            <div id="modalContent"></div>
        </div>
    </div>

    <!-- Modal for attendees -->
    <div id="attendeesModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h2><i class="fas fa-users"></i> Attendee List</h2>
            <div id="attendeesContent" class="attendee-list"></div>
        </div>
    </div>

    <!-- JavaScript Libraries
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php if (ENABLE_WEBSOCKETS): ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/4.5.1/socket.io.js"></script>
    <?php endif; ?> -->

    <script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#eventsTable').DataTable({
            paging: false,
            searching: true,
            info: false
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
                    // Custom event rendering with status colors
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
        
        // WebSocket for real-time updates
        <?php if (ENABLE_WEBSOCKETS): ?>
        const socket = io('<?php echo WEBSOCKET_SERVER; ?>');
        socket.on('rsvp_update', function(data) {
            if (data.eventId == currentEventId) {
                updateAttendeeList(data.eventId);
            }
        });
        <?php endif; ?>
        
        // Event handlers
        $('body').on('click', '.edit-event', function(e) {
            e.preventDefault();
            const eventId = $(this).data('event-id');
            window.location.href = `edit_event.php?id=${eventId}`;
        });
        
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
                        let html = '';
                        data.attendees.forEach(attendee => {
                            html += `
                                <div class="attendee-item">
                                    <span>${attendee.name || attendee.email}</span>
                                    <span class="rsvp-status rsvp-${attendee.rsvp_status || 'pending'}">
                                        ${(attendee.rsvp_status || 'pending').charAt(0).toUpperCase() + (attendee.rsvp_status || 'pending').slice(1)}
                                    </span>
                                </div>
                            `;
                        });
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
        
        function showEventDetails(eventId) {
            modalContent.html('<p>Loading event details...</p>');
            eventModal.show();
            
            $.get(`get_event_details.php?id=${eventId}`, function(data) {
                if (data.success) {
                    const event = data.event;
                    modalContent.html(`
                        <p><strong>Title:</strong> ${event.eventTitle}</p>
                        <p><strong>Start:</strong> ${event.startDateTime}</p>
                        <p><strong>End:</strong> ${event.endingDateTime}</p>
                        <p><strong>Location:</strong> ${event.eventLocation}</p>
                        <p><strong>Description:</strong> ${event.eventDescription || 'N/A'}</p>
                        <div style="margin-top: 20px;">
                            <button class="action-btn btn-edit" onclick="window.location.href='edit_event.php?id=${eventId}'">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="action-btn btn-delete" onclick="confirmDelete(${eventId})">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    `);
                } else {
                    modalContent.html(`<p>Error: ${data.message}</p>`);
                }
            }).fail(function() {
                modalContent.html('<p>Failed to load event details</p>');
            });
        }
        
        window.confirmDelete = function(eventId) {
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
                    window.location.href = `delete_event.php?id=${eventId}`;
                }
            });
        }
    });
    </script>
</body>
</html>