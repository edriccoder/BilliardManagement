<?php
session_start();
include 'conn.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_pic'])) {
    $profile_pic = $_FILES['profile_pic'];

    // Check if file was uploaded without errors
    if ($profile_pic['error'] === UPLOAD_ERR_OK && !empty($profile_pic['tmp_name'])) {
        // Define the upload directory and file path
        $upload_dir = 'img/profilepicture/';
        $file_name = basename($profile_pic['name']);
        $upload_file = $upload_dir . $file_name;

        // Check if the file is an image
        $check = getimagesize($profile_pic['tmp_name']);
        if ($check !== false) {
            // Move the file to the server
            if (move_uploaded_file($profile_pic['tmp_name'], $upload_file)) {
                // Update user's profile picture in the database
                $query = "UPDATE users SET profile_pic = ? WHERE user_id = ?";
                $stmt = $conn->prepare($query);
                $stmt->execute([$file_name, $user_id]); // Bind the parameters correctly

                // Redirect to the profile page
                header("Location: user_profile.php");
                exit();
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        } else {
            echo "File is not an image.";
        }
    } else {
        echo "No file uploaded or an error occurred.";
    }
} else {
    echo "Invalid request.";
}
?>
