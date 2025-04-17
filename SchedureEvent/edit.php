<?php
include('connect.php');

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_changes'])) {
        // Process inline edits
        $event_id = (int)$_POST['event_id'];
        $field = mysqli_real_escape_string($connection, $_POST['field']);
        $value = mysqli_real_escape_string($connection, $_POST['value']);
        
        // Special handling for datetime fields
        if ($field === 'startDateTime' || $field === 'endingDateTime') {
            $value = date('Y-m-d H:i:s', strtotime($value));
        }
        
        $query = "UPDATE schedule_events SET $field = '$value' WHERE event_id = $event_id";
        $result = mysqli_query($connection, $query);
        
        if ($result) {
            $_SESSION['message'] = 'Event updated successfully!';
        } else {
            $_SESSION['error'] = 'Error updating event: ' . mysqli_error($connection);
        }
        
        header("Location: manage_events.php");
        exit();
    }
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

// Get paginated events
$query = "SELECT * FROM schedule_events ORDER BY startDateTime DESC LIMIT $offset, $perPage";
$result = mysqli_query($connection, $query);
$events = mysqli_fetch_all($result, MYSQLI_ASSOC);

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
  <style>
    :root {
      --primary-color: #1a237e;
      --secondary-color: #2c3e50;
      --accent-color: #00A0DF;
      --background-color: #f0f2f5;
      --text-color: #333;
      --light-text: #ffffff;
      --border-color: #d1d5db;
    }

    body {
      font-family: 'Roboto', sans-serif;
      background: var(--background-color);
      margin: 20px;
    }

    .container {
      max-width: 900px;
      margin: auto;
      background: white;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th, td {
      padding: 10px;
      text-align: left;
      border-bottom: 1px solid var(--border-color);
    }

    .status-badge {
      padding: 4px 8px;
      border-radius: 12px;
      font-size: 12px;
      font-weight: 500;
      color: white;
      display: inline-block;
    }
    .status-upcoming { background-color: #2196F3; }
    .status-active { background-color: #4CAF50; }
    .status-completed { background-color: #2c3e50; }
    .status-inactive { background-color: #F44336; }

    .action-btn {
      background-color: var(--accent-color);
      color: white;
      border: none;
      padding: 6px 12px;
      border-radius: 6px;
      cursor: pointer;
      transition: 0.3s;
      text-decoration: none;
      display: inline-block;
      text-align: center;
    }

    .action-btn:hover {
      background-color: #0085b5;
    }

    .edit-form {
      display: flex;
      gap: 5px;
    }

    .edit-form input {
      padding: 5px;
      border: 1px solid var(--border-color);
      border-radius: 4px;
      width: 100%;
    }

    .message {
      padding: 10px;
      margin-bottom: 15px;
      border-radius: 4px;
    }
    .success {
      background-color: #d4edda;
      color: #155724;
    }
    .error {
      background-color: #f8d7da;
      color: #721c24;
    }
    
    .action-buttons {
      display: flex;
      gap: 5px;
    }
    
    .btn-save {
      background-color: #4CAF50;
    }
    
    .btn-cancel {
      background-color: #F44336;
    }







  </style>
</head>
<body>
<?php include("../Internees_task/header.php"); ?>

<div class="container">
  <h1><i class="fas fa-calendar-alt"></i> Event Management</h1>
  
  <?php if (isset($_SESSION['message'])): ?>
    <div class="message success">
      <?= $_SESSION['message'] ?>
    </div>
    <?php unset($_SESSION['message']); ?>
  <?php endif; ?>
  
  <?php if (isset($_SESSION['error'])): ?>
    <div class="message error">
      <?= $_SESSION['error'] ?>
    </div>
    <?php unset($_SESSION['error']); ?>
  <?php endif; ?>
  
  <table id="eventsTable">
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
          <!-- Title -->
          <td>
            <?php if (isset($_GET['edit']) && $_GET['edit'] == $event['event_id']): ?>
              <form class="edit-form" method="POST" action="">
                <input type="hidden" name="event_id" value="<?= $event['event_id'] ?>">
                <input type="hidden" name="field" value="eventTitle">
                <input type="text" name="value" value="<?= htmlspecialchars($event['eventTitle']) ?>">
                <div class="action-buttons">
                  <button type="submit" name="save_changes" class="action-btn btn-save">Save</button>
                  <a href="?page=<?= $page ?>" class="action-btn btn-cancel">Cancel</a>
                </div>
              </form>
            <?php else: ?>
              <?= htmlspecialchars($event['eventTitle']) ?>
            <?php endif; ?>
          </td>
          
          <!-- Date/Time -->
          <td>
            <?= $dateTimeRange ?>
          </td>
          
          <!-- Location -->
          <td>
            <?php if (isset($_GET['edit']) && $_GET['edit'] == $event['event_id']): ?>
              <form class="edit-form" method="POST" action="">
                <input type="hidden" name="event_id" value="<?= $event['event_id'] ?>">
                <input type="hidden" name="field" value="eventLocation">
                <input type="text" name="value" value="<?= htmlspecialchars($event['eventLocation']) ?>">
                <div class="action-buttons">
                  <button type="submit" name="save_changes" class="action-btn btn-save">Save</button>
                  <a href="?page=<?= $page ?>" class="action-btn btn-cancel">Cancel</a>
                </div>
              </form>
            <?php else: ?>
              <?= htmlspecialchars($event['eventLocation']) ?>
            <?php endif; ?>
          </td>
          
          <!-- Status (Not Editable) -->
          <td>
            <span class="status-badge <?= $status['class'] ?>">
              <?= $status['text'] ?>
            </span>
          </td>
          
<!-- Actions -->
<td>
    <?php if (isset($_GET['edit']) && $_GET['edit'] == $event['event_id']): ?>
        <!-- Save/Cancel buttons are already in the forms -->
    <?php else: ?>
        <div class="action-buttons">
            <a href="?page=<?= $page ?>&edit=<?= $event['event_id'] ?>" class="action-btn">
                <i class="fas fa-edit"></i> Edit
            </a>
        </div>
    <?php endif; ?>
</td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  
  <!-- Pagination -->
  <div class="pagination">
    <?php if ($page > 1): ?>
      <a href="?page=<?= $page - 1 ?>" class="action-btn">&laquo;</a>
    <?php endif; ?>
    
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
      <a href="?page=<?= $i ?>" class="action-btn <?= $i == $page ? 'active' : '' ?>">
        <?= $i ?>
      </a>
    <?php endfor; ?>
    
    <?php if ($page < $totalPages): ?>
      <a href="?page=<?= $page + 1 ?>" class="action-btn">&raquo;</a>
    <?php endif; ?>
  </div>
</div>
</body>
</html>