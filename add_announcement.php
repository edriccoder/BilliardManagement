<?php
include 'conn.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_POST['save'])) {
    $title = $_POST['title'];
    $body = $_POST['body'];

    // Calculate the expiration time (24 hours from now)
    $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));

    $sql = "INSERT INTO announcements (title, body, created_at, expires_at) VALUES (?, ?, NOW(), ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(1, $title);
    $stmt->bindParam(2, $body);
    $stmt->bindParam(3, $expiresAt); // Bind the expiration date

    if ($stmt->execute()) {
        echo "<script>
                alert('Adding announcement complete! It will expire in 24 hours.');
                window.location.href = 'admin_dashboard.php';
              </script>";
    } else {
        echo "<script>
                alert('ERROR!');
              </script>";
    }
}
?>
