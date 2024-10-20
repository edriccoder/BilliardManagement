<?php
// Include your database connection
include 'conn.php';

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $announcement_id = $_POST['announcement_id'];
    $title = $_POST['title'];
    $body = $_POST['body'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Start transaction
    try {
        $conn->beginTransaction();

        // Update the announcement details in the announcements table
        $sqlUpdateAnnouncement = "UPDATE announcements SET title = :title, body = :body WHERE tournament_id = :announcement_id";
        $stmtUpdateAnnouncement = $conn->prepare($sqlUpdateAnnouncement);
        $stmtUpdateAnnouncement->bindParam(':title', $title);
        $stmtUpdateAnnouncement->bindParam(':body', $body);
        $stmtUpdateAnnouncement->bindParam(':announcement_id', $announcement_id);
        $stmtUpdateAnnouncement->execute();

        // Update the tournament details (start_date and end_date) in the tournaments table
        $sqlUpdateTournament = "UPDATE tournaments SET start_date = :start_date, end_date = :end_date WHERE tournament_id = :announcement_id";
        $stmtUpdateTournament = $conn->prepare($sqlUpdateTournament);
        $stmtUpdateTournament->bindParam(':start_date', $start_date);
        $stmtUpdateTournament->bindParam(':end_date', $end_date);
        $stmtUpdateTournament->bindParam(':announcement_id', $announcement_id);
        $stmtUpdateTournament->execute();

        // Commit the transaction
        $conn->commit();

        // Redirect back to the announcements page or show success message
        header("Location: admin_announcement.php?status=success");
        exit();
    } catch (Exception $e) {
        // Rollback transaction if there's an error
        $conn->rollBack();
        echo "Error updating announcement: " . $e->getMessage();
    }
} else {
    // If the form was not submitted, redirect to the announcements page
    header("Location: admin_announcement.php");
    exit();
}
