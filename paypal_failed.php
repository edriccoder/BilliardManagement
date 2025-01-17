<?php
// Handle payment cancellation
$bookingId = $_GET['booking_id'];

// Optionally, update the booking or transaction status
include('conn.php');
$stmtUpdateTransaction = $conn->prepare("UPDATE transactions SET status = 'Failed' WHERE booking_id = ?");
$stmtUpdateTransaction->execute([$bookingId]);

echo "
<script>
    alert('Payment was cancelled or failed.');
    window.location.href = 'user_table.php';
</script>
";
exit();
?>
