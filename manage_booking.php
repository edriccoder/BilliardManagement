<?php
include 'conn.php';
session_start();

// Check if request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve POST data
    $booking_id = $_POST['booking_id'] ?? null;
    $user_id = $_POST['user_id'] ?? null;

    // Check if booking ID and user ID are provided
    if ($booking_id && $user_id) {
        try {
            // Determine the booking status based on the button clicked
            if (isset($_POST['confirm'])) {
                $status = "Confirmed";
            } elseif (isset($_POST['cancel'])) {
                $status = "Canceled";
            } else {
                throw new Exception("Invalid operation.");
            }

            // Prepare the SQL statement with placeholders for parameters
            $sql = "UPDATE bookings 
                    SET status = :status 
                    WHERE booking_id = :booking_id AND user_id = :user_id";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

            // Execute the update
            if ($stmt->execute()) {
                $_SESSION['message'] = "Booking status updated successfully.";
            } else {
                $_SESSION['error'] = "Failed to update booking status.";
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Error: " . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = "Booking ID and User ID are required.";
    }

    // Redirect back to billing page with message
    header("Location: cashier_billing.php");
    exit();
} else {
    // Invalid request method
    $_SESSION['error'] = "Invalid request.";
    header("Location: cashier_billing.php");
    exit();
}
?>
