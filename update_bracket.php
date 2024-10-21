<?php
<<<<<<< HEAD
// update_bracket.php
header('Content-Type: application/json');
include 'conn.php';

// Retrieve the JSON input
$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($data['tournament_id']) && isset($data['bracket_id']) && isset($data['winner_id'])) {
    $tournamentId = $data['tournament_id'];
    $bracketId = $data['bracket_id'];
    $winnerId = $data['winner_id'];

    try {
        // Update the winner in the current match
        $stmt = $conn->prepare('UPDATE bracket SET winner_id = ? WHERE bracket_id = ? AND tournament_id = ?');
        $result = $stmt->execute([$winnerId, $bracketId, $tournamentId]);

        if ($result) {
            // Determine the next match where the winner should be placed
            // This logic depends on how you structure your bracket

            // Fetch the current match details
            $stmt = $conn->prepare('SELECT round, match_number FROM bracket WHERE bracket_id = ? AND tournament_id = ?');
            $stmt->execute([$bracketId, $tournamentId]);
            $currentMatch = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($currentMatch) {
                $currentRound = $currentMatch['round'];
                $currentMatchNumber = $currentMatch['match_number'];

                // Define the next round
                $nextRound = getNextRound($currentRound);

                if ($nextRound) {
                    // Calculate the next match_number based on current match_number
                    // Example: For every two matches in the current round, they feed into one match in the next round
                    $nextMatchNumber = ceil($currentMatchNumber / 2);

                    // Fetch the next match where the winner should be placed
                    $stmt = $conn->prepare('SELECT bracket_id, player1_id, player2_id FROM bracket WHERE tournament_id = ? AND round = ? AND match_number = ?');
                    $stmt->execute([$tournamentId, $nextRound, $nextMatchNumber]);
                    $nextMatch = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($nextMatch) {
                        if ($currentMatchNumber % 2 == 1) {
                            // Odd match_number -> assign to player1
                            $stmt = $conn->prepare('UPDATE bracket SET player1_id = ? WHERE bracket_id = ? AND tournament_id = ?');
                            $stmt->execute([$winnerId, $nextMatch['bracket_id'], $tournamentId]);
                        } else {
                            // Even match_number -> assign to player2
                            $stmt = $conn->prepare('UPDATE bracket SET player2_id = ? WHERE bracket_id = ? AND tournament_id = ?');
                            $stmt->execute([$winnerId, $nextMatch['bracket_id'], $tournamentId]);
                        }
                    }
                }
            }

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update the winner.']);
        }
    } catch (Exception $e) {
        error_log($e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An unexpected error occurred.']);
=======
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
>>>>>>> parent of 6cacb1e (fix)
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
