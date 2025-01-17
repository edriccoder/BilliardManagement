<?php
include 'conn.php';

try {
    // SQL query to join reviews with users to get the username
    $sql = "SELECT reviews.id, users.username, reviews.rating_service, reviews.rating_facilities, 
                   reviews.rating_tournaments, reviews.comments, reviews.created_at
            FROM reviews
            JOIN users ON reviews.user_id = users.user_id";
    
    // Prepare and execute the query
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    
    // Fetch all results as an associative array
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Set the content type to JSON and output the reviews
    header('Content-Type: application/json');
    echo json_encode($reviews);
} catch (PDOException $e) {
    echo json_encode(["error" => "Failed to retrieve reviews: " . $e->getMessage()]);
}
?>
