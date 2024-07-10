<?php
include 'conn.php';

function createSingleEliminationBracket($players) {
    $bracket = [];
    shuffle($players); // Randomize the order of players

    while (count($players) > 1) {
        $bracket[] = array_splice($players, 0, 2); // Pair players
    }

    return $bracket;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['tournament_id'])) {
    $tournamentId = $_GET['tournament_id'];

    // Check if a bracket already exists for this tournament
    $stmt = $conn->prepare('SELECT COUNT(*) as bracket_count FROM bracket WHERE tournament_id = ?');
    $stmt->execute([$tournamentId]);
    $bracketCount = $stmt->fetch(PDO::FETCH_ASSOC)['bracket_count'];

    if ($bracketCount > 0) {
        echo json_encode(['success' => false, 'message' => 'Bracket already exists for this tournament.']);
        exit;
    }

    // Get tournament details
    $stmt = $conn->prepare('SELECT max_player FROM tournaments WHERE tournament_id = ?');
    $stmt->execute([$tournamentId]);
    $tournament = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($tournament) {
        // Get the players
        $stmt = $conn->prepare('SELECT user_id FROM players WHERE tournament_id = ?');
        $stmt->execute([$tournamentId]);
        $players = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($players) >= $tournament['max_player']) {
            // Create the bracket
            $bracket = createSingleEliminationBracket($players);

            // Save the bracket to the database
            foreach ($bracket as $match) {
                $stmt = $conn->prepare('INSERT INTO bracket (tournament_id, player1_id, player2_id) VALUES (?, ?, ?)');
                $stmt->execute([$tournamentId, $match[0]['user_id'], $match[1]['user_id']]);
            }

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Not enough players to create a bracket.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid tournament ID.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No tournament ID received.']);
}
?>