<?php
require 'vendor/autoload.php';
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

include('conn.php');

if (isset($_GET['booking_id'])) {
    $bookingId = intval($_GET['booking_id']);

    // Update booking status to 'Payment Failed'
    $stmt = $conn->prepare("UPDATE bookings SET status = 'Payment Failed' WHERE booking_id = ?");
    $stmt->execute([$bookingId]);

    // Optionally, update transaction status as well
    $stmtTrans = $conn->prepare("UPDATE transactions SET status = 'Failed' WHERE booking_id = ?");
    $stmtTrans->execute([$bookingId]);

    echo "
    <script>
        alert('Payment failed. Please try booking again.');
        window.location.href = 'user_table.php';
    </script>
    ";
    exit();
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
