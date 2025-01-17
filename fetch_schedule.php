<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['tournament_id'])) {
    $tournamentId = $_GET['tournament_id'];

    try {
        // Fetch matches for the tournament along with scheduled times
        $stmt = $conn->prepare('
            SELECT 
                b.round, 
                b.match_number, 
                b.player1_id, 
                b.player2_id, 
                b.scheduled_time,
                u1.username as player1_name,
                u2.username as player2_name
            FROM bracket b
            LEFT JOIN users u1 ON b.player1_id = u1.user_id
            LEFT JOIN users u2 ON b.player2_id = u2.user_id
            WHERE b.tournament_id = ?
            ORDER BY b.round, b.match_number
        ');
        $stmt->execute([$tournamentId]);
        $schedule = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($schedule) > 0) {
            echo json_encode(['success' => true, 'schedule' => $schedule]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No matches found for this tournament.']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'An error occurred while fetching the schedule.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
