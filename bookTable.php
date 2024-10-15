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
    $numPlayers = $_POST['num_players']; // Number of players is passed
    $numMatches = isset($_POST['num_matches']) ? $_POST['num_matches'] : null; // Matches passed only if booking type is 'match'
    $amount = $_POST['amount']; // Total amount
    $paymentMethod = $_POST['payment_method'];

    // Fetch table_number based on table_id
    $stmtTable = $conn->prepare("SELECT table_number FROM tables WHERE table_id = ?");
    $stmtTable->execute([$tableId]);
    $table = $stmtTable->fetch(PDO::FETCH_ASSOC);
    $tableName = $table['table_number'];

    // Check if booking is per hour or per match
    if ($bookingType === 'hour') {
        $startTime = $_POST['start_time'];
        $endTime = $_POST['end_time'];

        // Check for overlapping bookings
        $stmtCheck = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE table_id = ? AND (
            (start_time < ? AND end_time > ?) OR
            (start_time < ? AND end_time > ?) OR
            (start_time >= ? AND start_time < ?)
        )");
        $stmtCheck->execute([$tableId, $endTime, $startTime, $startTime, $endTime, $startTime, $endTime]);
        $isBooked = $stmtCheck->fetchColumn();

        if ($isBooked) {
            echo "
            <script>
                alert('The selected table is already booked during this time. Please choose a different time.');
                window.location.href = 'user_table.php';
            </script>
            ";
        } else {
            // Insert booking data for hour-based booking into the bookings table
            $sql = "INSERT INTO bookings (table_id, table_name, user_id, start_time, end_time, status, num_players, num_matches) 
                    VALUES (?, ?, ?, ?, ?, 'Pending', ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$tableId, $tableName, $userId, $startTime, $endTime, $numPlayers, null]);

            // Get the last inserted booking ID
            $bookingId = $conn->lastInsertId();

            // Handle the transaction based on payment method
            handleTransaction($bookingId, $amount, $paymentMethod);
        }
    } elseif ($bookingType === 'match') {
        // For match-based booking, we don't need start_time or end_time, just number of matches
        if ($numMatches && $numPlayers) {
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
            echo "
            <script>
                alert('Please enter the number of matches and players.');
                window.location.href = 'user_table.php';
            </script>
            ";
        }
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
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true); // Ensure the directory exists
            }
            $fileName = basename($_FILES['proof_of_payment']['name']);
            $targetFilePath = $uploadDir . $fileName;

            // Move the uploaded file to the server
            if (move_uploaded_file($_FILES['proof_of_payment']['tmp_name'], $targetFilePath)) {
                // Insert transaction with proof of payment, including folder path
                $proofOfPaymentPath = $uploadDir . $fileName; // Full path with folder
                $sqlTransaction = "INSERT INTO transactions (booking_id, amount, payment_method, status, timestamp, proof_of_payment) 
                                   VALUES (?, ?, ?, 'Pending', NOW(), ?)";
                $stmtTransaction = $conn->prepare($sqlTransaction);
                $stmtTransaction->execute([$bookingId, $amount, $paymentMethod, $proofOfPaymentPath]);

                echo "
                <script>
                    alert('Booking and GCash payment successful.');
                    window.location.href = 'user_table.php';
                </script>
                ";
                exit();
            } else {
                echo "
                <script>
                    alert('Error uploading proof of payment.');
                    window.location.href = 'user_table.php';
                </script>
                ";
            }
        } else {
            echo "
            <script>
                alert('Please upload the proof of payment.');
                window.location.href = 'user_table.php';
            </script>
            ";
        }
    } else {
        // Insert a cash transaction with pending status
        $sqlTransaction = "INSERT INTO transactions (booking_id, amount, payment_method, status, timestamp) 
                           VALUES (?, ?, ?, 'Pending', NOW())";
        $stmtTransaction = $conn->prepare($sqlTransaction);
        $stmtTransaction->execute([$bookingId, $amount, $paymentMethod]);

        // Cash payment success message
        echo "
        <script>
            alert('Booking Successful');
            window.location.href = 'user_table.php';
        </script>
        ";
        exit();
    }
}
?>
