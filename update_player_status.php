<?php
include 'conn.php';

// Assuming JSON request with player_id and new_status
$requestBody = file_get_contents('php://input');
$data = json_decode($requestBody);

if (!$data || !isset($data->player_id) || !isset($data->new_status)) {
    echo json_encode(['success' => false, 'message' => 'Invalid request data']);
    exit;
}

$player_id = $data->player_id;
$new_status = $data->new_status;

// Update player status in the database
$stmt = $conn->prepare('UPDATE players SET status = ? WHERE player_id = ?');
$result = $stmt->execute([$new_status, $player_id]);

if ($result) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update player status']);
}
?>
