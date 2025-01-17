<?php
session_start();
include 'conn.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the report_id parameter is provided in the URL
if (isset($_GET['report_id'])) {
    $reportId = intval($_GET['report_id']);

    // Prepare the SQL statement to delete the report
    $sql = $conn->prepare("DELETE FROM reports WHERE id = :report_id");
    $sql->bindParam(':report_id', $reportId, PDO::PARAM_INT);

    try {
        $sql->execute();

        if ($sql->rowCount() > 0) {
            $_SESSION['alert'] = [
                'title' => 'Success',
                'text' => 'Report deleted successfully.',
                'icon' => 'success'
            ];
        } else {
            $_SESSION['alert'] = [
                'title' => 'Error',
                'text' => 'No report found with the provided ID.',
                'icon' => 'error'
            ];
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        $_SESSION['alert'] = [
            'title' => 'Error',
            'text' => 'Unable to delete the report.',
            'icon' => 'error'
        ];
    }
} else {
    $_SESSION['alert'] = [
        'title' => 'Error',
        'text' => 'Invalid report ID.',
        'icon' => 'error'
    ];
}

// Redirect back to the reports page
header("Location: reports.php");
exit();
?>
