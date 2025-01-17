<?php
// create-paypal-order.php

header('Content-Type: application/json');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php'; // Autoload Composer dependencies
use GuzzleHttp\Client;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

include('conn.php');

// Retrieve POST data
$input = json_decode(file_get_contents('php://input'), true);
$amount = isset($input['amount']) ? floatval($input['amount']) : 0;
$bookingId = isset($input['booking_id']) ? htmlspecialchars($input['booking_id']) : '';

if ($amount <= 0 || empty($bookingId)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid amount or booking ID.']);
    exit();
}

// Create PayPal order
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

    // Create Order with custom_id
    $response = $client->request('POST', "$apiUrl/v2/checkout/orders", [
        'headers' => [
            'Content-Type' => 'application/json',
            'Authorization' => "Bearer $accessToken",
        ],
        'json' => [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'amount' => [
                        'currency_code' => 'PHP',
                        'value' => number_format($amount, 2, '.', ''),
                    ],
                    'custom_id' => $bookingId // Associate booking ID
                ],
            ],
            'application_context' => [
                'return_url' => 'https://tjamessportybar.com/BilliardManagement/paypal_success.php',
                'cancel_url' => 'https://tjamessportybar.com/BilliardManagement/paypal_cancel.php',
            ],
        ],
    ]);

    $order = json_decode($response->getBody(), true);

    // Store PayPal order ID in the bookings table
    $stmt = $conn->prepare("UPDATE bookings SET paypal_order_id = ? WHERE booking_id = ?");
    $stmt->execute([$order['id'], $bookingId]);

    echo json_encode(['id' => $order['id']]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Could not create order.', 'details' => $e->getMessage()]);
}
?>
