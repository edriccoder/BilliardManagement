<?php
// get_bracket.php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['tournament_id'])) {
    $tournamentId = $_GET['tournament_id'];

    // Fetch all matches for the tournament
    $stmt = $conn->prepare('SELECT b.match_id, b.round, b.player1_id, b.player2_id, p1.username AS player1_name, p2.username AS player2_name, b.winner_id, p3.username AS winner_name, p1.region
                            FROM bracket b
                            LEFT JOIN players p1 ON b.player1_id = p1.player_id
                            LEFT JOIN players p2 ON b.player2_id = p2.player_id
                            LEFT JOIN players p3 ON b.winner_id = p3.player_id
                            WHERE b.tournament_id = ? ORDER BY b.round ASC, b.match_number ASC');
    $stmt->execute([$tournamentId]);
    $matches = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($matches) {
        echo json_encode(['success' => true, 'players' => $matches]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No matches found for this tournament ID.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No tournament ID received.']);
}
?>
