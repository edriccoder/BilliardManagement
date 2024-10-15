<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include('conn.php');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capture necessary fields from the form
    $tableId = $_POST['table_id'];
    $userId = $_POST['user_id'];
    $bookingType = $_POST['booking_type'];
    $numPlayers = isset($_POST['num_players']) ? intval($_POST['num_players']) : 0;
    $numMatches = isset($_POST['num_matches']) ? intval($_POST['num_matches']) : null;
    $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
    $paymentMethod = $_POST['payment_method'];

    // Fetch table_number based on table_id
    $stmtTable = $conn->prepare("SELECT table_number FROM tables WHERE table_id = ?");
    $stmtTable->execute([$tableId]);
    $table = $stmtTable->fetch(PDO::FETCH_ASSOC);

    if (!$table) {
        alertAndRedirect('Invalid table selected.');
    }

    $tableName = $table['table_number'];

    // Booking logic based on type
    if ($bookingType === 'hour') {
        $startTime = isset($_POST['start_time']) ? $_POST['start_time'] : null;
        $endTime = isset($_POST['end_time']) ? $_POST['end_time'] : null;

        // Validate start and end times
        if (empty($startTime) || empty($endTime)) {
            alertAndRedirect('Please enter both start and end times.');
        }

        // Ensure the start time is before the end time
        if (strtotime($startTime) >= strtotime($endTime)) {
            alertAndRedirect('End time must be after the start time.');
        }

        // Check for overlapping bookings
        $stmtCheck = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE table_id = ? AND (
            (start_time < ? AND end_time > ?) OR
            (start_time < ? AND end_time > ?) OR
            (start_time >= ? AND start_time < ?)
        )");
        $stmtCheck->execute([$tableId, $endTime, $startTime, $startTime, $endTime, $startTime, $endTime]);
        $isBooked = $stmtCheck->fetchColumn();

        if ($isBooked) {
            alertAndRedirect('The selected table is already booked during this time. Please choose a different time.');
        } else {
            // Insert hour-based booking into bookings table
            $sql = "INSERT INTO bookings (table_id, table_name, user_id, start_time, end_time, num_players, num_matches, status) 
                    VALUES (?, ?, ?, ?, ?, ?, NULL, 'Pending')";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$tableId, $tableName, $userId, $startTime, $endTime, $numPlayers]);

            // Get last inserted booking ID
            $bookingId = $conn->lastInsertId();  // Use $conn instead of $stmt here

            // Handle transaction based on payment method
            handleTransaction($bookingId, $amount, $paymentMethod);
        }
    } elseif ($bookingType === 'match') {
        // For match-based booking, ensure num_matches and num_players are valid
        if ($numMatches > 0 && $numPlayers > 0) {
            // Insert match-based booking into bookings table
            $sql = "INSERT INTO bookings (table_id, table_name, user_id, start_time, end_time, num_matches, num_players, status) 
                    VALUES (?, ?, ?, NULL, NULL, ?, ?, 'Pending')";
            $stmt = $conn->prepare($sql);
            $result = $stmt->execute([$tableId, $tableName, $userId, $numMatches, $numPlayers]);

            if ($result) {
                // Get last inserted booking ID
                $bookingId = $conn->lastInsertId();  // Use $conn instead of $stmt here

                // Handle transaction based on payment method
                handleTransaction($bookingId, $amount, $paymentMethod);
            } else {
                alertAndRedirect('Failed to process your match-based booking.');
            }
        } else {
            alertAndRedirect('Please enter a valid number of matches and players.');
        }
    } else {
        alertAndRedirect('Invalid booking type selected.');
    }
}

// Function to handle transactions for both GCash and Cash payments
function handleTransaction($bookingId, $amount, $paymentMethod) {
    global $conn;

    if ($paymentMethod === 'gcash') {
        // Check if the proof of payment is uploaded
        if (isset($_FILES['proof_of_payment']) && $_FILES['proof_of_payment']['error'] === UPLOAD_ERR_OK) {
            // Define the upload directory
            $uploadDir = 'payments/';

            // Ensure the directory exists
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0777, true) && !is_dir($uploadDir)) {
                    alertAndRedirect('Error creating directory for payments.', 'Directory creation failed for GCash payment.');
                }
            }

            // Sanitize the file name
            $fileName = basename($_FILES['proof_of_payment']['name']);
            $uploadFile = $uploadDir . $fileName;

            // Move uploaded file
            if (move_uploaded_file($_FILES['proof_of_payment']['tmp_name'], $uploadFile)) {
                // Insert transaction with proof of payment, including folder path
                $proofOfPaymentPath = $uploadFile;

                $sqlTransaction = "INSERT INTO transactions (booking_id, amount, payment_method, status, timestamp, proof_of_payment) 
                                    VALUES (?, ?, ?, 'Pending', NOW(), ?)";
                $stmtTransaction = $conn->prepare($sqlTransaction);
                $execution = $stmtTransaction->execute([$bookingId, $amount, $paymentMethod, $proofOfPaymentPath]);

                if ($execution) {
                    alertAndRedirect('Booking and GCash payment successful.');
                } else {
                    alertAndRedirect('Failed to process your booking. Please try again.', 'Database insertion failed for booking ID: ' . $bookingId);
                }
            } else {
                alertAndRedirect('Error uploading proof of payment.', 'File upload failed for booking ID: ' . $bookingId);
            }
        } else {
            alertAndRedirect('Please upload the proof of payment.', 'No file uploaded for GCash payment.');
        }
    } else {
        // Insert a cash transaction with pending status
        $sqlTransaction = "INSERT INTO transactions (booking_id, amount, payment_method, status, timestamp) 
                            VALUES (?, ?, ?, 'Pending', NOW())";
        $stmtTransaction = $conn->prepare($sqlTransaction);
        $execution = $stmtTransaction->execute([$bookingId, $amount, $paymentMethod]);

        if ($execution) {
            // Cash payment success message
            alertAndRedirect('Booking Successful');
        } else {
            alertAndRedirect('Failed to process your booking. Please try again.', 'Database insertion failed for cash payment with booking ID: ' . $bookingId);
        }
    }
}

// Updated alertAndRedirect function with optional logging
function alertAndRedirect($message, $logMessage = null) {
    if ($logMessage) {
        error_log($logMessage);
    }
    echo "
    <script>
        alert('$message');
        window.location.href = 'user_table.php';
    </script>
    ";
    exit();
}
?>
