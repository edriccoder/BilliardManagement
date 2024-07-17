<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $status = isset($_POST['status']) ? $_POST['status'] : 'upcoming'; 
    $max_player = $_POST['max_player'];
    $prize = $_POST['prize'];
    $fee = $_POST['fee'];

    $sql = "INSERT INTO tournaments (name, start_date, end_date, status, max_player, created_at, prize, fee) VALUES (:name, :start_date, :end_date, :status, :max_player, NOW(), :prize, :fee)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':start_date', $start_date);
    $stmt->bindParam(':end_date', $end_date);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':max_player', $max_player);
    $stmt->bindParam(':prize', $prize);
    $stmt->bindParam(':fee', $fee);

    if ($stmt->execute()) {
        // Redirect to a success page or display a success message
        header("Location: manage_tournament.php");
        exit();
    } else {
        // Handle error
        $errorInfo = $stmt->errorInfo();
        echo "Error: " . $errorInfo[2];
    }
}
?>