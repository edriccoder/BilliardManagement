<?php
require('fpdf/fpdf.php');
require('conn.php'); // Include the database connection

class PDF extends FPDF
{
    // Header function to add a header on each page
    function Header()
    {
        // Logo
        if(file_exists('img/tjamesLOGO.jpg')) { // Replace with your logo path
            $this->Image('img/tjamesLOGO.jpg', 10, 6, 30); // Adjust the position and size
        }

        // Company Information
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 5, 'T JAMES SPORTY BAR', 0, 1, 'R');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 5, '123 Billiard Avenue, Manila, Philippines', 0, 1, 'R');
        $this->Cell(0, 5, 'Phone: (02) 1234-5678 | Email: info@tjamessportybar.com', 0, 1, 'R');
        $this->Ln(10); // Line break

        // Title
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'Sales Report (Last 7 Days)', 0, 1, 'C');
        $this->Ln(5); // Line break
    }

    // Footer function to add a footer on each page
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    // Table with colored headers and adjusted column widths
    function FancyTable($header, $data)
    {
        // Set colors, line width and bold font for header
        $this->SetFillColor(52, 152, 219); // Header background color (blue)
        $this->SetTextColor(255); // Header text color (white)
        $this->SetDrawColor(0, 0, 0); // Border color
        $this->SetLineWidth(.3);
        $this->SetFont('Arial', 'B', 10); // Reduced font size for header

        // Column widths
        $cols = [
            ['label' => 'Booking ID', 'width' => 20],
            ['label' => 'User ID', 'width' => 15],
            ['label' => 'User Name', 'width' => 40],
            ['label' => 'Table ID', 'width' => 15],
            ['label' => 'Table Name', 'width' => 35],
            ['label' => 'Start Time', 'width' => 30],
            ['label' => 'End Time', 'width' => 30],
            ['label' => 'Status', 'width' => 25],
            ['label' => 'Amount', 'width' => 20],
            ['label' => 'Payment Method', 'width' => 35],
        ];

        // Header
        foreach ($cols as $col) {
            $this->Cell($col['width'], 7, $col['label'], 1, 0, 'C', true);
        }
        $this->Ln();

        // Reset colors and font for data rows
        $this->SetFillColor(240, 248, 255); // Light blue background for rows
        $this->SetTextColor(0); // Black text
        $this->SetFont('Arial', '', 9); // Smaller font size for data

        // Data
        $fill = false;
        foreach ($data as $row) {
            $this->Cell($cols[0]['width'], 6, $row['booking_id'], 'LR', 0, 'C', $fill);
            $this->Cell($cols[1]['width'], 6, $row['user_id'], 'LR', 0, 'C', $fill);
            
            // MultiCell for User Name to handle longer text
            $x = $this->GetX();
            $y = $this->GetY();
            $this->MultiCell($cols[2]['width'], 6, $row['user_name'], 'LR', 'L', $fill);
            $this->SetXY($x + $cols[2]['width'], $y);
            
            $this->Cell($cols[3]['width'], 6, $row['table_id'], 'LR', 0, 'C', $fill);
            
            // MultiCell for Table Name
            $x = $this->GetX();
            $y = $this->GetY();
            $this->MultiCell($cols[4]['width'], 6, $row['table_name'], 'LR', 'L', $fill);
            $this->SetXY($x + $cols[4]['width'], $y);
            
            $this->Cell($cols[5]['width'], 6, $row['start_time'], 'LR', 0, 'C', $fill);
            $this->Cell($cols[6]['width'], 6, $row['end_time'], 'LR', 0, 'C', $fill);
            $this->Cell($cols[7]['width'], 6, $row['status'], 'LR', 0, 'C', $fill);
            $this->Cell($cols[8]['width'], 6, $row['amount'], 'LR', 0, 'R', $fill);
            
            // MultiCell for Payment Method
            $x = $this->GetX();
            $y = $this->GetY();
            $this->MultiCell($cols[9]['width'], 6, $row['payment_method'], 'LR', 'L', $fill);
            $this->SetXY($x + $cols[9]['width'], $y);
            
            $this->Ln();
            $fill = !$fill;
        }

        // Closing line
        $this->Cell(array_sum(array_column($cols, 'width')), 0, '', 'T');
    }
}

