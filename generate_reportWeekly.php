<?php
require('fpdf/fpdf.php');
require('conn.php'); // Include the database connection

// SQL query
// SQL query to fetch today's bookings only
// SQL query to fetch bookings from the last 7 days
$sqlBookings = "SELECT b.booking_id, b.user_id, b.table_id, b.table_name, b.start_time, b.end_time, b.status, b.num_matches, t.amount, t.payment_method, t.proof_of_payment
                FROM bookings b
                LEFT JOIN transactions t ON b.booking_id = t.booking_id
                WHERE DATE(b.start_time) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";



try {
    $stmt = $conn->prepare($sqlBookings);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($result) > 0) {
        // Create instance of FPDF
        $pdf = new FPDF('L', 'mm', array(216, 356)); // 'L' for Landscape, 'mm' for millimeters, 'A4' for page size
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 14);

        // Title
        $pdf->Cell(0, 10, 'T JAMES SPORTY BAR SALES REPORT', 0, 1, 'C');
        $pdf->Ln(10); // Line break

        // Column headers
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(30, 10, 'Booking ID', 1);
        $pdf->Cell(20, 10, 'User ID', 1);
        $pdf->Cell(20, 10, 'Table ID', 1);
        $pdf->Cell(50, 10, 'Table Name', 1);
        $pdf->Cell(50, 10, 'Start Time', 1);
        $pdf->Cell(50, 10, 'End Time', 1);
        $pdf->Cell(30, 10, 'Status', 1);
        $pdf->Cell(20, 10, 'Matches', 1);
        $pdf->Cell(20, 10, 'Amount', 1);
        $pdf->Cell(40, 10, 'Payment Method', 1);
        $pdf->Ln();

        // Data rows
        $pdf->SetFont('Arial', '', 12);
        foreach ($result as $row) {
            $pdf->Cell(30, 10, $row['booking_id'], 1);
            $pdf->Cell(20, 10, $row['user_id'], 1);
            $pdf->Cell(20, 10, $row['table_id'], 1);
            $pdf->Cell(50, 10, $row['table_name'], 1);
            $pdf->Cell(50, 10, $row['start_time'], 1);
            $pdf->Cell(50, 10, $row['end_time'], 1);
            $pdf->Cell(30, 10, $row['status'], 1);
            $pdf->Cell(20, 10, $row['num_matches'], 1);
            $pdf->Cell(20, 10, $row['amount'], 1);
            $pdf->Cell(40, 10, $row['payment_method'], 1);
            $pdf->Ln();
        }

        $pdf->Output();
    } else {
        echo "No results found.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$conn = null; // Close the database connection
?>