<?php
session_start();
include 'conn.php';
require_once('vendor/autoload.php');

function generate_invoice($booking) {
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('T JAMES SPORTY BAR');
    $pdf->SetTitle('Invoice');
    $pdf->SetSubject('Invoice for Booking ID: ' . htmlspecialchars($booking['booking_id']));
    $pdf->SetKeywords('TCPDF, PDF, invoice, booking');

    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 12);

    $pdf->Cell(0, 10, 'Invoice', 0, 1, 'C');

    $html = '<h2>Booking Details</h2>
             <table cellspacing="0" cellpadding="1" border="1">
                <tr><th>Booking ID</th><td>' . htmlspecialchars($booking['booking_id']) . '</td></tr>
                <tr><th>User ID</th><td>' . htmlspecialchars($booking['user_id']) . '</td></tr>
                <tr><th>Table Name</th><td>' . htmlspecialchars($booking['table_name']) . '</td></tr>
                <tr><th>Start Time</th><td>' . htmlspecialchars($booking['start_time']) . '</td></tr>
                <tr><th>End Time</th><td>' . htmlspecialchars($booking['end_time']) . '</td></tr>
                <tr><th>Status</th><td>' . htmlspecialchars($booking['status']) . '</td></tr>
                <tr><th>Number Of Matches</th><td>' . htmlspecialchars($booking['num_matches']) . '</td></tr>
                <tr><th>Amount</th><td>' . htmlspecialchars($booking['amount'] ?? 'N/A') . '</td></tr>
                <tr><th>Payment Method</th><td>' . htmlspecialchars($booking['payment_method'] ?? 'N/A') . '</td></tr>
             </table>';

    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output('invoice_' . htmlspecialchars($booking['booking_id']) . '.pdf', 'I');
}

if (isset($_GET['booking_id'])) {
    $booking_id = intval($_GET['booking_id']);
    $stmt = $conn->prepare("
        SELECT b.booking_id, b.user_id, b.table_id, b.table_name, b.start_time, b.end_time, b.status, b.num_matches, t.amount, t.payment_method, t.proof_of_payment
        FROM bookings b
        LEFT JOIN transactions t ON b.booking_id = t.booking_id
        WHERE b.booking_id = :booking_id
    ");
    $stmt->bindParam(":booking_id", $booking_id, PDO::PARAM_INT);
    $stmt->execute();
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($booking) {
        generate_invoice($booking);
    } else {
        echo "Booking not found.";
    }
} else {
    echo "No booking ID provided.";
}
?>