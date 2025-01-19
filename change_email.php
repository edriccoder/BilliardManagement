<?php
session_start();
include 'conn.php'; // Database connection
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Adjust the path as necessary

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_email = $_POST['new_email'];
    $user_id = $_SESSION['user_id']; // Retrieve user ID from session

    // Validate email
    if (filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        // Generate OTP
        $otp = rand(100000, 999999);
        $_SESSION['otp'] = $otp;
        $_SESSION['new_email'] = $new_email;

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
            $mail->Username = ''; // Your Gmail address
            $mail->Password = ''; // Your Gmail app password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('tjamesportybar@gmail.com', 'T james');
            $mail->addAddress($new_email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Your OTP Code';
            $mail->Body = "Your OTP code is: <strong>$otp</strong>";

            $mail->send();

            // Redirect to OTP verification page
            header("Location: verify_otp.php");
            exit();
        } catch (Exception $e) {
            // Log the error for debugging
            error_log("Mailer Error: {$mail->ErrorInfo}");
            header("Location: user_profile.php?error=Mail could not be sent. Error: {$mail->ErrorInfo}");
            exit();
        }
    } else {
        // Redirect back with an error message
        header("Location: user_profile.php?error=Invalid email format.");
        exit();
    }
}
