<?php
// get_users.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require 'conn.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Admin not authenticated.']);
        exit;
    }

    try {
        $stmt = $conn->prepare("SELECT user_id, username FROM users ORDER BY username ASC");
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['status' => 'success', 'users' => $users]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to retrieve users: ' . $e->getMessage()]);
    }
}
?>
