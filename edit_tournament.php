<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tournamentId = $_POST['tournament_id'];
    $name = $_POST['name'];
    $startDate = $_POST['start_date'];  // This now contains both date and time
    $endDate = $_POST['end_date'];      // This now contains both date and time
    $maxPlayer = $_POST['max_player'];
    $prize = $_POST['prize'];
    $status = $_POST['status'];
    $fee = $_POST['fee'];

    try {
        // Update query to handle datetime fields
        $stmt = $conn->prepare('UPDATE tournaments SET name = ?, start_date = ?, end_date = ?, max_player = ?, prize = ?, status = ?, fee = ? WHERE tournament_id = ?');
        $stmt->execute([$name, $startDate, $endDate, $maxPlayer, $prize, $status, $fee, $tournamentId]);

        // Redirect back to the manage tournaments page after update
        header('Location: manage_tournament.php');
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid request.";
}
?>
