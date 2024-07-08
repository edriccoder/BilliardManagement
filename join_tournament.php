<?php
session_start();
header('Content-Type: application/json');

include 'conn.php';

// Get the JSON input
$data = json_decode(file_get_contents('php://input'), true);

$user_id = $data['user_id'];
$username = $data['username'];
$tournament_id = $data['tournament_id'];

// Check if the user is already in the tournament
$sqlCheck = "SELECT COUNT(*) FROM players WHERE user_id = :user_id AND tournament_id = :tournament_id";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->execute(['user_id' => $user_id, 'tournament_id' => $tournament_id]);
if ($stmtCheck->fetchColumn() > 0) {
    echo json_encode(['success' => false, 'message' => 'You have already joined this tournament.']);
    exit();
}

// Check the number of current players in the tournament
$sqlCount = "SELECT COUNT(*) FROM players WHERE tournament_id = :tournament_id";
$stmtCount = $conn->prepare($sqlCount);
$stmtCount->execute(['tournament_id' => $tournament_id]);
$currentPlayers = $stmtCount->fetchColumn();

// Get the maximum number of players allowed for this tournament
$sqlMaxPlayers = "SELECT max_player FROM tournaments WHERE tournament_id = :tournament_id";
$stmtMaxPlayers = $conn->prepare($sqlMaxPlayers);
$stmtMaxPlayers->execute(['tournament_id' => $tournament_id]);
$maxPlayers = $stmtMaxPlayers->fetchColumn();

if ($currentPlayers >= $maxPlayers) {
    echo json_encode(['success' => false, 'message' => 'The tournament is full.']);
    exit();
}

// Insert the user into the players table
$sqlInsert = "INSERT INTO players (user_id, tournament_id, username) VALUES (:user_id, :tournament_id, :username)";
$stmtInsert = $conn->prepare($sqlInsert);
$stmtInsert->execute(['user_id' => $user_id, 'tournament_id' => $tournament_id, 'username' => $username]);

echo json_encode(['success' => true, 'message' => 'Joined the tournament successfully.']);
?>