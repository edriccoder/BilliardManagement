<?php
include 'conn.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tableId = $_POST['table_id'];
    $userId = $_POST['user_id'];
    $bookingType = $_POST['booking_type'];
    $amount = $_POST['amount'];
    $paymentMethod = $_POST['payment_method'];

    // Fetch table_number based on table_id
    $stmtTable = $conn->prepare("SELECT table_number FROM tables WHERE table_id = ?");
    $stmtTable->execute([$tableId]);
    $table = $stmtTable->fetch(PDO::FETCH_ASSOC);
    $tableName = $table['table_number'];

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
        } else if ($bookingType === 'match'){
            // Insert booking data into the bookings table
            $sql = "INSERT INTO bookings (table_id, table_name, user_id, start_time, end_time, num_matches, status, amount, payment_method) 
                    VALUES (?, ?, ?, ?, ?, NULL, 'Pending', ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$tableId, $tableName, $userId, $startTime, $endTime, $amount, $paymentMethod]);

        }

        if ($paymentMethod === 'gcash') {
            // Check if the proof of payment is uploaded
            if (isset($_FILES['proof_of_payment']) && $_FILES['proof_of_payment']['error'] === 0) {
                // Define the upload directory
                $uploadDir = 'payments/';
                $fileName = basename($_FILES['proof_of_payment']['name']);
                $targetFilePath = $uploadDir . $fileName;

                // Move the uploaded file to the server
                if (move_uploaded_file($_FILES['proof_of_payment']['tmp_name'], $targetFilePath)) {
                    // Update the booking record with the proof of payment image
                    $sqlTransaction = "INSERT INTO transactions (booking_id, amount, payment_method, status, timestamp, proof_of_payment) VALUES (?, ?, ?, 'Pending', NOW(), ?)";
                    $stmtTransaction = $conn->prepare($sqlTransaction);
                    $stmtTransaction->execute([$bookingId, $amount, $paymentMethod, $proofOfPayment]);

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
}
?>
