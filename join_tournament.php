<?php
session_start();
header('Content-Type: application/json');

include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $username = $_POST['username'];
    $tournament_id = $_POST['tournament_id'];
    $status = 'pending';

    if (isset($_FILES['proof_of_payment'])) {
        $proofOfPayment = $_FILES['proof_of_payment'];
        $uploadDir = 'payments/';
        $uploadFile = $uploadDir . basename($proofOfPayment['name']);

        if (move_uploaded_file($proofOfPayment['tmp_name'], $uploadFile)) {
            $sqlInsert = "INSERT INTO players (user_id, tournament_id, username, proof_of_payment, status) VALUES (:user_id, :tournament_id, :username, :proof_of_payment, :status)";
            $stmtInsert = $conn->prepare($sqlInsert);

            try {
                $stmtInsert->execute([
                    'user_id' => $user_id,
                    'tournament_id' => $tournament_id,
                    'username' => $username,
                    'proof_of_payment' => $uploadFile,
                    'status' => $status
                ]);
                echo json_encode(['success' => true, 'message' => 'Joined the tournament successfully.']);
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to upload proof of payment.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Proof of payment not provided.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
