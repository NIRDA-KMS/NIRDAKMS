<?php
header('Content-Type: application/json');
require_once('../SchedureEvent/connect.php');
$conn = $connection;

// Add connection check
if (!$conn) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

session_start();
header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Check authentication
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Get the request method
$method = $_SERVER['REQUEST_METHOD'];

// Get input data
$input = json_decode(file_get_contents('php://input'), true);
if ($input === null && $method === 'POST') {
    $input = $_POST;
}

// Get action from GET or POST
$action = $_GET['action'] ?? ($input['action'] ?? '');

$response = [];

try {
    switch ($method) {
        case 'GET':
            switch ($action) {
                case 'get_categories':
                    $response = getCategories();
                    break;
                case 'get_topics':
                    $response = getTopics();
                    break;
                case 'get_topic':
                    if (!isset($_GET['topic_id'])) {
                        throw new Exception('Topic ID is required');
                    }
                    $response = getTopic();
                    break;
                case 'get_flagged_content':
                    $response = getFlaggedContent();
                    break;
                case 'get_stats':
                    $response = getForumStats();
                    break;
                default:
                    http_response_code(400);
                    $response = ['error' => 'Invalid action'];
            }
            break;
            
        case 'POST':
            switch ($action) {
                case 'create_topic':
                    $response = createTopic($input);
                    break;
                case 'create_reply':
                    $response = createReply($input);
                    break;
                case 'subscribe_topic':
                    $response = subscribeToTopic($input);
                    break;
                case 'unsubscribe_topic':
                    $response = unsubscribeFromTopic($input);
                    break;
                case 'flag_reply':
                    $response = flagReply($input);
                    break;
                default:
                    http_response_code(400);
                    $response = ['error' => 'Invalid action'];
            }
            break;
            
        case 'PUT':
        case 'DELETE':
            switch ($action) {
                case 'update_topic':
                case 'update_reply':
                case 'delete_topic':
                case 'delete_reply':
                case 'toggle_pin':
                case 'toggle_active':
                case 'resolve_flag':
                    $response = handleModerationAction($action, $input);
                    break;
                default:
                    http_response_code(400);
                    $response = ['error' => 'Invalid action'];
            }
            break;
            
        default:
            http_response_code(405);
            $response = ['error' => 'Method not allowed'];
    }
} catch (Exception $e) {
    http_response_code(500);
    $response = ['error' => $e->getMessage()];
    error_log("API Error: " . $e->getMessage());
    error_log("Session: " . print_r($_SESSION, true));
    error_log("Request: " . print_r($_REQUEST, true));
    error_log("Trace: " . $e->getTraceAsString());
}

echo json_encode($response);


function getCategories() {
    global $conn;
    
    $query = "SELECT id as category_id, category_name as name, description, permission 
              FROM forum_categories";
    
    $result = $conn->query($query);
    $categories = [];
    
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
    
    return $categories;
}

