<?php
// update_table_status.php

// Enable error reporting for debugging (disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database connection
include 'conn.php'; // Ensure the path is correct

// Get current time in UTC to avoid timezone issues
$current_time = gmdate('Y-m-d H:i:s');

// Define the specific table IDs to update
$table_ids = [1, 2, 3, 4];
$table_ids_placeholder = implode(',', $table_ids);

// Error handling function
function checkSQLExecution($stmt, $context) {
    if (!$stmt->execute()) {
        $errorInfo = $stmt->errorInfo();
        // Return JSON error instead of plain text for AJAX handling
        header('Content-Type: application/json', true, 500);
        echo json_encode(["error" => "SQL error in $context: " . $errorInfo[2]]);
        exit();
    }
}

try {
    // Single UPDATE statement to set status based on current bookings for specified table IDs
    $sqlUpdateStatus = "
        UPDATE tables
        SET status = CASE 
            WHEN table_id IN (
                SELECT table_id 
                FROM bookings 
                WHERE start_time <= :current_time 
                  AND end_time >= :current_time 
                  AND archive = 0
                  AND table_id IN ($table_ids_placeholder)
            ) THEN 'occupied'
            ELSE 'available'
        END
        WHERE table_id IN ($table_ids_placeholder)
    ";

    $stmtUpdateStatus = $conn->prepare($sqlUpdateStatus);
    $stmtUpdateStatus->bindParam(':current_time', $current_time);
    checkSQLExecution($stmtUpdateStatus, "updating table statuses");

    // Fetch updated tables information to return as JSON
    $sqlTables = "SELECT table_number, status, table_id FROM tables WHERE table_id IN ($table_ids_placeholder)";
    $stmtTables = $conn->prepare($sqlTables);
    checkSQLExecution($stmtTables, "fetching tables");

    $tables = $stmtTables->fetchAll(PDO::FETCH_ASSOC);

    // Return the updated tables as JSON for real-time updates
    header('Content-Type: application/json');
    echo json_encode($tables);
} catch (Exception $e) {
    // Return JSON error response
    header('Content-Type: application/json', true, 500);
    echo json_encode(["error" => "Failed to update table statuses: " . $e->getMessage()]);
}
?>
