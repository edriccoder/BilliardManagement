<?php
include 'conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $booking_id = $_POST['booking_id'];
    $user_id = $_POST['user_id'];

    if (isset($_POST['confirm'])) {
        // Confirm booking
        $status = "Confirmed";
    } elseif (isset($_POST['cancel'])) {
        // Cancel booking
        $status = "Canceled";
    }

    $sql = "UPDATE bookings 
            SET status = :status
            WHERE booking_id = :booking_id AND user_id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':booking_id', $booking_id);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();

    header("Location: admin_booking.php");
    exit();
}
?>