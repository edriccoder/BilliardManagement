<?php
include 'conn.php'; 

$description = "";
$datetime = "";
$photoPath = "";
$reportType = "";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check which report type is being submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['saveReport'])) {

    // Item Damage Report
    if (isset($_POST['item_damage_description'])) {
        $description = trim($_POST['item_damage_description']);
        $datetime = $_POST['item_damage_datetime'];
        $reportType = 'item_damage';

        // Handle file upload
        if (isset($_FILES['item_damage_photo']) && $_FILES['item_damage_photo']['error'] === UPLOAD_ERR_OK) {
            $targetDir = "img/";
            $fileName = basename($_FILES['item_damage_photo']['name']);
            $targetFilePath = $targetDir . $fileName;

            // Validate file type
            $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($fileType, $allowedTypes)) {
                if (move_uploaded_file($_FILES['item_damage_photo']['tmp_name'], $targetFilePath)) {
                    $photoPath = $targetFilePath;
                } else {
                    echo "<script>alert('Error uploading file.'); window.location.href='reports.php';</script>";
                    exit();
                }
            } else {
                echo "<script>alert('Invalid file type. Only JPG, JPEG, PNG & GIF files are allowed.'); window.location.href='reports.php';</script>";
                exit();
            }
        }
    } 
    // Incident Report
    elseif (isset($_POST['incident_report_name'])) {
        $description = trim($_POST['incident_report_description']);
        $datetime = $_POST['incident_report_datetime'];
        $name = trim($_POST['incident_report_name']);
        $reportType = 'incident_report';

        // Debugging: Check if the fields are populated correctly
        echo "<script>console.log('Incident Report Name: $name');</script>";
        echo "<script>console.log('Incident Report Description: $description');</script>";
        echo "<script>console.log('Incident Report Datetime: $datetime');</script>";
    }

    // Prepare the SQL statement
    if ($reportType === 'item_damage') {
        $sql = $conn->prepare("INSERT INTO reports (type, description, datetime, photo) VALUES (?, ?, ?, ?)");
        $sql->bindValue(1, $reportType);
        $sql->bindValue(2, $description);
        $sql->bindValue(3, $datetime);
        $sql->bindValue(4, $photoPath);
    } elseif ($reportType === 'incident_report') {
        $sql = $conn->prepare("INSERT INTO reports (type, description, datetime, name) VALUES (?, ?, ?, ?)");
        $sql->bindValue(1, $reportType);
        $sql->bindValue(2, $description);
        $sql->bindValue(3, $datetime);
        $sql->bindValue(4, $name);
    }

    // Execute the query
    try {
        $sql->execute();
        echo "<script>alert('Report submitted successfully.'); window.location.href='reports.php';</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "'); window.location.href='reports.php';</script>";
        error_log($e->getMessage()); // Log the error for server-side debugging
    }

    // Close the connection
    $conn = null;
}
?>