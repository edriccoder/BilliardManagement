<?php 

$servername = "localhost";
$username = "u330488542_billiard";
$password = "billiard123";
$db = "u330488542_billiard";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Failed " . $e->getMessage();
}
?>