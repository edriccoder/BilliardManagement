<?php
require('fpdf/fpdf.php');
include 'conn.php';

function generateInventoryReport($inventoryItems) {
    // Create instance of FPDF
    $pdf = new FPDF('P', 'mm', 'A4');
    $pdf->AddPage();

    // Set title
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(190, 10, 'Inventory Report', 0, 1, 'C');
    $pdf->Ln(10); // Line break

    // Table Header for Inventory
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetFillColor(200, 220, 255); // Light blue background
    $pdf->Cell(20, 10, 'Item ID', 1, 0, 'C', true);
    $pdf->Cell(50, 10, 'Item Name', 1, 0, 'C', true);
    $pdf->Cell(30, 10, 'Quantity', 1, 0, 'C', true);
    $pdf->Cell(90, 10, 'Description', 1, 1, 'C', true);

    // Set font for the data rows
    $pdf->SetFont('Arial', '', 12);

    // Loop through inventory data
    foreach ($inventoryItems as $item) {
        $pdf->Cell(20, 10, htmlspecialchars($item['item_id']), 1);
        $pdf->Cell(50, 10, htmlspecialchars($item['item_name']), 1);
        $pdf->Cell(30, 10, htmlspecialchars($item['quantity']), 1);
        $pdf->Cell(90, 10, htmlspecialchars($item['description']), 1);
        $pdf->Ln(); // New line for the next row
    }

    // Output the PDF to the browser or force download
    $pdf->Output('D', 'inventory_report.pdf'); // 'D' for download, 'I' for inline display
}

// Retrieve the inventory data
$sqlInventory = "SELECT item_id, item_name, quantity, description FROM inventory";
$stmtInventory = $conn->prepare($sqlInventory);
$stmtInventory->execute();
$inventoryItems = $stmtInventory->fetchAll(PDO::FETCH_ASSOC);

// Call the function to generate the PDF report
generateInventoryReport($inventoryItems);
