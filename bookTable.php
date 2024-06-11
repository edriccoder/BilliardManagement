<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tableId = $_POST['table_id'];
    $userId = $_POST['user_id'];
    $startTime = $_POST['start_time'];
    $endTime = $_POST['end_time'];

    $sql = "INSERT INTO bookings (table_id, user_id, start_time, end_time, status) VALUES (?, ?, ?, ?, 'Pending')";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$tableId, $userId, $startTime, $endTime]);

    header("Location: user_dashboard.php");
    exit();
}
?>
