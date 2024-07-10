<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['tournament_id'])) {
    $tournamentId = $_GET['tournament_id'];

    // Fetch players in the specified tournament
    $stmt = $conn->prepare('SELECT * FROM players WHERE tournament_id = ?');
    $stmt->execute([$tournamentId]);
    $players = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($players) {
        echo json_encode(['success' => true, 'players' => $players]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No players found for this tournament ID.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No tournament ID received.']);
}
?>
