<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['tournament_id'])) {
    $tournamentId = $_GET['tournament_id'];

    // Fetch matches and their details
    $stmt = $conn->prepare('
        SELECT b.*, 
               u1.username AS player1_name, 
               u2.username AS player2_name, 
               uw.username AS winner_name
        FROM bracket b
        LEFT JOIN users u1 ON b.player1_id = u1.user_id
        LEFT JOIN users u2 ON b.player2_id = u2.user_id
        LEFT JOIN users uw ON b.winner_id = uw.user_id
        WHERE b.tournament_id = ?
        ORDER BY b.round, b.match_number
    ');
    $stmt->execute([$tournamentId]);
    $matches = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($matches) {
        echo json_encode(['success' => true, 'matches' => $matches]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No matches found for this tournament ID.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No tournament ID received.']);
}
?>
