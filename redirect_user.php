<?php
session_start();

// Check if the user is logged in by verifying session variables
if (isset($_SESSION['username']) && isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    $username = htmlspecialchars($_SESSION['username']);
    $user_id = htmlspecialchars($_SESSION['user_id']);
    $role = htmlspecialchars($_SESSION['role']);

    // Display SweetAlert for successful login before rendering anything else
    echo '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <title>Redirecting...</title>
    </head>
    <body>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                title: "Login Successful",
                text: "Welcome, ' . $username . '! You will now be redirected to your dashboard.",
                icon: "success",
                confirmButtonText: "Continue"
            }).then((result) => {
                if (result.isConfirmed) {';

                // Based on the user role, redirect to the appropriate dashboard
                if ($role == 'admin') {
                    echo 'window.location.href = "admin_dashboard.php?username=' . urlencode($username) . '&user_id=' . urlencode($user_id) . '";';
                } elseif ($role == 'user') {
                    echo 'window.location.href = "user_dashboard.php?username=' . urlencode($username) . '&user_id=' . urlencode($user_id) . '";';
                } elseif ($role == 'cashier') {
                    echo 'window.location.href = "cashier_dashboard.php?username=' . urlencode($username) . '&user_id=' . urlencode($user_id) . '";';
                } else {
                    // Redirect to a default or error page if the role is not recognized
                    echo 'window.location.href = "error_page.php";';
                }

    echo '       }
            });
        });
    </script>
    </body>
    </html>
    ';
} else {
    // Redirect to login page if session variables are not set
    header("Location: index.php");
    exit();
}
?>
