<?php
// payment_success.php

include 'conn.php';

if (isset($_GET['tournament_id']) && isset($_GET['reference_number'])) {
    $tournamentId = intval($_GET['tournament_id']);
    $referenceNumber = trim($_GET['reference_number']);

    // Update the player's status to 'confirmed' based on reference_number and tournament_id
    $stmt = $conn->prepare("UPDATE players SET status = 'confirmed' WHERE reference_number = :reference_number AND tournament_id = :tournament_id");
    $stmt->bindParam(':reference_number', $referenceNumber, PDO::PARAM_STR);
    $stmt->bindParam(':tournament_id', $tournamentId, PDO::PARAM_INT);
    $stmt->execute();

    echo "
    <script>
        alert('Payment successful! You have been registered for the tournament.');
        window.location.href = 'user_table.php';
    </script>
    ";
} else {
    echo "
    <script>
        alert('Invalid request.');
        window.location.href = 'index.php';
    </script>
    ";
}
?>
