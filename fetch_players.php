<?php
// Include the database connection
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['tournament_id'])) {
    $tournamentId = $_GET['tournament_id'];
    $stmt = $conn->prepare('SELECT user_id, username FROM players WHERE tournament_id = ?');
    $stmt->execute([$tournamentId]);
    $players = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($players)) {
        error_log("No players found for tournament_id: " . $tournamentId);
    } else {
        error_log("Players found for tournament_id: " . $tournamentId);
    }

    echo json_encode($players);
} else {
    error_log("No tournament_id received");
    echo json_encode([]);
}
?>
