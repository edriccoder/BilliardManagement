<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bookingId = $_POST['booking_id'];
    $userId = $_POST['user_id'];
    $startTime = $_POST['start_time'];
    $endTime = $_POST['end_time'];

    try {
        $sql = "UPDATE bookings SET user_id = ?, start_time = ?, end_time = ? WHERE booking_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$userId, $startTime, $endTime, $bookingId]);

        // Redirect back to the page where bookings are listed
        header("Location: booking_user.php");
        exit();
    } catch (PDOException $e) {
        // Handle database errors here
        echo '<script>alert("Database error: ' . $e->getMessage() . '");</script>';
    } catch (Exception $e) {
        // Handle other errors (if any)
        echo '<script>alert("Error: ' . $e->getMessage() . '");</script>';
    }
}
?>