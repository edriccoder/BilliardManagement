<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tableId = $_POST['table_id'];
    $userId = $_POST['user_id'];
    $bookingType = $_POST['booking_type'];
    $amount = $_POST['amount'];
    $paymentMethod = $_POST['payment_method'];

    // Fetch table_number based on table_id
    $stmtTable = $conn->prepare("SELECT table_number FROM tables WHERE table_id = ?");
    $stmtTable->execute([$tableId]);
    $table = $stmtTable->fetch(PDO::FETCH_ASSOC);
    $tableName = $table['table_number']; // Get the table_number

    if ($bookingType === 'hour') {
        $startTime = $_POST['start_time'];
        $endTime = $_POST['end_time'];
        $sql = "INSERT INTO bookings (table_id, table_name, user_id, start_time, end_time, num_matches, status) VALUES (?, ?, ?, ?, ?, NULL, 'Pending')";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$tableId, $tableName, $userId, $startTime, $endTime]);
    } else if ($bookingType === 'match') {
        $numMatches = $_POST['num_matches'];
        $sql = "INSERT INTO bookings (table_id, table_name, user_id, start_time, end_time, num_matches, status) VALUES (?, ?, ?, NULL, NULL, ?, 'Pending')";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$tableId, $tableName, $userId, $numMatches]);
    }

    $bookingId = $conn->lastInsertId();

    // Handle file upload if payment method is GCash
    $proofOfPayment = '';
    if ($paymentMethod === 'gcash' && isset($_FILES['proof_of_payment']) && $_FILES['proof_of_payment']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'payments/';
        $uploadFile = $uploadDir . basename($_FILES['proof_of_payment']['name']);
        if (move_uploaded_file($_FILES['proof_of_payment']['tmp_name'], $uploadFile)) {
            $proofOfPayment = $uploadFile;
        } else {
            // Handle file upload error
            die('Failed to upload proof of payment.');
        }
    }

    $sqlTransaction = "INSERT INTO transactions (booking_id, amount, payment_method, status, timestamp, proof_of_payment) VALUES (?, ?, ?, 'Pending', NOW(), ?)";
    $stmtTransaction = $conn->prepare($sqlTransaction);
    $stmtTransaction->execute([$bookingId, $amount, $paymentMethod, $proofOfPayment]);

    header("Location: user_table.php");
    exit();
}
?>
