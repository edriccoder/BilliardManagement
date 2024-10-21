<?php
// At the beginning of your PHP scripts
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/your/error.log');
error_reporting(E_ALL);
// create_bracket.php
include 'conn.php';

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
        $stmt = $conn->prepare('SELECT player_id, user_id, username FROM players WHERE tournament_id = ?');
        $stmt->execute([$tournamentId]);
        $players = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($players) >= $tournament['max_player']) {
            // Assign regions (East and West) evenly
            $half = ceil(count($players) / 2);
            $eastPlayers = array_slice($players, 0, $half);
            $westPlayers = array_slice($players, $half);

            // Create the bracket
            $bracket = createSingleEliminationBracket(array_merge($eastPlayers, $westPlayers));

            // Save the bracket to the database
            foreach ($bracket as $match) {
                $player1_id = isset($match['players'][0]) ? $match['players'][0]['player_id'] : null;
                $player2_id = isset($match['players'][1]) ? $match['players'][1]['player_id'] : null;
                $region = isset($match['players'][0]['player_id']) && in_array($match['players'][0], $eastPlayers) ? 'East' : 'West';

                $stmt = $conn->prepare('INSERT INTO bracket (tournament_id, round, match_number, player1_id, player2_id, region) VALUES (?, ?, ?, ?, ?, ?)');
                $stmt->execute([$tournamentId, $match['round'], $match['match_number'], $player1_id, $player2_id, $region]);
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

function createSingleEliminationBracket($players) {
    $bracket = [];
    shuffle($players); // Randomize the order of players
    $round = 'Round of 16'; // Starting round
    $matchNumber = 1;

    while (count($players) > 1) {
        $bracket[] = [
            'round' => $round,
            'match_number' => $matchNumber++,
            'players' => array_splice($players, 0, 2)
        ];
    }

    // Add finals if necessary
    if (count($players) == 1) {
        $bracket[] = [
            'round' => 'Finals',
            'match_number' => $matchNumber,
            'players' => [$players[0], null]
        ];
    }

    return $bracket;
}
?>
