<?php
// getMessagesBetweenUsers.php
session_start();
header('Content-Type: application/json');

include 'conn.php';

// Check if the admin is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Validate input
if (!isset($_GET['contact'])) {
    http_response_code(400);
    echo json_encode(['error' => 'No contact specified']);
    exit();
}

$contactUsername = trim($_GET['contact']);

if (empty($contactUsername)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid contact']);
    exit();
}

try {
    // Fetch contact's user_id
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = :username");
    $stmt->bindParam(':username', $contactUsername, PDO::PARAM_STR);
    $stmt->execute();
    $contact = $stmt->fetch();

    if (!$contact) {
        echo json_encode([]); // No messages if user not found
        exit();
    }

    $contact_id = $contact['user_id'];
    $admin_id = $_SESSION['user_id'];

    // Fetch messages between admin and contact
    $stmt = $conn->prepare("
        SELECT 
            cm.sender_id,
            u.username AS sender_username,
            cm.receiver_id,
            u2.username AS receiver_username,
            cm.message,
            cm.timestamp
        FROM 
            chat_messages cm
        JOIN 
            users u ON cm.sender_id = u.user_id
        JOIN 
            users u2 ON cm.receiver_id = u2.user_id
        WHERE 
            (cm.sender_id = :admin_id AND cm.receiver_id = :contact_id)
            OR
            (cm.sender_id = :contact_id AND cm.receiver_id = :admin_id)
        ORDER BY 
            cm.timestamp ASC
    ");
    $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
    $stmt->bindParam(':contact_id', $contact_id, PDO::PARAM_INT);
    $stmt->execute();
    $messages = $stmt->fetchAll();

    // Format messages for frontend
    $formattedMessages = [];
    foreach ($messages as $msg) {
        $formattedMessages[] = [
            'sender_id' => $msg['sender_id'],
            'sender_username' => htmlspecialchars($msg['sender_username'], ENT_QUOTES, 'UTF-8'),
            'receiver_id' => $msg['receiver_id'],
            'receiver_username' => htmlspecialchars($msg['receiver_username'], ENT_QUOTES, 'UTF-8'),
            'message' => htmlspecialchars($msg['message'], ENT_QUOTES, 'UTF-8'),
            'timestamp' => $msg['timestamp']
        ];
    }

    echo json_encode($formattedMessages);
} catch (PDOException $e) {
    error_log('Error fetching messages: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch messages']);
}
?>
