<?php
header('Content-Type: application/json');
include 'conn.php';
$input = json_decode(file_get_contents('php://input'), true);
$orderID = isset($input['orderID']) ? htmlspecialchars($input['orderID']) : '';
$username = isset($input['username']) ? trim($input['username']) : '';
$tournamentId = isset($input['tournament_id']) ? intval($input['tournament_id']) : 0;

if (empty($orderID)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid data: Missing order ID.']);
    exit();
}

if (empty($username) || $tournamentId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid data: Missing username or tournament ID.']);
    exit();
}

try {
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Username not found.']);
        exit();
    }

    $userId = $user['user_id'];

    $stmt = $conn->prepare("INSERT INTO players (user_id, username, tournament_id, proof_of_payment, status) VALUES (:user_id, :username, :tournament_id, :proof_of_payment, 'completed')");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':tournament_id', $tournamentId, PDO::PARAM_INT);
    $stmt->bindParam(':proof_of_payment', $orderID, PDO::PARAM_STR);
    $stmt->execute();

    echo json_encode(['success' => true, 'message' => 'Successfully registered for the tournament.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>

