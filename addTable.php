<?php
include('conn.php');

if(isset($_POST['save'])) {
    $table_name = $_POST['tablename'];
    $table_status = $_POST['status'];

    // Prepare the SQL statement using a prepared statement
    $sql = "INSERT INTO tables (table_number, status) VALUES (?, ?)";

    // Prepare the statement
    $stmt = $conn->prepare($sql);

    // Bind parameters
    $stmt->bindParam(1, $table_name);
    $stmt->bindParam(2, $table_status);

    // Execute the statement
    if ($stmt->execute()) {
        // Redirect to the previous page with success message
        echo "<script>alert('Adding table Complete!');window.location='billiard_table.php'</script>";
    } else {
        // Redirect to the previous page with error message
        echo "<script>alert('Error!');</script>";
    }
}
?>
