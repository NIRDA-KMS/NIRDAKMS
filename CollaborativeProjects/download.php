<?php
session_start();
require_once('../SchedureEvent/connect.php');

// Debugging - log that the script was accessed
error_log("Download script accessed. ID: " . ($_GET['id'] ?? 'none'));



$fileId = (int)$_GET['id'];
error_log("Processing download for file ID: $fileId");

try {
    // Get file info from database
    $query = "SELECT filename, filepath FROM files WHERE id = ?";
    error_log("Executing query: $query with ID: $fileId");
    
    $stmt = $connection->prepare($query);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $connection->error);
    }
    
    $stmt->bind_param("i", $fileId);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        throw new Exception("No file found with ID: $fileId");
    }

    $file = $result->fetch_assoc();
    $fileName = $file['filename'];
    $filePath = $file['filepath'];
    $stmt->close();

    error_log("File found: $fileName at $filePath");

    // Verify file exists on server
    if (!file_exists($filePath)) {
        throw new Exception("File not found at path: $filePath");
    }

    // Check if the file is readable
    if (!is_readable($filePath)) {
        throw new Exception("File not readable (check permissions)");
    }

    // Set headers
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=\"" . rawurlencode($fileName) . "\"");
    header("Content-Length: " . filesize($filePath));
    header("Expires: 0");
    header("Cache-Control: must-revalidate");
    header("Pragma: public");

    // Clear any output buffers
    while (ob_get_level()) {
        ob_end_clean();
    }

    // Read the file
    if (readfile($filePath) === false) {
        throw new Exception("Failed to read file");
    }
    
    error_log("File $fileName sent successfully");
    exit();

} catch (Exception $e) {
    error_log("Download error: " . $e->getMessage());
    $_SESSION['error'] = "Error downloading file: " . $e->getMessage();
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}
?>