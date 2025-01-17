<?php
// update_booking_status.php

session_start();
include 'conn.php';

// Enable error logging
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log'); // Correct path with slash
error_reporting(E_ALL);

// Set response header to JSON
header('Content-Type: application/json');

// Function to validate CSRF token
function validate_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Ensure the user is authenticated and has admin privileges
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    error_log("Unauthorized access attempt by user ID: " . ($_SESSION['user_id'] ?? 'Unknown'));
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

// Check if required POST parameters are set
if (!isset($_POST['booking_id']) || !isset($_POST['action']) || !isset($_POST['csrf_token'])) {
    error_log("Invalid request parameters.");
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit();
}

// Validate CSRF token
if (!validate_csrf_token($_POST['csrf_token'])) {
    error_log("Invalid CSRF token for user ID: " . $_SESSION['user_id']);
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
    exit();
}

$booking_id = intval($_POST['booking_id']);
$action = $_POST['action'];

// Validate action
if (!in_array($action, ['confirm', 'cancel', 'checkout'])) {
    error_log("Invalid action '$action' attempted by user ID: " . $_SESSION['user_id']);
    echo json_encode(['success' => false, 'message' => 'Invalid action.']);
    exit();
}

// Map action to status
$statusMap = [
    'confirm' => 'Confirmed',
    'cancel' => 'Cancelled',
    'checkout' => 'Checked Out'
];

$status = $statusMap[$action];

// Update booking status in the database
try {
    // Begin Transaction
    $conn->beginTransaction();

    // Check if booking exists before updating
    $sqlCheck = "SELECT * FROM bookings WHERE booking_id = :booking_id FOR UPDATE";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);
    $stmtCheck->execute();
    $booking = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if (!$booking) {
        throw new Exception("No booking found with the provided ID: $booking_id.");
    }

    // Check if the booking already has the desired status
    if (strtolower($booking['status']) === strtolower($status)) {
        throw new Exception("Booking with ID $booking_id already has status '$status'. No update performed.");
    }

    // Update booking status
    $sqlUpdate = "UPDATE bookings SET status = :status WHERE booking_id = :booking_id";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bindParam(':status', $status, PDO::PARAM_STR);
    $stmtUpdate->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);
    $stmtUpdate->execute();

    // Fetch the user_id associated with the booking
    $user_id = $booking['user_id'];
    $message = "Your booking (ID: $booking_id) status has been updated to $status.";
    $created_at = date('Y-m-d H:i:s');

    // Insert notification for the user
    $sqlNotification = "INSERT INTO notifications (user_id, message, created_at) VALUES (:user_id, :message, :created_at)";
    $stmtNotif = $conn->prepare($sqlNotification);
    $stmtNotif->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmtNotif->bindParam(':message', $message, PDO::PARAM_STR);
    $stmtNotif->bindParam(':created_at', $created_at, PDO::PARAM_STR);
    $stmtNotif->execute();

    // Commit Transaction
    $conn->commit();

    echo json_encode(['success' => true, 'message' => "Booking status updated to $status successfully!"]);
} catch (Exception $e) {
    // Rollback Transaction in case of error
    $conn->rollBack();
    error_log("Error updating booking status: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
