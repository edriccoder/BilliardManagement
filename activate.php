<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('conn.php');
session_start();

if (isset($_GET['code'])) {
    $activationCode = $_GET['code'];

    try {
        // Check if the activation code exists and is not activated
        $stmt = $conn->prepare("SELECT `user_id` FROM `users` WHERE `activation_code` = :activationCode AND `is_activated` = 0");
        $stmt->execute(['activationCode' => $activationCode]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Update the user to set as activated
            $updateStmt = $conn->prepare("UPDATE `users` SET `is_activated` = 1, `activation_code` = NULL WHERE `user_id` = :user_id");
            $updateStmt->execute(['user_id' => $user['user_id']]);

            $_SESSION['alert'] = [
                'type' => 'success',
                'title' => 'Account Activated!',
                'message' => 'Your account is now active. You can log in.'
            ];
            $_SESSION['show_login_form'] = true; // Set flag to show login form
            header('Location: register.php');
            exit;
        } else {
            // If no user found with that activation code
            $_SESSION['alert'] = [
                'type' => 'error',
                'title' => 'Activation Failed!',
                'message' => 'Invalid or expired activation link.'
            ];
            header('Location: register.php');
            exit;
        }
    } catch (PDOException $e) {
        // Log the database error and notify the user
        error_log("Database error: " . $e->getMessage(), 3, "error.log");
        $_SESSION['alert'] = [
            'type' => 'error',
            'title' => 'Database Error',
            'message' => 'An error occurred. Please try again later.'
        ];
        header('Location: register.php');
        exit;
    }
} else {
    // Redirect if no activation code is provided
    header('Location: register.php');
    exit;
}
?>
