<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

<style>
    * {
        margin: 0;
        padding: 0;
    }

    body {
        background-image: url("img/background.jpg");
        background-size: cover;
        background-repeat: no-repeat;
        background-attachment: fixed;
        height: 100vh;
    }

    .main {
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: rgba(0, 0, 0, 0.4);
        height: 100vh;
    }

    .login-container, .registration-container, .forgot-password-container {
        width: 500px;
        box-shadow: rgba(255, 255, 255, 0.24) 0px 3px 8px;
        border-radius: 10px;
        background-color: rgba(0, 0, 0, 0.7);
        padding: 30px;
        color: rgba(255, 255, 255, 0.7);
    }

    .title-container > h1 {
        font-size: 90px !important;
        color: rgb(255, 255, 255);
        text-shadow: 2px 4px 2px rgba(255, 255, 255, 0.7);
    }

    .form-group {
        display: flex;
        flex-direction: column;
        margin-bottom: 20px;
    }

    .form-group label {
        margin-bottom: 5px; /* Space between label and input */
        margin-left: 20px;
        color: white; /* Make label text white */
    }

    .form-control {
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #ccc;
        background-color: rgba(255, 255, 255, 0.1);
        color: white; /* Set input text color to white */
        transition: color 0.3s; /* Smooth transition for color change */
    }

    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.6); /* Placeholder text color */
    }

    .form-control:focus {
        border-color: #007bff; /* Change border color on focus */
        background-color: rgba(255, 255, 255, 0.2);
        color: #e0e0e0; /* Darker text color when focused */
    }

    .form-check {
        display: flex;
        align-items: center; /* Center checkbox and label vertically */
        margin-bottom: 15px; /* Space below the checkbox */
    }

    .form-check-input {
        margin-right: 8px; /* Space between checkbox and label */
        cursor: pointer; /* Change cursor on hover */
    }

    .show-form {
        cursor: pointer;
        margin-top: 10px;
        color: #007bff;
        text-decoration: underline;
    }

    .show-form:hover {
        text-decoration: none;
    }

    button.btn {
        background-color: #007bff; /* Button color */
        border: none;
        border-radius: 5px;
        padding: 10px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    button.btn:hover {
        background-color: #0056b3; /* Darker on hover */
    }

    .text-center {
        text-align: center;
        margin-bottom: 20px; /* Space below headings */
    }

    .checkbox-label-group {
        margin-left: 0px;
        display: flex;
    }

    .checkbox-label-group .form-check-input {
        cursor: pointer; /* Change cursor on hover */
    }

    .checkbox-label-group .form-check-label {
        color: white; /* Make label text white */
    }
</style>

</head>
<body>

<div class="main row">

    <div class="title-container col-6">
        <h1>T James Sporty Bar</h1>
    </div>

    <div class="main-container col-4">
        <!-- Registration Form -->
        <div class="registration-container" id="registrationForm">
            <h2 class="text-center">Register Your Account!</h2>
            <p class="text-center">Please enter your personal details.</p>
            <form action="add-user.php" method="POST">
                <div class="form-group">
                    <label for="name">Full Name:</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="registerUsername">Username:</label>
                    <input type="text" class="form-control" id="registerUsername" name="username" required>
                </div>
                <div class="form-group">
                    <label for="registerPassword">Password:</label>
                    <input type="password" class="form-control" id="registerPassword" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirmPassword">Confirm Password:</label>
                    <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                </div>
                <div class="form-group">
                    <small class="show-form" onclick="showForm('login')">Already have an account? Login Here.</small>
                </div>
                <button type="submit" class="btn form-control">Register</button>
            </form>
        </div>

        <!-- Forgot Password Form -->
        <div class="forgot-password-container" id="forgotPasswordForm" style="display:none;">
            <h2 class="text-center">Forgot Password</h2>
            <p class="text-center">Enter your email to receive OTP.</p>
            <form action="forgot_password.php" method="POST">
                <div class="form-group">
                    <label for="forgotEmail">Email:</label>
                    <input type="email" class="form-control" id="forgotEmail" name="email" required>
                </div>
                <div class="form-group">
                    <small class="show-form" onclick="showForm('login')">Remembered your password? Login Here.</small>
                    <small class="show-form" onclick="showForm('registration')">No Account? Register Here.</small>
                </div>
                <button type="submit" class="btn form-control">Send OTP</button>
            </form>
        </div>

        <!-- Login Form -->
        <div class="login-container" id="loginForm" style="display:none;">
            <h2 class="text-center">Welcome Back!</h2>
            <p class="text-center">Please enter your login details.</p>
            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="form-group checkbox-label-group">
                    <input type="checkbox" class="form-check-input" id="rememberCheck">
                    <label class="form-check-label" for="rememberCheck">Remember Password</label>
                </div>
                <div class="form-group">
                    <small class="show-form" onclick="showForm('forgot')">Forgot Password?</small>
                    <small class="show-form" onclick="showForm('registration')">No Account? Register Here.</small>
                </div>
                <button type="submit" class="btn form-control">Login</button>
            </form>
        </div>
    </div>

</div>

<!-- Bootstrap 4 JS -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>

<script>
    function showForm(form) {
        const loginForm = document.getElementById('loginForm');
        const registrationForm = document.getElementById('registrationForm');
        const forgotPasswordForm = document.getElementById('forgotPasswordForm');

        loginForm.style.display = 'none';
        registrationForm.style.display = 'none';
        forgotPasswordForm.style.display = 'none';

        if (form === 'login') {
            loginForm.style.display = 'block';
        } else if (form === 'registration') {
            registrationForm.style.display = 'block';
        } else if (form === 'forgot') {
            forgotPasswordForm.style.display = 'block';
        }
    }
</script>
</body>
</html>
