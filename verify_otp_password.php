<?php
session_start();
include 'conn.php'; 

// Redirect if OTP session is not set
if (!isset($_SESSION['otp'])) {
    header("Location: index.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input_otp = $_POST['otp'];

    // Check if the input OTP matches the session OTP
    if ($input_otp == $_SESSION['otp']) {
        // OTP is correct, update password
        $new_password = $_SESSION['new_password']; // Get the new password from session
        $user_id = $_SESSION['user_id'];

        // Update the user's password (ensure to hash it)
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $query = "UPDATE users SET password = ? WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$hashed_password, $user_id]);

        // Clear session variables
        unset($_SESSION['otp']);
        unset($_SESSION['new_password']);

        // Redirect to user profile with success message
        header("Location: user_profile.php?message=Password updated successfully.");
        exit();
    } else {
        // OTP is incorrect
        $error_message = "Invalid OTP. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Verify OTP</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
    <link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="css/util.css">
    <link rel="stylesheet" type="text/css" href="css/main.css">
</head>
<body>
    <div class="contact1">
        <div class="container-contact1">
            <div class="contact1-pic js-tilt" data-tilt>
                <img src="images/img-01.png" alt="IMG">
            </div>
            <form class="contact1-form validate-form" method="post" action="">
                <span class="contact1-form-title">Verify Your OTP</span>

                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>

                <div class="wrap-input1 validate-input" data-validate="OTP is required">
                    <input class="input1" type="text" id="otp" name="otp" placeholder="Enter your OTP" required>
                    <span class="shadow-input1"></span>
                </div>

                <div class="container-contact1-form-btn">
                    <button class="contact1-form-btn">
                        <span>Verify OTP <i class="fa fa-long-arrow-right" aria-hidden="true"></i></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="vendor/jquery/jquery-3.2.1.min.js"></script>
    <script src="vendor/bootstrap/js/popper.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="vendor/tilt/tilt.jquery.min.js"></script>
    <script>
        $('.js-tilt').tilt({ scale: 1.1 });
    </script>
    <script src="js/main.js"></script>
</body>
</html>
