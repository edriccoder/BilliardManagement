<?php
session_start();
include 'conn.php'; // Ensure this includes your database connection
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Adjust the path as necessary

// Check if session variables are set
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Retrieve user data from the database
$user_id = $_SESSION['user_id'];
$query = "SELECT name, email, username, profile_pic FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$user_id]); // Execute with the parameter

// Fetch user data
$user = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch the associative array

if ($user) {
    // Store user email in session
    $_SESSION['email'] = htmlspecialchars($user['email']); // Ensure this is sanitized
    $name = htmlspecialchars($user['name']);
    $username = htmlspecialchars($user['username']);
    $profile_pic = htmlspecialchars($user['profile_pic'] ?? 'img/admin.png'); // Default image
} else {
    echo "User not found.";
    exit();
}

// Check if the user is trying to change their password
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = $_POST['new_password'];

    // Generate OTP
    $otp = rand(100000, 999999);
    $_SESSION['otp'] = $otp;
    $_SESSION['new_password'] = $new_password; // Store the new password in session

    // Insert OTP into the database
    $query = "INSERT INTO otp_codes (user_id, otp_code, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 5 MINUTE))";
    $stmt = $conn->prepare($query);
    $stmt->execute([$user_id, $otp]);

    // Send OTP via email
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; 
        $mail->SMTPAuth = true;
        $mail->Username = 'tjamesportybar@gmail.com'; // Your Gmail address
        $mail->Password = 'pycj jhmw irtv yxka'; // Your Gmail app password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('tjamesportybar@gmail.com', 'T james');
        $mail->addAddress($_SESSION['email']); // Use the email from the session

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Your OTP Code for Password Change';
        $mail->Body = "Your OTP code is: <strong>$otp</strong>";

        $mail->send();

        // Redirect to OTP verification page
        header("Location: verify_otp_password.php");
        exit();
    } catch (Exception $e) {
        error_log("Mailer Error: {$mail->ErrorInfo}");
        header("Location: user_profile.php?error=Mail could not be sent. Error: {$mail->ErrorInfo}");
        exit();
    }
}
?>
