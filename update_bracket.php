<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tournament_id']) && isset($_POST['round']) && isset($_POST['match']) && isset($_POST['winner_id'])) {
    $tournamentId = $_POST['tournament_id'];
    $round = $_POST['round'];
    $match = $_POST['match'];
    $winnerId = $_POST['winner_id'];

    // Update the winner in the database
    $stmt = $conn->prepare('UPDATE bracket SET winner_id = ? WHERE tournament_id = ? AND round = ? AND match_number = ?');
    $result = $stmt->execute([$winnerId, $tournamentId, $round, $match]);

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update the winner.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
