<?php 
include ('conn.php');

if (isset($_POST['name'], $_POST['email'], $_POST['username'], $_POST['password'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = 'user'; // Automatically set role to 'user'
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    try {
        $stmt = $conn->prepare("SELECT `email` FROM `users` WHERE `email` =  :email ");
        $stmt->execute(['email' => $email]);

        $nameExist = $stmt->fetch(PDO::FETCH_ASSOC);

        if (empty($nameExist)) {
            $conn->beginTransaction();

            $insertStmt = $conn->prepare("INSERT INTO `users` (`name`, `email`, `role`, `username`, `password`) VALUES (:name, :email, :role, :username, :password)");
            $insertStmt->bindParam('name', $name, PDO::PARAM_STR);
            $insertStmt->bindParam('email', $email, PDO::PARAM_STR);
            $insertStmt->bindParam('role', $role, PDO::PARAM_STR);
            $insertStmt->bindParam('username', $username, PDO::PARAM_STR);
            $insertStmt->bindParam('password', $password, PDO::PARAM_STR);

            $insertStmt->execute();

            $conn->commit(); 

            echo "
            <script>
                alert('Registered Successfully!');
                window.location.href = 'http://localhost/BilliardManagement/';
            </script>
            ";
        } else {
            echo "
            <script>
                alert('Account Already Exist!');
                window.location.href = 'http://localhost/BilliardManagement/';
            </script>
            ";
        }

    }  catch (PDOException $e) {
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
    }

}
?>

