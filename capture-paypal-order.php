<?php
// capture-paypal-order.php

header('Content-Type: application/json');

require 'vendor/autoload.php'; // Autoload Composer dependencies
use GuzzleHttp\Client;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

include('conn.php');

// Retrieve POST data
$input = json_decode(file_get_contents('php://input'), true);
$orderID = isset($input['orderID']) ? $input['orderID'] : '';

if (empty($orderID)) {
    http_response_code(400);
    echo json_encode(['error' => 'Order ID is required.']);
    exit();
}

// Create PayPal client
$client = new Client();

$paypalClientId = $_ENV['PAYPAL_CLIENT_ID'];
$paypalSecret = $_ENV['PAYPAL_CLIENT_SECRET'];
$paypalMode = $_ENV['PAYPAL_MODE'];

// Set API URL based on mode
$apiUrl = $paypalMode === 'sandbox' ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';

try {
    // Get OAuth token
    $response = $client->request('POST', "$apiUrl/v1/oauth2/token", [
        'auth' => [$paypalClientId, $paypalSecret],
        'form_params' => [
            'grant_type' => 'client_credentials'
        ],
    ]);

    $body = json_decode($response->getBody(), true);
    $accessToken = $body['access_token'];

    // Capture Order
    $response = $client->request('POST', "$apiUrl/v2/checkout/orders/$orderID/capture", [
        'headers' => [
            'Content-Type' => 'application/json',
            'Authorization' => "Bearer $accessToken",
        ],
    ]);

    $order = json_decode($response->getBody(), true);

    // Update booking status to 'Confirmed'
    if (isset($order['purchase_units'][0]['custom_id'])) {
        $bookingId = $order['purchase_units'][0]['custom_id'];
        $paypalCaptureId = $order['purchase_units'][0]['payments']['captures'][0]['id'] ?? null;

        if ($paypalCaptureId) {
            $stmt = $conn->prepare("UPDATE bookings SET status = 'Confirmed', paypal_capture_id = ? WHERE booking_id = ?");
            $stmt->execute([$paypalCaptureId, $bookingId]);
        }
    }

    echo json_encode($order);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Could not capture order.', 'details' => $e->getMessage()]);
}
?>
