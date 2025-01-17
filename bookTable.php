<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php'; // Autoload Composer dependencies

use GuzzleHttp\Client;
use Dotenv\Dotenv;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Amount;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

include('conn.php');

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

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capture necessary fields from the form
    $tableId = $_POST['table_id'];
    $userId = $_POST['user_id'];
    $bookingType = $_POST['booking_type'];
    $numPlayers = isset($_POST['num_players']) ? intval($_POST['num_players']) : 0;
    $numMatches = isset($_POST['num_matches']) ? intval($_POST['num_matches']) : null;
    $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
    $paymentMethod = $_POST['payment_method'];

    // Validate amount
    if ($amount <= 0) {
        alertAndRedirect('Invalid booking amount.');
    }

    // Fetch table_number based on table_id
    $stmtTable = $conn->prepare("SELECT table_number FROM tables WHERE table_id = ?");
    $stmtTable->execute([$tableId]);
    $table = $stmtTable->fetch(PDO::FETCH_ASSOC);

    if (!$table) {
        alertAndRedirect('Invalid table selected.');
    }

    $tableName = $table['table_number'];

    // Booking logic based on type
    if ($bookingType === 'hour') {
        $startTime = isset($_POST['start_time']) ? $_POST['start_time'] : null;
        $endTime = isset($_POST['end_time']) ? $_POST['end_time'] : null;

        // Validate start and end times
        if (empty($startTime) || empty($endTime)) {
            alertAndRedirect('Please enter both start and end times.');
        }

        // Ensure the start time is before the end time
        if (strtotime($startTime) >= strtotime($endTime)) {
            alertAndRedirect('End time must be after the start time.');
        }

        // Check for overlapping bookings
        $stmtCheck = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE table_id = ? AND (
            (start_time < ? AND end_time > ?) OR
            (start_time < ? AND end_time > ?) OR
            (start_time >= ? AND start_time < ?)
        )");
        $stmtCheck->execute([$tableId, $endTime, $startTime, $startTime, $endTime, $startTime, $endTime]);
        $isBooked = $stmtCheck->fetchColumn();

        if ($isBooked) {
            alertAndRedirect('The selected table is already booked during this time. Please choose a different time.');
        } else {
            // Insert hour-based booking into bookings table
            $sql = "INSERT INTO bookings (table_id, table_name, user_id, start_time, end_time, num_players, num_matches, status) 
                    VALUES (?, ?, ?, ?, ?, ?, NULL, 'Pending')";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$tableId, $tableName, $userId, $startTime, $endTime, $numPlayers]);

            // Get last inserted booking ID
            $bookingId = $conn->lastInsertId();

            // Handle transaction based on payment method
            handleTransaction($bookingId, $amount, $paymentMethod,  $userId);
        }
    } elseif ($bookingType === 'match') {
        // For match-based booking, ensure num_matches and num_players are valid
        if ($numMatches > 0 && $numPlayers > 0) {
            // Insert match-based booking into bookings table
            $sql = "INSERT INTO bookings (table_id, table_name, user_id, start_time, end_time, num_players, num_matches, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$tableId, $tableName, $userId, null, null, $numPlayers, $numMatches]);


            // Get last inserted booking ID
            $bookingId = $conn->lastInsertId();

            // Handle transaction based on payment method
            handleTransaction($bookingId, $amount, $paymentMethod);
        } else {
            alertAndRedirect('Please enter a valid number of matches and players.');
        }
    } else {
        alertAndRedirect('Invalid booking type selected.');
    }
}

function handleTransaction($bookingId, $amount, $paymentMethod, $userId) {
    global $conn, $apiContext;

    // Fetch user name from users table using user_id
    $stmtUser = $conn->prepare("SELECT name FROM users WHERE user_id = ?");
    $stmtUser->execute([$userId]);
    $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        alertAndRedirect('Invalid user.');
    }

    $userName = $user['name'];

    // Fetch booking details for notification message
    $stmtBooking = $conn->prepare("SELECT table_name, start_time, end_time FROM bookings WHERE booking_id = ?");
    $stmtBooking->execute([$bookingId]);
    $booking = $stmtBooking->fetch(PDO::FETCH_ASSOC);

    $tableName = $booking['table_name'];
    $startTime = isset($booking['start_time']) ? date("Y-m-d h:i A", strtotime($booking['start_time'])) : 'N/A';
    $endTime = isset($booking['end_time']) ? date("Y-m-d h:i A", strtotime($booking['end_time'])) : 'N/A';

    // Format booking date and time details for message
    $bookingDateTime = ($startTime !== 'N/A' && $endTime !== 'N/A') 
        ? " on $tableName from $startTime to $endTime" 
        : " on $tableName";

    if ($paymentMethod === 'gcash') {
        // GCash payment handling...
        $paymongoLink = generatePaymongoLink($bookingId, $amount);

        if ($paymongoLink) {
            $stmtUpdate = $conn->prepare("UPDATE bookings SET payment_link = ? WHERE booking_id = ?");
            $stmtUpdate->execute([$paymongoLink, $bookingId]);

            $sqlTransaction = "INSERT INTO transactions (booking_id, amount, payment_method, status, timestamp) 
                               VALUES (?, ?, ?, 'Pending', NOW())";
            $stmtTransaction = $conn->prepare($sqlTransaction);
            $execution = $stmtTransaction->execute([$bookingId, $amount, $paymentMethod]);

            // Add a notification for this transaction
            insertNotification($userId, "Booking #$bookingId payment by $userName via GCash initiated$bookingDateTime.");

            header("Location: " . $paymongoLink);
            exit();
        } else {
            alertAndRedirect('Failed to generate payment link.');
        }
    } elseif ($paymentMethod === 'paypal') {
        $paypalPaymentLink = generatePayPalPaymentLink($bookingId, $amount, $apiContext);

        if ($paypalPaymentLink) {
            $stmtUpdate = $conn->prepare("UPDATE bookings SET paypal_payment_id = ? WHERE booking_id = ?");
            $stmtUpdate->execute([$paypalPaymentLink['payment_id'], $bookingId]);

            $sqlTransaction = "INSERT INTO transactions (booking_id, amount, payment_method, status, timestamp, paypal_payment_id) 
                               VALUES (?, ?, ?, 'Pending', NOW(), ?)";
            $stmtTransaction = $conn->prepare($sqlTransaction);
            $execution = $stmtTransaction->execute([$bookingId, $amount, $paymentMethod, $paypalPaymentLink['payment_id']]);

            // Add a notification for this transaction
            insertNotification($userId, "Booking #$bookingId payment by $userName via PayPal initiated$bookingDateTime.");

            header("Location: " . $paypalPaymentLink['approval_url']);
            exit();
        } else {
            alertAndRedirect('Failed to generate PayPal payment link.');
        }
    } else {
        $sqlTransaction = "INSERT INTO transactions (booking_id, amount, payment_method, status, timestamp) 
                            VALUES (?, ?, ?, 'Pending', NOW())";
        $stmtTransaction = $conn->prepare($sqlTransaction);
        $execution = $stmtTransaction->execute([$bookingId, $amount, $paymentMethod]);

        if ($execution) {
            // Add a notification for cash payment
            insertNotification($userId, "Booking #$bookingId payment by $userName via Cash confirmed$bookingDateTime.");
            alertAndRedirect('Booking Successful');
        } else {
            alertAndRedirect('Failed to process your booking.');
        }
    }
}

