<?php
require('fpdf/fpdf.php');
include 'conn.php'; // Include the database connection

class PDF extends FPDF
{
    function Header()
    {
        // Logo
        if (file_exists('img/tjamesLOGO.jpg')) {
            $this->Image('img/tjamesLOGO.jpg', 10, 6, 30);
        }
        
        // Company Information
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 5, 'T JAMES SPORTY BAR', 0, 1, 'R');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 5, '123 Billiard Avenue, Manila, Philippines', 0, 1, 'R');
        $this->Cell(0, 5, 'Phone: (02) 1234-5678 | Email: info@tjamessportybar.com', 0, 1, 'R');
        $this->Ln(10);

        // Title
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'Sales Report', 0, 1, 'C');
        $this->Ln(5);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    function FancyTable($header, $data)
    {
        $this->SetFillColor(52, 152, 219);
        $this->SetTextColor(255);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.3);
        $this->SetFont('Arial', 'B', 10);

        $columns = [
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

        foreach ($columns as $col) {
            $this->Cell($col['width'], 7, $col['label'], 1, 0, 'C', true);
        }
        $this->Ln();

        $this->SetFillColor(240, 248, 255);
        $this->SetTextColor(0);
        $this->SetFont('Arial', '', 9);

        $fill = false;
        foreach ($data as $row) {
            $this->Cell($columns[0]['width'], 6, $row['booking_id'], 'LR', 0, 'C', $fill);
            $this->Cell($columns[1]['width'], 6, $row['user_id'], 'LR', 0, 'C', $fill);
            $this->Cell($columns[2]['width'], 6, $row['user_name'], 'LR', 0, 'L', $fill);
            $this->Cell($columns[3]['width'], 6, $row['table_id'], 'LR', 0, 'C', $fill);
            $this->Cell($columns[4]['width'], 6, $row['table_name'], 'LR', 0, 'L', $fill);
            $this->Cell($columns[5]['width'], 6, $row['start_time'], 'LR', 0, 'C', $fill);
            $this->Cell($columns[6]['width'], 6, $row['end_time'], 'LR', 0, 'C', $fill);
            $this->Cell($columns[7]['width'], 6, $row['status'], 'LR', 0, 'C', $fill);
            $this->Cell($columns[8]['width'], 6, $row['amount'], 'LR', 0, 'R', $fill);
            $this->Cell($columns[9]['width'], 6, $row['payment_method'], 'LR', 0, 'L', $fill);
            $this->Ln();
            $fill = !$fill;
        }

        $this->Cell(array_sum(array_column($columns, 'width')), 0, '', 'T');
    }
}

$startDate = $_POST['start_date'] ?? null;
$endDate = $_POST['end_date'] ?? null;

if (!$startDate || !$endDate) {
    die('Please provide a valid start and end date.');
}

try {
    $sqlBookings = "
        SELECT 
            b.booking_id, 
            IFNULL(b.user_id, 'N/A') AS user_id, 
            COALESCE(u.username, b.customer_name) AS user_name, 
            b.table_id, 
            b.table_name, 
            b.start_time, 
            b.end_time, 
            b.status, 
            b.num_matches, 
            t.amount, 
            t.payment_method 
        FROM 
            bookings b
        LEFT JOIN 
            transactions t ON b.booking_id = t.booking_id
        LEFT JOIN 
            users u ON b.user_id = u.user_id
        WHERE 
            DATE(b.start_time) BETWEEN :start_date AND :end_date 
            AND b.status IN ('Confirmed', 'Checked Out')
        ORDER BY 
            b.start_time DESC
    ";

    $stmt = $conn->prepare($sqlBookings);
    $stmt->bindParam(':start_date', $startDate);
    $stmt->bindParam(':end_date', $endDate);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($result) > 0) {
        $totalAmount = 0;
        $totalBookings = count($result);
        $data = [];

        foreach ($result as $row) {
            $data[] = [
                'booking_id' => $row['booking_id'],
                'user_id' => $row['user_id'],
                'user_name' => $row['user_name'],
                'table_id' => $row['table_id'],
                'table_name' => $row['table_name'],
                'start_time' => date('d-m-Y H:i', strtotime($row['start_time'])),
                'end_time' => date('d-m-Y H:i', strtotime($row['end_time'])),
                'status' => ucfirst($row['status']),
                'amount' => number_format($row['amount'], 2),
                'payment_method' => ucfirst($row['payment_method']),
            ];
            $totalAmount += $row['amount'];
        }

        $pdf = new PDF('L', 'mm', 'A4');
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->FancyTable([], $data);

        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(50, 10, 'Total Bookings:', 0, 0, 'L');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(50, 10, $totalBookings, 0, 1, 'L');

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(50, 10, 'Total Amount:', 0, 0, 'L');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(50, 10, number_format($totalAmount, 2), 0, 1, 'L');

        $pdf->Output('I', 'Sales_Report_' . date('d-m-Y') . '.pdf');
    } else {
        echo "No results found for the selected date range.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$conn = null;
?>
