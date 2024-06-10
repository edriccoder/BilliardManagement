<?php
include ('conn.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT `password`, `role`, `user_id` FROM `users` WHERE `username` = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch();
        $stored_password = $row['password'];
        $stored_role = $row['role'];
        $user_id = $row['user_id'];

        if ($password === $stored_password) {
            session_start();
            $_SESSION['user_id'] = $user_id;

            if ($stored_role == 'admin') {
                echo "
                <script>
                    alert('Login Successfully!');
                    window.location.href = 'http://localhost/BilliardManagement/admin_dashboard.php';
                </script>
                "; 
            } else if ($stored_role == 'user') {
                echo "
                <script>
                    alert('Login Successfully!');
                    window.location.href = 'http://localhost/BilliardManagement/user_dashboard.php';
                </script>
                "; 
            }
            
        } else {
            echo "
            <script>
                alert('Login Failed, Incorrect Password!');
                window.location.href = 'http://localhost/BilliardManagement/';
            </script>
            ";
        }
        
    } else {
        echo "
            <script>
                alert('Login Failed, User Not Found!');
                window.location.href = 'http://localhost/BilliardManagement/';
            </script>
            ";
    }
}

?>