<?php
// delete_booking.php

session_start();
include 'conn.php';

// Set response header to JSON
header('Content-Type: application/json');

// Ensure the request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);
    exit();
}

// Validate CSRF token
if (
    !isset($_POST['csrf_token']) ||
    !isset($_SESSION['csrf_token']) ||
    $_POST['csrf_token'] !== $_SESSION['csrf_token']
) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid CSRF token.'
    ]);
    exit();
}

// Validate booking_id
if (!isset($_POST['booking_id']) || !is_numeric($_POST['booking_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid booking ID.'
    ]);
    exit();
}

$bookingId = intval($_POST['booking_id']);

try {
    // Check if booking exists and is not already archived
    $stmtCheck = $conn->prepare("SELECT archive FROM bookings WHERE booking_id = ?");
    $stmtCheck->execute([$bookingId]);
    $booking = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if (!$booking) {
        echo json_encode([
            'success' => false,
            'message' => 'Booking not found.'
        ]);
        exit();
    }

    if ($booking['archive'] == 1) {
        echo json_encode([
            'success' => false,
            'message' => 'Booking is already archived.'
        ]);
        exit();
    }

    // Update the archive field to 1
    $stmtUpdate = $conn->prepare("UPDATE bookings SET archive = 1 WHERE booking_id = ?");
    $stmtUpdate->execute([$bookingId]);

    // Optionally, insert an admin notification
    // Assuming you have a table 'admin_notifications' with fields (notification_id, user_id, message, created_at, is_read)
    $message = "Booking ID {$bookingId} has been archived by admin.";
    $stmtNotif = $conn->prepare("INSERT INTO admin_notifications (user_id, message, created_at, is_read) VALUES (NULL, ?, NOW(), 0)");
    $stmtNotif->execute([$message]);

    echo json_encode([
        'success' => true,
        'message' => 'Booking successfully archived.'
    ]);
} catch (PDOException $e) {
    // Log the error
    error_log("Error archiving booking: " . $e->getMessage());

    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while archiving the booking.'
    ]);
}
?>
