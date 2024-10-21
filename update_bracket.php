<?php
// At the beginning of your PHP scripts
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/your/error.log');
error_reporting(E_ALL);
// update_bracket.php
include 'conn.php';

header('Content-Type: application/json');

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
            // For example, you might have a mapping table or calculate based on round and match_number

            // Placeholder Logic:
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
                    // This logic will vary depending on your bracket structure
                    // For simplicity, assume the next match_number is floor((current_match_number + 1)/2)

                    $nextMatchNumber = ceil($currentMatchNumber / 2);

                    // Find the next match where this winner should be placed
                    // Assume that winners go to player1 or player2 based on match_number parity
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
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}

// Function to determine the next round
function getNextRound($currentRound) {
    $rounds = ['Round of 16', 'Quarterfinals', 'Semifinals', 'Finals'];
    $currentIndex = array_search($currentRound, $rounds);
    if ($currentIndex !== false && $currentIndex < count($rounds) - 1) {
        return $rounds[$currentIndex + 1];
    }
    return null;
}
?>
