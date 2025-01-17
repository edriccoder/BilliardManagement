<?php
require('fpdf/fpdf.php');
include 'conn.php';

// Enable error reporting for debugging purposes
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Function to generate the inventory PDF report
 *
 * @param array $inventoryItems Array of inventory items
 * @param array $transactions Array of inventory transactions
 */
function generateInventoryReport($inventoryItems, $transactions) {
    try {
        // Create instance of FPDF with Landscape orientation
        $pdf = new FPDF('L', 'mm', 'A4');
        $pdf->AddPage();

        // Company Logo
        $logoPath = 'img/tjamesLOGO.jpg';
        if (file_exists($logoPath)) {
            $pdf->Image($logoPath, 10, 10, 40); // Adjust size as needed
        }

        // Company Name and Address
        $pdf->SetFont('Arial', 'B', 20);
        $pdf->Cell(280, 10, 'T James Sporty Bar', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 14);
        $pdf->Cell(280, 7, '123 Business Address, City, Country', 0, 1, 'C');
        $pdf->Cell(280, 7, 'Phone: +1234567890 | Email: info@company.com', 0, 1, 'C');
        $pdf->Ln(10); // Line break

        // Report Title and Date
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(280, 10, 'Comprehensive Inventory Report', 0, 1, 'C');
        $pdf->SetFont('Arial', 'I', 12);
        $pdf->Cell(280, 7, 'Date: ' . date('F j, Y'), 0, 1, 'C');
        $pdf->Ln(10); // Additional line break

        // Summary Section
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(280, 10, 'Inventory Summary', 0, 1);
        $pdf->SetFont('Arial', '', 12);
        $totalItems = count($inventoryItems);
        $totalQuantity = array_sum(array_column($inventoryItems, 'quantity'));
        $pdf->Cell(140, 7, 'Total Unique Items: ' . $totalItems, 0, 0);
        $pdf->Cell(140, 7, 'Total Quantity: ' . $totalQuantity, 0, 1);
        $pdf->Ln(5);

        // Table Header for Inventory
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(200, 220, 255); // Light blue background
        $pdf->Cell(30, 10, 'Item ID', 1, 0, 'C', true);
        $pdf->Cell(70, 10, 'Item Name', 1, 0, 'C', true);
        $pdf->Cell(40, 10, 'Quantity', 1, 0, 'C', true);
        $pdf->Cell(140, 10, 'Description', 1, 1, 'C', true);

        // Set font for the data rows
        $pdf->SetFont('Arial', '', 12);

        // Loop through inventory data
        foreach ($inventoryItems as $item) {
            $pdf->Cell(30, 10, htmlspecialchars($item['item_id']), 1);
            $pdf->Cell(70, 10, htmlspecialchars($item['item_name']), 1);
            $pdf->Cell(40, 10, htmlspecialchars($item['quantity']), 1);
            $pdf->Cell(140, 10, htmlspecialchars($item['description']), 1);
            $pdf->Ln(); // New line for the next row
        }

        $pdf->Ln(10); // Space before transactions

        // Transactions Section
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(280, 10, 'Inventory Transactions', 0, 1);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(200, 220, 255);
        $pdf->Cell(30, 10, 'Trans ID', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Item ID', 1, 0, 'C', true);
        $pdf->Cell(40, 10, 'Type', 1, 0, 'C', true);
        $pdf->Cell(40, 10, 'Quantity', 1, 0, 'C', true);
        $pdf->Cell(90, 10, 'Description', 1, 0, 'C', true);
        $pdf->Cell(50, 10, 'Date & Time', 1, 1, 'C', true);

        $pdf->SetFont('Arial', '', 12);

        foreach ($transactions as $trans) {
            $pdf->Cell(30, 10, htmlspecialchars($trans['transaction_id']), 1);
            $pdf->Cell(30, 10, htmlspecialchars($trans['item_id']), 1);
            $pdf->Cell(40, 10, ucfirst(htmlspecialchars($trans['transaction_type'])), 1);
            $pdf->Cell(40, 10, htmlspecialchars($trans['quantity']), 1);
            $pdf->Cell(90, 10, htmlspecialchars($trans['description']), 1);
            $pdf->Cell(50, 10, htmlspecialchars($trans['date_time']), 1);
            $pdf->Ln();
        }

        // Output the PDF to the browser for download
        $pdf->Output('D', 'inventory_report.pdf'); // 'D' for download, 'I' for inline display

    } catch (Exception $e) {
        echo 'Error generating PDF: ' . $e->getMessage();
    }
}

// Fetch date range from GET request
$startDate = $_GET['start_date'] ?? null;
$endDate = $_GET['end_date'] ?? null;

// Validate and format dates
if ($startDate && $endDate) {
    try {
        $startDate = date('Y-m-d', strtotime($startDate));
        $endDate = date('Y-m-d', strtotime($endDate));
    } catch (Exception $e) {
        die('Invalid date format.');
    }
} else {
    die('Please provide a valid start and end date.');
}

// Retrieve the inventory data within the specified date range
try {
    $sqlInventory = "SELECT item_id, item_name, quantity, description, image, date 
                     FROM inventory 
                     WHERE DATE(date) BETWEEN :start_date AND :end_date";
    $stmtInventory = $conn->prepare($sqlInventory);
    $stmtInventory->bindParam(':start_date', $startDate);
    $stmtInventory->bindParam(':end_date', $endDate);
    $stmtInventory->execute();
    $inventoryItems = $stmtInventory->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Error retrieving inventory data: ' . $e->getMessage());
}

// Retrieve transactions within the date range
try {
    $sqlTransactions = "SELECT transaction_id, item_id, transaction_type, quantity, description, date_time 
                        FROM inventory_transactions 
                        WHERE DATE(date_time) BETWEEN :start_date AND :end_date
                        ORDER BY date_time DESC";
    $stmtTransactions = $conn->prepare($sqlTransactions);
    $stmtTransactions->bindParam(':start_date', $startDate);
    $stmtTransactions->bindParam(':end_date', $endDate);
    $stmtTransactions->execute();
    $transactions = $stmtTransactions->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Error retrieving transaction data: ' . $e->getMessage());
}

// Check if there is data to generate the report
if (!empty($inventoryItems) || !empty($transactions)) {
    generateInventoryReport($inventoryItems, $transactions);
} else {
    echo 'No data available to generate the report within the specified date range.';
}
?>
