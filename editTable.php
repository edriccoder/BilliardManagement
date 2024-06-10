<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tableId = $_POST['table_id'];
    $tableName = $_POST['table_number'];
    $status = $_POST['status'];

    $sql = "UPDATE tables SET table_number = :table_number, status = :status WHERE table_id = :table_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':table_number', $tableName);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':table_id', $tableId);
    $stmt->execute();

    header("Location: billiard_table.php");
}
?>
