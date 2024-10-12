<?php
session_start();
include 'conn.php'; // Database connection

// Initialize message variables
$errorMessage = "";
$successMessage = "";

// Check if OTP is set in session
if (!isset($_SESSION['otp'])) {
    header("Location: index.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the input OTP
    $input_otp = $_POST['otp'];
    
    // Check if the OTP is correct
    if ($input_otp == $_SESSION['otp']) {
        // OTP is correct, process password reset
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        $email = $_SESSION['new_email']; // Retrieve the email from session

        // Check if passwords match
        if ($new_password !== $confirm_password) {
            $errorMessage = "Passwords do not match. Please try again.";
        } else {
            // Hash the new password using a secure hashing algorithm
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

            // Update the user's password in the database
            $query = "UPDATE users SET password = ? WHERE email = ?";
            $stmt = $conn->prepare($query);

            if ($stmt->execute([$hashed_password, $email])) {
                // Password updated successfully
                header("Location: register.php?message=Password updated successfully.");
                // Clear session variables
                unset($_SESSION['otp']);
                unset($_SESSION['new_email']);
            } else {
                $errorMessage = "There was an error updating your password. Please try again.";
            }
        }
    } else {
        // OTP is incorrect
        $errorMessage = "Invalid OTP. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reset Password</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/main.css">
</head>
<body>
    <div class="contact1">
        <div class="container-contact1">
            <form class="contact1-form validate-form" method="post" action="reset_password.php">
                <span class="contact1-form-title">Reset Your Password</span>

                <div class="wrap-input1 validate-input" data-validate="OTP is required">
                    <input class="input1" type="text" id="otp" name="otp" placeholder="Enter your OTP" required>
                    <span class="shadow-input1"></span>
                </div>

                <div class="wrap-input1 validate-input" data-validate="New password is required">
                    <input class="input1" type="password" id="new_password" name="new_password" placeholder="New Password" required>
                    <span class="shadow-input1"></span>
                </div>

                <div class="wrap-input1 validate-input" data-validate="Confirm password is required">
                    <input class="input1" type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
                    <span class="shadow-input1"></span>
                </div>

                <div class="container-contact1-form-btn">
                    <button class="contact1-form-btn" type="submit">
                        <span>
                            Update Password
                            <i class="fa fa-long-arrow-right" aria-hidden="true"></i>
                        </span>
                    </button>
                </div>

                <?php if ($errorMessage): ?>
                    <div class="alert alert-danger mt-2" role="alert">
                        <?php echo $errorMessage; ?>
                    </div>
                <?php endif; ?>

                <?php if ($successMessage): ?>
                    <div class="alert alert-success mt-2" role="alert">
                        <?php echo $successMessage; ?>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <script src="vendor/jquery/jquery-3.2.1.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
