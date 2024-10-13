<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $status = isset($_POST['status']) ? $_POST['status'] : 'upcoming'; 
    $max_player = $_POST['max_player'];
    $prize = $_POST['prize'];
    $fee = $_POST['fee'];
    $qualification = $_POST['qualification']; // New qualification field

    // Begin transaction
    $conn->beginTransaction();

    try {
        // Insert into tournaments table
        $sql = "INSERT INTO tournaments (name, start_date, end_date, status, max_player, created_at, prize, fee, qualification) 
                VALUES (:name, :start_date, :end_date, :status, :max_player, NOW(), :prize, :fee, :qualification)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':end_date', $end_date);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':max_player', $max_player);
        $stmt->bindParam(':prize', $prize);
        $stmt->bindParam(':fee', $fee);
        $stmt->bindParam(':qualification', $qualification); // Bind qualification
        $stmt->execute();

        // Get the last inserted tournament_id
        $tournament_id = $conn->lastInsertId();

        // Insert into announcements table
        $title = "New Tournament: " . $name;  // Announcement title
        $body = "The tournament " . $name . " is starting on " . $start_date . 
                " and will end on " . $end_date . 
                ". Max players: " . $max_player . 
                ". Qualification: " . ucfirst($qualification) . 
                ". Status: " . ucfirst($status); // Include status
        $expires_at = $end_date;  // You can set when this announcement will expire (e.g., at the end of the tournament)

        $sqlAnnouncement = "INSERT INTO announcements (title, body, tournament_id, created_at, expires_at) 
                            VALUES (:title, :body, :tournament_id, NOW(), :expires_at)";
        $stmtAnn = $conn->prepare($sqlAnnouncement);
        $stmtAnn->bindParam(':title', $title);
        $stmtAnn->bindParam(':body', $body);
        $stmtAnn->bindParam(':tournament_id', $tournament_id);
        $stmtAnn->bindParam(':expires_at', $expires_at);
        $stmtAnn->execute();

        // Commit the transaction
        $conn->commit();

        // Redirect to a success page or display a success message
        header("Location: manage_tournament.php");
        exit();
    } catch (Exception $e) {
        // Rollback the transaction if something goes wrong
        $conn->rollBack();
        echo "Failed to insert: " . $e->getMessage();
    }
}
?>
