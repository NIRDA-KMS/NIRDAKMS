<?php
// Prevent PHP errors from being displayed
error_reporting(0);
ini_set('display_errors', 0);

// Ensure we're sending JSON response
header('Content-Type: application/json');

// Enable CORS if needed
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once('../SchedureEvent/connect.php');
$conn = $connection;

// Add connection check
if (!$conn) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

session_start();

// Handle OPTIONS request for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Check authentication - modified to handle both session and direct user_id
if (!isset($_SESSION['user_id'])) {
    $input = json_decode(file_get_contents('php://input'), true);
    if (isset($input['user_id'])) {
        $_SESSION['user_id'] = $input['user_id'];
    } else if (isset($_GET['user_id'])) {
        $_SESSION['user_id'] = $_GET['user_id'];
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized - Please log in']);
        exit;
    }
}

// Get the request method and input data
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

// For PUT and DELETE requests, get the input from php://input
if ($method === 'PUT' || $method === 'DELETE') {
    if ($input === null) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON data']);
        exit;
    }
}

// Get action from GET or POST or JSON body
$action = $_GET['action'] ?? ($input['action'] ?? '');

try {
    // Validate required parameters
    if (empty($action)) {
        throw new Exception('Action parameter is required');
    }
    
    $response = [];

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
                case 'get_users':
                    $response = getUsers();
                    break;
                case 'get_roles':
                    $response = getRoles();
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
                case 'toggle_user_status':
                    $response = toggleUserStatus($input);
                    break;
                case 'update_user_role':
                    $response = updateUserRole($input);
                    break;
                case 'create_category':
                    $response = createCategory($input);
                    break;
                case 'update_category':
                    $response = updateCategory($input);
                    break;
                case 'delete_category':
                    $response = deleteCategory($input);
                    break;
                default:
                    http_response_code(400);
                    $response = ['error' => 'Invalid action'];
            }
            break;
            
        case 'PUT':
            switch ($action) {
                case 'update_topic':
                    $response = updateTopic($input);
                    break;
                case 'update_reply':
                    $response = updateReply($input);
                    break;
                case 'toggle_pin':
                    $response = togglePin($input);
                    break;
                case 'toggle_active':
                    $response = toggleActive($input);
                    break;
                case 'update_category':
                    $response = updateCategory($input);
                    break;
                default:
                    http_response_code(400);
                    $response = ['error' => 'Invalid action'];
            }
            break;
            
        case 'DELETE':
            switch ($action) {
                case 'delete_topic':
                    $response = deleteTopic($input);
                    break;
                case 'delete_reply':
                    $response = deleteReply($input);
                    break;
                case 'delete_category':
                    $response = deleteCategory($input);
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
    $date_filter = isset($_GET['date_filter']) ? $_GET['date_filter'] : null;
    $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $per_page = 10;
    $offset = ($page - 1) * $per_page;
    $current_user_id = $_SESSION['user_id'];
    
    try {
        // Get user role first
        $role_stmt = $conn->prepare("SELECT role_id FROM users WHERE user_id = ?");
        $role_stmt->bind_param("i", $current_user_id);
        $role_stmt->execute();
        $role_result = $role_stmt->get_result();
        $user_role = $role_result->fetch_assoc()['role_id'];
        
        // First get total count
        $count_query = "SELECT COUNT(*) as total FROM forum_topics t";
        $where_conditions = [];
        $params = [];
        $param_types = "";
        
        // Only show active topics for non-admin users
        if (!isset($_SESSION['role_id']) || ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 2)) {
            $where_conditions[] = "t.is_active = 1";
        }
        
        if ($category_id) {
            $where_conditions[] = "t.category_id = ?";
            $params[] = $category_id;
            $param_types .= "i";
        }
        
        if ($search) {
            $where_conditions[] = "(t.title LIKE ? OR t.content LIKE ?)";
            $search_param = "%$search%";
            $params[] = $search_param;
            $params[] = $search_param;
            $param_types .= "ss";
        }
        
        if ($user_id) {
            $where_conditions[] = "t.user_id = ?";
            $params[] = $user_id;
            $param_types .= "i";
        }
        
        if ($date_filter) {
            switch ($date_filter) {
                case 'today':
                    $where_conditions[] = "DATE(t.created_at) = CURDATE()";
                    break;
                case 'week':
                    $where_conditions[] = "t.created_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
                    break;
                case 'month':
                    $where_conditions[] = "t.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
                    break;
            }
        }
        
        if (!empty($where_conditions)) {
            $count_query .= " WHERE " . implode(" AND ", $where_conditions);
        }
        
        $count_stmt = $conn->prepare($count_query);
        if (!empty($params)) {
            $count_stmt->bind_param($param_types, ...$params);
        }
        $count_stmt->execute();
        $total_count = $count_stmt->get_result()->fetch_assoc()['total'];
        
        // Now get the actual topics with subscription status and edit permissions
        $query = "SELECT 
                    t.topic_id, 
                    t.title, 
                    t.content, 
                    t.is_pinned, 
                    t.is_active, 
                    t.created_at, 
                    t.updated_at,
                    t.user_id as author_id,
                    u.full_name as author_name, 
                    u.username as author_username,
                    c.id as category_id, 
                    c.category_name,
                    (SELECT COUNT(*) FROM forum_replies r WHERE r.topic_id = t.topic_id) as reply_count,
                    EXISTS(SELECT 1 FROM forum_subscriptions fs WHERE fs.topic_id = t.topic_id AND fs.user_id = ?) as is_subscribed,
                    CASE 
                        WHEN t.user_id = ? THEN 1
                        WHEN ? IN (1, 2, 3) THEN 1
                        ELSE 0
                    END as can_edit,
                    CASE 
                        WHEN ? IN (1, 2, 3) THEN 1
                        ELSE 0
                    END as can_pin
                FROM forum_topics t
                LEFT JOIN users u ON t.user_id = u.user_id
                LEFT JOIN forum_categories c ON t.category_id = c.id";
        
        if (!empty($where_conditions)) {
            $query .= " WHERE " . implode(" AND ", $where_conditions);
        }
        
        $query .= " ORDER BY t.is_pinned DESC, t.created_at DESC LIMIT ? OFFSET ?";
        
        // Add parameters for subscription check and edit permissions
        array_unshift($params, $current_user_id, $current_user_id, $user_role, $user_role);
        $params[] = $per_page;
        $params[] = $offset;
        $param_types = "iiii" . $param_types . "ii";
        
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        if (!empty($params)) {
            $stmt->bind_param($param_types, ...$params);
        }
        
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        
        if ($result === false) {
            throw new Exception("Get result failed: " . $stmt->error);
        }
        
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
                    'id' => $row['author_id'],
                    'name' => $row['author_name'] ?? 'Unknown',
                    'username' => $row['author_username'] ?? 'unknown'
                ],
                'category' => [
                    'id' => $row['category_id'],
                    'name' => $row['category_name'] ?? 'Uncategorized'
                ],
                'reply_count' => $row['reply_count'],
                'is_subscribed' => (bool)$row['is_subscribed'],
                'can_edit' => (bool)$row['can_edit'],
                'can_pin' => (bool)$row['can_pin']
            ];
        }
        
        return [
            'topics' => $topics,
            'total_count' => $total_count,
            'per_page' => $per_page,
            'current_page' => $page,
            'current_user_role' => $user_role
        ];
        
    } catch (Exception $e) {
        error_log("Error in getTopics: " . $e->getMessage());
        throw new Exception("Failed to load topics: " . $e->getMessage());
    }
}

