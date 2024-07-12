<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['tournament_id'], $_GET['round'], $_GET['match'], $_GET['winner_id'])) {
    $tournamentId = $_GET['tournament_id'];
    $round = $_GET['round'];
    $match = $_GET['match'];
    $winnerId = $_GET['winner_id'];

    // Update the bracket to set the winner for the match
    $stmt = $conn->prepare('UPDATE bracket SET winner_id = ? WHERE tournament_id = ? AND round = ? AND match_number = ?');
    $stmt->execute([$winnerId, $tournamentId, $round, $match]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update the winner.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request parameters.']);
}
?>
