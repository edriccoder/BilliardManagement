<?php
// get_bookings_admin.php

include 'conn.php';

header('Content-Type: application/json');

try {
    $sql = "SELECT 
                b.booking_id, 
                b.user_id, 
                u.username, 
                b.customer_name,
                b.table_id, 
                b.table_name, 
                b.start_time, 
                b.end_time, 
                COALESCE(b.contact_number, u.contact_number) AS contact_number, -- Fallback to user's contact number
                b.status, 
                b.num_matches, 
                b.num_players, 
                t.amount, 
                t.payment_method
            FROM bookings b
            LEFT JOIN transactions t ON b.booking_id = t.booking_id
            LEFT JOIN users u ON b.user_id = u.user_id
            WHERE b.archive = 0";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Add debugging to check if contact_number is retrieved
    error_log("Bookings retrieved: " . print_r($bookings, true));

    // Format bookings for FullCalendar
    $events = [];
    foreach ($bookings as $booking) {
        $events[] = [
            'id' => $booking['booking_id'],
            'title' => ($booking['username'] ?? $booking['customer_name'] ?? 'Walk-In') . ' - ' . $booking['table_name'],
            'start' => $booking['start_time'],
            'end' => $booking['end_time'],
            'extendedProps' => [
                'booking_id' => $booking['booking_id'],
                'user_id' => $booking['user_id'],
                'username' => $booking['username'],
                'customer_name' => $booking['customer_name'],
                'table_name' => $booking['table_name'],
                'start_time' => $booking['start_time'],
                'end_time' => $booking['end_time'],
                'status' => $booking['status'],
                'num_matches' => $booking['num_matches'],
                'num_players' => $booking['num_players'],
                'contact_number' => $booking['contact_number'], // Now retrieves from either bookings or users
                'amount' => $booking['amount'],
                'payment_method' => $booking['payment_method'],
            ]
        ];
    }

    echo json_encode($events);
} catch (PDOException $e) {
    error_log("SQL Error (Fetching Bookings for Calendar): " . $e->getMessage());
    echo json_encode([]);
}
?>
