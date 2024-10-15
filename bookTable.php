<?php
include 'conn.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve POST data
    $tableId = $_POST['table_id'];
    $userId = $_POST['user_id'];
    $bookingType = $_POST['booking_type'];
    $amount = $_POST['amount'];
    $paymentMethod = $_POST['payment_method'];
    $numPlayers = $_POST['num_players'];

    // Initialize proof of payment variable
    $proofOfPaymentPath = null;

    // Handle GCash proof of payment if selected
    if ($paymentMethod === 'gcash') {
        if (isset($_FILES['proof_of_payment']) && $_FILES['proof_of_payment']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['proof_of_payment']['tmp_name'];
            $fileName = $_FILES['proof_of_payment']['name'];
            $fileSize = $_FILES['proof_of_payment']['size'];
            $fileType = $_FILES['proof_of_payment']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            // Define allowed file extensions
            $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'pdf'];

            if (in_array($fileExtension, $allowedfileExtensions)) {
                // Check file size (e.g., max 5MB)
                if ($fileSize < 5 * 1024 * 1024) {
                    // Sanitize file name
                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

                    // Directory where the file will be stored
                    $uploadFileDir = './uploads/proof_of_payment/';
                    if (!is_dir($uploadFileDir)) {
                        mkdir($uploadFileDir, 0755, true);
                    }
                    $dest_path = $uploadFileDir . $newFileName;

                    if(move_uploaded_file($fileTmpPath, $dest_path)) {
                        $proofOfPaymentPath = $dest_path;
                    } else {
                        $error .= "There was an error moving the uploaded file.<br>";
                    }
                } else {
                    $error .= "Uploaded file is too large. Maximum size is 5MB.<br>";
                }
            } else {
                $error .= "Unsupported file type. Allowed types: " . implode(", ", $allowedfileExtensions) . ".<br>";
            }
        } else {
            $error .= "Please upload proof of payment for GCash.<br>";
        }
    }

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
            exit();
        } else {
            if (empty($error)) {
                // Proceed with booking
                $sql = "INSERT INTO bookings (table_id, table_name, user_id, start_time, end_time, num_matches, num_players, payment_method, amount, proof_of_payment, status) VALUES (?, ?, ?, ?, ?, NULL, ?, ?, ?, ?, 'Pending')";
                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    $tableId,
                    $tableName,
                    $userId,
                    $startTime,
                    $endTime,
                    $numPlayers,
                    $paymentMethod,
                    $amount,
                    $proofOfPaymentPath
                ]);

                echo "
                <script>
                    alert('Booking Successful');
                    window.location.href = 'user_table.php';
                </script>
                ";
                exit();
            } else {
                // Display errors
                echo "
                <script>
                    alert('" . addslashes($error) . "');
                    window.location.href = 'user_table.php';
                </script>
                ";
                exit();
            }
        }
    }
    // Handle other booking types (e.g., 'match')
    elseif ($bookingType === 'match') {
        $numMatches = $_POST['num_matches'];

        // Implement similar booking logic for 'match' type
        // ...

        if (empty($error)) {
            // Example insertion for 'match' booking type
            $sql = "INSERT INTO bookings (table_id, table_name, user_id, num_matches, num_players, payment_method, amount, proof_of_payment, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending')";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                $tableId,
                $tableName,
                $userId,
                $numMatches,
                $numPlayers,
                $paymentMethod,
                $amount,
                $proofOfPaymentPath
            ]);

            echo "
            <script>
                alert('Booking Successful');
                window.location.href = 'user_table.php';
            </script>
            ";
            exit();
        } else {
            // Display errors
            echo "
            <script>
                alert('" . addslashes($error) . "');
                window.location.href = 'user_table.php';
            </script>
            ";
            exit();
        }
    }
}
?>