// SQL query to fetch bookings from the last 7 days, joined with transactions and users tables
$sqlBookings = "
    SELECT 
        b.booking_id, 
        IFNULL(b.user_id, 'N/A') AS user_id, 
        COALESCE(u.name, b.customer_name) AS user_name, 
        b.table_id, 
        b.table_name, 
        b.start_time, 
        b.end_time, 
        b.status, 
        b.num_matches, 
        t.amount, 
        t.payment_method, 
        t.proof_of_payment
    FROM 
        bookings b
    LEFT JOIN 
        transactions t ON b.booking_id = t.booking_id
    LEFT JOIN 
        users u ON b.user_id = u.user_id
    WHERE 
        DATE(b.start_time) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND b.status IN ('Confirmed', 'Checked Out')
    ORDER BY 
        b.start_time DESC
";

try {
    $stmt = $conn->prepare($sqlBookings);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($result) > 0) {
        // Initialize totals
        $totalAmount = 0;
        $totalMatches = 0;
        $totalBookings = count($result);

        // Prepare data for the table
        $data = [];
        foreach ($result as $row) {
            $data[] = [
                'booking_id' => $row['booking_id'],
                'user_id' => $row['user_id'],
                'user_name' => htmlspecialchars($row['user_name']),
                'table_id' => $row['table_id'],
                'table_name' => htmlspecialchars($row['table_name']),
                'start_time' => date('d-m-Y H:i', strtotime($row['start_time'])),
                'end_time' => date('d-m-Y H:i', strtotime($row['end_time'])),
                'status' => ucfirst($row['status']),
                'amount' => number_format($row['amount'], 2),
                'payment_method' => ucfirst($row['payment_method']),
            ];

            // Accumulate totals
            $totalAmount += $row['amount'];
            $totalMatches += $row['num_matches'];
        }

        // Create instance of PDF
        $pdf = new PDF('L', 'mm', 'A4'); // Landscape, millimeters, A4 size
        $pdf->AliasNbPages(); // For total page numbers
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 12);

        // Define table headers with labels and widths
        $header = [
            ['label' => 'Booking ID', 'width' => 20],
            ['label' => 'User ID', 'width' => 15],
            ['label' => 'User Name', 'width' => 40],
            ['label' => 'Table ID', 'width' => 15],
            ['label' => 'Table Name', 'width' => 35],
            ['label' => 'Start Time', 'width' => 30],
            ['label' => 'End Time', 'width' => 30],
            ['label' => 'Status', 'width' => 15],
            ['label' => 'Amount', 'width' => 20],
            ['label' => 'Payment Method', 'width' => 35],
        ];

        // Add the table
        $pdf->FancyTable($header, $data);

        // Summary section
        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'B', 12);

        // Total Bookings
        $pdf->Cell(50, 10, 'Total Bookings:', 0, 0, 'L');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(50, 10, $totalBookings, 0, 1, 'L');

        // Total Amount
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(50, 10, 'Total Amount:', 0, 0, 'L');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(50, 10, number_format($totalAmount, 2), 0, 1, 'L');

        // Optional: Add Proof of Payment images if needed
        /*
        foreach ($result as $row) {
            if (!empty($row['proof_of_payment'])) {
                if(file_exists($row['proof_of_payment'])) {
                    $pdf->AddPage();
                    $pdf->SetFont('Arial', 'B', 14);
                    $pdf->Cell(0, 10, 'Proof of Payment for Booking ID: ' . $row['booking_id'], 0, 1, 'C');
                    $pdf->Ln(10);
                    $pdf->Image($row['proof_of_payment'], 10, 30, 190); // Adjust the position and size as needed
                }
            }
        }
        */

        // Output the PDF
        $pdf->Output('I', 'Weekly_Sales_Report_Last_7_Days.pdf'); // 'I' for inline display in browser
    } else {
        echo "No bookings found for the last 7 days.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$conn = null; // Close the database connection
?>
