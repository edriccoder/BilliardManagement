<?php
include 'conn.php';

if (isset($_GET['tournament_id'])) {
    $tournamentId = $_GET['tournament_id'];

    try {
        $stmt = $conn->prepare('DELETE FROM tournaments WHERE tournament_id = ?');
        $stmt->execute([$tournamentId]);

        header('Location: manage_tournament.php');  // Redirect to the main page after deletion
        exit();
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {  // Integrity constraint violation
            $error_message = "Error: Cannot delete this tournament because there are players associated with it.";
        } else {
            $error_message = "Error: " . $e->getMessage();
        }
        header("Location: manage_tournament.php?error=" . urlencode($error_message));
        exit();
    }
} else {
    $error_message = "No tournament ID provided.";
    header("Location: manage_tournament.php?error=" . urlencode($error_message));
    exit();
}
?>