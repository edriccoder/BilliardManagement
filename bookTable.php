<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tableId = $_POST['table_id'];
    $userId = $_POST['user_id'];
    $startTime = $_POST['start_time'];
    $endTime = $_POST['end_time'];
    $amount = $_POST['amount'];
    $paymentMethod = $_POST['payment_method'];

    // Fetch table_number based on table_id
    $stmtTable = $conn->prepare("SELECT table_number FROM tables WHERE table_id = ?");
    $stmtTable->execute([$tableId]);
    $table = $stmtTable->fetch(PDO::FETCH_ASSOC);
    $tableName = $table['table_number']; // Get the table_number

    $sql = "INSERT INTO bookings (table_id, table_name, user_id, start_time, end_time, status) VALUES (?, ?, ?, ?, ?, 'Pending')";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$tableId, $tableName, $userId, $startTime, $endTime]);

    $bookingId = $conn->lastInsertId();

    $sqlTransaction = "INSERT INTO transactions (booking_id, amount, payment_method, status, timestamp) VALUES (?, ?, ?, 'Pending', NOW())";
    $stmtTransaction = $conn->prepare($sqlTransaction);
    $stmtTransaction->execute([$bookingId, $amount, $paymentMethod]);

    header("Location: user_table.php");
    exit();
}
?>
