<?php
session_start();
include 'conn.php';

if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    // Redirect to login page if session variables are not set
    header("Location: index.php");
    exit();
}

if (isset($_GET['announcement_id'])) {
    $announcement_id = htmlspecialchars($_GET['announcement_id']);

    // Prepare the delete SQL query
    $sqlDelete = "DELETE FROM announcements WHERE id = :announcement_id";
    $stmtDelete = $conn->prepare($sqlDelete);

    // Bind the parameter
    $stmtDelete->bindParam(':announcement_id', $announcement_id, PDO::PARAM_INT);

    // Execute the query
    if ($stmtDelete->execute()) {
        // Redirect to the page that lists the announcements after deletion
        header("Location: admin_announcement.php?message=deleted");
        exit();
    } else {
        // Handle error case if needed
        echo "Error deleting the announcement.";
    }
} else {
    // If no announcement ID is passed, redirect to the announcement page
    header("Location: announcements.php");
    exit();
}
?>
