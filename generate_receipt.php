<?php
session_start();
include 'conn.php';

if (!isset($_GET['booking_id'])) {
    die("Booking ID is required.");
}

$booking_id = htmlspecialchars($_GET['booking_id'], ENT_QUOTES, 'UTF-8');

// Fetch booking details
$sqlBooking = "SELECT b.booking_id, b.user_id, b.table_id, b.table_name, b.start_time, b.end_time, b.status, t.amount, t.payment_method
               FROM bookings b
               LEFT JOIN transactions t ON b.booking_id = t.booking_id
               WHERE b.booking_id = :booking_id";
$stmtBooking = $conn->prepare($sqlBooking);
$stmtBooking->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);
$stmtBooking->execute();
$booking = $stmtBooking->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    die("Booking not found.");
}

// Fetch username
$sqlUser = "SELECT username FROM users WHERE user_id = :user_id";
$stmtUser = $conn->prepare($sqlUser);
$stmtUser->bindParam(':user_id', $booking['user_id'], PDO::PARAM_INT);
$stmtUser->execute();
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

$username = htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8');

// Check if the booking status is cancelled or pending
if ($booking['status'] == 'Canceled' || $booking['status'] == 'Pending') {
    echo "<script>alert('Cannot generate receipt for a booking with status: " . htmlspecialchars($booking['status'], ENT_QUOTES, 'UTF-8') . "');</script>";
    echo "<script>window.history.back();</script>";
    exit();
}

// Check if GD library is enabled
if (!function_exists('imagecreatetruecolor')) {
    die("GD library is not enabled.");
}

// Create the receipt image
$width = 400;
$height = 300;
$image = imagecreatetruecolor($width, $height);

// Set the colors
$white = imagecolorallocate($image, 255, 255, 255);
$black = imagecolorallocate($image, 0, 0, 0);
$grey = imagecolorallocate($image, 128, 128, 128);

// Fill the background
imagefill($image, 0, 0, $white);

// Set the font path
$font = __DIR__ . '/arial.ttf'; // Ensure you have a TTF font file in the same directory

// Check if the font file exists
if (!file_exists($font)) {
    die("Font file not found.");
}

// Add text to the image
imagettftext($image, 16, 0, 10, 30, $black, $font, 'Booking Receipt');
imagettftext($image, 12, 0, 10, 60, $grey, $font, 'Booking ID: ' . htmlspecialchars($booking['booking_id'], ENT_QUOTES, 'UTF-8'));
imagettftext($image, 12, 0, 10, 90, $grey, $font, 'Username: ' . $username);
imagettftext($image, 12, 0, 10, 120, $grey, $font, 'Table Name: ' . htmlspecialchars($booking['table_name'], ENT_QUOTES, 'UTF-8'));
imagettftext($image, 12, 0, 10, 150, $grey, $font, 'Start Time: ' . htmlspecialchars($booking['start_time'], ENT_QUOTES, 'UTF-8'));
imagettftext($image, 12, 0, 10, 180, $grey, $font, 'End Time: ' . htmlspecialchars($booking['end_time'], ENT_QUOTES, 'UTF-8'));
imagettftext($image, 12, 0, 10, 210, $grey, $font, 'Status: ' . htmlspecialchars($booking['status'], ENT_QUOTES, 'UTF-8'));
imagettftext($image, 12, 0, 10, 240, $grey, $font, 'Amount: ' . htmlspecialchars($booking['amount'], ENT_QUOTES, 'UTF-8'));
imagettftext($image, 12, 0, 10, 270, $grey, $font, 'Payment Method: ' . htmlspecialchars($booking['payment_method'], ENT_QUOTES, 'UTF-8'));

// Check if the receipts directory exists and is writable; if not, create it
$receiptsDir = 'receipts';
if (!is_dir($receiptsDir)) {
    mkdir($receiptsDir, 0777, true);
}

// Save the image as PNG
$filename = $receiptsDir . '/receipt_' . htmlspecialchars($booking['booking_id'], ENT_QUOTES, 'UTF-8') . '.png';
imagepng($image, $filename);

// Clean up
imagedestroy($image);

// Redirect to the image
header('Content-Type: image/png');
header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
readfile($filename);
?>