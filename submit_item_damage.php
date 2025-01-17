<?php
session_start();
include 'conn.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $description = trim($_POST['item_damage_description']);
    $datetime = $_POST['item_damage_datetime'];
    $reportedBy = trim($_POST['item_reported_by']);
    $causedBy = trim($_POST['item_damage_caused_by']); // New field for the person who caused the damage
    $charges = $_POST['item_damage_charges']; // Charges Field
    $contactNumber = trim($_POST['item_damage_contact_number']); // Contact number field
    $reportType = 'item_damage';
    $photoPath = "";

    if (isset($_FILES['item_damage_photo']) && $_FILES['item_damage_photo']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "img/";
        $fileName = basename($_FILES['item_damage_photo']['name']);
        $targetFilePath = $targetDir . $fileName;

        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['item_damage_photo']['tmp_name'], $targetFilePath)) {
                $photoPath = $targetFilePath;
            } else {
                $_SESSION['alert'] = ['title' => 'Error', 'text' => 'Error uploading file.', 'icon' => 'error'];
                header("Location: reports.php");
                exit();
            }
        } else {
            $_SESSION['alert'] = ['title' => 'Error', 'text' => 'Invalid file type. Only JPG, JPEG, PNG & GIF files are allowed.', 'icon' => 'error'];
            header("Location: reports.php");
            exit();
        }
    }

    // Prepare the SQL query using tinsert
    $sql = "
        INSERT INTO reports (
            type, 
            description, 
            datetime, 
            photo, 
            name, 
            caused_by, 
            charges, 
            contact_number
        ) VALUES (
            :reportType, 
            :description, 
            :datetime, 
            :photo, 
            :reportedBy, 
            :causedBy, 
            :charges,
            :contactNumber
        )
    ";

    try {
        $stmt = $conn->prepare($sql);
        
        // Bind the parameters
        $stmt->bindParam(':reportType', $reportType);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':datetime', $datetime);
        $stmt->bindParam(':photo', $photoPath);
        $stmt->bindParam(':reportedBy', $reportedBy);
        $stmt->bindParam(':causedBy', $causedBy);
        $stmt->bindParam(':charges', $charges);
        $stmt->bindParam(':contactNumber', $contactNumber); // Bind the contact number

        // Execute the query
        $stmt->execute();
        $_SESSION['alert'] = ['title' => 'Success', 'text' => 'Item Damage Report submitted successfully.', 'icon' => 'success'];
    } catch (PDOException $e) {
        error_log($e->getMessage());
        $_SESSION['alert'] = ['title' => 'Error', 'text' => 'Unable to submit report.', 'icon' => 'error'];
    }

    header("Location: reports.php");
    exit();
}
?>
