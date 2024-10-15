<?php
// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session for flash messages and CSRF protection
session_start();

// Include database connection
include 'conn.php';
$error = '';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize POST data
    $tableId = isset($_POST['table_id']) ? trim($_POST['table_id']) : '';
    $userId = isset($_POST['user_id']) ? trim($_POST['user_id']) : '';
    $bookingType = isset($_POST['booking_type']) ? trim($_POST['booking_type']) : '';
    $amount = isset($_POST['amount']) ? trim($_POST['amount']) : '';
    $paymentMethod = isset($_POST['payment_method']) ? trim($_POST['payment_method']) : '';
    $numPlayers = isset($_POST['num_players']) ? intval($_POST['num_players']) : 0;
    $csrfToken = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';

    // Validate CSRF Token
    if (!hash_equals($_SESSION['csrf_token'], $csrfToken)) {
        $_SESSION['message'] = "Invalid CSRF token.";
        header("Location: user_table.php");
        exit();
    }

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
                    $uploadFileDir = 'payments/';
                    if (!is_dir($uploadFileDir)) {
                        if (!mkdir($uploadFileDir, 0755, true)) {
                            $error .= "Failed to create upload directory.<br>";
                        }
                    }
                    $dest_path = $uploadFileDir . $newFileName;

                    if (move_uploaded_file($fileTmpPath, $dest_path)) {
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
    try {
        $stmtTable = $conn->prepare("SELECT table_number FROM tables WHERE table_id = ?");
        $stmtTable->execute([$tableId]);
        $table = $stmtTable->fetch(PDO::FETCH_ASSOC);
        if ($table) {
            $tableName = $table['table_number'];
        } else {
            $error .= "Invalid table selected.<br>";
        }
    } catch (PDOException $e) {
        error_log("Database query failed: " . $e->getMessage());
        $error .= "An internal error occurred. Please try again later.<br>";
    }

    // Proceed only if no errors so far
    if (empty($error)) {
        if ($bookingType === 'hour') {
            $startTime = isset($_POST['start_time']) ? trim($_POST['start_time']) : '';
            $endTime = isset($_POST['end_time']) ? trim($_POST['end_time']) : '';

            // Validate date and time
            if (empty($startTime) || empty($endTime)) {
                $error .= "Start time and end time are required.<br>";
            } elseif (strtotime($startTime) >= strtotime($endTime)) {
                $error .= "End time must be after start time.<br>";
            }

            if (empty($error)) {
                try {
                    // Check for overlapping bookings
                    $stmtCheck = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE table_id = ? AND (
                        (start_time < ? AND end_time > ?) OR
                        (start_time < ? AND end_time > ?) OR
                        (start_time >= ? AND start_time < ?)
                    )");
                    $stmtCheck->execute([$tableId, $endTime, $startTime, $startTime, $endTime, $startTime, $endTime]);
                    $isBooked = $stmtCheck->fetchColumn();

                    if ($isBooked) {
                        $error .= "The selected table is already booked during this time. Please choose a different time.<br>";
                    } else {
                        // Proceed with booking
                        $sql = "INSERT INTO bookings (table_id, table_name, user_id, start_time, end_time, num_matches, num_players, payment_method, amount, proof_of_payment, status) VALUES (?, ?, ?, ?, ?, NULL, ?, ?, ?, ?, 'Pending')";
                        $stmt = $conn->prepare($sql);
                        $executeSuccess = $stmt->execute([
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

                        if ($executeSuccess) {
                            $_SESSION['message'] = "Booking Successful";
                            header("Location: user_table.php");
                            exit();
                        } else {
                            $error .= "Failed to create booking. Please try again later.<br>";
                        }
                    }
                } catch (PDOException $e) {
                    error_log("Booking insertion failed: " . $e->getMessage());
                    $error .= "An internal error occurred. Please try again later.<br>";
                }
            }
        }
        // Handle other booking types (e.g., 'match')
        elseif ($bookingType === 'match') {
            $numMatches = isset($_POST['num_matches']) ? intval($_POST['num_matches']) : 0;

            // Validate number of matches
            if ($numMatches <= 0) {
                $error .= "Number of matches must be at least 1.<br>";
            }

            // Additional business logic can be added here (e.g., check overlapping matches)

            if (empty($error)) {
                try {
                    // Proceed with booking
                    $sql = "INSERT INTO bookings (table_id, table_name, user_id, num_matches, num_players, payment_method, amount, proof_of_payment, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending')";
                    $stmt = $conn->prepare($sql);
                    $executeSuccess = $stmt->execute([
                        $tableId,
                        $tableName,
                        $userId,
                        $numMatches,
                        $numPlayers,
                        $paymentMethod,
                        $amount,
                        $proofOfPaymentPath
                    ]);

                    if ($executeSuccess) {
                        $_SESSION['message'] = "Booking Successful";
                        header("Location: user_table.php");
                        exit();
                    } else {
                        $error .= "Failed to create booking. Please try again later.<br>";
                    }
                } catch (PDOException $e) {
                    error_log("Booking insertion failed: " . $e->getMessage());
                    $error .= "An internal error occurred. Please try again later.<br>";
                }
            }
        }
        // Handle unknown booking types
        else {
            $error .= "Unknown booking type selected.<br>";
        }
    }

    // If there are any errors, display them
    if (!empty($error)) {
        $_SESSION['message'] = $error;
        header("Location: user_table.php");
        exit();
    }
}
?>
