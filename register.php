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
            overflow: hidden;
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
            color: rgb(255, 255, 255);
        }

        .title-container > h1 {
            font-size: 90px !important;
            color: rgb(255, 255, 255);
            text-shadow: 2px 4px 2px rgba(200,200,200,0.6);
        }


        .form-group.float-right {
            margin-top: 15px; /* Add spacing between elements */
        }

            .form-group {
            display: flex;
            flex-direction: column;
            align-items: flex-start; /* Aligns the checkbox and labels to the left */
            margin-bottom: 20px; /* Adds space below the form group */
        }

        .form-check-label {
            margin-left: 8px; /* Adds some space between checkbox and label */
        }

        .show-form {
            cursor: pointer; /* Changes cursor to pointer for better UX */
            margin-top: 10px; /* Adds space above each link */
            color: #007bff; /* Makes the text look like a link */
            text-decoration: underline; /* Underlines the text */
        }

        .show-form:hover {
            text-decoration: none; /* Removes underline on hover */
        }
    </style>
</head>
<body>

<div class="main row">

    <div class="title-container col-6">
        <h1>T James Sporty Bar</h1>
    </div>

    <div class="main-container col-4">
        <!-- Registration Area -->
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
                <div class="form-group float-right">
                    <small class="show-form" onclick="showForm('login')">Already have an account? Login Here.</small>
                </div>
                <button type="submit" class="btn btn-primary form-control">Register</button>
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
                <div class="form-group float-right">
                    <small class="show-form" onclick="showForm('login')">Remembered your password? Login Here.</small>
                    <small class="show-form" onclick="showForm('registration')">No Account? Register Here.</small>
                </div>
                <button type="submit" class="btn btn-primary form-control">Send OTP</button>
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
                
                <div class="row m-auto">
                    <div class="form-group form-check col-6">
                        <input type="checkbox" class="form-check-input" id="rememberCheck">
                        <label class="form-check-label" for="rememberCheck">Remember Password</label>
                        
                        <small class="show-form col-6 text-center pl-4" onclick="showForm('forgot')">Forgot Password?</small>
                        <small class="show-form col-6 text-center pl-4" onclick="showForm('registration')">No Account? Register Here.</small>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary form-control">Login</button>
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
