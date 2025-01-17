<?php
// update_match.php
header('Content-Type: application/json');
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['match_id']) && isset($_POST['winner_id'])) {
    $matchId = $_POST['match_id'];
    $winnerId = $_POST['winner_id'];

    try {
        $conn->beginTransaction();

        // Update the winner of the current match
        $stmt = $conn->prepare('UPDATE bracket SET winner_id = ? WHERE match_id = ?');
        $stmt->execute([$winnerId, $matchId]);

        // Advance the winner to the next match
        advanceWinner($conn, $matchId, $winnerId);

        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Winner updated and advanced to next round successfully.']);
    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode(['success' => false, 'message' => 'Error updating match: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}

// Function to advance winner to the next match
function advanceWinner($conn, $matchId, $winnerId) {
    // Get the parent match ID
    $stmt = $conn->prepare('SELECT parent_match_id FROM bracket WHERE match_id = ?');
    $stmt->execute([$matchId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result && $result['parent_match_id']) {
        $parentMatchId = $result['parent_match_id'];

        // Determine whether to place the winner in player1 or player2
        $stmt = $conn->prepare('SELECT player1_id, player2_id FROM bracket WHERE match_id = ?');
        $stmt->execute([$parentMatchId]);
        $parentMatch = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($parentMatch['player1_id'] === null) {
            $stmt = $conn->prepare('UPDATE bracket SET player1_id = ? WHERE match_id = ?');
            $stmt->execute([$winnerId, $parentMatchId]);
        } elseif ($parentMatch['player2_id'] === null) {
            $stmt = $conn->prepare('UPDATE bracket SET player2_id = ? WHERE match_id = ?');
            $stmt->execute([$winnerId, $parentMatchId]);
        } else {
            throw new Exception('Both players in the next match are already set.');
        }
    } else {
        // If there is no parent match, this is the final match and the winner is the tournament winner
        // You can update the tournament winner here if needed
    }
}
?>
