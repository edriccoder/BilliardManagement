<?php
include ('conn.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Fetch the user details from the database
    $stmt = $conn->prepare("SELECT `password`, `role`, `user_id`, `email` FROM `users` WHERE `username` = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch();
        $stored_password = $row['password'];
        $stored_role = $row['role'];
        $user_id = $row['user_id'];
        $email = $row['email'];

        // Debugging output
        error_log("Username: $username");
        error_log("Stored Password: $stored_password");
        error_log("Entered Password: $password");

        // Check if the stored password is hashed
        if (password_verify($password, $stored_password)) {
            // Password matches the hashed version
            session_start();
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            $_SESSION['role'] = $stored_role;

            echo "
            <script>
                alert('Login Successfully!');
                window.location.href = 'redirect_user.php';
            </script>
            "; 

        } elseif ($password === $stored_password) {
            // Plain-text password matches
            session_start();
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            $_SESSION['role'] = $stored_role;

            echo "
            <script>
                alert('Login Successfully!');
                window.location.href = 'redirect_user.php';
            </script>
            "; 

        } else {
            echo "
            <script>
                alert('Login Failed, Incorrect Password!');
                window.location.href = 'register.php';
            </script>
            ";
        }
        
    } else {
        echo "
            <script>
                alert('Login Failed, User Not Found!');
                window.location.href = 'register.php';
            </script>
        ";
    }
}
?>
