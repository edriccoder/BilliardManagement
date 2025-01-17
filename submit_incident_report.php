<?php
session_start();
include 'conn.php';

// Enable error reporting for debugging (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify the report type
    $reportType = $_POST['report_type'] ?? '';

    if ($reportType === 'incident_report') {
        // Sanitize and retrieve form inputs
        $nameToReport = trim($_POST['incident_report_name'] ?? '');
        $descriptionDetail = trim($_POST['incident_report_description'] ?? '');
        $datetime = $_POST['incident_report_datetime'] ?? '';
        $reportedBy = trim($_POST['reported_by'] ?? '');
        $damaged_by = trim($_POST['damaged_by'] ?? '');
        $contactNumber = trim($_POST['incident_contact_number'] ?? '');

        // Debugging: Log input values
        error_log("Report Type: $reportType");
        error_log("Name to Report: $nameToReport");
        error_log("Description Detail: $descriptionDetail");
        error_log("Datetime: $datetime");
        error_log("Reported By: $reportedBy");
        error_log("Contact Number: $contactNumber");

        // Basic validation (additional validation can be added as needed)
        if (empty($nameToReport) || empty($descriptionDetail) || empty($datetime) || empty($reportedBy) || empty($contactNumber)) {
            $_SESSION['alert'] = [
                'title' => 'Error',
                'text' => 'All fields are required.',
                'icon' => 'error'
            ];
            header("Location: reports.php");
            exit();
        }

        // Optionally, validate the contact number format
        if (!preg_match('/^[0-9]{10,15}$/', $contactNumber)) {
            $_SESSION['alert'] = [
                'title' => 'Error',
                'text' => 'Invalid contact number format. Please enter 10 to 15 digits.',
                'icon' => 'error'
            ];
            header("Location: reports.php");
            exit();
        }

        // Prepare the SQL statement
        try {
            $sql = $conn->prepare("
                INSERT INTO reports (
                    type, 
                    description, 
                    datetime, 
                    name, 
                    caused_by, 
                    contact_number
                ) VALUES (
                    :type, 
                    :description, 
                    :datetime, 
                    :name, 
                    :caused_by, 
                    :contact_number
                )
            ");

            // Bind parameters
            $sql->bindParam(':type', $reportType);
            $sql->bindParam(':description', $descriptionDetail);
            $sql->bindParam(':datetime', $datetime);
            $sql->bindParam(':name', $reportedBy);
            $sql->bindParam(':caused_by', $damaged_by);
            $sql->bindParam(':contact_number', $contactNumber);

            // Execute the query
            $sql->execute();

            // Debugging: Log success message
            error_log("Incident Report successfully inserted into the database.");

            // Set a success alert
            $_SESSION['alert'] = [
                'title' => 'Success',
                'text' => 'Incident Report submitted successfully.',
                'icon' => 'success'
            ];

        } catch (PDOException $e) {
            // Log the detailed error for debugging purposes
            error_log("Database Insert Error: " . $e->getMessage());

            // Debugging: Set a detailed error alert for debugging (remove in production)
            $_SESSION['alert'] = [
                'title' => 'Error',
                'text' => 'Unable to submit the Incident Report. Error Details: ' . $e->getMessage(), // Remove this message in production to avoid disclosing sensitive info
                'icon' => 'error'
            ];
        }

        // Redirect back to the reports page
        header("Location: reports.php");
        exit();
    } else {
        // Invalid report type
        $_SESSION['alert'] = [
            'title' => 'Error',
            'text' => 'Invalid report type.',
            'icon' => 'error'
        ];
        // Debugging: Log invalid report type error
        error_log("Invalid report type provided: " . $reportType);
        header("Location: reports.php");
        exit();
    }
} else {
    // Invalid request method
    $_SESSION['alert'] = [
        'title' => 'Error',
        'text' => 'Invalid request method.',
        'icon' => 'error'
    ];
    // Debugging: Log invalid request method
    error_log("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
    header("Location: reports.php");
    exit();
}
?>
