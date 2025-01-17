<?php
header('Content-Type: application/json'); // Set header for JSON response

include 'conn.php'; // Include your database connection

// Get the raw POST data
$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

// Check if all required fields are present
if (
    isset($data['tournament_id']) && 
    isset($data['round']) && 
    isset($data['match']) && 
    isset($data['winner_id']) &&
    isset($data['player1_id']) &&
    isset($data['player2_id']) &&
    isset($data['player1_score']) &&
    isset($data['player2_score'])
) {
    $tournamentId = $data['tournament_id'];
    $round = intval($data['round']);
    $matchNumber = intval($data['match']);
    $winnerId = $data['winner_id'];
    $player1Id = $data['player1_id'];
    $player2Id = $data['player2_id'];
    $player1Score = floatval($data['player1_score']);
    $player2Score = floatval($data['player2_score']);

    // Determine the loser's ID
    $loserId = ($winnerId == $player1Id) ? $player2Id : $player1Id;
    $winnerScore = ($winnerId == $player1Id) ? $player1Score : $player2Score;
    $loserScore = ($winnerId == $player1Id) ? $player2Score : $player1Score;

    // Begin transaction
    $conn->beginTransaction();

    try {
        // Validate that winner's score is greater than loser's score
        if ($winnerScore <= $loserScore) {
            throw new Exception("Winner's score must be greater than the loser's score.");
        }

        // Update the winner in the bracket
        $stmt = $conn->prepare('UPDATE bracket SET winner_id = ? WHERE tournament_id = ? AND round = ? AND match_number = ?');
        $result = $stmt->execute([$winnerId, $tournamentId, $round, $matchNumber]);

        if (!$result) {
            throw new Exception('Failed to update the winner in the bracket.');
        }

        // Calculate next match number
        $nextRound = $round + 1;
        $nextMatchNumber = ceil($matchNumber / 2);

        // Determine if the winner should be player1 or player2 in the next match
        $playerPosition = ($matchNumber % 2 == 1) ? 'player1_id' : 'player2_id';

        // Update the next match with the winner's ID
        $stmt = $conn->prepare("UPDATE bracket SET $playerPosition = ? WHERE tournament_id = ? AND round = ? AND match_number = ?");
        $stmt->execute([$winnerId, $tournamentId, $nextRound, $nextMatchNumber]);

        // Update tournament_scores for Player 1
        $stmt = $conn->prepare('SELECT scores FROM tournament_scores WHERE tournament_id = ? AND user_id = ?');
        $stmt->execute([$tournamentId, $player1Id]);
        $scoreEntry1 = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($scoreEntry1) {
            // Update the score
            $stmt = $conn->prepare('UPDATE tournament_scores SET scores = scores + ? WHERE tournament_id = ? AND user_id = ?');
            $stmt->execute([$player1Score, $tournamentId, $player1Id]);
        } else {
            // Insert a new score entry
            $stmt = $conn->prepare('INSERT INTO tournament_scores (tournament_id, user_id, scores) VALUES (?, ?, ?)');
            $stmt->execute([$tournamentId, $player1Id, $player1Score]);
        }

        // Update tournament_scores for Player 2
        $stmt = $conn->prepare('SELECT scores FROM tournament_scores WHERE tournament_id = ? AND user_id = ?');
        $stmt->execute([$tournamentId, $player2Id]);
        $scoreEntry2 = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($scoreEntry2) {
            // Update the score
            $stmt = $conn->prepare('UPDATE tournament_scores SET scores = scores + ? WHERE tournament_id = ? AND user_id = ?');
            $stmt->execute([$player2Score, $tournamentId, $player2Id]);
        } else {
            // Insert a new score entry
            $stmt = $conn->prepare('INSERT INTO tournament_scores (tournament_id, user_id, scores) VALUES (?, ?, ?)');
            $stmt->execute([$tournamentId, $player2Id, $player2Score]);
        }

        // Fetch tournament name
        $stmt = $conn->prepare('SELECT name FROM tournaments WHERE tournament_id = ?');
        $stmt->execute([$tournamentId]);
        $tournamentName = $stmt->fetch(PDO::FETCH_ASSOC)['name'] ?? 'Tournament';

        // Insert into announcements table about the match winner with scores
        $stmt = $conn->prepare('SELECT username FROM users WHERE user_id = ?');
        $stmt->execute([$winnerId]);
        $winnerName = $stmt->fetch(PDO::FETCH_ASSOC)['username'] ?? 'Unknown Player';

        $stmt = $conn->prepare('SELECT username FROM users WHERE user_id = ?');
        $stmt->execute([$loserId]);
        $loserName = $stmt->fetch(PDO::FETCH_ASSOC)['username'] ?? 'Unknown Player';

        // Set expiration time to 1 hour from now
        $createdAt = date('Y-m-d H:i:s');
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Determine if this is the final match
        $stmt = $conn->prepare('SELECT COUNT(*) AS total_matches FROM bracket WHERE tournament_id = ? AND round = ?');
        $stmt->execute([$tournamentId, $nextRound]);
        $nextRoundMatches = $stmt->fetch(PDO::FETCH_ASSOC)['total_matches'];

        if ($nextRoundMatches == 0) {
            // This was the final match, announce the tournament winner
            $tournamentWinnerName = $winnerName; // Already fetched

            // Create a final announcement including the winner and final score
            $finalAnnouncementTitle = "$tournamentName Winner Announced";
            $finalAnnouncementBody = "Tournament Name: $tournamentName\n"
                                   . "Round: Final Match\n"
                                   . "Players: $winnerName vs $loserName\n"
                                   . "Score: $winnerScore-$loserScore\n"
                                   . "Winner: $tournamentWinnerName (has won the tournament in the final match).";

            // Insert the final announcement into the announcements table
            $stmt = $conn->prepare('INSERT INTO announcements (title, body, tournament_id, round, created_at, expires_at) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([$finalAnnouncementTitle, $finalAnnouncementBody, $tournamentId, $nextRound, $createdAt, $expiresAt]);

            // Commit the transaction before announcing
            $conn->commit();

            // Return a success message with the final announcement
            echo json_encode([
                'success' => true, 
                'message' => "$tournamentWinnerName has won the $tournamentName with a final score of $winnerScore to $loserScore!",
                'announcement' => [
                    'title' => $finalAnnouncementTitle,
                    'body' => $finalAnnouncementBody
                ]
            ]);
            exit();
        }

        // Create a match announcement
        $announcementTitle = "Round $round Match Winner";
        $announcementBody = "Tournament Name: $tournamentName\n"
                         . "Round: $round\n"
                         . "Players: $winnerName vs $loserName\n"
                         . "Score: $winnerScore-$loserScore\n"
                         . "Winner: $winnerName (has won in match $matchNumber).";

        // Insert the announcement into the announcements table
        $stmt = $conn->prepare('INSERT INTO announcements (title, body, tournament_id, round, created_at, expires_at) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$announcementTitle, $announcementBody, $tournamentId, $round, $createdAt, $expiresAt]);

        // Commit transaction
        $conn->commit();

        // Return a success message
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        // Rollback transaction in case of an error
        $conn->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request parameters.']);
}
