<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'conn.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

    // Check if booking is per hour or per match
    if ($bookingType === 'hour') {
        $startTime = $_POST['start_time'];
        $endTime = $_POST['end_time'];

        // Validate start and end times
        if (empty($startTime) || empty($endTime)) {
            alertAndRedirect('Please enter both start and end times.');
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
            // Insert booking data for hour-based booking into the bookings table
            $sql = "INSERT INTO bookings (table_id, table_name, user_id, start_time, end_time, num_players, num_matches, status) 
                    VALUES (?, ?, ?, ?, ?, ?, NULL,  'Pending')";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$tableId, $tableName, $userId, $startTime, $endTime, $numPlayers]);

            // Get the last inserted booking ID
            $bookingId = $conn->lastInsertId();

            // Handle the transaction based on payment method
            handleTransaction($bookingId, $amount, $paymentMethod);
        }
    } elseif ($bookingType === 'match') {
        // For match-based booking, ensure num_matches and num_players are valid
        if ($numMatches > 0 && $numPlayers > 0) {
            // Insert booking data for match-based booking into the bookings table
            $sql = "INSERT INTO bookings (table_id, table_name, user_id, start_time, end_time, num_matches, status) VALUES (?, ?, ?, NULL, NULL, ?, 'Pending')";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$tableId, $tableName, $userId, $numMatches]);
            $result = $stmt->execute([$tableId, $tableName, $userId, $numPlayers, $numMatches]);


            if ($result) {
                // Get the last inserted booking ID
                $bookingId = $conn->lastInsertId();

                // Handle the transaction based on payment method
                handleTransaction($bookingId, $amount, $paymentMethod);
            } else {
                $error = 'Failed to insert match-based booking.';
                // Optionally log the error
                // file_put_contents('debug_log.txt', $error . "\n", FILE_APPEND);
                alertAndRedirect('Failed to process your booking. Please try again.');
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
        if (isset($_FILES['proof_of_payment']) && $_FILES['proof_of_payment']['error'] === 0) {
            // Define the upload directory
            $uploadDir = 'payments/';

            // Ensure the directory exists
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) { // Use 0755 permissions for security
                    alertAndRedirect('Failed to create the payments directory.', 'Directory creation failed.');
                }
            }

            // Ensure the directory is writable
            if (!is_writable($uploadDir)) {
                alertAndRedirect('The payments directory is not writable. Please check permissions.', 'Directory not writable.');
            }

            // Sanitize the file name
            $fileName = basename($_FILES['proof_of_payment']['name']);
            $fileName = preg_replace("/[^A-Z0-9._-]/i", "_", $fileName);
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowedExt = ['jpg', 'jpeg', 'png', 'pdf'];
            $allowedMime = ['image/jpeg', 'image/png', 'application/pdf'];
            $maxFileSize = 5 * 1024 * 1024; // 5MB

            // Validate file extension
            if (!in_array($fileExt, $allowedExt)) {
                alertAndRedirect('Invalid file type for proof of payment. Allowed types: jpg, jpeg, png, pdf.', 'Invalid file extension: ' . $fileExt);
            }

            // Validate MIME type
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $_FILES['proof_of_payment']['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mimeType, $allowedMime)) {
                alertAndRedirect('Invalid file content for proof of payment.', 'Invalid MIME type: ' . $mimeType);
            }

            // Validate file size
            if ($_FILES['proof_of_payment']['size'] > $maxFileSize) {
                alertAndRedirect('Proof of payment file size exceeds the 5MB limit.', 'File size too large: ' . $_FILES['proof_of_payment']['size']);
            }

            // Generate a unique file name to prevent overwriting
            $uniqueFileName = uniqid() . '_' . $fileName;
            $uploadFile = $uploadDir . $uniqueFileName;

            // Move the uploaded file to the server
            if (move_uploaded_file($_FILES['proof_of_payment']['tmp_name'], $uploadFile)) {
                // Insert transaction with proof of payment, including folder path
                $proofOfPaymentPath = $uploadFile; // This should be 'payments/unique_filename.ext'

                // Debugging Step: Log the path to ensure it's correct
                // You can uncomment the following line during development to verify
                // error_log('Proof of Payment Path: ' . $proofOfPaymentPath);

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
