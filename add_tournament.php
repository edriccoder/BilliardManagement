<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    
    // Convert start_date and end_date to proper date-time format
    $start_date = $_POST['start_date'];  // Assuming this comes with both date and time
    $end_date = $_POST['end_date'];      // Assuming this comes with both date and time
    
    $status = isset($_POST['status']) ? $_POST['status'] : 'upcoming'; 
    $max_player = $_POST['max_player'];
    $prize = $_POST['prize'];
    $fee = $_POST['fee'];
    $qualification = $_POST['qualification']; // New qualification field

    // Begin transaction
    $conn->beginTransaction();

    try {
        // Insert into tournaments table, including date and time fields for start_date and end_date
        $sql = "INSERT INTO tournaments (name, start_date, end_date, status, max_player, created_at, prize, fee, qualification) 
                VALUES (:name, :start_date, :end_date, :status, :max_player, NOW(), :prize, :fee, :qualification)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':start_date', $start_date);  // Insert both date and time
        $stmt->bindParam(':end_date', $end_date);      // Insert both date and time
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':max_player', $max_player);
        $stmt->bindParam(':prize', $prize);
        $stmt->bindParam(':fee', $fee);
        $stmt->bindParam(':qualification', $qualification); // Bind qualification
        $stmt->execute();

        // Get the last inserted tournament_id
        $tournament_id = $conn->lastInsertId();

        // Prepare the title and body for the announcement
        $title = "New Tournament: " . $name;  // Announcement title
        $body = "The tournament " . $name . " is starting on " . date('F j, Y, g:i a', strtotime($start_date)) . 
                " and will end on " . date('F j, Y, g:i a', strtotime($end_date)) . 
                ". Maximum players allowed: " . $max_player . 
                ". Qualification: " . ucfirst($qualification) . 
                ". Status: " . ucfirst($status) . ".";  // Including status in the body
        $expires_at = $end_date;  // The announcement expires at the end of the tournament

        // Insert into announcements table
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
