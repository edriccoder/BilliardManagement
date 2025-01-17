<?php
include 'conn.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $venue = $_POST['venue'];
    
    // Get the date and time separately
    $start_date_raw = $_POST['start_date'];
    $start_time_raw = $_POST['start_time'];
    $end_date_raw = $_POST['end_date'];
    $end_time_raw = $_POST['end_time'];

    // Combine the date and time into proper formats
    $start_date = $start_date_raw;
    $start_time = $start_time_raw;
    $end_date = $end_date_raw;
    $end_time = $end_time_raw;

    $status = isset($_POST['status']) ? $_POST['status'] : 'upcoming'; 
    $max_player = $_POST['max_player'];
    $prize = $_POST['prize'];
    $fee = $_POST['fee'];
    $qualification = $_POST['qualification'];

    // Begin transaction
    $conn->beginTransaction();

    try {
        // Insert into tournaments table, including the start_time and end_time
        $sql = "INSERT INTO tournaments (name, start_date, start_time, end_date, end_time, status, max_player, created_at, prize, fee, qualification, venue) 
                VALUES (:name, :start_date, :start_time, :end_date, :end_time, :status, :max_player, NOW(), :prize, :fee, :qualification, :venue)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':start_time', $start_time);
        $stmt->bindParam(':end_date', $end_date);
        $stmt->bindParam(':end_time', $end_time);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':max_player', $max_player);
        $stmt->bindParam(':prize', $prize);
        $stmt->bindParam(':fee', $fee);
        $stmt->bindParam(':qualification', $qualification);
        $stmt->bindParam(':venue', $venue);
        $stmt->execute();

        // Get the last inserted tournament_id
        $tournament_id = $conn->lastInsertId();

        // Prepare the title and body for the announcement
        $title = "New Tournament: " . $name;
        $body = "The tournament " . $name . " is starting on " . date('F j, Y', strtotime($start_date)) . "\n" .
                "Start time at " . date('g:i a', strtotime($start_time)) . "\n" .
                "and will end on " . date('F j, Y', strtotime($end_date)) . "\n" .
                "End Time at " . date('g:i a', strtotime($end_time)) . "\n" .
                "Venue: " . $venue . "\n" .
                "Maximum players allowed: " . $max_player . "\n" .
                "Category: Class " . ucfirst($qualification) . "\n" .
                "Status: " . ucfirst($status) . ".";
        $expires_at = $end_date . ' ' . $end_time;

        // Insert into announcements table
        $sqlAnnouncement = "INSERT INTO announcements (title, body, tournament_id, created_at, expires_at) 
                            VALUES (:title, :body, :tournament_id, NOW(), :expires_at)";
        $stmtAnn = $conn->prepare($sqlAnnouncement);
        $stmtAnn->bindParam(':title', $title);
        $stmtAnn->bindParam(':body', $body);
        $stmtAnn->bindParam(':tournament_id', $tournament_id);
        $stmtAnn->bindParam(':expires_at', $expires_at);
        $stmtAnn->execute();

        // Prepare notification message
        $notification_message = "Upcoming Tournament: " . $name . " at " . $venue . " starting on " . date('F j, Y', strtotime($start_date)) . " at " . date('g:i a', strtotime($start_time)) . ".";

        // Insert notification with user_id as NULL
        $sqlNotification = "INSERT INTO notifications (user_id, message, created_at) VALUES (NULL, :message, NOW())";
        $stmtNotif = $conn->prepare($sqlNotification);
        $stmtNotif->bindParam(':message', $notification_message);
        $stmtNotif->execute();

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
