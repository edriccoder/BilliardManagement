<?php
    include 'conn.php';

    if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['booking_id'])) {
        $bookingId = $_GET['booking_id'];

        $sql = "DELETE FROM bookings WHERE booking_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$bookingId]);

        // Redirect back to the page where bookings are listed
        header("Location: booking_user.php");
        exit();
    }
?>
