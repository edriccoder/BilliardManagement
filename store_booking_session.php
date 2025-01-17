<?php
// store_booking_session.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capture necessary fields from the AJAX request
    $tableId = $_POST['table_id'] ?? null;
    $tableName = $_POST['table_name'] ?? null;
    $userId = $_POST['user_id'] ?? null;
    $bookingType = $_POST['booking_type'] ?? null;
    $numPlayers = isset($_POST['num_players']) ? intval($_POST['num_players']) : 0;
    $numMatches = isset($_POST['num_matches']) ? intval($_POST['num_matches']) : null;
    $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
    $startTime = $_POST['start_time'] ?? null;
    $endTime = $_POST['end_time'] ?? null;

    // Basic validation
    if (!$tableId || !$userId || !$bookingType || $amount <= 0) {
        echo 'error';
        exit();
    }

    // Store booking details in session
    $_SESSION['pending_booking'] = [
        'table_id' => $tableId,
        'table_name' => $tableName,
        'user_id' => $userId,
        'booking_type' => $bookingType,
        'num_players' => $numPlayers,
        'num_matches' => $numMatches,
        'amount' => $amount,
        'start_time' => $startTime,
        'end_time' => $endTime
    ];

    echo 'success';
} else {
    echo 'invalid_request';
}
?>
