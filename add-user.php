<?php
include('conn.php');
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function sendActivationEmail($email, $activationCode) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'tjamesportybar@gmail.com'; // SMTP username
        $mail->Password = 'pycj jhmw irtv yxka'; // SMTP password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('no-reply@tjamessportybar.com', 'T James Sporty Bar');
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Account Activation Required';
        $activationLink = "https://tjamessportybar.com/BilliardManagement/activate.php?code=" . urlencode($activationCode);
        $mail->Body = "<p>Hello,</p><p>Thank you for registering. Please click the link below to activate your account:</p><p><a href='$activationLink'>$activationLink</a></p><p>If you did not request this, please ignore this email.</p>";

        $mail->send();
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
    }
}

if (isset($_POST['firstName'], $_POST['lastName'], $_POST['email'], $_POST['username'], $_POST['password'], $_POST['confirmPassword'], $_POST['number'])) {
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $name = $firstName . ' ' . $lastName;
    $email = trim($_POST['email']);
    $role = 'user';
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $contactNumber = trim($_POST['number']);

    // Check if passwords match
    if ($password !== $confirmPassword) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'title' => 'Oops...',
            'message' => 'Passwords do not match!'
        ];
        header('Location: register.php');
        exit;
    }

    try {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT `email` FROM `users` WHERE `email` = :email");
        $stmt->execute(['email' => $email]);
        $emailExists = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($emailExists) {
            $_SESSION['alert'] = [
                'type' => 'info',
                'title' => 'Account Already Exists!',
                'message' => 'An account with this email already exists. Please use a different email.'
            ];
            header('Location: register.php');
            exit;
        }

        // Check if username already exists
        $stmt = $conn->prepare("SELECT `username` FROM `users` WHERE `username` = :username");
        $stmt->execute(['username' => $username]);
        $usernameExists = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usernameExists) {
            $_SESSION['alert'] = [
                'type' => 'info',
                'title' => 'Username Already Taken!',
                'message' => 'The chosen username is already in use. Please use a different username.'
            ];
            header('Location: register.php');
            exit;
        }

        // If both email and username are unique, proceed with registration
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $activationCode = bin2hex(random_bytes(32));

        $insertStmt = $conn->prepare("INSERT INTO `users` (`name`, `email`, `role`, `username`, `password`, `contact_number`, `is_activated`, `activation_code`) VALUES (:name, :email, :role, :username, :password, :contactNumber, 0, :activationCode)");
        $insertStmt->bindParam(':name', $name);
        $insertStmt->bindParam(':email', $email);
        $insertStmt->bindParam(':role', $role);
        $insertStmt->bindParam(':username', $username);
        $insertStmt->bindParam(':password', $hashedPassword);
        $insertStmt->bindParam(':contactNumber', $contactNumber);
        $insertStmt->bindParam(':activationCode', $activationCode);

        if ($insertStmt->execute()) {
            sendActivationEmail($email, $activationCode);

            $_SESSION['alert'] = [
                'type' => 'success',
                'title' => 'Registered Successfully!',
                'message' => 'Please check your email to activate your account.'
            ];
            header('Location: register.php');
            exit;
        }
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        $_SESSION['alert'] = [
            'type' => 'error',
            'title' => 'Database Error',
            'message' => 'An error occurred. Please try again later.'
        ];
        header('Location: register.php');
        exit;
    }
}
?>
