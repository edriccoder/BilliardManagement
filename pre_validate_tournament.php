<?php
session_start();
header('Content-Type: application/json');

include 'conn.php';

// Initialize response array
$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize input data
    $tournamentId = intval($_POST['tournament_id']);

    try {
        // 1. Check if the tournament exists and its status is 'upcoming'
        $stmt = $conn->prepare("SELECT status, max_player FROM tournaments WHERE tournament_id = :tournament_id");
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

        // 2. Check if the tournament is full
        $stmt = $conn->prepare("SELECT COUNT(*) FROM players WHERE tournament_id = :tournament_id");
        $stmt->bindParam(':tournament_id', $tournamentId, PDO::PARAM_INT);
        $stmt->execute();
        $currentPlayers = $stmt->fetchColumn();

        if ($currentPlayers >= $tournament['max_player']) {
            $response['message'] = 'The tournament is full.';
            echo json_encode($response);
            exit();
        }

        // All checks passed
        $response['success'] = true;
        $response['message'] = 'You are eligible to join this tournament. Proceed to registration.';
    } catch (Exception $e) {
        // Handle any unexpected errors
        $response['message'] = 'An error occurred: ' . $e->getMessage();
    }
}

echo json_encode($response);
?>
