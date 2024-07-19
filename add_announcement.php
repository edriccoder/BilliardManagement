<?php
include 'conn.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_POST['save'])) {
    $title = $_POST['title'];
    $body = $_POST['body'];

    $sql = "INSERT INTO announcements (title, body, created_at) VALUES (?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(1, $title);
    $stmt->bindParam(2, $body);

    if ($stmt->execute()) {
        echo "<script>
                alert('Adding announcement complete!');
                window.location.href = 'admin_dashboard.php';
              </script>";
    } else {
        echo "<script>
                alert('ERROR!');
              </script>";
    }
}
?>
