<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'mark_as_read') {
    $notificationId = $_POST['notification_id'];

    // Update the notification as read
    try {
        $stmt = $conn->prepare("UPDATE admin_notifications SET is_read = 1 WHERE notification_id = :notification_id");
        $stmt->bindParam(':notification_id', $notificationId, PDO::PARAM_INT);
        $stmt->execute();
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}
?>
