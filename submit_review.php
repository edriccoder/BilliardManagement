<?php
// submit_review.php
session_start();
require 'conn.php'; // Assuming you saved your connection script as db_connection.php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'User not authenticated.']);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $rating_service = intval($_POST['rating_service']);
    $rating_facilities = intval($_POST['rating_facilities']);
    $rating_tournaments = intval($_POST['rating_tournaments']);
    $comments = trim($_POST['comments']);

    // Validate ratings
    if ($rating_service < 1 || $rating_service > 5 ||
        $rating_facilities < 1 || $rating_facilities > 5 ||
        $rating_tournaments < 1 || $rating_tournaments > 5) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid rating values.']);
        exit;
    }

    // Insert the review into the database
    $stmt = $conn->prepare("INSERT INTO reviews (user_id, rating_service, rating_facilities, rating_tournaments, comments) VALUES (:user_id, :rating_service, :rating_facilities, :rating_tournaments, :comments)");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':rating_service', $rating_service);
    $stmt->bindParam(':rating_facilities', $rating_facilities);
    $stmt->bindParam(':rating_tournaments', $rating_tournaments);
    $stmt->bindParam(':comments', $comments);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Review submitted successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to submit review.']);
    }
}
?>
