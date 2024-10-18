<?php
include 'conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $user_id = $_POST['user_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare the SQL update query
    $sql = "UPDATE users SET name = :name, email = :email, username = :username";

    // If the password is provided, include it in the update query
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT); // Hash the password for security
        $sql .= ", password = :password";
    }

    $sql .= " WHERE user_id = :user_id AND role = 'cashier'"; // Ensuring the role is 'cashier'

    // Prepare and execute the query
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':user_id', $user_id);

    // If the password was set, bind the password parameter
    if (!empty($password)) {
        $stmt->bindParam(':password', $hashed_password);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Cashier updated successfully.'); window.location.href = 'udpate_user.php';</script>";
    } else {
        echo "<script>alert('Error updating user.'); window.location.href = 'udpate_user.php';</script>";
    }
} else {
    echo "<script>alert('Invalid request.'); window.location.href = 'udpate_user.php';</script>";
}
?>