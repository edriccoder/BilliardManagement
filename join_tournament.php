<?php
// join_tournament.php

ini_set('display_errors', 1); // Disable error display for security
error_reporting(E_ALL);
session_start();
header('Content-Type: application/json');

require 'vendor/autoload.php';
use GuzzleHttp\Client;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Include database connection
include 'conn.php';

$response = ['success' => false, 'message' => ''];

function logError($message) {
    $logFile = __DIR__ . '/error.log';
    $date = date('Y-m-d H:i:s');
    $formattedMessage = "[$date] $message" . PHP_EOL;

    file_put_contents($logFile, $formattedMessage, FILE_APPEND);
}

// Function to generate receipt image
function generateReceiptImage($username, $tournamentId, $amount) {
    // Ensure that the font files 'arial.ttf' and 'arialbd.ttf' exist in the script's directory
    $imgWidth = 500;
    $imgHeight = 350;
    $image = imagecreatetruecolor($imgWidth, $imgHeight);

    $backgroundColor = imagecolorallocate($image, 230, 230, 250);
    $headerColor = imagecolorallocate($image, 70, 130, 180);
    $textColor = imagecolorallocate($image, 0, 0, 0);

    imagefilledrectangle($image, 0, 0, $imgWidth, $imgHeight, $backgroundColor);

    $headerFont = __DIR__ . '/arialbd.ttf';
    $font = __DIR__ . '/arial.ttf';

    if (!file_exists($headerFont) || !file_exists($font)) {
        logError('Font files missing.');
        return null;
    }

    imagettftext($image, 18, 0, 15, 40, $headerColor, $headerFont, 'Payment Receipt');

    $textContent = "Username: $username\nTournament ID: $tournamentId\nAmount: PHP " . number_format($amount, 2);

    $lineSpacing = 30;
    $yPosition = 80;
    foreach (explode("\n", $textContent) as $line) {
        imagettftext($image, 14, 0, 15, $yPosition, $textColor, $font, $line);
        $yPosition += $lineSpacing;
    }

    $footerText = "Thank you for your registration!";
    imagettftext($image, 12, 0, 15, $imgHeight - 20, $textColor, $font, $footerText);

    $directory = 'uploads/';
    $fileName = "receipt_{$username}_{$tournamentId}_" . time() . ".png"; // Added timestamp for uniqueness

    if (!is_dir($directory)) {
        mkdir($directory, 0777, true);
    }

    imagepng($image, "$directory$fileName");
    imagedestroy($image);

    return "$directory$fileName";
}

