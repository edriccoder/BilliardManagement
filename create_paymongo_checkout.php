<?php
// join_tournament2.php

session_start();
header('Content-Type: application/json');

require 'vendor/autoload.php'; // Composer autoload
require 'config.php'; // Include PayMongo configuration

use GuzzleHttp\Client;

// Include your existing database connection
include 'conn.php';

// Initialize response array
$response = ['success' => false, 'message' => ''];

function generatePaymongoLink($tournamentId, $amount) {
    $client = new Client();

    // Convert amount to cents (PayMongo expects smallest currency unit)
    $amountInCents = intval($amount * 100);

    // Retrieve PayMongo secret key from config.php
    $secretKey = 'sk_test_bF3PwBWyXDZS56TXmTAnvQDu'; // Defined in config.php

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
                        'description' => 'Billiard Payment for Tournament #' . $tournamentId,
                        'redirect' => [
                            'success' => 'https://tjamessportybar.com/BilliardManagement/payment_success.php?tournament_id=' . $tournamentId,
                            'failed' => 'https://tjamessportybar.com/BilliardManagement/payment_failed.php?tournament_id=' . $tournamentId,
                        ],
                        'reference_number' => 'tournament_' . $tournamentId . '_' . time(),
                    ],
                ],
            ],
        ]);

        $body = $response->getBody();
        $data = json_decode($body, true);

        // Log the entire response for debugging (optional)
        error_log('PayMongo Response: ' . $body);

        // Access the checkout_url directly from attributes
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize input data
    $username = trim($_POST['username']);
    $tournamentId = intval($_POST['tournament_id']);
    $paymentMethod = isset($_POST['paymentMethod']) ? strtolower(trim($_POST['paymentMethod'])) : '';

    // Basic validation
    if (empty($username) || empty($tournamentId) || empty($paymentMethod)) {
        $response['message'] = 'All fields are required.';
        echo json_encode($response);
        exit();
    }

    try {
        // 1. Check if username exists in the users table
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            // Username does not exist
            $response['message'] = 'Username not found.';
            echo json_encode($response);
            exit();
        }

        $userId = $user['user_id'];

        // 2. Check if the tournament exists and its status is 'upcoming'
        $stmt = $conn->prepare("SELECT status, max_player, fee FROM tournaments WHERE tournament_id = :tournament_id");
        $stmt->bindParam(':tournament_id', $tournamentId, PDO::PARAM_INT);
        $stmt->execute();
        $tournament = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$tournament) {
            // Tournament does not exist
            $response['message'] = 'Tournament not found.';
            echo json_encode($response);
            exit();
        }

        if (strtolower($tournament['status']) !== 'upcoming') {
            // Tournament is not upcoming
            $response['message'] = 'This tournament is not open for registration.';
            echo json_encode($response);
            exit();
        }

        // 3. Check if the user is already registered for the tournament
        $stmt = $conn->prepare("SELECT COUNT(*) FROM players WHERE user_id = :user_id AND tournament_id = :tournament_id");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':tournament_id', $tournamentId, PDO::PARAM_INT);
        $stmt->execute();
        $isRegistered = $stmt->fetchColumn() > 0;

        if ($isRegistered) {
            $response['message'] = 'You have already joined this tournament.';
            echo json_encode($response);
            exit();
        }

        // 4. Check if the tournament is full
        $stmt = $conn->prepare("SELECT COUNT(*) FROM players WHERE tournament_id = :tournament_id");
        $stmt->bindParam(':tournament_id', $tournamentId, PDO::PARAM_INT);
        $stmt->execute();
        $currentPlayers = $stmt->fetchColumn();

        if ($currentPlayers >= $tournament['max_player']) {
            $response['message'] = 'The tournament is full.';
            echo json_encode($response);
            exit();
        }

        if ($paymentMethod === 'paymongo') {
            // Handle PayMongo Payment

            // 5. Generate PayMongo payment link
            // Assume the tournament fee is stored in $tournament['fee']
            $amount = floatval($tournament['fee']);
            $paymentLink = generatePaymongoLink($tournamentId, $amount);

            if (!$paymentLink) {
                $response['message'] = 'Failed to generate PayMongo payment link. Please try again.';
                echo json_encode($response);
                exit();
            }

            // 6. Insert registration data into the players table with status 'pending' and store PayMongo link
            $stmt = $conn->prepare("INSERT INTO players (user_id, username, tournament_id, paymongo_link, status) VALUES (:user_id, :username, :tournament_id, :paymongo_link, 'pending')");
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':tournament_id', $tournamentId, PDO::PARAM_INT);
            $stmt->bindParam(':paymongo_link', $paymentLink, PDO::PARAM_STR);
            $stmt->execute();

            // Registration successful, provide the payment link
            $response['success'] = true;
            $response['message'] = 'Successfully registered for the tournament. Please complete your payment using the provided link.';
            $response['payment_link'] = $paymentLink;
        } else {
            $response['message'] = 'Invalid payment method selected.';
            echo json_encode($response);
            exit();
        }
    } catch (Exception $e) {
        // Handle any unexpected errors
        $response['message'] = 'An error occurred: ' . $e->getMessage();
    }
}

echo json_encode($response);
?>
