<?php
include 'conn.php';

$query = "SELECT DATE(timestamp) as date, COUNT(*) as transactions FROM transactions GROUP BY DATE(timestamp) ORDER BY DATE(timestamp) DESC LIMIT 7";
$stmt = $conn->prepare($query);
$stmt->execute();

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($data);
?>
