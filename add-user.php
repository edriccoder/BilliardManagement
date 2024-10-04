<?php 
include('conn.php');

if (isset($_POST['name'], $_POST['email'], $_POST['username'], $_POST['password'], $_POST['confirmPassword'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = 'user'; // Automatically set role to 'user'
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    
    // Check if passwords match
    if ($password !== $confirmPassword) {
        echo "
        <script>
            alert('Passwords do not match!');
            window.location.href = 'register.php'; // Change to your registration page
        </script>
        ";
        exit;
    }

    // Check if the email already exists
    try {
        $stmt = $conn->prepare("SELECT `email` FROM `users` WHERE `email` = :email");
        $stmt->execute(['email' => $email]);
        $nameExist = $stmt->fetch(PDO::FETCH_ASSOC);

        if (empty($nameExist)) {
            // Password hashing
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $insertStmt = $conn->prepare("INSERT INTO `users` (`name`, `email`, `role`, `username`, `password`) VALUES (:name, :email, :role, :username, :password)");
            $insertStmt->bindParam(':name', $name, PDO::PARAM_STR);
            $insertStmt->bindParam(':email', $email, PDO::PARAM_STR);
            $insertStmt->bindParam(':role', $role, PDO::PARAM_STR);
            $insertStmt->bindParam(':username', $username, PDO::PARAM_STR);
            $insertStmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR); // Ensure this is hashed

            if ($insertStmt->execute()) {
                echo "
                <script>
                    alert('Registered Successfully!');
                    window.location.href = 'register.php'; // Redirect after registration
                </script>
                ";
            } else {
                echo "
                <script>
                    alert('Error registering the user.');
                    window.location.href = 'register.php';
                </script>
                ";
            }
        } else {
            echo "
            <script>
                alert('Account Already Exists!');
                window.location.href = 'register.php'; // Change to your registration page
            </script>
            ";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
