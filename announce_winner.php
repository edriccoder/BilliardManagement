<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['winner_name']) && isset($_POST['tournament_id']) && isset($_POST['round'])) {
    $winnerName = $_POST['winner_name'];
    $tournamentId = $_POST['tournament_id'];
    $round = $_POST['round'];

    // Get the tournament name
    $stmt = $conn->prepare('SELECT name FROM tournaments WHERE tournament_id = ?');
    $stmt->execute([$tournamentId]);
    $tournamentName = $stmt->fetchColumn();

    if (!$tournamentName) {
        echo json_encode(['success' => false, 'message' => 'Tournament not found.']);
        exit;
    }

    $title = "Tournament Winner Announced!";
    $body = "{$winnerName} has won the {$tournamentName} tournament!";

    // Insert the announcement into the database with expiration time of 1 minute
    $stmt = $conn->prepare('
        INSERT INTO announcements (title, body, tournament_id, round, created_at, expires_at) 
        VALUES (?, ?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 1 MINUTE))
    ');
    $result = $stmt->execute([$title, $body, $tournamentId, $round]);

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to insert announcement into database.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