function getTopic() {
    global $conn;
    
    try {
        $topic_id = intval($_GET['topic_id']);
        $current_user_id = $_SESSION['user_id'];
        
        // Debug log
        error_log("Getting topic with ID: " . $topic_id);
        
        // First check if topic exists and get user role
        $check = $conn->prepare("
            SELECT t.*, u.role_id 
            FROM forum_topics t 
            LEFT JOIN users u ON u.user_id = ? 
            WHERE t.topic_id = ?
        ");
        $check->bind_param("ii", $current_user_id, $topic_id);
        $check->execute();
        $result = $check->get_result();
        
        if ($result->num_rows === 0) {
            error_log("Topic not found with ID: " . $topic_id);
            throw new Exception('Topic not found');
        }
        
        $topic_data = $result->fetch_assoc();
        $user_role = $topic_data['role_id'];
        
        // Get topic with author and category info
        $stmt = $conn->prepare("SELECT 
            t.*, 
            u.full_name as author_name, 
            u.username as author_username,
            u.user_id as author_id,
            c.id as category_id, 
            c.category_name,
            CASE 
                WHEN t.user_id = ? THEN 1
                WHEN ? IN (1, 2) THEN 1
                ELSE 0
            END as can_edit
            FROM forum_topics t
            LEFT JOIN users u ON t.user_id = u.user_id
            LEFT JOIN forum_categories c ON t.category_id = c.id
            WHERE t.topic_id = ?");
            
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            throw new Exception('Database error');
        }
        
        $stmt->bind_param("iii", $current_user_id, $user_role, $topic_id);
        if (!$stmt->execute()) {
            error_log("Execute failed: " . $stmt->error);
            throw new Exception('Database error');
        }
        
        $result = $stmt->get_result();
        $topic = $result->fetch_assoc();
        
        if (!$topic) {
            error_log("Topic fetch failed after existence check");
            throw new Exception('Topic not found');
        }
        
        // Get replies with author info and edit permissions
        $stmt = $conn->prepare("SELECT 
            r.*, 
            u.full_name as author_name, 
            u.username as author_username,
            u.user_id as author_id,
            CASE 
                WHEN r.user_id = ? THEN 1
                WHEN ? IN (1, 2) THEN 1
                ELSE 0
            END as can_edit
            FROM forum_replies r
            LEFT JOIN users u ON r.user_id = u.user_id
            WHERE r.topic_id = ?
            ORDER BY r.created_at ASC");
            
        $stmt->bind_param("iii", $current_user_id, $user_role, $topic_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $replies = [];
        
        while ($row = $result->fetch_assoc()) {
            $replies[] = [
                'id' => $row['reply_id'],
                'content' => $row['content'],
                'author' => [
                    'id' => $row['author_id'],
                    'name' => $row['author_name'] ?? 'Unknown',
                    'username' => $row['author_username'] ?? 'unknown'
                ],
                'created_at' => $row['created_at'],
                'updated_at' => $row['updated_at'],
                'flagged' => (bool)$row['is_flagged'],
                'flag_reason' => $row['flag_reason'],
                'can_edit' => (bool)$row['can_edit']
            ];
        }
        
        // Check subscription status
        $is_subscribed = false;
        if (isset($_SESSION['user_id'])) {
            $stmt = $conn->prepare("SELECT 1 FROM forum_subscriptions 
                                WHERE topic_id = ? AND user_id = ?");
            $stmt->bind_param("ii", $topic_id, $_SESSION['user_id']);
            $stmt->execute();
            $is_subscribed = $stmt->get_result()->num_rows > 0;
        }
        
        $response = [
            'topic' => [
                'id' => $topic['topic_id'],
                'title' => $topic['title'],
                'content' => $topic['content'],
                'pinned' => (bool)$topic['is_pinned'],
                'active' => (bool)$topic['is_active'],
                'created_at' => $topic['created_at'],
                'updated_at' => $topic['updated_at'],
                'author' => [
                    'id' => $topic['author_id'],
                    'name' => $topic['author_name'] ?? 'Unknown',
                    'username' => $topic['author_username'] ?? 'unknown'
                ],
                'category' => [
                    'id' => $topic['category_id'],
                    'name' => $topic['category_name'] ?? 'Uncategorized'
                ],
                'can_edit' => (bool)$topic['can_edit']
            ],
            'replies' => $replies,
            'is_subscribed' => $is_subscribed
        ];
        
        error_log("Successfully retrieved topic: " . json_encode($response));
        return $response;
        
    } catch (Exception $e) {
        error_log("Error in getTopic: " . $e->getMessage());
        throw $e;
    }
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

function handleModerationAction($action, $input) {
    global $conn;
    
    $user_id = $_SESSION['user_id'];
    
    // Check if user has moderation privileges or is the content owner
    if (!hasModerationPrivileges($user_id) && !isContentOwner($user_id, $input)) {
        throw new Exception('Unauthorized - You do not have permission to perform this action');
    }
    
    switch ($action) {
        case 'update_topic':
            return updateTopic($input);
        case 'update_reply':
            return updateReply($input);
        case 'delete_topic':
            return deleteTopic($input);
        case 'delete_reply':
            return deleteReply($input);
        case 'toggle_pin':
            return togglePin($input);
        case 'toggle_active':
            return toggleActive($input);
        case 'resolve_flag':
            return resolveFlag($input);
        default:
            throw new Exception('Invalid action');
    }
}

function isContentOwner($user_id, $data) {
    global $conn;
    
    if (isset($data['topic_id'])) {
        $stmt = $conn->prepare("SELECT 1 FROM forum_topics WHERE topic_id = ? AND user_id = ?");
        $topic_id = intval($data['topic_id']);
        $stmt->bind_param("ii", $topic_id, $user_id);
    } else if (isset($data['reply_id'])) {
        $stmt = $conn->prepare("SELECT 1 FROM forum_replies WHERE reply_id = ? AND user_id = ?");
        $reply_id = intval($data['reply_id']);
        $stmt->bind_param("ii", $reply_id, $user_id);
    } else {
        return false;
    }
    
    $stmt->execute();
    return $stmt->get_result()->num_rows > 0;
}

function togglePin($data) {
    global $conn;
    
    $topic_id = intval($data['topic_id']);
    $pinned = $data['pinned'] ? 1 : 0;
    
    $stmt = $conn->prepare("UPDATE forum_topics SET is_pinned = ?, updated_at = NOW() WHERE topic_id = ?");
    $stmt->bind_param("ii", $pinned, $topic_id);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to update pin status');
    }
    
    return ['success' => true, 'message' => 'Pin status updated successfully'];
}

function toggleActive($data) {
    global $conn;
    
    $topic_id = intval($data['topic_id']);
    $active = $data['active'] ? 1 : 0;
    
    $stmt = $conn->prepare("UPDATE forum_topics SET is_active = ?, updated_at = NOW() WHERE topic_id = ?");
    $stmt->bind_param("ii", $active, $topic_id);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to update active status');
    }
    
    return ['success' => true, 'message' => 'Active status updated successfully'];
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
    
    try {
        $topic_id = intval($data['topic_id']);
        $user_id = isset($data['user_id']) ? intval($data['user_id']) : $_SESSION['user_id'];
        
        // Validate topic exists
        $check = $conn->prepare("SELECT 1 FROM forum_topics WHERE topic_id = ?");
        $check->bind_param("i", $topic_id);
        $check->execute();
        if ($check->get_result()->num_rows === 0) {
            throw new Exception('Topic not found');
        }
        
        // Check if already subscribed
        $check = $conn->prepare("SELECT subscription_id FROM forum_subscriptions WHERE topic_id = ? AND user_id = ?");
        $check->bind_param("ii", $topic_id, $user_id);
        $check->execute();
        
        if ($check->get_result()->num_rows > 0) {
            return ['success' => true, 'message' => 'Already subscribed to this topic'];
        }
        
        // Add subscription - note we don't specify subscription_id as it's auto-increment
        $stmt = $conn->prepare("INSERT INTO forum_subscriptions (topic_id, user_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $topic_id, $user_id);
        
        if (!$stmt->execute()) {
            error_log("Failed to subscribe to topic: " . $conn->error);
            throw new Exception('Failed to subscribe to topic: ' . $conn->error);
        }
        
        return ['success' => true, 'message' => 'Subscribed to topic successfully'];
        
    } catch (Exception $e) {
        error_log("Error in subscribeToTopic: " . $e->getMessage());
        throw $e;
    }
}

function unsubscribeFromTopic($data) {
    global $conn;
    
    $topic_id = intval($data['topic_id']);
    $user_id = isset($data['user_id']) ? intval($data['user_id']) : $_SESSION['user_id'];
    
    $stmt = $conn->prepare("DELETE FROM forum_subscriptions WHERE topic_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $topic_id, $user_id);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to unsubscribe from topic');
    }
    
    return ['success' => true, 'message' => 'Unsubscribed successfully'];
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
    
    if (!isset($data['reply_id']) || !isset($data['resolution'])) {
        throw new Exception('Reply ID and resolution are required');
    }
    
    $reply_id = intval($data['reply_id']);
    $resolution = $conn->real_escape_string($data['resolution']);
    $admin_id = $_SESSION['user_id'];
    
    try {
        $conn->begin_transaction();
        
        // First verify the reply exists
        $check_stmt = $conn->prepare("SELECT r.reply_id, r.user_id, r.content, r.topic_id 
                                    FROM forum_replies r 
                                    WHERE r.reply_id = ? AND r.is_flagged = 1");
        $check_stmt->bind_param("i", $reply_id);
        if (!$check_stmt->execute()) {
            throw new Exception('Failed to verify reply: ' . $check_stmt->error);
        }
        
        $result = $check_stmt->get_result();
        if ($result->num_rows === 0) {
            throw new Exception('Reply not found or not flagged');
        }
        
        $reply_info = $result->fetch_assoc();
        
        switch ($resolution) {
            case 'keep':
                // Just remove the flag
                $stmt = $conn->prepare("UPDATE forum_replies SET is_flagged = 0, flag_reason = NULL, updated_at = NOW() WHERE reply_id = ?");
                $stmt->bind_param("i", $reply_id);
                if (!$stmt->execute()) {
                    throw new Exception('Failed to update reply flag status: ' . $stmt->error);
                }
                
                // Log the action
                $log_stmt = $conn->prepare("INSERT INTO moderation_logs (admin_id, action_type, content_type, content_id, details, created_at) 
                                          VALUES (?, 'keep_content', 'reply', ?, 'Content reviewed and kept', NOW())");
                $log_stmt->bind_param("ii", $admin_id, $reply_id);
                if (!$log_stmt->execute()) {
                    throw new Exception('Failed to log action: ' . $log_stmt->error);
                }
                break;
                
            case 'warn':
                // Remove flag
                $stmt = $conn->prepare("UPDATE forum_replies SET is_flagged = 0, flag_reason = NULL, updated_at = NOW() WHERE reply_id = ?");
                $stmt->bind_param("i", $reply_id);
                if (!$stmt->execute()) {
                    throw new Exception('Failed to update reply flag status: ' . $stmt->error);
                }
                
                // Create warning record
                $warn_stmt = $conn->prepare("INSERT INTO user_warnings (user_id, admin_id, warning_type, content_id, content_type, created_at) 
                                           VALUES (?, ?, 'inappropriate_content', ?, 'reply', NOW())");
                $warn_stmt->bind_param("iii", $reply_info['user_id'], $admin_id, $reply_id);
                if (!$warn_stmt->execute()) {
                    throw new Exception('Failed to create warning record: ' . $warn_stmt->error);
                }
                
                // Log the action
                $log_stmt = $conn->prepare("INSERT INTO moderation_logs (admin_id, action_type, content_type, content_id, user_id, details, created_at) 
                                          VALUES (?, 'warn_user', 'reply', ?, ?, 'User warned for inappropriate content', NOW())");
                $log_stmt->bind_param("iii", $admin_id, $reply_id, $reply_info['user_id']);
                if (!$log_stmt->execute()) {
                    throw new Exception('Failed to log action: ' . $log_stmt->error);
                }
                break;
                
            case 'delete':
                // Log the content before deletion
                $log_stmt = $conn->prepare("INSERT INTO moderation_logs (admin_id, action_type, content_type, content_id, user_id, content_snapshot, created_at) 
                                          VALUES (?, 'delete_content', 'reply', ?, ?, ?, NOW())");
                $log_stmt->bind_param("iiis", $admin_id, $reply_id, $reply_info['user_id'], $reply_info['content']);
                if (!$log_stmt->execute()) {
                    throw new Exception('Failed to log deletion: ' . $log_stmt->error);
                }
                
                // Delete the reply
                $delete_stmt = $conn->prepare("DELETE FROM forum_replies WHERE reply_id = ?");
                $delete_stmt->bind_param("i", $reply_id);
                if (!$delete_stmt->execute()) {
                    throw new Exception('Failed to delete reply: ' . $delete_stmt->error);
                }
                break;
                
            default:
                throw new Exception('Invalid resolution action: ' . $resolution);
        }
        
        $conn->commit();
        return [
            'success' => true,
            'message' => 'Flag resolved successfully',
            'action' => $resolution
        ];
        
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Error in resolveFlag: " . $e->getMessage());
        error_log("Resolution type: " . $resolution);
        error_log("Reply ID: " . $reply_id);
        error_log("Admin ID: " . $admin_id);
        throw new Exception('Failed to resolve flag: ' . $e->getMessage());
    }
}

function notifySubscribers($topic_id, $user_id, $reply_id) {
    // In a real implementation, this would send notifications to subscribers
    return ['success' => true];
}


// Add to forum_api.php after existing functions

function updateTopic($data) {
    global $conn;
    
    $topic_id = intval($data['topic_id']);
    $title = trim($conn->real_escape_string($data['title']));
    $content = trim($conn->real_escape_string($data['content']));
    
    if (empty($title) || empty($content)) {
        throw new Exception('Title and content are required');
    }
    
    $stmt = $conn->prepare("UPDATE forum_topics SET title = ?, content = ?, updated_at = NOW() WHERE topic_id = ?");
    $stmt->bind_param("ssi", $title, $content, $topic_id);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to update topic');
    }
    
    return ['success' => true, 'message' => 'Topic updated successfully'];
}

function updateReply($data) {
    global $conn;
    
    $reply_id = intval($data['reply_id']);
    $content = trim($conn->real_escape_string($data['content']));
    
    if (empty($content)) {
        throw new Exception('Content is required');
    }
    
    $stmt = $conn->prepare("UPDATE forum_replies SET content = ?, updated_at = NOW() WHERE reply_id = ?");
    $stmt->bind_param("si", $content, $reply_id);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to update reply');
    }
    
    return ['success' => true, 'message' => 'Reply updated successfully'];
}

function deleteTopic($data) {
    global $conn;
    
    $topic_id = intval($data['topic_id']);
    
    // First delete all replies
    $stmt = $conn->prepare("DELETE FROM forum_replies WHERE topic_id = ?");
    $stmt->bind_param("i", $topic_id);
    $stmt->execute();
    
    // Then delete the topic
    $stmt = $conn->prepare("DELETE FROM forum_topics WHERE topic_id = ?");
    $stmt->bind_param("i", $topic_id);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to delete topic');
    }
    
    return ['success' => true, 'message' => 'Topic deleted successfully'];
}

function deleteReply($data) {
    global $conn;
    
    $reply_id = intval($data['reply_id']);
    
    $stmt = $conn->prepare("DELETE FROM forum_replies WHERE reply_id = ?");
    $stmt->bind_param("i", $reply_id);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to delete reply');
    }
    
    return ['success' => true, 'message' => 'Reply deleted successfully'];
}

function getUsers() {
    global $conn;
    
    try {
        $query = "SELECT user_id, full_name, username FROM users WHERE is_active = 1 ORDER BY full_name";
        $result = $conn->query($query);
        
        if (!$result) {
            throw new Exception("Database error: " . $conn->error);
        }
        
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = [
                'user_id' => $row['user_id'],
                'full_name' => $row['full_name'],
                'username' => $row['username']
            ];
        }
        
        return $users;
        
    } catch (Exception $e) {
        error_log("Error in getUsers: " . $e->getMessage());
        throw new Exception("Failed to load users: " . $e->getMessage());
    }
}

function toggleUserStatus($data) {
    global $conn;
    
    if (!isset($data['user_id']) || !isset($data['is_active'])) {
        throw new Exception('User ID and status are required');
    }
    
    $user_id = intval($data['user_id']);
    $is_active = $data['is_active'] ? 1 : 0;
    $admin_id = $_SESSION['user_id'];
    
    try {
        $conn->begin_transaction();
        
        // Update user status
        $stmt = $conn->prepare("UPDATE users SET is_active = ?, updated_at = NOW() WHERE user_id = ?");
        $stmt->bind_param("ii", $is_active, $user_id);
        if (!$stmt->execute()) {
            throw new Exception('Failed to update user status');
        }
        
        // Log the action
        $action_type = $is_active ? 'activate_user' : 'deactivate_user';
        $stmt = $conn->prepare("INSERT INTO moderation_logs (admin_id, action_type, user_id, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("isi", $admin_id, $action_type, $user_id);
        if (!$stmt->execute()) {
            throw new Exception('Failed to log user status change');
        }
        
        $conn->commit();
        return ['success' => true, 'message' => 'User status updated successfully'];
        
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Error in toggleUserStatus: " . $e->getMessage());
        throw new Exception('Failed to update user status: ' . $e->getMessage());
    }
}

function updateUserRole($data) {
    global $conn;
    
    if (!isset($data['user_id']) || !isset($data['role_id'])) {
        throw new Exception('User ID and role ID are required');
    }
    
    $user_id = intval($data['user_id']);
    $role_id = intval($data['role_id']);
    $admin_id = $_SESSION['user_id'];
    
    try {
        $conn->begin_transaction();
        
        // Update user role
        $stmt = $conn->prepare("UPDATE users SET role_id = ?, updated_at = NOW() WHERE user_id = ?");
        $stmt->bind_param("ii", $role_id, $user_id);
        if (!$stmt->execute()) {
            throw new Exception('Failed to update user role');
        }
        
        // Log the action
        $stmt = $conn->prepare("INSERT INTO moderation_logs (admin_id, action_type, user_id, details, created_at) VALUES (?, 'update_role', ?, ?, NOW())");
        $details = json_encode(['new_role_id' => $role_id]);
        $stmt->bind_param("iis", $admin_id, $user_id, $details);
        if (!$stmt->execute()) {
            throw new Exception('Failed to log role change');
        }
        
        $conn->commit();
        return ['success' => true, 'message' => 'User role updated successfully'];
        
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Error in updateUserRole: " . $e->getMessage());
        throw new Exception('Failed to update user role: ' . $e->getMessage());
    }
}

function getRoles() {
    global $conn;
    
    try {
        $query = "SELECT role_id, role_name FROM roles WHERE is_active = 1 ORDER BY role_name";
        $result = $conn->query($query);
        
        if (!$result) {
            throw new Exception("Database error: " . $conn->error);
        }
        
        $roles = [];
        while ($row = $result->fetch_assoc()) {
            $roles[] = [
                'id' => $row['role_id'],
                'name' => $row['role_name']
            ];
        }
        
        return $roles;
        
    } catch (Exception $e) {
        error_log("Error in getRoles: " . $e->getMessage());
        throw new Exception("Failed to load roles: " . $e->getMessage());
    }
}

// Add new category management functions
function createCategory($data) {
    global $conn;
    
    if (empty($data['name']) || empty($data['description']) || empty($data['permission'])) {
        throw new Exception('Name, description and permission are required');
    }
    
    $name = trim($conn->real_escape_string($data['name']));
    $description = trim($conn->real_escape_string($data['description']));
    $permission = trim($conn->real_escape_string($data['permission']));
    
    $stmt = $conn->prepare("INSERT INTO forum_categories (category_name, description, permission) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $description, $permission);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to create category: ' . $conn->error);
    }
    
    return [
        'success' => true,
        'category_id' => $stmt->insert_id,
        'message' => 'Category created successfully'
    ];
}

function updateCategory($data) {
    global $conn;
    
    if (empty($data['category_id']) || empty($data['name']) || empty($data['description']) || empty($data['permission'])) {
        throw new Exception('Category ID, name, description and permission are required');
    }
    
    $category_id = intval($data['category_id']);
    $name = trim($conn->real_escape_string($data['name']));
    $description = trim($conn->real_escape_string($data['description']));
    $permission = trim($conn->real_escape_string($data['permission']));
    
    $stmt = $conn->prepare("UPDATE forum_categories SET category_name = ?, description = ?, permission = ? WHERE id = ?");
    $stmt->bind_param("sssi", $name, $description, $permission, $category_id);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to update category: ' . $conn->error);
    }
    
    return [
        'success' => true,
        'message' => 'Category updated successfully'
    ];
}

function deleteCategory($data) {
    global $conn;
    
    if (empty($data['category_id'])) {
        throw new Exception('Category ID is required');
    }
    
    $category_id = intval($data['category_id']);
    
    try {
        $conn->begin_transaction();
        
        // First, check if there are any topics in this category
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM forum_topics WHERE category_id = ?");
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_assoc()['count'];
        
        if ($count > 0) {
            throw new Exception('Cannot delete category: It contains topics. Please move or delete the topics first.');
        }
        
        // If no topics, delete the category
        $stmt = $conn->prepare("DELETE FROM forum_categories WHERE id = ?");
        $stmt->bind_param("i", $category_id);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to delete category: ' . $conn->error);
        }
        
        $conn->commit();
        return [
            'success' => true,
            'message' => 'Category deleted successfully'
        ];
        
    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }
}
?>