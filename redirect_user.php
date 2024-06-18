<?php
session_start();

if (isset($_SESSION['username']) && isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    $username = $_SESSION['username'];
    $user_id = $_SESSION['user_id'];
    $role = $_SESSION['role'];

    // Based on the user role, redirect to the appropriate dashboard
    if ($role == 'admin') {
        header("Location: admin_dashboard.php?username=" . urlencode($username) . "&user_id=" . urlencode($user_id));
        exit();
    } elseif ($role == 'user') {
        header("Location: user_dashboard.php?username=" . urlencode($username) . "&user_id=" . urlencode($user_id));
        exit();
    } elseif ($role == 'cashier') {
        header("Location: cashier_dashboard.php?username=" . urlencode($username) . "&user_id=" . urlencode($user_id));
        exit();
    } else {
        // Redirect to a default or error page if the role is not recognized
        header("Location: error_page.php");
        exit();
    }
} else {
    // Redirect to login page if session variables are not set
    header("Location: index.php");
    exit();
}
