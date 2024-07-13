<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tableId = $_POST['table_id'];
    $userId = $_POST['user_id'];
    $startTime = $_POST['start_time'];
    $endTime = $_POST['end_time'];
    $amount = $_POST['amount'];
    $paymentMethod = $_POST['payment_method'];

    $sql = "INSERT INTO bookings (table_id, user_id, start_time, end_time, status) VALUES (?, ?, ?, ?, 'Pending')";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$tableId, $userId, $startTime, $endTime]);

    $bookingId = $conn->lastInsertId();

    $sqlTransaction = "INSERT INTO transactions (booking_id, amount, payment_method, status, timestamp) VALUES (?, ?, ?, ?, NOW())";
    $stmtTransaction = $conn->prepare($sqlTransaction);
    $stmtTransaction->execute([$bookingId, $amount, $paymentMethod, 'Pending']);

    header("Location: user_table.php");
    exit();
}
?>
