<?php
session_start();
include 'conn.php';
require_once('vendor/autoload.php'); // Ensure TCPDF is installed via Composer

use TCPDF;

// Function to generate the PDF receipt
function generate_invoice($booking) {
    // Create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Document Information
    $pdf->SetCreator('T JAMES SPORTY BAR');
    $pdf->SetAuthor('T JAMES SPORTY BAR');
    $pdf->SetTitle('Receipt for Booking ID: ' . htmlspecialchars($booking['booking_id']));
    $pdf->SetSubject('Invoice for Booking ID: ' . htmlspecialchars($booking['booking_id']));
    $pdf->SetKeywords('TCPDF, PDF, invoice, booking, receipt');

    // Header Data
    $logoFile = 'path/to/logo.png'; // Replace with the path to your logo image
    $pdf->SetHeaderData($logoFile, 30, 'T JAMES SPORTY BAR', "1234 Sporty Ave.\nCity, State, ZIP\nPhone: (123) 456-7890\nEmail: info@tjamessportybar.com");

    // Header and Footer Fonts
    $pdf->setHeaderFont(['helvetica', '', 12]);
    $pdf->setFooterFont(['helvetica', '', 10]);

    // Default Monospaced Font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    // Margins
    $pdf->SetMargins(15, 45, 15); // Left, Top, Right
    $pdf->SetHeaderMargin(10);
    $pdf->SetFooterMargin(15);

    // Auto Page Break
    $pdf->SetAutoPageBreak(TRUE, 25);

    // Image Scale
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    // Font
    $pdf->SetFont('helvetica', '', 12);

    // Add a page
    $pdf->AddPage();

    // Title
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, 'Receipt', 0, 1, 'C');
    $pdf->Ln(5);

    // Receipt Information
    $pdf->SetFont('helvetica', '', 12);
    $html = '
    <table cellspacing="0" cellpadding="5" border="0">
        <tr>
            <td><strong>Invoice Number:</strong> ' . htmlspecialchars($booking['booking_id']) . '</td>
            <td><strong>Date:</strong> ' . date('F j, Y, g:i a') . '</td>
        </tr>
        <tr>
            <td><strong>Customer ID:</strong> ' . htmlspecialchars($booking['user_id']) . '</td>
            <td><strong>Status:</strong> ' . htmlspecialchars(ucfirst($booking['status'])) . '</td>
        </tr>
    </table>
    ';
    $pdf->writeHTML($html, true, false, false, false, '');
    $pdf->Ln(5);

    // Booking Details Table
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Booking Details', 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 12);

    $html = '
    <table cellspacing="0" cellpadding="5" border="1">
        <thead>
            <tr style="background-color:#f2f2f2;">
                <th width="20%">Field</th>
                <th width="80%">Details</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Booking ID</strong></td>
                <td>' . htmlspecialchars($booking['booking_id']) . '</td>
            </tr>
            <tr>
                <td><strong>User ID</strong></td>
                <td>' . htmlspecialchars($booking['user_id']) . '</td>
            </tr>
            <tr>
                <td><strong>Table Name</strong></td>
                <td>' . htmlspecialchars($booking['table_name']) . '</td>
            </tr>
            <tr>
                <td><strong>Start Time</strong></td>
                <td>' . htmlspecialchars(date('F j, Y, g:i a', strtotime($booking['start_time']))) . '</td>
            </tr>
            <tr>
                <td><strong>End Time</strong></td>
                <td>' . htmlspecialchars(date('F j, Y, g:i a', strtotime($booking['end_time']))) . '</td>
            </tr>
            <tr>
                <td><strong>Number of Matches</strong></td>
                <td>' . htmlspecialchars($booking['num_matches']) . '</td>
            </tr>
            <tr>
                <td><strong>Amount</strong></td>
                <td>$' . number_format((float)$booking['amount'], 2) . '</td>
            </tr>
            <tr>
                <td><strong>Payment Method</strong></td>
                <td>' . htmlspecialchars(ucfirst($booking['payment_method'])) . '</td>
            </tr>';
    
    // Include proof of payment image if available
    if (!empty($booking['proof_of_payment'])) {
        $proofImage = htmlspecialchars($booking['proof_of_payment']);
        $html .= '
            <tr>
                <td><strong>Proof of Payment</strong></td>
                <td><img src="' . $proofImage . '" alt="Proof of Payment" style="max-width:150px; max-height:150px;"></td>
            </tr>';
    }

    $html .= '
        </tbody>
    </table>
    ';
    $pdf->writeHTML($html, true, false, false, false, '');
    $pdf->Ln(10);

    // Thank You Note
    $pdf->SetFont('helvetica', 'I', 12);
    $pdf->Cell(0, 10, 'Thank you for your business!', 0, 1, 'C');

    // Output PDF
    $pdf->Output('receipt_' . htmlspecialchars($booking['booking_id']) . '.pdf', 'I');
}

// Handle the request
if (isset($_GET['booking_id'])) {
    $booking_id = intval($_GET['booking_id']);

    try {
        // Prepare and execute the SQL statement
        $stmt = $conn->prepare("
            SELECT b.booking_id, b.user_id, b.table_id, b.table_name, b.start_time, b.end_time, b.status, b.num_matches, 
                   t.amount, t.payment_method, t.proof_of_payment
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
            // Booking not found
            echo "<h2>Error</h2><p>Booking not found.</p>";
        }
    } catch (PDOException $e) {
        // Handle SQL errors
        echo "<h2>Database Error</h2><p>" . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    // No booking ID provided
    echo "<h2>Error</h2><p>No booking ID provided.</p>";
}
?>
