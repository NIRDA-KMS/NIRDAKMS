<?php
session_start();
include('../SchedureEvent/connect.php'); // Adjust path as needed

// Check if ID parameter exists and user is authorized
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid file ID";
    header("Location:delete.php" ); // Redirect to delete page with file ID");
    exit();
}

// Verify user authentication here if needed
// if (!isset($_SESSION['user_id'])) { ... }

$fileId = (int)$_GET['id'];

try {
    // Begin transaction
    $connection->begin_transaction();

    // Get file info before deletion
    $stmt = $connection->prepare("SELECT filepath FROM files WHERE id = ?");
    $stmt->bind_param("i", $fileId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("File not found");
    }

    $file = $result->fetch_assoc();
    $filePath = $file['filepath'];
    $stmt->close();

    // Delete from database
    $stmt = $connection->prepare("DELETE FROM files WHERE id = ?");
    $stmt->bind_param("i", $fileId);
    
    if (!$stmt->execute()) {
        throw new Exception("Database deletion failed");
    }
    $stmt->close();

    // Delete physical file
    if (file_exists($filePath)) {
        if (!unlink($filePath)) {
            throw new Exception("File deletion failed");
        }
    }

    $connection->commit();
    $_SESSION['message'] = "File deleted successfully";
} catch (Exception $e) {
    $connection->rollback();
    $_SESSION['error'] = "Error deleting file: " . $e->getMessage();
}

header("Location: ".$_SERVER['HTTP_REFERER']);
exit();
?>