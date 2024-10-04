<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['booking_id'])) {
    $bookingId = $_GET['booking_id'];

    // Update the archive field to 1 instead of deleting the record
    $sql = "UPDATE bookings SET archive = 1 WHERE booking_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$bookingId]);

    // Redirect back to the page where bookings are listed
    header("Location: admin_booking.php"); // Assuming we want to see only active bookings by default
    exit();
}
?>
