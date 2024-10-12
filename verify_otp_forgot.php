<?php
session_start();
include 'conn.php'; // Database connection

// Initialize error message
$errorMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $entered_otp = $_POST['otp'];
    $email = $_SESSION['email'];

    // Retrieve the OTP from the session
    $stored_otp = $_SESSION['otp'];

    // Check if the entered OTP matches the stored one
    if ($entered_otp == $stored_otp) {
        // OTP is correct, allow the user to reset their password
        header("Location: reset_password.php");
        exit();
    } else {
        // OTP is incorrect
        $errorMessage = "Invalid OTP. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Verify OTP</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/main.css">
</head>
<body>
    <div class="contact1">
        <div class="container-contact1">
            <form class="contact1-form validate-form" method="post" action="verify_otp.php">
                <span class="contact1-form-title">Verify OTP</span>

                <div class="wrap-input1 validate-input" data-validate="OTP is required">
                    <input class="input1" type="text" id="otp" name="otp" placeholder="Enter OTP" required>
                    <span class="shadow-input1"></span>
                </div>

                <div class="container-contact1-form-btn">
                    <button class="contact1-form-btn" type="submit">
                        <span>
                            Verify OTP
                            <i class="fa fa-long-arrow-right" aria-hidden="true"></i>
                        </span>
                    </button>
                </div>

                <?php if ($errorMessage): ?>
                    <div class="alert alert-danger mt-2" role="alert">
                        <?php echo $errorMessage; ?>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <script src="vendor/jquery/jquery-3.2.1.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>