// Function to generate PayMongo payment link
function generatePaymongoLink($tournamentId, $amount) {
    $client = new Client();
    $amountInCents = intval($amount * 100);
    $secretKey = 'sk_test_bF3PwBWyXDZS56TXmTAnvQDu';

    if (!$secretKey) {
        logError('PayMongo secret key is missing.');
        return null;
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
                        'description' => 'Tournament Registration for #' . $tournamentId,
                        'redirect' => [
                            'success' => 'https://tjamessportybar.com/BilliardManagement/paymongo_success.php?booking_id=' . $tournamentId,
                            'failed' => 'https://tjamessportybar.com/BilliardManagement/payment_failed.php?booking_id=' . $tournamentId,
                        ],
                        'reference_number' => 'tournament_' . $tournamentId . '_' . time(), // Ensure uniqueness
                    ],
                ],
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        // Log the full response for debugging
        logError('PayMongo Response: ' . print_r($data, true));

        return $data['data']['attributes']['checkout_url'] ?? null;
    } catch (Exception $e) {
        // Log full exception for debugging purposes
        logError('PayMongo Exception: ' . $e->getMessage());
        $statusCode = '';
        $errorBody = '';

        if (method_exists($e, 'hasResponse') && $e->hasResponse()) {
            $response = $e->getResponse();
            $statusCode = $response->getStatusCode();
            $errorBody = $response->getBody()->getContents();
            logError('PayMongo Response Status Code: ' . $statusCode);
            logError('PayMongo Response Body: ' . $errorBody);

            // Add additional details to the exception message
            throw new Exception('Failed to generate PayMongo payment link. Status Code: ' . $statusCode . ' Response: ' . $errorBody);
        }

        // If no response body is available, throw a generic exception
        throw new Exception('Failed to generate PayMongo payment link. Exception: ' . $e->getMessage());
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Check if the session variables are set
        if (!isset($_SESSION['user_id'], $_SESSION['username'])) {
            throw new Exception('User session not set. Please log in again.');
        }

        $userId = intval($_SESSION['user_id']);
        $username = trim($_SESSION['username']);

        // Extract form data
        $tournamentId = isset($_POST['tournament_id']) ? intval($_POST['tournament_id']) : null;
        $totalAmount = isset($_POST['totalAmount']) ? floatval($_POST['totalAmount']) : null;
        $paymentMethod = isset($_POST['paymentMethod']) ? trim($_POST['paymentMethod']) : 'gcash';

        if ($tournamentId === null) {
            throw new Exception('Tournament ID is required.');
        }

        if ($totalAmount === null || $totalAmount <= 0) {
            $stmt = $conn->prepare("SELECT fee FROM tournaments WHERE tournament_id = :tournament_id");
            $stmt->execute(['tournament_id' => $tournamentId]);
            $tournament = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($tournament) {
                $totalAmount = floatval($tournament['fee']);
            } else {
                throw new Exception('Invalid tournament ID.');
            }
        }

        // Verify user
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE user_id = :user_id AND username = :username");
        $stmt->execute(['user_id' => $userId, 'username' => $username]);
        if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
            throw new Exception('User not found or mismatch.');
        }

        // Verify tournament status
        $stmt = $conn->prepare("SELECT status, max_player FROM tournaments WHERE tournament_id = :tournament_id");
        $stmt->execute(['tournament_id' => $tournamentId]);
        $tournament = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$tournament || strcasecmp($tournament['status'], 'upcoming') !== 0) {
            throw new Exception('Tournament not open for registration. Current status: ' . ($tournament['status'] ?? 'N/A'));
        }

        // Check if user is already registered
        $stmt = $conn->prepare("SELECT COUNT(*) FROM players WHERE user_id = :user_id AND tournament_id = :tournament_id");
        $stmt->execute(['user_id' => $userId, 'tournament_id' => $tournamentId]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception('Already registered for this tournament.');
        }

        // Check if tournament is full
        $stmt = $conn->prepare("SELECT COUNT(*) FROM players WHERE tournament_id = :tournament_id");
        $stmt->execute(['tournament_id' => $tournamentId]);
        if ($stmt->fetchColumn() >= $tournament['max_player']) {
            throw new Exception('Tournament is full.');
        }

        // Register the player
        $stmt = $conn->prepare("INSERT INTO players (user_id, username, tournament_id, status) VALUES (:user_id, :username, :tournament_id, 'pending')");
        $stmt->execute([
            'user_id' => $userId,
            'username' => $username,
            'tournament_id' => $tournamentId
        ]);

        // Generate and save receipt image
        $receiptFileName = generateReceiptImage($username, $tournamentId, $totalAmount);
        if (!$receiptFileName) {
            throw new Exception('Failed to generate receipt image.');
        }

        // Update players table with proof of payment
        $stmt = $conn->prepare("UPDATE players SET proof_of_payment = :receiptFileName WHERE user_id = :user_id AND tournament_id = :tournament_id");
        $stmt->execute([
            'receiptFileName' => $receiptFileName,
            'user_id' => $userId,
            'tournament_id' => $tournamentId
        ]);

        // Generate payment link and respond accordingly
        if ($paymentMethod === 'gcash') {
            $paymongoLink = generatePaymongoLink($tournamentId, $totalAmount);

            if ($paymongoLink) {
                // Redirect to the PayMongo link
                header("Location: $paymongoLink");
                exit();
            } else {
                throw new Exception('Failed to generate PayMongo payment link.');
            }
        } else {
            // Handle PayPal or other payment methods here
            $response['success'] = true;
            $response['message'] = 'Successfully registered. Proceed with PayPal payment.';
        }
    } catch (Exception $e) {
        $response['message'] = 'An error occurred: ' . $e->getMessage();
        logError($response['message']);
    }

    // Ensure a valid JSON response is always returned
    echo json_encode($response);
    exit();
}

// If the request method is not POST, return an error response
echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
exit();