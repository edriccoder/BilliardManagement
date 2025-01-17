<?php
// send_message.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

require 'conn.php'; // Ensure you have a separate file for DB connection

// Get POST data
$sender_id = $_SESSION['user_id'];
$receiver_id = 3; // Admin ID
$message = trim($_POST['message'] ?? '');

// Validate message
if (empty($message)) {
    echo json_encode(['status' => 'error', 'message' => 'Message cannot be empty']);
    exit();
}

// Sanitize message
$message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

try {
    $stmt = $conn->prepare("INSERT INTO chat_messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    $stmt->execute([$sender_id, $receiver_id, $message]);
    echo json_encode(['status' => 'success', 'message' => 'Message sent']);
} catch (Exception $e) {
    // Log error in real application
    echo json_encode(['status' => 'error', 'message' => 'Failed to send message']);
}
?>
