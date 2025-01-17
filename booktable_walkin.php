<?php
// booktable_walkin.php

// Enable error logging
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log'); // Ensure this path is writable
error_reporting(E_ALL);

session_start();
include 'conn.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Validate CSRF token
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        !isset($_POST['csrf_token']) ||
        !isset($_SESSION['csrf_token']) ||
        $_POST['csrf_token'] !== $_SESSION['csrf_token']
    ) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'message' => "Invalid CSRF token."
        ];
        header("Location: admin_booking.php");
        exit();
    }

    // Capture and sanitize input data
    $tableId = isset($_POST['table_id']) ? intval($_POST['table_id']) : 0;
    $bookingType = isset($_POST['booking_type']) ? $_POST['booking_type'] : 'hour';
    $numPlayers = isset($_POST['num_players']) ? intval($_POST['num_players']) : 0;
    $startTime = isset($_POST['start_time']) ? $_POST['start_time'] : null;
    $endTime = isset($_POST['end_time']) ? $_POST['end_time'] : null;
    $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0.00;
    $contactNumber = isset($_POST['contact_number']) ? trim($_POST['contact_number']) : '';
    $paymentMethod = 'cash'; // Only Cash is allowed
    $customerName = isset($_POST['customer_name']) ? trim($_POST['customer_name']) : '';
    

    // Validate required fields
    if ($tableId <= 0 || $numPlayers <= 0 || $amount <= 0.00 || empty($customerName)) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'message' => "Please provide all required fields with valid values."
        ];
        header("Location: admin_booking.php");
        exit();
    }
    if (empty($contactNumber) || !preg_match('/^\+?\d{10,15}$/', $contactNumber)) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'message' => "Please provide a valid contact number."
        ];
        header("Location: admin_booking.php");
        exit();
    }


    try {
        // Fetch table_number and status based on table_id
        $stmtTable = $conn->prepare("SELECT table_number, status FROM tables WHERE table_id = ?");
        $stmtTable->execute([$tableId]);
        $table = $stmtTable->fetch();

        if (!$table) {
            $_SESSION['alert'] = [
                'type' => 'error',
                'message' => "Selected table does not exist."
            ];
            header("Location: admin_booking.php");
            exit();
        }

        if ($table['status'] !== 'Available') {
            $_SESSION['alert'] = [
                'type' => 'error',
                'message' => "Selected table is not available."
            ];
            header("Location: admin_booking.php");
            exit();
        }

        $tableName = $table['table_number'];

        // Depending on booking type, handle accordingly
        if ($bookingType === 'hour') {
            // Validate start and end times
            if (empty($startTime) || empty($endTime)) {
                $_SESSION['alert'] = [
                    'type' => 'error',
                    'message' => "Please provide both start and end times for hour-based booking."
                ];
                header("Location: admin_booking.php");
                exit();
            }

            // Ensure start_time is before end_time
            if (strtotime($startTime) >= strtotime($endTime)) {
                $_SESSION['alert'] = [
                    'type' => 'error',
                    'message' => "End time must be after start time."
                ];
                header("Location: admin_booking.php");
                exit();
            }

            // Check for overlapping bookings
            $stmtCheck = $conn->prepare("
                SELECT COUNT(*) 
                FROM bookings 
                WHERE table_id = ? 
                  AND archive = 0 
                  AND (
                      (start_time < ? AND end_time > ?) OR
                      (start_time < ? AND end_time > ?) OR
                      (start_time >= ? AND start_time < ?)
                  )
            ");
            $stmtCheck->execute([
                $tableId,
                $endTime, $startTime,
                $startTime, $endTime,
                $startTime, $endTime
            ]);
            $isBooked = $stmtCheck->fetchColumn();

            if ($isBooked) {
                $_SESSION['alert'] = [
                    'type' => 'error',
                    'message' => "The selected table is already booked during this time. Please choose a different time."
                ];
                header("Location: admin_booking.php");
                exit();
            }

            // Insert hour-based booking with user_id set to NULL for walk-in
            $sqlInsertBooking = "
                INSERT INTO bookings 
                    (table_id, table_name, user_id, customer_name, contact_number, start_time, end_time, num_players, num_matches, status, archive) 
                VALUES 
                    (?, ?, NULL, ?, ?, ?, ?, ?, NULL, 'Confirmed', 0)
            ";
            $stmtInsertBooking = $conn->prepare($sqlInsertBooking);
            $stmtInsertBooking->execute([
                $tableId,
                $tableName,
                $customerName,
                $contactNumber, // add contact number here
                $startTime,
                $endTime,
                $numPlayers
            ]);


            $bookingId = $conn->lastInsertId();

            // Insert transaction for Cash payment
            $sqlInsertTransaction = "
                INSERT INTO transactions 
                    (booking_id, amount, payment_method, status, timestamp) 
                VALUES 
                    (?, ?, 'cash', 'Confirmed', NOW())
            ";
            $stmtInsertTransaction = $conn->prepare($sqlInsertTransaction);
            $stmtInsertTransaction->execute([
                $bookingId,
                $amount
            ]);

            // Insert admin notification
            $message = "New walk-in booking confirmed for Table {$tableName} by {$customerName} from {$startTime} to {$endTime}.";
            $stmtAdminNotif = $conn->prepare("INSERT INTO admin_notifications (user_id, message, created_at) VALUES (NULL, ?, NOW())");
            $stmtAdminNotif->execute([$message]);

            // Success alert
            $_SESSION['alert'] = [
                'type' => 'success',
                'message' => "Walk-In booking successfully confirmed and recorded with Cash payment."
            ];
            header("Location: admin_booking.php");
            exit();

        } else {
            // If you plan to handle other booking types in the future
            $_SESSION['alert'] = [
                'type' => 'error',
                'message' => "Invalid booking type selected."
            ];
            header("Location: admin_booking.php");
            exit();
        }

    } catch (PDOException $e) {
        error_log("SQL Error (Booking Walk-In): " . $e->getMessage());
        $_SESSION['alert'] = [
            'type' => 'error',
            'message' => "An error occurred while processing the booking."
        ];
        header("Location: admin_booking.php");
        exit();
    }
} else {
    // If not a POST request, redirect back
    header("Location: admin_booking.php");
    exit();
}
?>
