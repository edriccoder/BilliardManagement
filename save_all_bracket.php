<?php
// save_all_bracket.php

header('Content-Type: application/json');

include 'conn.php'; // Ensure this file establishes a PDO connection as $conn

// Function to determine final standings
function determine_final_standings($conn, $tournament_id) {
    // Fetch all matches ordered by round and match_number
    $stmt = $conn->prepare('
        SELECT *
        FROM bracket
        WHERE tournament_id = ?
        ORDER BY round ASC, match_number ASC
    ');
    $stmt->execute([$tournament_id]);
    $matches = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($matches)) {
        return ['error' => 'No matches found for this tournament.'];
    }

    // Determine the total number of rounds
    $rounds = array_column($matches, 'round');
    $total_rounds = max($rounds);

    // Identify the final match (last round)
    $final_matches = array_filter($matches, function($match) use ($total_rounds) {
        return $match['round'] == $total_rounds;
    });

    if (count($final_matches) != 1) {
        return ['error' => 'Invalid number of final matches.'];
    }

    $final_match = array_values($final_matches)[0];

    if (empty($final_match['winner_id'])) {
        return ['error' => 'Final match does not have a winner yet.'];
    }

    $first_place_id = $final_match['winner_id'];

    // Determine second place
    $second_place_id = ($final_match['player1_id'] == $first_place_id) ? $final_match['player2_id'] : $final_match['player1_id'];

    // Determine third place (if applicable)
    // Assuming there is a third-place match in round = total_rounds - 1
    $third_place_matches = array_filter($matches, function($match) use ($total_rounds) {
        return $match['round'] == ($total_rounds - 1);
    });

    $third_place_id = null;
    if (count($third_place_matches) == 1) {
        $third_place_match = array_values($third_place_matches)[0];
        if (!empty($third_place_match['winner_id'])) {
            $third_place_id = $third_place_match['winner_id'];
        }
    }

    return [
        'first_place_id' => $first_place_id,
        'second_place_id' => $second_place_id,
        'third_place_id' => $third_place_id
    ];
}

// Main Execution
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and decode the JSON payload
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['tournament_id']) || !isset($input['matches'])) {
        echo json_encode(['success' => false, 'message' => 'Missing tournament_id or matches data.']);
        exit;
    }

    $tournament_id = $input['tournament_id'];
    $matches = $input['matches'];

    if (!is_array($matches) || empty($matches)) {
        echo json_encode(['success' => false, 'message' => 'Invalid matches data.']);
        exit;
    }

    try {
        // Begin Transaction
        $conn->beginTransaction();

        // Update each match with scores and determine the winner
        foreach ($matches as $match) {
            // Validate match data
            if (
                !isset($match['match_number']) ||
                !isset($match['round']) ||
                !isset($match['player1_id']) ||
                !isset($match['player2_id']) ||
                !isset($match['player1_score']) ||
                !isset($match['player2_score'])
            ) {
                throw new Exception('Incomplete match data.');
            }

            $match_number = $match['match_number'];
            $round = $match['round'];
            $player1_id = $match['player1_id'];
            $player2_id = $match['player2_id'];
            $player1_score = $match['player1_score'];
            $player2_score = $match['player2_score'];

            // Determine the winner
            if ($player1_score > $player2_score) {
                $winner_id = $player1_id;
            } elseif ($player2_score > $player1_score) {
                $winner_id = $player2_id;
            } else {
                throw new Exception("Match {$match_number} in round {$round} has a tie. Ties are not allowed.");
            }

            // Update the bracket table
            $update_stmt = $conn->prepare('
                UPDATE bracket 
                SET player1_score = ?, player2_score = ?, winner_id = ? 
                WHERE tournament_id = ? AND round = ? AND match_number = ?
            ');
            $update_stmt->execute([
                $player1_score,
                $player2_score,
                $winner_id,
                $tournament_id,
                $round,
                $match_number
            ]);
        }

        // Determine final standings
        $standings = determine_final_standings($conn, $tournament_id);

        if (isset($standings['error'])) {
            throw new Exception($standings['error']);
        }

        $first_place_id = $standings['first_place_id'];
        $second_place_id = $standings['second_place_id'];
        $third_place_id = $standings['third_place_id'];

        // Update the tournaments table with final standings
        $update_tournament_stmt = $conn->prepare('
            UPDATE tournaments 
            SET first_place_id = ?, second_place_id = ?, third_place_id = ?
            WHERE tournament_id = ?
        ');
        $update_tournament_stmt->execute([
            $first_place_id,
            $second_place_id,
            $third_place_id,
            $tournament_id
        ]);

        // Commit Transaction
        $conn->commit();

        echo json_encode(['success' => true, 'message' => 'Bracket and standings saved successfully.']);
    } catch (Exception $e) {
        // Rollback Transaction
        $conn->rollBack();

        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
