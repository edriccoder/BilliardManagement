<?php
// create-paypal-order.php
header('Content-Type: application/json');
require 'vendor/autoload.php';
use GuzzleHttp\Client;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
include('conn.php');

$input = json_decode(file_get_contents('php://input'), true);
$tournamentId = isset($input['tournament_id']) ? intval($input['tournament_id']) : 0;

if ($tournamentId <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid tournament ID.']);
    exit();
}

// Fetch tournament fee from the database
$stmt = $conn->prepare("SELECT fee FROM tournaments WHERE tournament_id = :tournament_id");
$stmt->bindParam(':tournament_id', $tournamentId, PDO::PARAM_INT);
$stmt->execute();
$tournament = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tournament) {
    http_response_code(400);
    echo json_encode(['error' => 'Tournament not found.']);
    exit();
}

$amount = floatval($tournament['fee']);

$client = new Client();
$paypalClientId = $_ENV['PAYPAL_CLIENT_ID'];
$paypalSecret = $_ENV['PAYPAL_CLIENT_SECRET'];
$paypalMode = $_ENV['PAYPAL_MODE'];
$apiUrl = $paypalMode === 'sandbox' ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';

try {
    $response = $client->request('POST', "$apiUrl/v1/oauth2/token", [
        'auth' => [$paypalClientId, $paypalSecret],
        'form_params' => [
            'grant_type' => 'client_credentials'
        ],
    ]);
    $body = json_decode($response->getBody(), true);
    $accessToken = $body['access_token'];

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
                    'custom_id' => $tournamentId
                ],
            ],
            'application_context' => [
                'return_url' => 'https://yourwebsite.com/paypal_success_tournament.php',
                'cancel_url' => 'https://yourwebsite.com/paypal_cancel.php',
            ],
        ],
    ]);

    $order = json_decode($response->getBody(), true);
    echo json_encode(['id' => $order['id']]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Could not create order.', 'details' => $e->getMessage()]);
}
?>