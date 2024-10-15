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
            $sql = "INSERT INTO bookings (table_id, table_name, user_id, start_time, end_time, status, num_players, num_matches) 
                    VALUES (?, ?, ?, ?, ?, 'Pending', ?, NULL)";
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
            $sql = "INSERT INTO bookings (table_id, table_name, user_id, status, num_players, num_matches) 
                    VALUES (?, ?, ?, 'Pending', ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$tableId, $tableName, $userId, $numPlayers, $numMatches]);

            // Get the last inserted booking ID
            $bookingId = $conn->lastInsertId();

            // Handle the transaction based on payment method
            handleTransaction($bookingId, $amount, $paymentMethod);
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

            // Ensure the directory exists and is writable
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0777, true)) {
                    alertAndRedirect('Failed to create the payments directory.');
                }
            }

            if (!is_writable($uploadDir)) {
                alertAndRedirect('The payments directory is not writable. Please check permissions.');
            }

            $fileName = basename($_FILES['proof_of_payment']['name']);
            $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
            $allowedExt = ['jpg', 'jpeg', 'png', 'pdf'];

            // Validate file extension
            if (!in_array(strtolower($fileExt), $allowedExt)) {
                alertAndRedirect('Invalid file type for proof of payment. Allowed types: jpg, jpeg, png, pdf.');
            }

            // Generate a unique file name to prevent overwriting
            $uniqueFileName = uniqid('proof_', true) . '.' . $fileExt;
            $targetFilePath = $uploadDir . $uniqueFileName;

            // Move the uploaded file to the server
            if (move_uploaded_file($_FILES['proof_of_payment']['tmp_name'], $targetFilePath)) {
                // Insert transaction with proof of payment, including folder path
                $proofOfPaymentPath = $targetFilePath; // Full relative path with folder

                $sqlTransaction = "INSERT INTO transactions (booking_id, amount, payment_method, status, timestamp, proof_of_payment) 
                                   VALUES (?, ?, ?, 'Pending', NOW(), ?)";
                $stmtTransaction = $conn->prepare($sqlTransaction);
                $stmtTransaction->execute([$bookingId, $amount, $paymentMethod, $proofOfPaymentPath]);

                alertAndRedirect('Booking and GCash payment successful.');
            } else {
                alertAndRedirect('Error uploading proof of payment.');
            }
        } else {
            alertAndRedirect('Please upload the proof of payment.');
        }
    } else {
        // Insert a cash transaction with pending status
        $sqlTransaction = "INSERT INTO transactions (booking_id, amount, payment_method, status, timestamp) 
                           VALUES (?, ?, ?, 'Pending', NOW())";
        $stmtTransaction = $conn->prepare($sqlTransaction);
        $stmtTransaction->execute([$bookingId, $amount, $paymentMethod]);

        // Cash payment success message
        alertAndRedirect('Booking Successful');
    }
}

// Helper function to alert and redirect
function alertAndRedirect($message) {
    echo "
    <script>
        alert('$message');
        window.location.href = 'user_table.php';
    </script>
    ";
    exit();
}
?>