function getTopics() {
    global $conn;
    
    $category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : null;
    $search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : null;
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $per_page = 10;
    $offset = ($page - 1) * $per_page;
    
    $query = "SELECT t.topic_id, t.title, t.content, t.is_pinned, t.is_active, t.created_at, t.updated_at,
                 u.user_id, u.full_name as author_name, u.username as author_username,
                 c.id as category_id, c.category_name,
                 (SELECT COUNT(*) FROM forum_replies r WHERE r.topic_id = t.topic_id) as reply_count
          FROM forum_topics t
          JOIN users u ON t.user_id = u.user_id
          JOIN forum_categories c ON t.category_id = c.id
          WHERE t.is_active = 1";
    
    if ($category_id) {
        $query .= " AND t.category_id = $category_id";
    }
    
    if ($search) {
        $query .= " AND (t.title LIKE '%$search%' OR t.content LIKE '%$search%')";
    }
    
    $query .= " ORDER BY t.is_pinned DESC, t.created_at DESC LIMIT $per_page OFFSET $offset";
    
    $result = $conn->query($query);
    $topics = [];
    

    
    while ($row = $result->fetch_assoc()) {
        $topics[] = [
            'id' => $row['topic_id'],
            'title' => $row['title'],
            'excerpt' => substr(strip_tags($row['content']), 0, 200),
            'pinned' => (bool)$row['is_pinned'],
            'active' => (bool)$row['is_active'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at'],
            'author' => [
                'id' => $row['user_id'],
                'name' => $row['author_name'],
                'username' => $row['author_username']
            ],
            'category' => [
                'id' => $row['category_id'],
                'name' => $row['category_name']
            ],
            'reply_count' => $row['reply_count']
        ];
    }
    
    return $topics;
}

function getTopic() {
    global $conn;
    
    $topic_id = intval($_GET['topic_id']);
    
    $stmt = $conn->prepare("SELECT t.*, u.full_name as author_name, u.username as author_username, 
                           c.id as category_id, c.category_name
                           FROM forum_topics t
                           JOIN users u ON t.user_id = u.user_id
                           JOIN forum_categories c ON t.category_id = c.id
                           WHERE t.topic_id = ?");
    $stmt->bind_param("i", $topic_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $topic = $result->fetch_assoc();
    
    if (!$topic) {
        throw new Exception('Topic not found');
    }
    
     // Get replies
     $stmt = $conn->prepare("SELECT r.*, u.full_name as author_name, u.username as author_username
     FROM forum_replies r
     JOIN users u ON r.user_id = u.user_id
     WHERE r.topic_id = ?
     ORDER BY r.created_at ASC");
$stmt->bind_param("i", $topic_id);
$stmt->execute();
$result = $stmt->get_result();
$replies = [];

while ($row = $result->fetch_assoc()) {
$replies[] = [
'id' => $row['reply_id'],
'content' => $row['content'],
'author' => [
'id' => $row['user_id'],
'name' => $row['author_name'],
'username' => $row['author_username']
],
'created_at' => $row['created_at'],
'updated_at' => $row['updated_at'],
'flagged' => (bool)$row['is_flagged'],
'flag_reason' => $row['flag_reason']
];
}
    
    // Check if user is subscribed
    $is_subscribed = false;
    if (isset($_SESSION['user_id'])) {
        $stmt = $conn->prepare("SELECT 1 FROM forum_subscriptions 
                               WHERE topic_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $topic_id, $_SESSION['user_id']);
        $stmt->execute();
        $is_subscribed = $stmt->get_result()->num_rows > 0;
    }
    
    return [
        'topic' => [
            'id' => $topic['topic_id'],
            'title' => $topic['title'],
            'content' => $topic['content'],
            'pinned' => (bool)$topic['is_pinned'],
            'active' => (bool)$topic['is_active'],
            'created_at' => $topic['created_at'],
            'updated_at' => $topic['updated_at'],
            'author' => [
                'id' => $topic['user_id'],
                'name' => $topic['author_name'],
                'username' => $topic['author_username']
            ],
            'category' => [
                'id' => $topic['category_id'],
                'name' => $topic['category_name']
            ]
        ],
        'replies' => $replies,
        'is_subscribed' => $is_subscribed
    ];
}


function createTopic($data) {
    global $conn;
    
 // Validate required fields
 if (empty($data['category_id']) || empty($data['title']) || empty($data['content'])) {
    throw new Exception('Category ID, title and content are required');
}

$category_id = intval($data['category_id']);
$title = trim($conn->real_escape_string($data['title']));
$content = trim($conn->real_escape_string($data['content']));
$user_id = $_SESSION['user_id'];
    
    // if (empty($title) || empty($content)) {
    //     throw new Exception('Title and content are required');
    // }
    
    $stmt = $conn->prepare("INSERT INTO forum_topics (category_id, user_id, title, content) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $category_id, $user_id, $title, $content);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to create topic: ' . $conn->error);
    }
    
    $topic_id = $stmt->insert_id;
    $stmt->close();
    // Subscribe the creator to the topic
    subscribeToTopic(['topic_id' => $topic_id, 'user_id' => $user_id]);
    
    return [
        'success' => true,
        'topic_id' => $topic_id,
        'message' => 'Topic created successfully'
    ];
}

function createReply() {
    global $conn;
    
    $data = json_decode(file_get_contents('php://input'), true);
    $topic_id = intval($data['topic_id']);
    $content = trim($conn->real_escape_string($data['content']));
    $user_id = $_SESSION['user_id'];
    
    if (empty($content)) {
        throw new Exception('Content is required');
    }
    
    $stmt = $conn->prepare("INSERT INTO forum_replies (topic_id, user_id, content) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $topic_id, $user_id, $content);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to create reply');
    }
    
    $reply_id = $stmt->insert_id;
    $stmt->close();
    
    // Notify subscribers
    notifySubscribers($topic_id, $user_id, $reply_id);
    
    return [
        'success' => true,
        'reply_id' => $reply_id
    ];
}

function handleModerationAction($action) {
    global $conn;
    
    $data = json_decode(file_get_contents('php://input'), true);
    $user_id = $_SESSION['user_id'];
    
    // Check if user has moderation privileges
    if (!hasModerationPrivileges($user_id) && $action !== 'flag_reply') {
        throw new Exception('Unauthorized');
    }
    
    switch ($action) {
        case 'update_topic':
            return updateTopic($data);
        case 'update_reply':
            return updateReply($data);
        case 'delete_topic':
            return deleteTopic($data);
        case 'delete_reply':
            return deleteReply($data);
        case 'toggle_pin':
            return togglePin($data);
        case 'toggle_active':
            return toggleActive($data);
        case 'flag_reply':
            return flagReply($data);
        case 'get_flagged_content':
            return getFlaggedContent();
        case 'resolve_flag':
            return resolveFlag($data);
        default:
            throw new Exception('Invalid action');
    }
}

function hasModerationPrivileges($user_id) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT role_id FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    // Assuming roles 1 and 2 are admin/moderator
    return in_array($user['role_id'], [1, 2]);
}

function subscribeToTopic($data) {
    global $conn;
    
    $topic_id = intval($data['topic_id']);
    $user_id = isset($data['user_id']) ? $data['user_id'] : $_SESSION['user_id'];
    
    // Check if already subscribed
    $check = $conn->prepare("SELECT subscription_id FROM forum_subscriptions WHERE topic_id = ? AND user_id = ?");
    $check->bind_param("ii", $topic_id, $user_id);
    $check->execute();
    
    if ($check->get_result()->num_rows > 0) {
        return ['error' => 'Already subscribed to this topic'];
    }
    
    // Add subscription
    $stmt = $conn->prepare("INSERT INTO forum_subscriptions (topic_id, user_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $topic_id, $user_id);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to subscribe to topic: ' . $conn->error);
    }
    
    return ['success' => true, 'message' => 'Subscribed to topic successfully'];
}

function unsubscribeFromTopic() {
    global $conn;
    
    $data = json_decode(file_get_contents('php://input'), true);
    $topic_id = intval($data['topic_id']);
    $user_id = $_SESSION['user_id'];
    
    $stmt = $conn->prepare("DELETE FROM forum_subscriptions WHERE topic_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $topic_id, $user_id);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to unsubscribe from topic');
    }
    
    return ['success' => true];
}

function getForumStats() {
    global $conn;
    
    $stats = [];
    
    // Get topic count
    $result = $conn->query("SELECT COUNT(*) as count FROM forum_topics WHERE is_active = 1");
    $stats['topics'] = $result->fetch_assoc()['count'];
    
    // Get post count (topics + replies)
    $result = $conn->query("SELECT COUNT(*) as count FROM forum_replies");
    $replies = $result->fetch_assoc()['count'];
    $stats['posts'] = $stats['topics'] + $replies;
    
    // Get member count
    $result = $conn->query("SELECT COUNT(*) as count FROM users WHERE is_active = 1");
    $stats['members'] = $result->fetch_assoc()['count'];
    
    return $stats;
}

function getFlaggedContent() {
    global $conn;
    
    $query = "SELECT r.reply_id, r.content, r.is_flagged, r.flag_reason, 
                     u.user_id as author_id, u.full_name as author_name,
                     t.topic_id, t.title as topic_title
              FROM forum_replies r
              JOIN forum_topics t ON r.topic_id = t.topic_id
              JOIN users u ON r.user_id = u.user_id
              WHERE r.is_flagged = 1
              ORDER BY r.created_at DESC";
    
    $result = $conn->query($query);
    $flagged = [];
    
    while ($row = $result->fetch_assoc()) {
        $flagged[] = [
            'reply_id' => $row['reply_id'],
            'content' => $row['content'],
            'flag_reason' => $row['flag_reason'],
            'author_id' => $row['author_id'],
            'author_name' => $row['author_name'],
            'topic_id' => $row['topic_id'],
            'topic_title' => $row['topic_title']
        ];
    }
    
    return $flagged;
}

function flagReply($data) {
    global $conn;
    
    $reply_id = intval($data['reply_id']);
    $reason = $conn->real_escape_string($data['reason']);
    
    $stmt = $conn->prepare("UPDATE forum_replies SET is_flagged = 1, flag_reason = ? WHERE reply_id = ?");
    $stmt->bind_param("si", $reason, $reply_id);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to flag reply');
    }
    
    return ['success' => true];
}

function resolveFlag($data) {
    global $conn;
    
    $reply_id = intval($data['reply_id']);
    $resolution = $conn->real_escape_string($data['resolution']);
    
    // Handle different resolution actions
    switch ($resolution) {
        case 'keep':
            // Just remove the flag
            $stmt = $conn->prepare("UPDATE forum_replies SET is_flagged = 0, flag_reason = NULL WHERE reply_id = ?");
            $stmt->bind_param("i", $reply_id);
            break;
            
        case 'warn':
            // Remove flag and log warning (you'd need a warnings table)
            $stmt = $conn->prepare("UPDATE forum_replies SET is_flagged = 0, flag_reason = NULL WHERE reply_id = ?");
            $stmt->bind_param("i", $reply_id);
            // Here you would also insert into a warnings table
            break;
            
        case 'delete':
            // Delete the reply
            $stmt = $conn->prepare("DELETE FROM forum_replies WHERE reply_id = ?");
            $stmt->bind_param("i", $reply_id);
            break;
            
        default:
            throw new Exception('Invalid resolution action');
    }
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to resolve flag');
    }
    
    return ['success' => true];
}

function notifySubscribers($topic_id, $user_id, $reply_id) {
    // In a real implementation, this would send notifications to subscribers
    return ['success' => true];
}
?>