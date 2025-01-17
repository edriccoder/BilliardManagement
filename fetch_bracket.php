<?php
include 'conn.php';

// Set the content type to JSON
header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['tournament_id'])) {
        $tournamentId = $_GET['tournament_id'];

        // Fetch tournament details
        $stmt = $conn->prepare('SELECT status, winner_id FROM tournaments WHERE tournament_id = ?');
        $stmt->execute([$tournamentId]);
        $tournament = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$tournament) {
            echo json_encode(['success' => false, 'message' => 'Tournament not found.']);
            exit;
        }

        // Fetch matches for the tournament, ordered by round
        $stmt = $conn->prepare('
            SELECT b.match_id, b.round, b.player1_id, b.player2_id, b.winner_id, b.parent_match_id,
                   u1.username AS player1_name, u2.username AS player2_name
            FROM bracket b
            LEFT JOIN players p1 ON b.player1_id = p1.player_id
            LEFT JOIN users u1 ON p1.user_id = u1.user_id
            LEFT JOIN players p2 ON b.player2_id = p2.player_id
            LEFT JOIN users u2 ON p2.user_id = u2.user_id
            WHERE b.tournament_id = ?
            ORDER BY b.round ASC, b.match_id ASC
        ');
        $stmt->execute([$tournamentId]);
        $matches = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($matches) {
            echo json_encode(['success' => true, 'bracket' => $matches, 'tournament' => $tournament]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No matches found.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    }
} catch (PDOException $e) {
    // Return an error message with details from the exception
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching tournament details: ' . $e->getMessage()
    ]);
    exit;
}
?>
