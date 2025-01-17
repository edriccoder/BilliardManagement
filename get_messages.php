<?php
// fetch_messages.php
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

$user_id = $_SESSION['user_id'];
$admin_id = 3; // Admin ID

try {
    // Fetch messages where user is sender and admin is receiver, or vice versa
    $stmt = $conn->prepare("
        SELECT cm.*, u.username 
        FROM chat_messages cm
        JOIN users u ON cm.sender_id = u.user_id
        WHERE (cm.sender_id = ? AND cm.receiver_id = ?)
           OR (cm.sender_id = ? AND cm.receiver_id = ?)
        ORDER BY cm.timestamp ASC
    ");
    $stmt->execute([$user_id, $admin_id, $admin_id, $user_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['status' => 'success', 'messages' => $messages]);
} catch (Exception $e) {
    // Log error in real application
    echo json_encode(['status' => 'error', 'message' => 'Failed to fetch messages']);
}
?>
