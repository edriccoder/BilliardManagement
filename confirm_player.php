<?php
// confirm_player.php

header('Content-Type: application/json');

include 'conn.php';

// Get the JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['player_id'])) {
    echo json_encode(['success' => false, 'message' => 'Player ID is missing.']);
    exit;
}

$player_id = $input['player_id'];

// Validate player_id (ensure it's an integer)
if (!filter_var($player_id, FILTER_VALIDATE_INT)) {
    echo json_encode(['success' => false, 'message' => 'Invalid Player ID.']);
    exit;
}

try {
    // Update the player's status to 'confirmed'
    $stmt = $conn->prepare("UPDATE tournament_players SET status = 'confirmed' WHERE user_id = :player_id AND tournament_id = :tournament_id");
    $stmt->bindParam(':player_id', $player_id, PDO::PARAM_INT);
    $stmt->bindParam(':tournament_id', $currentTournamentId, PDO::PARAM_INT); // Assuming you have the tournament_id
    
    // Alternatively, if you don't have the tournament_id, you may need to fetch it based on player_id
    // Here's an example assuming the tournament_id is known from the session or passed as a parameter
    // For now, let's assume tournament_id is provided via POST

    if (isset($input['tournament_id'])) {
        $tournament_id = $input['tournament_id'];
    } else {
        // Fetch the tournament_id based on player_id
        $stmt_tournament = $conn->prepare("SELECT tournament_id FROM tournament_players WHERE user_id = :player_id LIMIT 1");
        $stmt_tournament->bindParam(':player_id', $player_id, PDO::PARAM_INT);
        $stmt_tournament->execute();
        $tournament = $stmt_tournament->fetch(PDO::FETCH_ASSOC);
        if ($tournament) {
            $tournament_id = $tournament['tournament_id'];
        } else {
            echo json_encode(['success' => false, 'message' => 'Tournament not found for the player.']);
            exit;
        }
    }

    // Now, update the status
    $stmt->bindParam(':tournament_id', $tournament_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Player confirmed successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Player could not be confirmed.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
