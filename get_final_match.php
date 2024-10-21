<?php
// get_final_match.php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['tournament_id'])) {
    $tournamentId = $_GET['tournament_id'];

    // Fetch the finals match
    $stmt = $conn->prepare('SELECT b.match_id, b.round, b.player1_id, b.player2_id, p1.username AS player1_name, p2.username AS player2_name, b.winner_id, p3.username AS winner_name
                            FROM bracket b
                            LEFT JOIN players p1 ON b.player1_id = p1.player_id
                            LEFT JOIN players p2 ON b.player2_id = p2.player_id
                            LEFT JOIN players p3 ON b.winner_id = p3.player_id
                            WHERE b.tournament_id = ? AND b.round = "Finals"');
    $stmt->execute([$tournamentId]);
    $match = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($match) {
        echo json_encode(['success' => true, 'match' => $match]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Finals match not found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
