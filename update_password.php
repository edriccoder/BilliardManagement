<?php
session_start();
include 'conn.php'; // Database connection


// Process the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $email = $_SESSION['email']; // Assuming the user's email is stored in the session

    // Validate the new password
    if ($new_password !== $confirm_password) {
        header("Location: verify_otp_forgot.php?error=Passwords do not match.");
        exit();
    }

    // Hash the new password
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

    // Update the password in the database
    $query = "UPDATE users SET password = ? WHERE email = ?";
    $stmt = $conn->prepare($query);

    if ($stmt->execute([$hashed_password, $email])) {
        // Clear session variables if necessary
        unset($_SESSION['email']);
        header("Location: register.php?message=Password updated successfully.");
    } else {
        header("Location: verify_otp_forgot.php?error=Failed to update password.");
    }
    exit();
}
?>
