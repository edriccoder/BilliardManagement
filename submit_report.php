<?php
include 'conn.php'; 

$description = "";
$datetime = "";
$photoPath = "";
$reportType = "";

// Check which report type is being submitted
if (isset($_POST['item_damage_description'])) {
    $description = trim($_POST['item_damage_description']);
    $datetime = $_POST['item_damage_datetime'];
    $reportType = 'item_damage';

    // Handle file upload
    if (isset($_FILES['item_damage_photo']) && $_FILES['item_damage_photo']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "img/";
        $fileName = basename($_FILES['item_damage_photo']['name']);
        $targetFilePath = $targetDir . $fileName;

        // Validate file type (only allow images)
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileType, $allowedTypes)) {
            // Move the uploaded file to the target directory
            if (move_uploaded_file($_FILES['item_damage_photo']['tmp_name'], $targetFilePath)) {
                $photoPath = $targetFilePath;
            } else {
                echo "Error uploading file.";
                exit();
            }
        } else {
            echo "Invalid file type. Only JPG, JPEG, PNG & GIF files are allowed.";
            exit();
        }
    }

} elseif (isset($_POST['incident_report_name'])) {
    $description = trim($_POST['incident_report_description']);
    $datetime = $_POST['incident_report_datetime'];
    $name = trim($_POST['incident_report_name']);
    $reportType = 'incident_report';
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
    echo "Report submitted successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Close the connection
$conn = null;
?>
