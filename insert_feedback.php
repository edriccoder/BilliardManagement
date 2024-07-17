<?php
include 'conn.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $experience = trim($_POST['experience']);
    $feedback = trim($_POST['Feedback']);

    try {
        if (empty($name) || empty($email) || empty($experience) || empty($feedback)) {
            echo "<script>alert('All fields are required.'); window.history.back();</script>";
            exit;
        }

        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO feedback (name, email, experience, feedback) VALUES (:name, :email, :experience, :feedback)");
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':experience', $experience, PDO::PARAM_STR);
        $stmt->bindParam(':feedback', $feedback, PDO::PARAM_STR);

        // Execute the statement
        if ($stmt->execute()) {
            echo "<script>alert('New feedback recorded successfully'); window.location.href = 'feedback.php';</script>";
        }
    } catch (Exception $e) {
        // Handle other errors (if any)
        echo "<script>alert('Error: " . $e->getMessage() . "'); window.history.back();</script>";
    }
}
?>
