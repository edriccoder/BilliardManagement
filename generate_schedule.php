<?php
// generate_schedule.php

// Error Handling Configuration
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log'); // Replace with the actual path to your error log file

header('Content-Type: application/json');
include 'conn.php';

// Read the input data
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($input['tournament_id'])) {
    echo json_encode(['success' => false, 'message' => 'Tournament ID is missing.']);
    exit;
}

$tournament_id = $input['tournament_id'];

try {
    // Check if schedule already exists
    $stmt_check = $conn->prepare("SELECT COUNT(*) AS count FROM tournament_schedule WHERE tournament_id = :tournament_id");
    $stmt_check->bindParam(':tournament_id', $tournament_id, PDO::PARAM_INT);
    $stmt_check->execute();
    $result_check = $stmt_check->fetch(PDO::FETCH_ASSOC);

    if ($result_check['count'] > 0) {
        echo json_encode(['success' => false, 'message' => 'Schedule already exists for this tournament.']);
        exit;
    }

    // Fetch tournament details
    $stmt_tournament = $conn->prepare("SELECT start_date, start_time, end_date, end_time FROM tournaments WHERE tournament_id = :tournament_id");
    $stmt_tournament->bindParam(':tournament_id', $tournament_id, PDO::PARAM_INT);
    $stmt_tournament->execute();
    $tournament = $stmt_tournament->fetch(PDO::FETCH_ASSOC);

    if (!$tournament) {
        echo json_encode(['success' => false, 'message' => 'Tournament not found.']);
        exit;
    }

    // Extract only the date part from start_date and end_date
    $start_date_only = (new DateTime($tournament['start_date']))->format('Y-m-d');
    $end_date_only = (new DateTime($tournament['end_date']))->format('Y-m-d');

    // Combine date with start_time and end_time to create DateTime objects
    $start_datetime_str = $start_date_only . ' ' . $tournament['start_time'];
    $end_datetime_str = $end_date_only . ' ' . $tournament['end_time'];

    $start_datetime = new DateTime($start_datetime_str);
    $end_datetime = new DateTime($end_datetime_str);

    // Calculate total duration in minutes
    $interval = $start_datetime->diff($end_datetime);
    $total_duration_minutes = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;

    if ($total_duration_minutes <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid tournament start and end times.']);
        exit;
    }

    // Fetch all confirmed players for the tournament
    $stmt_players = $conn->prepare("SELECT user_id FROM players WHERE tournament_id = :tournament_id AND status = 'confirmed'");
    $stmt_players->bindParam(':tournament_id', $tournament_id, PDO::PARAM_INT);
    $stmt_players->execute();
    $players = $stmt_players->fetchAll(PDO::FETCH_ASSOC);

    $player_ids = array_column($players, 'user_id');
    $num_players = count($player_ids);

    if ($num_players < 2) {
        echo json_encode(['success' => false, 'message' => 'Not enough players to generate a schedule.']);
        exit;
    }

    // Shuffle players for random matchups
    shuffle($player_ids);

    // Determine the number of rounds (single elimination)
    $rounds = ceil(log($num_players, 2));

    // Calculate total matches
    $total_matches = pow(2, $rounds) - 1;

    // Initialize matches array
    $matches = [];
    for ($i = 1; $i <= $total_matches; $i++) {
        $matches[] = [
            'match_number' => $i,
            'player1_id' => null,
            'player2_id' => null,
            'scheduled_time' => null,
            'status' => 'pending',
            'round' => ceil(log($i + 1, 2)) // Simple round calculation
        ];
    }

    // Assign players to first round matches
    $first_round_matches = pow(2, $rounds - 1);
    for ($i = 0; $i < $first_round_matches; $i++) {
        if (isset($player_ids[$i * 2])) {
            $matches[$i]['player1_id'] = $player_ids[$i * 2];
        }
        if (isset($player_ids[$i * 2 + 1])) {
            $matches[$i]['player2_id'] = $player_ids[$i * 2 + 1];
        }
    }

    // Calculate duration per match
    $duration_per_match = floor($total_duration_minutes / $total_matches); // in minutes

    if ($duration_per_match <= 0) {
        echo json_encode(['success' => false, 'message' => 'Not enough time allocated for each match. Please extend the tournament duration.']);
        exit;
    }

    // Assign scheduled_time to each match
    foreach ($matches as &$match) {
        $match_number = $match['match_number'];
        $scheduled_time = clone $start_datetime;
        $scheduled_time->modify("+".(($match_number -1) * $duration_per_match)." minutes");
        
        // Ensure that scheduled_time does not exceed end_datetime
        if ($scheduled_time > $end_datetime) {
            echo json_encode(['success' => false, 'message' => 'Not enough time to schedule all matches within the tournament duration.']);
            exit;
        }

        $match['scheduled_time'] = $scheduled_time->format('Y-m-d H:i:s');
    }
    unset($match); // Break the reference

    // Insert matches into the database
    $stmt_insert = $conn->prepare("INSERT INTO tournament_schedule (tournament_id, match_number, player1_id, player2_id, scheduled_time, status, round) 
                                   VALUES (:tournament_id, :match_number, :player1_id, :player2_id, :scheduled_time, :status, :round)");

    foreach ($matches as $match) {
        // Handle null player IDs
        $player1_id = $match['player1_id'] ? $match['player1_id'] : null;
        $player2_id = $match['player2_id'] ? $match['player2_id'] : null;

        // Bind parameters
        $stmt_insert->bindParam(':tournament_id', $tournament_id, PDO::PARAM_INT);
        $stmt_insert->bindParam(':match_number', $match['match_number'], PDO::PARAM_INT);
        $stmt_insert->bindParam(':player1_id', $player1_id, PDO::PARAM_INT);
        $stmt_insert->bindParam(':player2_id', $player2_id, PDO::PARAM_INT);
        $stmt_insert->bindParam(':scheduled_time', $match['scheduled_time'], PDO::PARAM_STR);
        $stmt_insert->bindParam(':status', $match['status'], PDO::PARAM_STR);
        $stmt_insert->bindParam(':round', $match['round'], PDO::PARAM_INT);
        $stmt_insert->execute();
    }

    echo json_encode(['success' => true, 'message' => 'Schedule generated successfully.']);
} catch (PDOException $e) {
    // Log the detailed error on the server
    error_log('Error generating schedule: ' . $e->getMessage());

    // Return a generic error message to the client
    echo json_encode(['success' => false, 'message' => 'An error occurred while generating the schedule. Please try again later.']);
}
?>
