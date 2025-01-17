<?php
// get_bookings.php
session_start();
include 'conn.php';

header('Content-Type: application/json');

// Ensure the user is authenticated
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit();
}

$current_user_id = $_SESSION['user_id'];

// Fetch all bookings, joining with transactions and users for relevant details
$sql = "SELECT 
            b.booking_id, 
            b.user_id, 
            b.table_id, 
            b.table_name, 
            b.start_time, 
            b.end_time, 
            b.status, 
            b.num_matches, 
            b.num_players, 
            b.customer_name, 
            t.amount, 
            t.payment_method, 
            t.proof_of_payment,
            COALESCE(b.customer_name, u.username) AS display_name -- Use COALESCE to prefer customer_name if available
        FROM bookings b
        LEFT JOIN transactions t ON b.booking_id = t.booking_id
        LEFT JOIN users u ON b.user_id = u.user_id
        WHERE b.archive = 0";

$stmt = $conn->prepare($sql);
$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Function to map booking status to colors
function getStatusColor($status) {
    switch (strtolower($status)) {
        case 'confirmed':
            return '#28a745'; // Green
        case 'pending':
            return '#ffc107'; // Yellow
        case 'cancelled':
            return '#dc3545'; // Red
        case 'checked out':
            return '#dc3545'; // Red
        default:
            return '#007bff'; // Blue
    }
}

$events = [];

foreach ($bookings as $booking) {
    if (!empty($booking['start_time']) && !empty($booking['end_time'])) {
        $events[] = [
            'id' => $booking['booking_id'],
            'title' => $booking['display_name'] . ' - ' . $booking['table_name'],
            'start' => $booking['start_time'],
            'end' => $booking['end_time'],
            'color' => getStatusColor($booking['status']),
            'extendedProps' => [
                'status' => $booking['status'],
                'user_id' => $booking['user_id'], // Important for authorization
                'table_id' => $booking['table_id'],
                'num_players' => $booking['num_players'],
                'amount' => $booking['amount'],
                'payment_method' => $booking['payment_method'],
                'proof_of_payment' => $booking['proof_of_payment'],
            ],
        ];
    }
}

echo json_encode($events);
?>
