<?php
session_start();
include 'conn.php';

// Check if the user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    // Redirect to login page if not authenticated
    header("Location: index.php");
    exit();
}

// Check if 'booking_id' is provided in the GET parameters
if (!isset($_GET['booking_id']) || empty($_GET['booking_id'])) {
    // Redirect back with an error message if 'booking_id' is missing
    header("Location: booking_user.php?error=Invalid booking ID");
    exit();
}

$booking_id = $_GET['booking_id'];

// Validate that 'booking_id' is a positive integer
if (!filter_var($booking_id, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]])) {
    header("Location: booking_user.php?error=Invalid booking ID");
    exit();
}

// Retrieve the current user's ID from the session
$user_id = $_SESSION['user_id'];

try {
    // Prepare a SQL statement to verify that the booking exists and belongs to the user
    $sqlCheck = "SELECT booking_id FROM bookings WHERE booking_id = :booking_id AND user_id = :user_id AND archive = 0";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);
    $stmtCheck->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmtCheck->execute();

    if ($stmtCheck->rowCount() === 0) {
        // No matching booking found or it doesn't belong to the user
        header("Location: bookings_user.php?error=Booking not found or access denied");
        exit();
    }

    // Perform the soft delete by setting 'archive' to 1
    $sqlDelete = "UPDATE bookings SET archive = 1 WHERE booking_id = :booking_id";
    $stmtDelete = $conn->prepare($sqlDelete);
    $stmtDelete->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);

    if ($stmtDelete->execute()) {
        // Deletion successful
        header("Location: booking_user.php?success=Booking deleted successfully");
    } else {
        // Deletion failed
        header("Location: booking_user.php?error=Failed to delete booking");
    }
    exit();
} catch (PDOException $e) {
    // Handle any database errors
    // Log the error message in a real-world scenario
    header("Location: booking_user.php?error=An unexpected error occurred");
    exit();
}
?>
