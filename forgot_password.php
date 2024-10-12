<?php
session_start();
include 'conn.php'; // Database connection
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Adjust the path as necessary

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email']; // User email

    // Validate the email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: error.php?error=Invalid email format.");
        exit();
    }

    // Generate OTP
    $otp = rand(100000, 999999); // 6-digit OTP
    $_SESSION['otp'] = $otp;
    $_SESSION['new_email'] = $email; // Store email for password reset

    // Find the user_id based on the email
    $query = "SELECT user_id FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $user_id = $user['user_id'];

        // Insert OTP into the database
        $query = "INSERT INTO otp_codes (user_id, email, otp_code, expires_at) VALUES (?, ?, ?, DATE_ADD(NOW(), INTERVAL 5 MINUTE))";
        $stmt = $conn->prepare($query);

        if ($stmt->execute([$user_id, $email, $otp])) {
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
                $mail->setFrom('tjamesportybar@gmail.com', 'OTP Verification');
                $mail->addAddress($email);

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Your OTP Code';
                $mail->Body = "Your OTP code is: <strong>$otp</strong>";

                $mail->send();

                // Redirect to the same page to handle OTP and password reset
                header("Location: reset_password.php");
                exit();
            } catch (Exception $e) {
                error_log("Mailer Error: {$mail->ErrorInfo}");
                header("Location: error.php?error=Mail could not be sent.");
                exit();
            }
        } else {
            header("Location: error.php?error=Could not save OTP to database.");
            exit();
        }
    } else {
        // Handle case where email is not found
        header("Location: error.php?error=Email not found.");
        exit();
    }
}
?>
