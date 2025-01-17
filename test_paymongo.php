<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php';

use GuzzleHttp\Client;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$client = new Client();

// It's recommended to use environment variables for sensitive data.
// For testing purposes, you're using a hardcoded secret key.
// Ensure this key is correct and complete.
$secretKey = 'sk_test_bF3PwBWyXDZS56TXmTAnvQDu'; // Replace with your full secret key

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
                    'amount' => 100000, // Example amount in cents (PHP 1,000.00)
                    'currency' => 'PHP',
                    'description' => 'Test Payment',
                    'redirect' => [
                        'success' => 'https://tjamessportybar.com/BilliardManagement/payment_success.php',
                        'failed' => 'https://tjamessportybar.com/BilliardManagement/payment_failed.php',
                    ],
                    'reference_number' => 'test_payment_123',
                ],
            ],
        ],
    ]);

    $body = $response->getBody();
    $data = json_decode($body, true);

    // Display the entire response for debugging
    echo "<pre>";
    print_r($data);
    echo "</pre>";

    // Correctly access the checkout_url directly from attributes
    if (isset($data['data']['attributes']['checkout_url'])) {
        echo "Payment Link: " . $data['data']['attributes']['checkout_url'];
    } else {
        echo "Payment Link: Not Available";
        if (isset($data['errors'])) {
            echo "<br>Error Details:<br>";
            foreach ($data['errors'] as $error) {
                echo "Message: " . htmlspecialchars($error['message']) . "<br>";
                echo "Code: " . htmlspecialchars($error['code']) . "<br>";
                echo "Detail: " . htmlspecialchars($error['detail']) . "<br><br>";
            }
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
