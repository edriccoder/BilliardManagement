<?php
// At the beginning of your PHP scripts
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/your/error.log');
error_reporting(E_ALL);
// get_bracket.php
header('Content-Type: application/json');
include 'conn.php';


try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['tournament_id'])) {
        $tournamentId = intval($_GET['tournament_id']); // Sanitize input

        // Prepare and execute the query
        $stmt = $conn->prepare('
            SELECT 
                b.bracket_id, 
                b.tournament_id, 
                b.player1_id, 
                b.player2_id, 
                b.round, 
                b.match_number, 
                b.winner_id,
                p1.username AS player1_name,
                p2.username AS player2_name,
                p3.username AS winner_name
            FROM 
                bracket b
            LEFT JOIN 
                players p1 ON b.player1_id = p1.player_id
            LEFT JOIN 
                players p2 ON b.player2_id = p2.player_id
            LEFT JOIN 
                players p3 ON b.winner_id = p3.player_id
            WHERE 
                b.tournament_id = ?
            ORDER BY 
                b.round ASC, b.match_number ASC
        ');
        $stmt->execute([$tournamentId]);
        $matches = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($matches) {
            echo json_encode(['success' => true, 'players' => $matches]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No matches found for this tournament ID.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No tournament ID received or invalid request method.']);
    }
} catch (Exception $e) {
    // Log the error message to a file or monitoring system
    error_log($e->getMessage());

    // Return a generic error message to the client
    echo json_encode(['success' => false, 'message' => 'An unexpected error occurred.']);
}
?>
