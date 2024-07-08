<?php
include 'conn.php';

$tournament_id = $_GET['tournament_id'];
$sql = "SELECT * FROM tournaments WHERE tournament_id = :tournament_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':tournament_id', $tournament_id);
$stmt->execute();
$tournament = $stmt->fetch(PDO::FETCH_ASSOC);

$max_player = $tournament['max_player'];

function generateBracket($max_player) {
    $rounds = ceil(log($max_player, 2));
    $bracket = '<div class="container">
            <h1 class="text-center">Single Elimination Tournament Bracket</h1>
            <div class="bracket">';
    
    for ($i = 1; $i <= $rounds; $i++) {
        $bracket .= '<div class="round">
                         <h2>Round ' . $i . '</h2>';
        $matches = pow(2, $rounds - $i);
        for ($j = 1; $j <= $matches; $j++) {
            $bracket .= '<div class="match">
                             <div class="team">Team ' . ($j * 2 - 1) . '</div>
                             <div class="team">Team ' . ($j * 2) . '</div>
                         </div>';
        }
        $bracket .= '</div>';
    }
    
    $bracket .= '<div class="round">
                     <h2>Final</h2>
                     <div class="match">
                         <div class="team">Winner</div>
                     </div>
                 </div>
             </div>
         </div>';

    return $bracket;
}

echo generateBracket($max_player);
?>