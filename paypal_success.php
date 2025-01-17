<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'vendor/autoload.php';
use GuzzleHttp\Client;
use Dotenv\Dotenv;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Initialize PayPal API context
$apiContext = new ApiContext(
    new OAuthTokenCredential(
        $_ENV['PAYPAL_CLIENT_ID'],     // ClientID
        $_ENV['PAYPAL_CLIENT_SECRET']  // ClientSecret
    )
);

$apiContext->setConfig([
    'mode' => $_ENV['PAYPAL_MODE'], // sandbox or live
]);

// Get payment ID and Payer ID from query parameters
$paymentId = $_GET['paymentId'];
$payerId = $_GET['PayerID'];
$bookingId = $_GET['booking_id'];

if (!$paymentId || !$payerId) {
    alertAndRedirect('Payment failed or was cancelled.', 'Missing payment information.');
}

// Retrieve the payment object by paymentId
$payment = Payment::get($paymentId, $apiContext);

// Execute the payment
$execution = new PaymentExecution();
$execution->setPayerId($payerId);

try {
    // Execute payment
    $result = $payment->execute($execution, $apiContext);

    // Check if payment is approved
    if ($result->getState() == 'approved') {
        // Update booking and transaction status to 'Completed'
        include('conn.php');

        // Update bookings table
        $stmtUpdateBooking = $conn->prepare("UPDATE bookings SET status = 'Confirmed' WHERE booking_id = ?");
        $stmtUpdateBooking->execute([$bookingId]);

        // Update transactions table
        $stmtUpdateTransaction = $conn->prepare("UPDATE transactions SET status = 'Completed' WHERE booking_id = ?");
        $stmtUpdateTransaction->execute([$bookingId]);

        // Redirect to a success page or display a success message
        echo "
        <script>
            alert('Payment successful! Your booking has been confirmed.');
            window.location.href = 'user_table.php';
        </script>
        ";
        exit();
    } else {
        alertAndRedirect('Payment not approved.', 'Payment state: ' . $result->getState());
    }
} catch (Exception $e) {
    error_log("PayPal Payment Execution Error: " . $e->getMessage());
    alertAndRedirect('Payment failed. Please try again.');
}

// Function to alert and redirect
function alertAndRedirect($message, $logMessage = null) {
    if ($logMessage) {
        error_log($logMessage);
    }
    echo "
    <script>
        alert('$message');
        window.location.href = 'user_table.php';
    </script>
    ";
    exit();
}
?>
