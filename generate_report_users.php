<?php
require('fpdf/fpdf.php');
include 'conn.php';

function generateUserCashierReport($users, $cashiers) {
    // Create instance of FPDF
    $pdf = new FPDF('P', 'mm', 'A4');
    $pdf->AddPage();

    // Set title
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(190, 10, 'User and Cashier Report', 0, 1, 'C');
    $pdf->Ln(10); // Line break

    // Section: Users
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(190, 10, 'Users List', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 12);

    // Table Header for Users
    $pdf->SetFillColor(200, 220, 255); // Light blue background
    $pdf->Cell(60, 10, 'Name', 1, 0, 'C', true);
    $pdf->Cell(65, 10, 'Email', 1, 0, 'C', true);
    $pdf->Cell(65, 10, 'Username', 1, 1, 'C', true);

    // Loop through users data
    foreach ($users as $user) {
        $pdf->Cell(60, 10, htmlspecialchars($user['name']), 1);
        $pdf->Cell(65, 10, htmlspecialchars($user['email']), 1);
        $pdf->Cell(65, 10, htmlspecialchars($user['username']), 1);
        $pdf->Ln(); // New line
    }

    // Add space before the next section
    $pdf->Ln(10);

    // Section: Cashiers
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(190, 10, 'Cashiers List', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 12);

    // Table Header for Cashiers
    $pdf->SetFillColor(200, 220, 255);
    $pdf->Cell(60, 10, 'Name', 1, 0, 'C', true);
    $pdf->Cell(65, 10, 'Email', 1, 0, 'C', true);
    $pdf->Cell(65, 10, 'Username', 1, 1, 'C', true);

    // Loop through cashiers data
    foreach ($cashiers as $cashier) {
        $pdf->Cell(60, 10, htmlspecialchars($cashier['name']), 1);
        $pdf->Cell(65, 10, htmlspecialchars($cashier['email']), 1);
        $pdf->Cell(65, 10, htmlspecialchars($cashier['username']), 1);
        $pdf->Ln(); // New line
    }

    // Output the PDF to the browser
    $pdf->Output('D', 'user_cashier_report.pdf'); // 'D' forces download, 'I' displays in the browser
}

// Retrieve user and cashier data from the database
$sql_users = "SELECT user_id, name, email, username FROM users WHERE role = 'user'";
$stmt_users = $conn->prepare($sql_users);
$stmt_users->execute();
$users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);

$sql_cashiers = "SELECT user_id, name, email, username FROM users WHERE role = 'cashier'";
$stmt_cashiers = $conn->prepare($sql_cashiers);
$stmt_cashiers->execute();
$cashiers = $stmt_cashiers->fetchAll(PDO::FETCH_ASSOC);

// Call the function to generate the PDF report
generateUserCashierReport($users, $cashiers);
