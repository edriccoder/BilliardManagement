<?php
// update_bracket_scores.php
include 'conn.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$tournament_id = $data['tournament_id'] ?? null;
$round = $data['round'] ?? null;
$match = $data['match'] ?? null;
$winner_id = $data['winner_id'] ?? null;
$player1_score = $data['player1_score'] ?? null;
$player2_score = $data['player2_score'] ?? null;

if (!$tournament_id || !$round || !$match || !$winner_id || $player1_score === null || $player2_score === null) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

try {
    // Begin transaction
    $conn->beginTransaction();
    
    // Fetch the bracket_id
    $stmt = $conn->prepare('SELECT bracket_id FROM bracket WHERE tournament_id = :tournament_id AND round = :round AND match_number = :match');
    $stmt->execute([
        ':tournament_id' => $tournament_id,
        ':round' => $round,
        ':match' => $match
    ]);
    $bracket = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$bracket) {
        throw new Exception('Bracket not found.');
    }
    
    $bracket_id = $bracket['bracket_id'];
    
    // Update the match with the winner and scores
    $stmt = $conn->prepare('
        UPDATE bracket
        SET winner_id = :winner_id, player1_score = :player1_score, player2_score = :player2_score
        WHERE bracket_id = :bracket_id
    ');
    $stmt->execute([
        ':winner_id' => $winner_id,
        ':player1_score' => $player1_score,
        ':player2_score' => $player2_score,
        ':bracket_id' => $bracket_id
    ]);
    
    // Determine the next round and match
    $next_round = $round + 1;
    $next_match = ceil($match / 2);
    $player_position = ($match % 2 == 1) ? 1 : 2;
    
    // Fetch or create the next bracket match
    $stmt = $conn->prepare('SELECT bracket_id FROM bracket WHERE tournament_id = :tournament_id AND round = :next_round AND match_number = :next_match');
    $stmt->execute([
        ':tournament_id' => $tournament_id,
        ':next_round' => $next_round,
        ':next_match' => $next_match
    ]);
    $next_bracket = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$next_bracket) {
        // Create the next match if it doesn't exist
        $stmt = $conn->prepare('
            INSERT INTO bracket (tournament_id, round, match_number)
            VALUES (:tournament_id, :next_round, :next_match)
        ');
        $stmt->execute([
            ':tournament_id' => $tournament_id,
            ':next_round' => $next_round,
            ':next_match' => $next_match
        ]);
        $next_bracket_id = $conn->lastInsertId();
    } else {
        $next_bracket_id = $next_bracket['bracket_id'];
    }
    
    // Assign the winner to the next match
    $stmt = $conn->prepare('
        INSERT INTO bracket_players (bracket_id, tournament_id, round, match_number, player_position, user_id)
        VALUES (:bracket_id, :tournament_id, :next_round, :next_match, :player_position, :winner_id)
        ON DUPLICATE KEY UPDATE user_id = :winner_id
    ');
    $stmt->execute([
        ':bracket_id' => $next_bracket_id,
        ':tournament_id' => $tournament_id,
        ':next_round' => $next_round,
        ':next_match' => $next_match,
        ':player_position' => $player_position,
        ':winner_id' => $winner_id
    ]);
    
    $conn->commit();
    
    echo json_encode(['success' => true, 'message' => 'Bracket updated successfully']);
} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
