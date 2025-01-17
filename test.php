<?php
require 'vendor/autoload.php';
use GuzzleHttp\Client;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Function to generate PayMongo payment link
function generatePaymongoLink($description, $amount, $redirectSuccess, $redirectFailed) {
    $client = new Client();
    $amountInCents = intval($amount * 100);
    $secretKey = $_ENV['PAYMONGO_SECRET_KEY'] ?? null; 

    if (!$secretKey) {
        die('PayMongo secret key is missing.');
    }

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
                        'currency' => 'PHP',
                        'description' => $description,
                        'redirect' => [
                            'success' => $redirectSuccess,
                            'failed' => $redirectFailed,
                        ],
                        'reference_number' => 'test_' . time(),
                    ],
                ],
            ],
        ]);

        $data = json_decode($response->getBody(), true);
        print_r($data);
        return $data['data']['attributes']['checkout_url'] ?? null;
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
        if ($e->hasResponse()) {
            $errorBody = $e->getResponse()->getBody()->getContents();
            echo 'Response Body: ' . $errorBody;
        }
        return null;
    }
}

// Example usage
$description = 'Test Payment Link';
$amount = 500.00; // PHP 500.00
$redirectSuccess = 'https://yourdomain.com/success.php';
$redirectFailed = 'https://yourdomain.com/failed.php';

$link = generatePaymongoLink($description, $amount, $redirectSuccess, $redirectFailed);
if ($link) {
    echo "Payment Link: $link";
} else {
    echo "Failed to generate payment link.";
}
?>
