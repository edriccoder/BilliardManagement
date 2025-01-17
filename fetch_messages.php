<?php
// fetch_messages.php

// Error Reporting and Logging
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log'); // Ensure the path is correct and writable

session_start();
include 'conn.php';

// Check if admin is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$admin_id = intval($_SESSION['user_id']);
$chat_with = isset($_POST['chat_with']) ? intval($_POST['chat_with']) : 0;

// Debugging: Log received chat_with
error_log("fetch_messages.php called with chat_with = " . $chat_with);
error_log("Admin ID: $admin_id, Chat with ID: $chat_with");

if ($chat_with <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid user ID provided.']);
    exit();
}

try {
    // Verify that chat_with user exists in the database
    $user_check_sql = "SELECT user_id FROM users WHERE user_id = :chat_with";
    $user_check_stmt = $conn->prepare($user_check_sql);
    $user_check_stmt->bindParam(':chat_with', $chat_with, PDO::PARAM_INT);
    $user_check_stmt->execute();

    if ($user_check_stmt->rowCount() === 0) {
        echo json_encode(['status' => 'error', 'message' => 'User does not exist in the database.']);
        exit();
    }

    // Fetch messages between admin and selected user
    $sql = "SELECT cm.*, 
                   sender.username AS sender_username,
                   receiver.username AS receiver_username
            FROM chat_messages cm
            INNER JOIN users sender ON sender.user_id = cm.sender_id
            INNER JOIN users receiver ON receiver.user_id = cm.receiver_id
            WHERE (cm.sender_id = :admin_id AND cm.receiver_id = :chat_with) 
               OR (cm.sender_id = :chat_with AND cm.receiver_id = :admin_id)
            ORDER BY cm.timestamp ASC";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
    $stmt->bindParam(':chat_with', $chat_with, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll();

    $messages = [];
    foreach ($result as $row) {
        $messages[] = [
            'sender_id' => $row['sender_id'],
            'username' => htmlspecialchars($row['sender_username'], ENT_QUOTES, 'UTF-8'),
            'message' => htmlspecialchars($row['message'], ENT_QUOTES, 'UTF-8'),
            'timestamp' => $row['timestamp']
        ];
    }

    echo json_encode(['status' => 'success', 'messages' => $messages]);
} catch (PDOException $e) {
    error_log('Database Error: ' . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'An error occurred while fetching messages.']);
    exit();
}
?>
