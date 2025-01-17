<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['tournament_id'])) {
    $tournamentId = $_GET['tournament_id'];

    // Start a transaction
    $conn->beginTransaction();

    try {
        // Fetch tournament details
        $stmt = $conn->prepare('SELECT start_date, end_date, start_time, end_time FROM tournaments WHERE tournament_id = ?');
        $stmt->execute([$tournamentId]);
        $tournament = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$tournament) {
            throw new Exception('Tournament not found.');
        }

        $startDate = $tournament['start_date'];
        $endDate = $tournament['end_date'];
        $startTime = $tournament['start_time'];
        $endTime = $tournament['end_time'];

        // Extract only the date part (first 10 characters)
        $startDateOnly = substr($startDate, 0, 10);
        $endDateOnly = substr($endDate, 0, 10);

        // Create DateTime objects
        $startDateTime = new DateTime($startDateOnly . ' ' . $startTime);
        $endDateTime = new DateTime($endDateOnly . ' ' . $endTime);

        // Fetch confirmed players for the tournament
        $stmt = $conn->prepare('SELECT user_id FROM players WHERE tournament_id = ? AND status = "confirmed"');
        $stmt->execute([$tournamentId]);
        $players = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (count($players) < 2) {
            throw new Exception('Not enough confirmed players to create a bracket.');
        }

        // Shuffle players to randomize matchups
        shuffle($players);

        // Calculate the number of rounds and matches
        $numPlayers = count($players);
        $numRounds = ceil(log($numPlayers, 2));
        $expectedPlayers = pow(2, $numRounds);

        // Pad players array to the next power of two
        while (count($players) < $expectedPlayers) {
            $players[] = null; // Add a bye (null player)
        }

        // Calculate the total number of matches
        $totalMatches = $expectedPlayers - 1;

        // Calculate the total tournament duration in seconds
        $totalDuration = $endDateTime->getTimestamp() - $startDateTime->getTimestamp();

        if ($totalDuration <= 0) {
            throw new Exception('Invalid tournament start and end times.');
        }

        // Calculate the time interval between matches
        $matchInterval = floor($totalDuration / $totalMatches);

        // Create matches for each round
        $matchCounter = 0;
        for ($round = 1; $round <= $numRounds; $round++) {
            $numMatchesInRound = pow(2, $numRounds - $round);
            for ($matchNumber = 1; $matchNumber <= $numMatchesInRound; $matchNumber++) {
                if ($round == 1) {
                    $player1_id = array_shift($players);
                    $player2_id = array_shift($players);
                } else {
                    $player1_id = null;
                    $player2_id = null;
                }

                // Calculate scheduled time for the match
                $scheduledTimestamp = $startDateTime->getTimestamp() + $matchCounter * $matchInterval;
                $scheduledTime = date('Y-m-d H:i:s', $scheduledTimestamp);

                // Insert match into the bracket table
                $stmt = $conn->prepare('INSERT INTO bracket (tournament_id, round, match_number, player1_id, player2_id, scheduled_time) VALUES (?, ?, ?, ?, ?, ?)');
                $stmt->execute([$tournamentId, $round, $matchNumber, $player1_id, $player2_id, $scheduledTime]);

                $matchCounter++;
            }
        }

        // Commit transaction
        $conn->commit();

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        // Roll back transaction
        $conn->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
