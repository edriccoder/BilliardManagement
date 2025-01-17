<?php
include ('conn.php');
session_start(); // Start session here for SweetAlert alerts to persist on redirect

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Fetch the user details from the database
    $stmt = $conn->prepare("SELECT `password`, `role`, `user_id`, `email`, `is_activated`, `archive` FROM `users` WHERE `username` = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch();
        $stored_password = $row['password'];
        $stored_role = $row['role'];
        $user_id = $row['user_id'];
        $email = $row['email'];
        $is_activated = $row['is_activated'];
        $archive = $row['archive'];

        // Check activation and archive status
        if ($is_activated == 1 && $archive == 0) {
            // Verify password
            if (password_verify($password, $stored_password)) {
                // Password matches the hashed version
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $username;
                $_SESSION['email'] = $email;
                $_SESSION['role'] = $stored_role;


                header('Location: redirect_user.php');
                exit;

            } elseif ($password === $stored_password) {
                // Plain-text password match (for non-hashed passwords, if allowed)
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $username;
                $_SESSION['email'] = $email;
                $_SESSION['role'] = $stored_role;

                header('Location: redirect_user.php');
                exit;

            } else {
                $_SESSION['alert'] = [
                    'type' => 'error',
                    'title' => 'Login Failed',
                    'message' => 'Incorrect password.'
                ];
                
                $_SESSION['show_login_form'] = true; // Set flag to show login form
                header('Location: register.php');
                exit;
            }

        } else {
            $_SESSION['alert'] = [
                'type' => 'error',
                'title' => 'Login Failed',
                'message' => 'Account not activated or archived.'
            ];
            $_SESSION['show_login_form'] = true; // Set flag to show login form
            header('Location: register.php');
            exit;
        }
        
    } else {
        $_SESSION['alert'] = [
            'type' => 'error',
            'title' => 'Login Failed',
            'message' => 'User not found.'
        ];
        $_SESSION['show_login_form'] = true; // Set flag to show login form
        header('Location: register.php');
        exit;
    }
}
?>
