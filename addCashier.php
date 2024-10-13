<?php
// your_backend_script.php
include 'conn.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['saveCashier'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password
    $role = $_POST['role'];

    $sql = "INSERT INTO users (name, email, username, password, role) VALUES (:name, :email, :username, :password, :role)";
    $stmt = $conn->prepare($sql);
    
    // Bind parameters
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':role', $role);

    // Execute the statement
    if ($stmt->execute()) {
        echo "
            <script>
                alert('Account created successfully!');
                window.location.href = 'manage_user.php';
            </script>
        ";
    } else {
        echo "Error: " . $stmt->errorInfo()[2];

        echo "
            <script>
                alert('Error: " . addslashes($stmt->errorInfo()[2]) . "');
                window.location.href = 'manage_user.php';
            </script>
        ";

    }
}
?>
