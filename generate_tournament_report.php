<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'conn.php';

// Fetch tournaments with optional date filters
$startDate = $_GET['start_date'] ?? '';
$endDate = $_GET['end_date'] ?? '';
$whereClause = '';

if (!empty($startDate) && !empty($endDate)) {
    $whereClause = "WHERE DATE(start_date) BETWEEN :start_date AND :end_date";
}

$sqlTournaments = "SELECT tournament_id, name, DATE(start_date) AS start_date, DATE(end_date) AS end_date, 
                  DATE_FORMAT(start_time, '%h:%i %p') AS start_time, DATE_FORMAT(end_time, '%h:%i %p') AS end_time, 
                  max_player, fee, prize, status 
                  FROM tournaments 
                  $whereClause 
                  ORDER BY start_date DESC";

$stmtTournaments = $conn->prepare($sqlTournaments);

if (!empty($whereClause)) {
    $stmtTournaments->bindParam(':start_date', $startDate);
    $stmtTournaments->bindParam(':end_date', $endDate);
}

$stmtTournaments->execute();
$tournaments = $stmtTournaments->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['generate_report'])) {
    require('fpdf/fpdf.php');
    generateTournamentReport($tournaments, $conn);
}

function generateTournamentReport($tournaments, $conn) {
    try {
        // Extend the FPDF class to create custom Header and Footer
        class PDF extends FPDF {
            // Header function to create a consistent header for each page
            function Header() {
                // Logo
                $logoPath = 'img/tjamesLOGO.jpg';
                if (file_exists($logoPath)) {
                    $this->Image($logoPath, 10, 10, 30);
                }

                // Header Titles
                $this->SetFont('Arial', 'B', 20);
                $this->Cell(0, 10, 'T James Sporty Bar', 0, 1, 'C');
                $this->SetFont('Arial', '', 14);
                $this->Cell(0, 7, 'Tournament Report', 0, 1, 'C');
                $this->SetFont('Arial', 'I', 12);
                $this->Cell(0, 7, 'Date: ' . date('F j, Y'), 0, 1, 'C');
                $this->Ln(10);
            }

            // Footer function to add page numbers
            function Footer() {
                $this->SetY(-15);
                $this->SetFont('Arial', 'I', 8);
                $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
            }
        }

        $pdf = new PDF('L', 'mm', 'A4');
        $pdf->AddPage();

        // Set initial font
        $pdf->SetFont('Arial', '', 12);

        foreach ($tournaments as $tournament) {
            // Tournament Details
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->Cell(0, 10, 'Tournament: ' . htmlspecialchars($tournament['name']), 0, 1);
            $pdf->SetFont('Arial', '', 12);

            // Tournament Information
            $pdf->SetFillColor(230, 230, 230);
            $pdf->Cell(40, 8, 'Start Date:', 1, 0, 'L', true);
            $pdf->Cell(50, 8, htmlspecialchars($tournament['start_date']), 1, 0, 'L');
            $pdf->Cell(40, 8, 'End Date:', 1, 0, 'L', true);
            $pdf->Cell(50, 8, htmlspecialchars($tournament['end_date']), 1, 1, 'L');

            $pdf->Cell(40, 8, 'Start Time:', 1, 0, 'L', true);
            $pdf->Cell(50, 8, htmlspecialchars($tournament['start_time']), 1, 0, 'L');
            $pdf->Cell(40, 8, 'End Time:', 1, 0, 'L', true);
            $pdf->Cell(50, 8, htmlspecialchars($tournament['end_time']), 1, 1, 'L');


            $pdf->Cell(40, 8, 'Max Players:', 1, 0, 'L', true);
            $pdf->Cell(50, 8, htmlspecialchars($tournament['max_player']), 1, 0, 'L');
            $pdf->Cell(40, 8, 'Fee:', 1, 0, 'L', true);
            $pdf->Cell(50, 8, htmlspecialchars(number_format($tournament['fee'], 2)), 1, 1, 'L');

            $pdf->Cell(40, 8, 'Prize:', 1, 0, 'L', true);
            $pdf->Cell(50, 8, htmlspecialchars(number_format($tournament['prize'], 2)), 1, 0, 'L');
            $pdf->Cell(40, 8, 'Status:', 1, 0, 'L', true);
            $pdf->Cell(50, 8, htmlspecialchars($tournament['status']), 1, 1, 'L');

            $pdf->Ln(5);

            // Fetch players for this tournament
            $sqlPlayers = "SELECT username 
                           FROM players 
                           WHERE tournament_id = :tournament_id 
                           ORDER BY username ASC";
            $stmtPlayers = $conn->prepare($sqlPlayers);
            $stmtPlayers->bindParam(':tournament_id', $tournament['tournament_id'], PDO::PARAM_INT);
            $stmtPlayers->execute();
            $players = $stmtPlayers->fetchAll(PDO::FETCH_ASSOC);

            if ($players) {
                // Player List Header
                $pdf->SetFont('Arial', 'B', 12);
                $pdf->SetFillColor(200, 220, 255);
                $pdf->Cell(80, 10, 'Player Username Joined', 1, 0, 'C', true);
                $pdf->Ln();

                // Player Data
                $pdf->SetFont('Arial', '', 12);
                foreach ($players as $player) {
                    $pdf->Cell(80, 10, htmlspecialchars($player['username']), 1, 0, 'L');
                    $pdf->Ln();
                }
            } else {
                $pdf->SetFont('Arial', 'I', 12);
                $pdf->Cell(0, 10, 'No players have joined this tournament.', 0, 1, 'C');
            }

            $pdf->Ln(15); // Space before next tournament
        }

        // Output the PDF
        $pdf->Output('D', 'tournament_report.pdf');
    } catch (Exception $e) {
        echo 'Error generating PDF: ' . $e->getMessage();
    }
}
?>
