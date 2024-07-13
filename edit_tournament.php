<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tournamentId = $_POST['tournament_id'];
    $name = $_POST['name'];
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];
    $maxPlayer = $_POST['max_player'];
    $prize = $_POST['prize'];
    $status = $_POST['status'];

    try {
        $stmt = $conn->prepare('UPDATE tournaments SET name = ?, start_date = ?, end_date = ?, max_player = ?, prize = ?, status = ? WHERE tournament_id = ?');
        $stmt->execute([$name, $startDate, $endDate, $maxPlayer, $prize, $status, $tournamentId]);

        header('Location: manage_tournament.php');  // Redirect to the main page after update
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid request.";
}
?>