// Function to insert a notification in the admin_notifications table
function insertNotification($userId, $message) {
    global $conn;

    $sqlNotification = "INSERT INTO admin_notifications (user_id, message, created_at, is_read) 
                        VALUES (?, ?, NOW(), 0)";
    $stmtNotification = $conn->prepare($sqlNotification);
    $stmtNotification->execute([$userId, $message]);
}


function generatePaymongoLink($bookingId, $amount) {
    $client = new Client();

    // Convert amount to cents (PayMongo expects smallest currency unit)
    $amountInCents = intval($amount * 100);

    // Retrieve PayMongo secret key from environment variable
    // It's better to use environment variables instead of hardcoding
    $secretKey = 'sk_test_bF3PwBWyXDZS56TXmTAnvQDu'; // Ensure this is correctly set in your .env file

    try {
        $response = $client->request('POST', 'https://api.paymongo.com/v1/links', [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($secretKey . ':'),
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'data' => [
                    'attributes' => [
                        'amount' => $amountInCents,
                        'currency' => 'PHP', // Change to your currency if needed
                        'description' => 'Billiard Payment for Booking #' . $bookingId,
                        'redirect' => [
                            'success' => 'https://tjamessportybar.com/BilliardManagement/payment_success.php?booking_id=' . $bookingId,
                            'failed' => 'https://tjamessportybar.com/BilliardManagement/payment_failed.php?booking_id=' . $bookingId,
                        ],
                        'reference_number' => 'booking_' . $bookingId,
                    ],
                ],
            ],
        ]);

        $body = $response->getBody();
        $data = json_decode($body, true);

        // Debug: Log the entire response
        error_log('PayMongo Response: ' . $body);

        // Correctly access the checkout_url directly from attributes
        if (isset($data['data']['attributes']['checkout_url'])) {
            return $data['data']['attributes']['checkout_url'];
        } else {
            error_log('Unexpected PayMongo response structure.');
            return null;
        }
    } catch (Exception $e) {
        // Log the exception message
        error_log('PayMongo Link Generation Error: ' . $e->getMessage());

        // Optionally, display the error message during development
        // Remove or comment out in production
        echo "
        <script>
            alert('Failed to generate payment link. Error: " . addslashes($e->getMessage()) . "');
            window.location.href = 'user_table.php';
        </script>
        ";
        exit();

        return null;
    }
}

// Function to generate PayPal payment link
function generatePayPalPaymentLink($bookingId, $amount, $apiContext) {
    // Create new payer instance
    $payer = new Payer();
    $payer->setPaymentMethod("paypal");

    // Set redirect URLs
    $redirectUrls = new RedirectUrls();
    $redirectUrls->setReturnUrl("https://tjamessportybar.com/BilliardManagement/paypal_success.php?booking_id={$bookingId}")
                 ->setCancelUrl("https://tjamessportybar.com/BilliardManagement/paypal_failed.php?booking_id={$bookingId}");

    // Set payment amount
    $amountObj = new Amount();
    $amountObj->setCurrency("PHP")
              ->setTotal($amount);

    // Set transaction object
    $transaction = new Transaction();
    $transaction->setAmount($amountObj)
                ->setDescription("Billiard Payment for Booking #{$bookingId}");

    // Create the full payment object
    $payment = new Payment();
    $payment->setIntent("sale")
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrls)
            ->setTransactions(array($transaction));

    try {
        // Create payment
        $payment->create($apiContext);

        // Get PayPal redirect URL and payment ID
        $approvalUrl = $payment->getApprovalLink();
        $paymentId = $payment->getId();

        return [
            'approval_url' => $approvalUrl,
            'payment_id' => $paymentId
        ];
    } catch (Exception $e) {
        error_log("PayPal Payment Creation Error: " . $e->getMessage());
        return null;
    }
}

// Updated alertAndRedirect function with optional logging
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
