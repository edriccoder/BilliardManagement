<?php
// fetch_users.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include 'conn.php';

// Check if admin is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$admin_id = $_SESSION['user_id'];

// Fetch distinct users who have chatted with the admin (either as sender or receiver)
$sql = "SELECT DISTINCT u.user_id, u.username, u.profile_pic
        FROM users u
        INNER JOIN chat_messages cm 
            ON (cm.sender_id = u.user_id AND cm.receiver_id = :admin_id) 
            OR (cm.receiver_id = u.user_id AND cm.sender_id = :admin_id)
        ORDER BY cm.timestamp DESC";

$stmt = $conn->prepare($sql);
$stmt->bindValue(':admin_id', $admin_id, PDO::PARAM_INT);
$stmt->execute();

$users = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $users[] = [
        'id' => $row['user_id'], // Return user_id as 'id' for compatibility with frontend
        'username' => htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8'),
        'profile_pic' => $row['profile_pic'] ? $row['profile_pic'] : 'default_avatar.png' // Default avatar if none
    ];
}

echo json_encode(['status' => 'success', 'users' => $users]);
?>
