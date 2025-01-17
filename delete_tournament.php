<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'conn.php'; // Ensure this path is correct

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tournament_id'])) {
    // Get the tournament_id from POST data
    $tournament_id = $_POST['tournament_id'];

    // Debugging: Check if tournament_id is received properly
    if (empty($tournament_id) || !is_numeric($tournament_id)) {
        header("Location: manage_tournament.php?status=error&message=Invalid tournament ID");
        exit;
    }

    // Prepare SQL statement to delete the tournament
    $stmt = $conn->prepare("DELETE FROM tournaments WHERE tournament_id = :tournament_id");
    $stmt->bindParam(':tournament_id', $tournament_id, PDO::PARAM_INT); // Using bindParam for PDO

    try {
        if ($stmt->execute()) {
            // Redirect back to manage_tournament.php with a success message
            header("Location: manage_tournament.php?status=success&message=Tournament deleted successfully");
            exit;
        } else {
            $errorInfo = $stmt->errorInfo(); // Get error information
            header("Location: manage_tournament.php?status=error&message=Failed to delete tournament: " . urlencode($errorInfo[2]));
            exit;
        }
    } catch (Exception $e) {
        // Handle exceptions during execution
        header("Location: manage_tournament.php?status=error&message=" . urlencode($e->getMessage()));
        exit;
    }
} else {
    // Redirect back to manage_tournament.php with an invalid request message
    header("Location: manage_tournament.php?status=error&message=Invalid request");
    exit;
}
?>
