<?php
session_start();
include 'conn.php'; // Ensure this includes your database connection

// Check if the user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize input
    $user_id = $_SESSION['user_id'];
    $new_contact_number = trim($_POST['new_contact_number']);
    $current_password = trim($_POST['current_password']);

    // Validate contact number (e.g., ensure it's 10 digits)
    if (!preg_match('/^[0-9]{10}$/', $new_contact_number)) {
        // Redirect back with an error message
        header("Location: user_profile.php?error=Invalid contact number format. Please enter a 10-digit number.");
        exit();
    }

    try {
        // Fetch the user's current password from the database
        $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Verify the current password
            if (password_verify($current_password, $user['password'])) {
                // Update the contact number
                $updateStmt = $conn->prepare("UPDATE users SET contact_number = ? WHERE user_id = ?");
                $updateStmt->execute([$new_contact_number, $user_id]);

                // Redirect back with a success message
                header("Location: user_profile.php?success=Contact number updated successfully.");
                exit();
            } else {
                // Incorrect password
                header("Location: user_profile.php?error=Incorrect current password.");
                exit();
            }
        } else {
            // User not found
            header("Location: user_profile.php?error=User not found.");
            exit();
        }
    } catch (PDOException $e) {
        // Handle database errors
        header("Location: user_profile.php?error=An error occurred: " . urlencode($e->getMessage()));
        exit();
    }
} else {
    // Invalid request method
    header("Location: user_profile.php");
    exit();
}
?>
