<?php
// export_bracket.php

session_start();

// Enable error reporting for debugging (disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ensure the user is authenticated and has the necessary role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Unauthorized access
    http_response_code(403);
    echo "Unauthorized access.";
    exit;
}

// Start output buffering to prevent accidental output
ob_start();

// Include database connection
require_once 'conn.php';

// Include TCPDF library via Composer's autoloader
require_once 'vendor/autoload.php';

// Validate and sanitize input
if (!isset($_GET['tournament_id'])) {
    http_response_code(400); // Bad Request
    echo "Tournament ID not provided.";
    exit;
}

$tournament_id = intval($_GET['tournament_id']);

// Fetch tournament details
$stmtTournament = $conn->prepare("SELECT * FROM tournaments WHERE tournament_id = ?");
$stmtTournament->execute([$tournament_id]);
$tournament = $stmtTournament->fetch(PDO::FETCH_ASSOC);

if (!$tournament) {
    http_response_code(404); // Not Found
    echo "Tournament not found.";
    exit;
}

// Fetch bracket matches from 'bracket' table
$stmtMatches = $conn->prepare("SELECT * FROM bracket WHERE tournament_id = ? ORDER BY round, match_number");
$stmtMatches->execute([$tournament_id]);
$matches = $stmtMatches->fetchAll(PDO::FETCH_ASSOC);

if (!$matches) {
    http_response_code(404); // Not Found
    echo "No matches found for this tournament.";
    exit;
}

// Organize matches by rounds
$bracket = [];
foreach ($matches as $match) {
    $bracket[$match['round']][] = $match;
}

// Fetch all unique player IDs (player1_id, player2_id, winner_id)
$player_ids = [];
foreach ($matches as $match) {
    if (!empty($match['player1_id'])) {
        $player_ids[] = $match['player1_id'];
    }
    if (!empty($match['player2_id'])) {
        $player_ids[] = $match['player2_id'];
    }
    if (!empty($match['winner_id'])) {
        $player_ids[] = $match['winner_id'];
    }
}
$player_ids = array_values(array_unique($player_ids)); // Re-index the array

// Fetch player names from 'users' table
if (!empty($player_ids)) {
    // Prepare placeholders
    $placeholders = implode(',', array_fill(0, count($player_ids), '?'));
    $stmtUsers = $conn->prepare("SELECT user_id, name FROM users WHERE user_id IN ($placeholders)");
    try {
        $stmtUsers->execute($player_ids);
    } catch (PDOException $e) {
        http_response_code(500); // Internal Server Error
        echo "Error fetching user data: " . htmlspecialchars($e->getMessage());
        exit;
    }
    $users = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);
    
    // Create a mapping of user_id to name
    $userMap = [];
    foreach ($users as $user) {
        $userMap[$user['user_id']] = $user['name'];
    }
} else {
    $userMap = [];
}

// Fetch tournament scores from 'tournament_scores' table
$stmtScores = $conn->prepare("SELECT user_id, scores FROM tournament_scores WHERE tournament_id = ?");
$stmtScores->execute([$tournament_id]);
$scores = $stmtScores->fetchAll(PDO::FETCH_ASSOC);

// Create a mapping of user_id to scores
$scoreMap = [];
foreach ($scores as $score) {
    $scoreMap[$score['user_id']] = $score['scores'];
}

// Assign player names and scores to matches
foreach ($bracket as $round => &$matchesInRound) {
    foreach ($matchesInRound as &$match) {
        // Assign player names
        $match['player1_name'] = isset($userMap[$match['player1_id']]) ? $userMap[$match['player1_id']] : 'TBA';
        $match['player2_name'] = isset($userMap[$match['player2_id']]) ? $userMap[$match['player2_id']] : 'TBA';
        
        // Assign scores
        $match['player1_score'] = isset($scoreMap[$match['player1_id']]) ? $scoreMap[$match['player1_id']] : '';
        $match['player2_score'] = isset($scoreMap[$match['player2_id']]) ? $scoreMap[$match['player2_id']] : '';
        
        // Assign winner name
        $match['winner_name'] = isset($userMap[$match['winner_id']]) ? $userMap[$match['winner_id']] : 'TBA';
    }
}
unset($match);
unset($matchesInRound);

// Clear any output that might have been captured
ob_clean();

// Create new PDF document
$pdf = new TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('Billiard Management System');
$pdf->SetAuthor('Your Company');
$pdf->SetTitle('Tournament Bracket - ' . $tournament['name']);
$pdf->SetSubject('Bracket Export');
$pdf->SetKeywords('Tournament, Bracket, PDF');

// Remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Set margins
$pdf->SetMargins(15, 15, 15, true);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 15);

// Set font
$pdf->SetFont('helvetica', '', 12);

// Add a page
$pdf->AddPage();

// Title
$pdf->Cell(0, 10, 'Tournament Bracket: ' . $tournament['name'], 0, 1, 'C');

// Add some space
$pdf->Ln(5);

// Start building the bracket table
$html = '<table border="1" cellpadding="5" cellspacing="0" style="width: 100%; border-collapse: collapse;">';

foreach ($bracket as $round => $matchesInRound) {
    $html .= '<tr>';
    $html .= '<td><strong>Round ' . $round . '</strong></td>';
    $html .= '<td>';
    
    foreach ($matchesInRound as $match) {
        $player1 = htmlspecialchars($match['player1_name']) ?: 'TBA';
        $player2 = htmlspecialchars($match['player2_name']) ?: 'TBA';
        $winner = htmlspecialchars($match['winner_name']) ?: 'TBA';
        $score1 = htmlspecialchars($match['player1_score']) ?: '';
        $score2 = htmlspecialchars($match['player2_score']) ?: '';
        
        $html .= '<div style="margin-bottom: 10px;">';
        $html .= '<strong>Match ' . $match['match_number'] . '</strong><br>';
        $html .= $player1 . ' (' . $score1 . ') vs ' . $player2 . ' (' . $score2 . ')<br>';
        $html .= '<em>Winner: ' . $winner . '</em>';
        $html .= '</div>';
    }
    
    $html .= '</td>';
    $html .= '</tr>';
}

$html .= '</table>';

// Output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// Close and output PDF document
// 'D' forces download, 'I' displays in browser
$pdf->Output('Tournament_Bracket_' . preg_replace('/[^A-Za-z0-9\-]/', '_', $tournament['name']) . '.pdf', 'D');

exit;
?>
