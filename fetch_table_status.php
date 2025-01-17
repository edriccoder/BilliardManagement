<?php
include 'conn.php';

// Enable error reporting for debugging (disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set timezone
date_default_timezone_set('UTC'); // Adjust as needed
$current_time = date('Y-m-d H:i:s');

// Function to handle SQL execution and debugging
function checkSQLExecution($stmt, $context) {
    try {
        $stmt->execute();
        echo "SQL executed successfully for $context<br>";
    } catch (PDOException $e) {
        die("SQL error in $context: " . $e->getMessage());
    }
}

try {
    // Begin transaction
    $conn->beginTransaction();

    // Step 1: Set all tables to 'Available'
    $sqlSetAvailable = "UPDATE tables SET status = 'Available'";
    $stmtSetAvailable = $conn->prepare($sqlSetAvailable);
    checkSQLExecution($stmtSetAvailable, "setting all tables to 'Available'");

    // Step 2: Set 'Occupied' based on active bookings
    $sqlSetOccupied = "
        UPDATE tables
        SET status = 'Occupied'
        WHERE table_id IN (
            SELECT table_id 
            FROM bookings 
            WHERE start_time <= :current_time 
              AND end_time >= :current_time 
              AND archive = 0
        )
    ";
    $stmtSetOccupied = $conn->prepare($sqlSetOccupied);
    $stmtSetOccupied->bindParam(':current_time', $current_time);
    checkSQLExecution($stmtSetOccupied, "setting 'Occupied' tables");

    // Commit transaction
    $conn->commit();
    echo "Transaction committed successfully.<br>";
} catch (Exception $e) {
    // Rollback in case of error
    $conn->rollBack();
    die("Failed to update table statuses: " . $e->getMessage());
}

// Fetch updated tables
$sqlTables = "SELECT table_number, status, table_id FROM tables";
$stmtTables = $conn->prepare($sqlTables);
checkSQLExecution($stmtTables, "fetching tables");

$tables = $stmtTables->fetchAll(PDO::FETCH_ASSOC);

// Display fetched tables for debugging
echo "Fetched Tables:<br><pre>";
print_r($tables);
echo "</pre>";
?>
