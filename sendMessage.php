<?php
// send_message.php

// Error Reporting and Logging
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log'); // Ensure this path is correct and writable

session_start();
include 'conn.php';

// Check if admin is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$admin_id = intval($_SESSION['user_id']);
$receiver_id = isset($_POST['receiver_id']) ? intval($_POST['receiver_id']) : 0;
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

// Debugging: Log received data
error_log("send_message.php called with receiver_id = " . $receiver_id);
error_log("Admin ID: $admin_id, Message: $message");

if ($receiver_id <= 0 || empty($message)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
    exit();
}

try {
    // Optional: Verify that receiver exists
    $user_check_sql = "SELECT user_id FROM users WHERE user_id = :receiver_id";
    $user_check_stmt = $conn->prepare($user_check_sql);
    $user_check_stmt->bindParam(':receiver_id', $receiver_id, PDO::PARAM_INT);
    $user_check_stmt->execute();

    if ($user_check_stmt->rowCount() === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Receiver does not exist in the database.']);
        exit();
    }

    // Insert the new message into the database
    $sql = "INSERT INTO chat_messages (sender_id, receiver_id, message, timestamp) VALUES (:sender_id, :receiver_id, :message, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':sender_id', $admin_id, PDO::PARAM_INT);
    $stmt->bindParam(':receiver_id', $receiver_id, PDO::PARAM_INT);
    $stmt->bindParam(':message', $message, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Message sent']);
    } else {
        // Log detailed error
        $errorInfo = $stmt->errorInfo();
        error_log('Database Error: ' . implode(' | ', $errorInfo));
        echo json_encode(['status' => 'error', 'message' => 'Failed to send message']);
    }
} catch (PDOException $e) {
    // Log exception details
    error_log('PDO Exception: ' . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'An error occurred while sending the message.']);
    exit();
}
?>
