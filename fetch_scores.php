<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['tournament_id'])) {
    $tournamentId = $_GET['tournament_id'];

    try {
        // Fetch tournament scores and join with users table to get usernames
        $stmt = $conn->prepare('
            SELECT ts.user_id, u.username, ts.scores
            FROM tournament_scores ts
            LEFT JOIN users u ON ts.user_id = u.user_id
            WHERE ts.tournament_id = ?
            ORDER BY ts.scores DESC
        ');
        $stmt->execute([$tournamentId]);
        $scores = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($scores) {
            echo json_encode(['success' => true, 'scores' => $scores]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No scores found for this tournament.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error fetching scores: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
