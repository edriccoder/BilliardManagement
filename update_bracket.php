<?php
// update_bracket.php
include 'conn.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($data['tournament_id']) && isset($data['match_id']) && isset($data['winner_id'])) {
    $tournamentId = $data['tournament_id'];
    $matchId = $data['match_id'];
    $winnerId = $data['winner_id'];

    // Update the winner in the current match
    $stmt = $conn->prepare('UPDATE bracket SET winner_id = ? WHERE match_id = ? AND tournament_id = ?');
    $result = $stmt->execute([$winnerId, $matchId, $tournamentId]);

    if ($result) {
        // Determine the next match where the winner should be placed
        // This logic depends on how you structure your bracket
        // For simplicity, let's assume single elimination and matches are ordered sequentially

        // Fetch the current match details
        $stmt = $conn->prepare('SELECT round FROM bracket WHERE match_id = ? AND tournament_id = ?');
        $stmt->execute([$matchId, $tournamentId]);
        $currentMatch = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($currentMatch) {
            $currentRound = $currentMatch['round'];
            // Determine the next round (e.g., Round of 16 -> Quarterfinals -> Semifinals -> Finals)
            // This requires knowing your tournament's round structure

            // Example logic:
            $nextRound = getNextRound($currentRound);
            if ($nextRound) {
                // Find the next match in the next round where this winner should be placed
                // This requires a mapping between current matches and next matches
                // Implementation depends on your bracket setup

                // Placeholder: Assume next match ID is current match ID + 1
                $nextMatchId = $matchId + 1;
                $stmt = $conn->prepare('UPDATE bracket SET player1_id = ? WHERE match_id = ? AND tournament_id = ? AND player1_id IS NULL');
                $stmt->execute([$winnerId, $nextMatchId, $tournamentId]);
            }
        }

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update the winner.']);
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
