<?php
require 'vendor/autoload.php';
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

include('conn.php');

if (isset($_GET['booking_id'])) {
    $bookingId = intval($_GET['booking_id']);

    // Begin a transaction
    $conn->beginTransaction();

    try {
        // Update booking status to 'Confirmed'
        $stmt = $conn->prepare("UPDATE bookings SET status = 'Confirmed' WHERE booking_id = ?");
        $stmt->execute([$bookingId]);

        // Update transaction status to 'Completed'
        $stmtTrans = $conn->prepare("UPDATE transactions SET status = 'Completed' WHERE booking_id = ?");
        $stmtTrans->execute([$bookingId]);

        // Create an admin notification
        $message = "Booking ID $bookingId has been confirmed and marked as paid.";
        $stmtNotif = $conn->prepare("INSERT INTO admin_notifications (admin_id, message, created_at) VALUES (NULL, ?, NOW())");
        $stmtNotif->execute([$message]);

        // Commit the transaction
        $conn->commit();

        echo "
        <script>
            alert('Payment successful. Your booking is confirmed.');
            window.location.href = 'user_table.php';
        </script>
        ";
        exit();
    } catch (Exception $e) {
        // Rollback the transaction on failure
        $conn->rollBack();
        echo "
        <script>
            alert('An error occurred: " . $e->getMessage() . "');
            window.location.href = 'user_table.php';
        </script>
        ";
        exit();
    }
} else {
    echo "
    <script>
        alert('Invalid booking ID.');
        window.location.href = 'user_table.php';
    </script>
    ";
    exit();
}
?>
