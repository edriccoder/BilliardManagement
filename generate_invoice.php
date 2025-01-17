<?php
session_start();
ob_start(); // Start output buffering

include 'conn.php';
require_once('vendor/autoload.php'); // Ensure TCPDF is installed via Composer
use TCPDF;

// Function to generate the PDF receipt
function generate_invoice($booking) {
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Document Information
    $pdf->SetCreator('T JAMES SPORTY BAR');
    $pdf->SetAuthor('T JAMES SPORTY BAR');
    $pdf->SetTitle('Receipt for Booking ID: ' . htmlspecialchars($booking['booking_id']));
    $pdf->SetSubject('Invoice for Booking ID: ' . htmlspecialchars($booking['booking_id']));
    $pdf->SetKeywords('TCPDF, PDF, invoice, booking, receipt');

    // Header Data
     $logoFile ='img/tjamesLOGO.jpg';
    $pdf->SetHeaderData($logoFile, 30, 'T JAMES SPORTY BAR', "1234 Sporty Ave.\nGeneral Santos City, South Cotabato, 9500\nPhone: (123) 456-7890\nEmail: info@tjamessportybar.com");

    // Set fonts, margins, and page setup
    $pdf->setHeaderFont(['helvetica', '', 12]);
    $pdf->setFooterFont(['helvetica', '', 10]);
    $pdf->SetMargins(15, 45, 15);
    $pdf->SetHeaderMargin(15);
    $pdf->SetFooterMargin(20);
    $pdf->SetAutoPageBreak(false); // Disable automatic page breaks
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    $pdf->SetFont('helvetica', '', 12);

    // Add a page and set initial styling
    $pdf->AddPage();
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, 'RECEIPT', 0, 1, 'C');
    $pdf->Ln(5);

    // Company Information (in the header)
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'T JAMES SPORTY BAR', 0, 1, 'C');
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 8, '1234 Sporty Ave.', 0, 1, 'C');
    $pdf->Cell(0, 8, 'General Santos City, South Cotabato, 9500', 0, 1, 'C');
    $pdf->Cell(0, 8, 'Phone: (123) 456-7890 | Email: info@tjamessportybar.com', 0, 1, 'C');
    $pdf->Ln(10);

    // Transaction Details
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Transaction Details', 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 10);

    // Table with transaction info
    $html = '
    <table cellpadding="5" cellspacing="0" border="1">
        <tr>
            <td><strong>Invoice No:</strong> ' . htmlspecialchars($booking['booking_id']) . '</td>
            <td><strong>Date:</strong> ' . date('F j, Y, g:i a') . '</td>
        </tr>
        <tr>
            <td><strong>Customer ID:</strong> ' . htmlspecialchars($booking['user_id']) . '</td>
            <td><strong>Status:</strong> ' . htmlspecialchars(ucfirst($booking['status'])) . '</td>
        </tr>
    </table>';
    $pdf->writeHTML($html, true, false, false, false, '');
    $pdf->Ln(5);

    // Booking and Service Details
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Booking and Service Details', 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 10);

    $html = '
    <table cellpadding="5" cellspacing="0" border="1">
        <thead>
            <tr style="background-color:#f2f2f2;">
                <th width="25%"><strong>Item</strong></th>
                <th width="45%"><strong>Description</strong></th>
                <th width="15%"><strong>Quantity</strong></th>
                <th width="15%"><strong>Amount</strong></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Table Reservation</td>
                <td>' . htmlspecialchars($booking['table_name']) . ' | ' . htmlspecialchars(date('F j, Y, g:i a', strtotime($booking['start_time']))) . ' to ' . htmlspecialchars(date('F j, Y, g:i a', strtotime($booking['end_time']))) . '</td>
                <td>1</td>
                <td>' . number_format((float)$booking['amount'], 2) . '</td>
            </tr>
            <tr>
                <td>Number of Matches</td>
                <td>Total matches during reservation</td>
                <td>' . htmlspecialchars($booking['num_matches']) . '</td>
                <td>Included</td>
            </tr>
            <tr>
                <td>Payment Method</td>
                <td colspan="3">' . htmlspecialchars(ucfirst($booking['payment_method'])) . '</td>
            </tr>';
    
    // Include proof of payment image if available
    if (!empty($booking['proof_of_payment'])) {
        $proofImagePath = $booking['proof_of_payment'];
        if (file_exists($proofImagePath)) {
            $html .= '
            <tr>
                <td>Proof of Payment</td>
                <td colspan="3"><img src="' . $proofImagePath . '" style="width:100px; height:auto;"></td>
            </tr>';
        } else {
            $html .= '
            <tr>
                <td>Proof of Payment</td>
                <td colspan="3">Image not available.</td>
            </tr>';
        }
    }

    $html .= '
        </tbody>
    </table>';
    $pdf->writeHTML($html, true, false, false, false, '');
    $pdf->Ln(10);

    // Summary and Total Amount
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Total Amount Due', 0, 1, 'R');
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 8,number_format((float)$booking['amount'], 2), 0, 1, 'R');
    $pdf->Ln(5);

    // Additional Notes
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Terms and Conditions', 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 10);
    $terms = '
    <p><strong>Note:</strong> This receipt serves as proof of payment</p>
    <p>for services rendered at T JAMES SPORTY BAR.</p>
    <p>Please retain this receipt for your records.</p>';
        
    $pdf->writeHTML($terms, true, false, false, false, '');
    $pdf->Ln(5);

    // QR Code with Booking Link
    $bookingURL = "https://www.tjamessportybar.com/bookings.php?booking_id=" . urlencode($booking['booking_id']);
    $pdf->write2DBarcode($bookingURL, 'QRCODE,H', 150, 245, 30, 30);
    $pdf->Text(150, 275, 'Scan for Booking Details');

    // Output PDF
    ob_end_clean(); // Clean any buffered output before sending the PDF
    $pdf->Output('receipt_' . htmlspecialchars($booking['booking_id']) . '.pdf', 'I');
}

// Handle the request
if (isset($_GET['booking_id'])) {
    $booking_id = intval($_GET['booking_id']);

    try {
        // Prepare and execute the SQL statement
        $stmt = $conn->prepare("SELECT b.booking_id, b.user_id, b.table_id, b.table_name, b.start_time, b.end_time, b.status, b.num_matches, t.amount, t.payment_method, t.proof_of_payment FROM bookings b LEFT JOIN transactions t ON b.booking_id = t.booking_id WHERE b.booking_id = :booking_id");
        $stmt->bindParam(":booking_id", $booking_id, PDO::PARAM_INT);
        $stmt->execute();
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($booking) {
            generate_invoice($booking); // Generate the PDF
            exit(); // End the script after generating PDF
        } else {
            echo "<h2>Error</h2><p>Booking not found.</p>";
        }
    } catch (PDOException $e) {
        echo "<h2>Database Error</h2><p>" . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    echo "<h2>Error</h2><p>No booking ID provided.</p>";
}
?>
