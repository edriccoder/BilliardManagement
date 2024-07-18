<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['tournament_id'])) {
    $tournamentId = $_GET['tournament_id'];

    $stmt = $conn->prepare('SELECT player_id, user_id, username, proof_of_payment, status FROM players WHERE tournament_id = ?');
    $stmt->execute([$tournamentId]);
    $players = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($players) {
        echo json_encode(['success' => true, 'players' => $players]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No players found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No tournament ID received.']);
}
?>
