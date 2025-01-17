<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
header('Content-Type: application/json');

require 'vendor/autoload.php';

use GuzzleHttp\Client;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

include 'conn.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $tournamentId = intval($_POST['tournament_id']);
    $totalAmount = isset($_POST['totalAmount']) ? intval($_POST['totalAmount']) : null;
    $paymentMethod = $_POST['paymentMethod'] ?? 'gcash';

    if ($totalAmount === null) {
        $response['message'] = 'Total amount is missing.';
        echo json_encode($response);
        exit();
    }

    try {
        // Verify user
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $response['message'] = 'Username not found.';
            echo json_encode($response);
            exit();
        }

        $userId = $user['user_id'];

        // Verify tournament status
        $stmt = $conn->prepare("SELECT status, max_player FROM tournaments WHERE tournament_id = :tournament_id");
        $stmt->bindParam(':tournament_id', $tournamentId, PDO::PARAM_INT);
        $stmt->execute();
        $tournament = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$tournament || strtolower($tournament['status']) !== 'upcoming') {
            $response['message'] = 'Tournament not open for registration.';
            echo json_encode($response);
            exit();
        }

        // Check if user is already registered
        $stmt = $conn->prepare("SELECT COUNT(*) FROM players WHERE user_id = :user_id AND tournament_id = :tournament_id");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':tournament_id', $tournamentId, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->fetchColumn() > 0) {
            $response['message'] = 'Already registered for this tournament.';
            echo json_encode($response);
            exit();
        }

        // Check if tournament is full
        $stmt = $conn->prepare("SELECT COUNT(*) FROM players WHERE tournament_id = :tournament_id");
        $stmt->bindParam(':tournament_id', $tournamentId, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->fetchColumn() >= $tournament['max_player']) {
            $response['message'] = 'Tournament is full.';
            echo json_encode($response);
            exit();
        }

        // Register the player
        $stmt = $conn->prepare("INSERT INTO players (user_id, username, tournament_id, status) VALUES (:user_id, :username, :tournament_id, 'pending')");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':tournament_id', $tournamentId, PDO::PARAM_INT);
        $stmt->execute();

        // Generate and save receipt image
        $receiptFileName = generateReceiptImage($username, $tournamentId, $totalAmount);


        // Update players table with proof of payment
       $stmt = $conn->prepare("UPDATE players SET proof_of_payment = :receiptFileName WHERE user_id = :user_id AND tournament_id = :tournament_id");
        $stmt->bindParam(':receiptFileName', $receiptFileName, PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':tournament_id', $tournamentId, PDO::PARAM_INT);
        $stmt->execute();

        // Generate payment link and redirect
        if ($paymentMethod === 'gcash') {
            $paymongoLink = generatePaymongoLink($tournamentId, $totalAmount);

            if ($paymongoLink) {
                header("Location: $paymongoLink");
                exit(); // Exit after redirect
            } else {
                $response['message'] = 'Failed to generate PayMongo payment link.';
            }
        } else {
            $response['success'] = true;
            $response['message'] = 'Successfully registered. Proceed with PayPal payment.';
        }
    } catch (Exception $e) {
        $response['message'] = 'An error occurred: ' . $e->getMessage();
    }
}

// Generate a receipt image function
function generateReceiptImage($username, $tournamentId, $amount) {
    // Set image dimensions
    $imgWidth = 500;
    $imgHeight = 350;
    $image = imagecreatetruecolor($imgWidth, $imgHeight);

    // Define colors
    $backgroundColor = imagecolorallocate($image, 230, 230, 250); // Light lavender background
    $headerColor = imagecolorallocate($image, 70, 130, 180); // Steel blue for header
    $textColor = imagecolorallocate($image, 0, 0, 0); // Black for text

    // Fill background
    imagefilledrectangle($image, 0, 0, $imgWidth, $imgHeight, $backgroundColor);

    // Add header
    $headerFont = __DIR__ . '/arialbd.ttf'; // Ensure bold Arial font file is available
    imagettftext($image, 18, 0, 15, 40, $headerColor, $headerFont, 'Payment Receipt');

    // Add receipt details
    $font = __DIR__ . '/arial.ttf'; // Regular Arial font
    $textContent = "Username: $username\nTournament ID: $tournamentId\nAmount: PHP " . number_format($amount, 2);
    
    // Position and format the text content
    $lineSpacing = 30;
    $yPosition = 80;
    foreach (explode("\n", $textContent) as $line) {
        imagettftext($image, 14, 0, 15, $yPosition, $textColor, $font, $line);
        $yPosition += $lineSpacing;
    }

    // Add footer
    $footerText = "Thank you for your registration!";
    imagettftext($image, 12, 0, 15, $imgHeight - 20, $textColor, $font, $footerText);

    // Define the directory and file name
    $directory = 'uploads/';
    $fileName = "receipt_{$username}_{$tournamentId}.png";

    // Check if directory exists, if not, create it
    if (!is_dir($directory)) {
        mkdir($directory, 0777, true);
    }

    // Save the image as a PNG file and destroy the image resource to free memory
    imagepng($image, "$directory$fileName");
    imagedestroy($image);
    
    $combine = $directory . $fileName;

    // Return the full path including directory and file name
    return "$combine";
}


function generatePaymongoLink($bookingId, $amount) {
    $client = new Client();
    $amountInCents = intval($amount * 100);
    $secretKey = 'sk_test_bF3PwBWyXDZS56TXmTAnvQDu';
    
    if (!$secretKey) {
        error_log('PayMongo secret key is missing.');
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
                        'description' => 'Tournament Registration for #' . $bookingId,
                        'redirect' => [
                            'success' => 'https://tjamessportybar.com/BilliardManagement/paymongo_success.php?booking_id=' . $bookingId,
                            'failed' => 'https://tjamessportybar.com/BilliardManagement/payment_failed.php?booking_id=' . $bookingId,
                        ],
                        'reference_number' => 'booking_' . $bookingId,
                    ],
                ],
            ],
        ]);

        $data = json_decode($response->getBody(), true);
        return $data['data']['attributes']['checkout_url'] ?? null;
    } catch (Exception $e) {
        error_log('PayMongo Error: ' . $e->getMessage());
        return null;
    }
}

echo json_encode($response);
?>
