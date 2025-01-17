<?php
include 'conn.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username'])) {
    $username = trim($_POST['username']);

    try {
        // Fetch user details
        $stmt = $conn->prepare("SELECT name, email, contact_number FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $userDetails = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userDetails) {
            echo "<p><strong>Name:</strong> " . htmlspecialchars($userDetails['name'], ENT_QUOTES, 'UTF-8') . "</p>";
            echo "<p><strong>Email:</strong> " . htmlspecialchars($userDetails['email'], ENT_QUOTES, 'UTF-8') . "</p>";
            echo "<p><strong>Contact Number:</strong> " . htmlspecialchars($userDetails['contact_number'], ENT_QUOTES, 'UTF-8') . "</p>";
        } else {
            echo "<p>No details found for this user.</p>";
        }
    } catch (PDOException $e) {
        echo "<p>Error retrieving user details: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</p>";
    }
}
?>
