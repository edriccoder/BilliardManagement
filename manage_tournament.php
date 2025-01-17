<?php
include 'conn.php';

// Initialize filter variables
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Base SQL query
$sqlTournaments = "SELECT * FROM tournaments";
$params = [];

// Check if filter dates are provided and valid
if (!empty($startDate) && !empty($endDate)) {
    // Validate date formats
    $startDateValid = DateTime::createFromFormat('Y-m-d', $startDate) !== false;
    $endDateValid = DateTime::createFromFormat('Y-m-d', $endDate) !== false;

    if ($startDateValid && $endDateValid) {
        $sqlTournaments .= " WHERE start_date >= :start_date AND end_date <= :end_date";
        $params[':start_date'] = $startDate;
        $params[':end_date'] = $endDate;
    } else {
        // Handle invalid date formats
        $error_message = "Invalid date format. Please use YYYY-MM-DD.";
        echo "<script>alert('$error_message');</script>";
    }
}

// Prepare and execute the query
$stmtTournaments = $conn->prepare($sqlTournaments);
$stmtTournaments->execute($params);
$tournaments = $stmtTournaments->fetchAll(PDO::FETCH_ASSOC);

// Handle errors (if any)
if (isset($_GET['error'])) {
    $error_message = htmlspecialchars($_GET['error']);
    echo "<script>alert('$error_message');</script>";
}

try {
    $stmt = $conn->prepare("SELECT notification_id, user_id, message, created_at, is_read FROM admin_notifications ORDER BY created_at DESC");
    $stmt->execute();
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch unread notifications count
    $stmt_unread = $conn->prepare("SELECT COUNT(*) AS unread_count FROM admin_notifications WHERE is_read = 0");
    $stmt_unread->execute();
    $unreadCountResult = $stmt_unread->fetch(PDO::FETCH_ASSOC);
    $unreadCount = $unreadCountResult['unread_count'] ?? 0;
} catch (PDOException $e) {
    echo "Error retrieving notifications: " . $e->getMessage();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Existing head content remains unchanged -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- ... other links and scripts ... -->
    <title>Billiard Management</title>
    <!-- Fonts and Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700" />
    <!-- Nucleo Icons -->
    <link href="./assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="./assets/css/nucleo-svg.css" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <!-- CSS Files -->
    <link id="pagestyle" href="./assets/css/material-dashboard.css?v=3.1.0" rel="stylesheet" />
    <!-- Nepcha Analytics -->
    <script defer data-site="YOUR_DOMAIN_HERE" src="https://api.nepcha.com/js/nepcha-analytics.js"></script>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <style>
        /* Custom styles for filter and generate sections */
        .filter-section {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .filter-section .form-label {
            font-weight: 500;
        }
        .generate-section {
            text-align: right;
            margin-bottom: 20px;
        }
        @media (max-width: 767.98px) {
            .generate-section {
                text-align: left;
                margin-top: 15px;
            }
        }

        /* Custom border styles for input fields */
        .form-control {
            border: 2px solid #ced4da;
            border-radius: 4px;
            transition: border-color 0.3s ease;
        }
        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }

        /* Enhancing the navigation styles */
        .sidenav {
            /* Ensure the sidebar has a consistent width and style */
            width: 250px;
        }
        .sidenav .nav-link {
            color: #ffffff;
            font-weight: 500;
        }
        .sidenav .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
            border-radius: 8px;
        }

        /* Bracket styles remain unchanged */
        .bracket {
            display: flex;
            justify-content: center;
            flex-wrap: nowrap;
            overflow-x: auto;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .round {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 0 20px;
            position: relative;
        }

        .round h2 {
            text-align: center;
            margin-bottom: 10px;
        }

        .match {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #fff;
            position: relative;
        }

        .team {
            width: 150px;
            text-align: center;
            padding: 5px;
            cursor: pointer;
        }

        .winner-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100px;
            width: 200px;
            border: 2px solid #000;
            border-radius: 5px;
            background-color: #f5f5f5;
            margin-top: 20px; /* Space above the winner area */
            align-self: center; /* Center within the flex container */
        }

        .winner-placeholder .team {
            width: auto;
            text-align: center;
            font-weight: bold;
        }

        .round-line {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 50%;
            width: 2px;
            background-color: #ccc;
        }

        .match-line {
            position: absolute;
            width: 2px;
            height: 20px;
            background-color: #ccc;
            top: 50%;
            left: 100%;
        }

        .vertical-center {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .final-round {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
        }

        .final-winner {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100px;
            width: 200px;
            border: 2px solid #000;
            border-radius: 5px;
            background-color: #ddd;
        }

        .team.selected {
            background-color: #e0f7fa;
            border-radius: 10px;
        }

        .team.eliminated {
            text-decoration: line-through;
            color: #999;
        }
    </style>
</head>
<body class="g-sidenav-show bg-gray-100">
    <!-- Sidebar Navigation -->
    <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 bg-gradient-dark" id="sidenav-main">
        <div class="sidenav-header">
            <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
            <a class="navbar-brand m-0" href="admin_dashboard.php">
                <img src="./img/admin.png" class="navbar-brand-img h-100" alt="main_logo">
                <span class="ms-1 font-weight-bold text-white">Admin</span>
            </a>
        </div>
        <hr class="horizontal light mt-0 mb-2">
        <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link active text-white" href="admin_dashboard.php">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">dashboard</i>
                        </div>
                        <span class="nav-link-text ms-1">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="billiard_table.php">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">table_view</i>
                        </div>
                        <span class="nav-link-text ms-1">Billiard Tables</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="manage_user.php">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">person</i>
                        </div>
                        <span class="nav-link-text ms-1">User Account Management</span>
                    </a>
                </li> 
                <li class="nav-item">
                    <a class="nav-link text-white" href="manage_tournament.php">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">flag</i>
                        </div>
                        <span class="nav-link-text ms-1">Billiard Tournament Scheduling Management</span>
                    </a>
                </li> 
                <li class="nav-item">
                    <a class="nav-link text-white" href="inventory_management.php">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">inventory</i>
                        </div>
                        <span class="nav-link-text ms-1">Inventory Management</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="admin_announcement.php">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">campaign</i>
                        </div>
                        <span class="nav-link-text ms-1">Announcement Management</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="admin_booking.php">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">book</i>
                        </div>
                        <span class="nav-link-text ms-1">Reservation Management</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="admin_reports.php">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">bar_chart</i>
                        </div>
                        <span class="nav-link-text ms-1">Reports & Analytics</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="admin_feedback.php">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">feedback</i>
                        </div>
                        <span class="nav-link-text ms-1">Manage Feedback</span>
                    </a>
                </li>
                <li class="nav-item mt-3"></li>
            </ul>
        </div>
    </aside>
    
    <!-- Main Content -->
    <main class="main-content border-radius-lg">
        <!-- Navbar -->
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" data-scroll="true">
            <div class="container-fluid py-1 px-3">
                <nav aria-label="breadcrumb"></nav>
                <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
                    <div class="ms-md-auto pe-md-3 d-flex align-items-center"></div>
                    <ul class="navbar-nav justify-content-end">
                        <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                            <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                                <div class="sidenav-toggler-inner">
                                    <i class="sidenav-toggler-line"></i>
                                    <i class="sidenav-toggler-line"></i>
                                    <i class="sidenav-toggler-line"></i>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item px-3 d-flex align-items-center">
                            <a href="javascript:;" class="nav-link text-body p-0">
                                <i class="fa fa-cog fixed-plugin-button-nav cursor-pointer"></i>
                            </a>
                        </li>
                        <!-- Notification Icon with Unread Count Badge -->
                        <li class="nav-item dropdown pe-2 d-flex align-items-center">
                            <a href="javascript:;" class="nav-link text-body p-0" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-bell cursor-pointer"></i>
                                <?php if ($unreadCount > 0): ?>
                                    <span class="badge bg-danger text-white position-absolute top-0 start-100 translate-middle p-1 rounded-circle" style="font-size: 0.75rem;">
                                        <?php echo $unreadCount; ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end px-2 py-3 me-sm-n4" aria-labelledby="dropdownMenuButton">
                                <?php foreach ($notifications as $notification): ?>
                                    <li class="mb-2">
                                        <a class="dropdown-item border-radius-md notification <?php echo $notification['is_read'] ? 'read' : ''; ?>" 
                                            href="javascript:;" 
                                            data-notification-id="<?php echo $notification['notification_id']; ?>">
                                            <div class="d-flex py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="text-sm font-weight-normal mb-1">
                                                        <?php echo htmlspecialchars($notification['message']); ?>
                                                    </h6>
                                                    <p class="text-xs text-secondary mb-0">
                                                        <i class="fa fa-clock me-1"></i>
                                                        <?php echo htmlspecialchars($notification['created_at']); ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                        <li class="nav-item d-flex align-items-center">
                            <a href="logout.php" class="nav-link text-body font-weight-bold px-0">
                                <span class="d-sm-inline d-none">Logout</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- End Navbar -->        
        <div class="container-fluid py-4">
            <!-- Filter Section -->
            <div class="filter-section">
                <form method="GET" action="" class="row g-3">
                    <div class="col-md-4">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="<?= htmlspecialchars($startDate) ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="<?= htmlspecialchars($endDate) ?>">
                    </div>
                    <div class="col-md-4 align-self-end">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </form>
            </div>

            <!-- Generate Report Section -->
            <div class="generate-section">
                <form method="GET" action="generate_tournament_report.php" class="d-flex align-items-center">
                    <input type="hidden" name="start_date" value="<?= htmlspecialchars($startDate) ?>">
                    <input type="hidden" name="end_date" value="<?= htmlspecialchars($endDate) ?>">
                    <button type="submit" name="generate_report" value="1" class="btn btn-success">
                        <i class="fa fa-file-pdf me-2"></i>Generate Report
                    </button>
                </form>
            </div>

            <!-- Page Heading -->      
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Manage Tournaments</h1>
                <div>
                    <button class='btn btn-primary me-2' data-bs-toggle='modal' data-bs-target='#addTournamentModal'>
                        <i class="fa fa-plus me-1"></i>Add Tournament
                    </button>
                    <a href="all_brackets.php" class="btn btn-secondary">
                        <i class="fa fa-trophy me-1"></i>Show All Brackets
                    </a>
                </div>
            </div>

            <!-- Table Row -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header pb-0 px-3">
                            <h6 class="mb-0">Tournaments</h6>
                        </div>
                        <div class="card-body pt-4 p-3">
                            <ul class="list-group">
                                <?php if (!empty($tournaments)) : ?>
                                    <?php foreach ($tournaments as $tournament) : ?>
                                        <li class="list-group-item border-0 d-flex p-4 mb-2 bg-gray-100 border-radius-lg">
                                            <div class="d-flex flex-column">
                                                <h6 class="mb-3 text-sm"><?php echo htmlspecialchars($tournament['name']); ?></h6>
                                                <span class="mb-2 text-xs">Start Date: <span class="text-dark font-weight-bold ms-sm-2"><?php echo htmlspecialchars($tournament['start_date']); ?></span></span>
                                                <span class="mb-2 text-xs">End Date: <span class="text-dark font-weight-bold ms-sm-2"><?php echo htmlspecialchars($tournament['end_date']); ?></span></span>
                                                <span class="mb-2 text-xs">Start Time: <span class="text-dark font-weight-bold ms-sm-2"><?php echo htmlspecialchars($tournament['start_time']); ?></span></span>
                                                <span class="mb-2 text-xs">End Time: <span class="text-dark font-weight-bold ms-sm-2"><?php echo htmlspecialchars($tournament['end_time']); ?></span></span>
                                                <span class="mb-2 text-xs">Max Players: <span class="text-dark font-weight-bold ms-sm-2"><?php echo htmlspecialchars($tournament['max_player']); ?></span></span>
                                                <span class="mb-2 text-xs">Prizes: <span class="text-dark font-weight-bold ms-sm-2"><?php echo htmlspecialchars($tournament['prize']); ?></span></span>
                                                <span class="mb-2 text-xs">Tournament Fee: <span class="text-dark font-weight-bold ms-sm-2">₱<?php echo htmlspecialchars(number_format($tournament['fee'], 2)); ?></span></span>
                                                <span class="text-xs">Status: <span class="text-dark font-weight-bold ms-sm-2 text-capitalize"><?php echo htmlspecialchars($tournament['status']); ?></span></span>
                                            </div>
                                            <div class="ms-auto text-end">
                                                <form action="delete_tournament.php" method="POST" style="display: inline;">
                                                    <input type="hidden" name="tournament_id" value="<?php echo htmlspecialchars($tournament['tournament_id']); ?>">
                                                    <button class="btn btn-link text-danger text-gradient px-3 mb-0" type="submit" onclick="return confirm('Are you sure you want to delete this tournament?');">
                                                        <i class="material-icons text-sm me-2">delete</i>Delete
                                                    </button>
                                                </form>
                                                <button class="btn btn-link text-dark px-3 mb-0" onclick='editTournament(<?php echo json_encode($tournament); ?>)'>
                                                    <i class="material-icons text-sm me-2">edit</i>Edit
                                                </button>
                                                <button class="btn btn-link text-dark px-3 mb-0 show-players" data-bs-toggle="modal" data-bs-target="#playersModal" data-tournament-id="<?php echo htmlspecialchars($tournament['tournament_id']); ?>">
                                                    <i class="material-icons text-sm me-2">person</i>Show Players
                                                </button>
                                                <button class="btn btn-link text-dark px-3 mb-0 show-schedule" data-bs-toggle="modal" data-bs-target="#scheduleModal" data-tournament-id="<?php echo htmlspecialchars($tournament['tournament_id']); ?>">
                                                    <i class="material-icons text-sm me-2">schedule</i>Show Schedule
                                                </button>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <li class="list-group-item border-0 d-flex p-4 mb-2 bg-gray-100 border-radius-lg">
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-3 text-sm">No tournaments found.</h6>
                                        </div>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card h-100">
                        <div class="card-header pb-0 p-3">
                            <div class="row">
                                <div class="col-6 d-flex align-items-center">
                                    <h6 class="mb-0">Available Bracket Tournaments</h6>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-3 pb-0">
                            <ul class="list-group">
                                <?php
                                // Fetch available tournaments with their names
                                $stmt = $conn->prepare('
                                    SELECT DISTINCT b.tournament_id, t.name 
                                    FROM bracket b
                                    JOIN tournaments t ON b.tournament_id = t.tournament_id
                                ');
                                $stmt->execute();
                                $availableTournaments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                ?>
                                <?php if (!empty($availableTournaments)) : ?>
                                    <?php foreach ($availableTournaments as $tournament) : ?>
                                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                                            <div class="d-flex flex-column">
                                                <h6 class="mb-1 text-dark font-weight-bold text-sm">
                                                    <?php echo htmlspecialchars($tournament['name']); ?>
                                                </h6>
                                            </div>
                                            <div>
                                                <button class="btn btn-link text-dark text-sm mb-0 px-0 me-2 show-bracket" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#bracketModal" 
                                                        data-tournament-id="<?php echo htmlspecialchars($tournament['tournament_id']); ?>">
                                                    <i class="material-icons text-lg position-relative me-1">view_week</i> Show Bracket
                                                </button>
                                                <button class="btn btn-link text-dark text-sm mb-0 px-0 show-scores" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#showScores" 
                                                        data-tournament-id="<?php echo htmlspecialchars($tournament['tournament_id']); ?>">
                                                    <i class="material-icons text-lg position-relative me-1">score</i> Show Scores
                                                </button>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-1 text-dark font-weight-bold text-sm">No tournaments available.</h6>
                                        </div>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <footer class="sticky-footer bg-white mt-4">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>T James Sporty Bar &copy; <?= date('Y'); ?></span>
                    </div>
                </div>
            </footer>
        </div>
    </main>

    <!-- Fixed Plugin (Remains Unchanged) -->
    <div class="fixed-plugin">
        <a class="fixed-plugin-button text-dark position-fixed px-3 py-2">
            <i class="material-icons py-2">settings</i>
        </a>
        <div class="card shadow-lg">
            <div class="card-header pb-0 pt-3">
                <div class="float-start">
                    <h5 class="mt-3 mb-0">Material UI Configurator</h5>
                    <p>See our dashboard options.</p>
                </div>
                <div class="float-end mt-4">
                    <button class="btn btn-link text-dark p-0 fixed-plugin-close-button">
                        <i class="material-icons">clear</i>
                    </button>
                </div>
            </div>
            <hr class="horizontal dark my-1">
            <div class="card-body pt-sm-3 pt-0">
                <!-- Sidebar Backgrounds -->
                <div>
                    <h6 class="mb-0">Sidebar Colors</h6>
                </div>
                <a href="javascript:void(0)" class="switch-trigger background-color">
                    <div class="badge-colors my-2 text-start">
                        <span class="badge filter bg-gradient-primary active" data-color="primary" onclick="sidebarColor(this)"></span>
                        <span class="badge filter bg-gradient-dark" data-color="dark" onclick="sidebarColor(this)"></span>
                        <span class="badge filter bg-gradient-info" data-color="info" onclick="sidebarColor(this)"></span>
                        <span class="badge filter bg-gradient-success" data-color="success" onclick="sidebarColor(this)"></span>
                        <span class="badge filter bg-gradient-warning" data-color="warning" onclick="sidebarColor(this)"></span>
                        <span class="badge filter bg-gradient-danger" data-color="danger" onclick="sidebarColor(this)"></span>
                    </div>
                </a>
                <!-- Sidenav Type -->
                <div class="mt-3">
                    <h6 class="mb-0">Sidenav Type</h6>
                    <p class="text-sm">Choose between 2 different sidenav types.</p>
                </div>
                <div class="d-flex">
                    <button class="btn bg-gradient-dark px-3 mb-2 active" data-class="bg-gradient-dark" onclick="sidebarType(this)">Dark</button>
                    <button class="btn bg-gradient-dark px-3 mb-2 ms-2" data-class="bg-transparent" onclick="sidebarType(this)">Transparent</button>
                    <button class="btn bg-gradient-dark px-3 mb-2 ms-2" data-class="bg-white" onclick="sidebarType(this)">White</button>
                </div>
                <p class="text-sm d-xl-none d-block mt-2">You can change the sidenav type just on desktop view.</p>
                <!-- Navbar Fixed -->
                <div class="mt-3 d-flex">
                    <h6 class="mb-0">Navbar Fixed</h6>
                    <div class="form-check form-switch ps-0 ms-auto my-auto">
                        <input class="form-check-input mt-1 ms-auto" type="checkbox" id="navbarFixed" onclick="navbarFixed(this)">
                    </div>
                </div>
                <hr class="horizontal dark my-3">
                <div class="mt-2 d-flex">
                    <h6 class="mb-0">Light / Dark</h6>
                    <div class="form-check form-switch ps-0 ms-auto my-auto">
                        <input class="form-check-input mt-1 ms-auto" type="checkbox" id="dark-version" onclick="darkMode(this)">
                    </div>
                </div>
                <hr class="horizontal dark my-sm-4">
                <a class="btn bg-gradient-info w-100" href="https://www.creative-tim.com/product/material-dashboard-pro">Free Download</a>
                <a class="btn btn-outline-dark w-100" href="https://www.creative-tim.com/learning-lab/bootstrap/overview/material-dashboard">View documentation</a>
                <div class="w-100 text-center">
                    <a class="github-button" href="https://github.com/creativetimofficial/material-dashboard" data-icon="octicon-star" data-size="large" data-show-count="true" aria-label="Star creativetimofficial/material-dashboard on GitHub">Star</a>
                    <h6 class="mt-3">Thank you for sharing!</h6>
                    <a href="https://twitter.com/intent/tweet?text=Check%20Material%20UI%20Dashboard%20made%20by%20%40CreativeTim%20%23webdesign%20%23dashboard%20%23bootstrap5&amp;url=https%3A%2F%2Fwww.creative-tim.com%2Fproduct%2Fsoft-ui-dashboard" class="btn btn-dark mb-0 me-2" target="_blank">
                        <i class="fab fa-twitter me-1" aria-hidden="true"></i> Tweet
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=https://www.creative-tim.com/product/material-dashboard" class="btn btn-dark mb-0 me-2" target="_blank">
                        <i class="fab fa-facebook-square me-1" aria-hidden="true"></i> Share
                    </a>
                </div>
            </div>
        </div>

        <!-- Add Tournament Modal -->
        <div class="modal fade" id="addTournamentModal" tabindex="-1" aria-labelledby="addTournamentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Tournament</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="addTournamentForm" method="POST" action="add_tournament.php">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="tournamentName" class="form-label">Tournament Name</label>
                                    <input type="text" class="form-control" id="tournamentName" name="name" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="venue" class="form-label">Venue</label>
                                    <input type="text" class="form-control" id="venue" name="venue" required>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <label for="startDate" class="form-label">Start Date</label>
                                    <input type="date" class="form-control" id="startDate" name="start_date" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="startTime" class="form-label">Start Time</label>
                                    <input type="time" class="form-control" id="startTime" name="start_time" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="endDate" class="form-label">End Date</label>
                                    <input type="date" class="form-control" id="endDate" name="end_date" required>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <label for="endTime" class="form-label">End Time</label>
                                    <input type="time" class="form-control" id="endTime" name="end_time" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="maxPlayers" class="form-label">Max Players</label>
                                    <input type="number" class="form-control" id="maxPlayers" name="max_player" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="fee" class="form-label">Tournament Fee (₱)</label>
                                    <input type="number" step="0.01" class="form-control" id="fee" name="fee" required>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label for="prize" class="form-label">Prizes</label>
                                    <input type="text" class="form-control" id="prize" name="prize" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="qualification" class="form-label">Qualification</label>
                                    <select class="form-select" id="qualification" name="qualification" required>
                                        <option value="" selected disabled>Select Qualification</option>
                                        <option value="A">Class A</option>
                                        <option value="B">Class B</option>
                                        <option value="C">Class C</option>
                                        <option value="D">Class D</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="" selected disabled>Select Status</option>
                                        <option value="upcoming">Upcoming</option>
                                        <option value="ongoing">Ongoing</option>
                                        <option value="completed">Completed</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary w-100">Add Tournament</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Tournament Modal -->
        <div class="modal fade" id="editTournamentModal" tabindex="-1" role="dialog" aria-labelledby="editTournamentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Tournament</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editTournamentForm" method="POST" action="edit_tournament.php">
                            <input type="hidden" id="editTournamentId" name="tournament_id">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="editTournamentName" class="form-label">Tournament Name</label>
                                    <input type="text" class="form-control" id="editTournamentName" name="name" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="editVenue" class="form-label">Venue</label>
                                    <input type="text" class="form-control" id="editVenue" name="venue" required>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label for="editStartDate" class="form-label">Start Date</label>
                                    <input type="date" class="form-control" id="editStartDate" name="start_date" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="editEndDate" class="form-label">End Date</label>
                                    <input type="date" class="form-control" id="editEndDate" name="end_date" required>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label for="editMaxPlayers" class="form-label">Max Players</label>
                                    <input type="number" class="form-control" id="editMaxPlayers" name="max_player" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="editFee" class="form-label">Tournament Fee (₱)</label>
                                    <input type="number" step="0.01" class="form-control" id="editFee" name="fee" required>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label for="editPrize" class="form-label">Prizes</label>
                                    <input type="text" class="form-control" id="editPrize" name="prize" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="editStatus" class="form-label">Status</label>
                                    <select class="form-select" id="editStatus" name="status" required>
                                        <option value="" selected disabled>Select Status</option>
                                        <option value="upcoming">Upcoming</option>
                                        <option value="ongoing">Ongoing</option>
                                        <option value="completed">Completed</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary w-100">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Players Modal -->
        <div class="modal fade" id="playersModal" tabindex="-1" aria-labelledby="playersModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tournament Players</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">User ID</th>
                                    <th scope="col">Username</th>
                                    <th scope="col">Proof of Payment</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="playersTableBody">
                                <!-- Player rows will be appended here dynamically -->
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="createBracketBtn" class="btn btn-primary">Create Bracket</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bracket Modal -->
        <div class="modal fade" id="bracketModal" tabindex="-1" aria-labelledby="bracketModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Single Elimination Tournament Bracket</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="bracket" id="bracketContainer">
                            <!-- Bracket content will be loaded here -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Image Modal -->
        <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Proof of Payment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="imageModalContent">
                        <!-- Image will be displayed here dynamically -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Scores Modal -->
        <div class="modal fade" id="showScores" tabindex="-1" role="dialog" aria-labelledby="showScoresLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tournament Scores</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">Player ID</th>
                                    <th scope="col">Username</th>
                                    <th scope="col">Score</th>
                                </tr>
                            </thead>
                            <tbody id="scoresTableBody">
                                <!-- Scores will be dynamically inserted here -->
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Scores Input Modal -->
        <div class="modal fade" id="scoresModal" tabindex="-1" aria-labelledby="scoresModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="scoresForm">
                        <div class="modal-header">
                            <h5 class="modal-title">Input Match Scores</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        
                        <div class="modal-body">
                            <input type="hidden" id="tournamentId" name="tournament_id">
                            <input type="hidden" id="roundNumber" name="round">
                            <input type="hidden" id="matchNumber" name="match_number">
                            <input type="hidden" id="winnerId" name="winner_id">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="player1Name" class="form-label">Player 1 Name</label>
                                    <input type="text" class="form-control" id="player1Name" name="player1_name" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label for="player2Name" class="form-label">Player 2 Name</label>
                                    <input type="text" class="form-control" id="player2Name" name="player2_name" readonly>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label for="player1Score" class="form-label">Player 1 Score</label>
                                    <input type="number" class="form-control" id="player1Score" name="player1_score" min="0" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="player2Score" class="form-label">Player 2 Score</label>
                                    <input type="number" class="form-control" id="player2Score" name="player2_score" min="0" required>
                                </div>
                            </div>
                            
                            <div id="scoreError" class="text-danger mt-3" style="display: none;">
                                Winner's score must be greater than the loser's score.
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Submit Scores</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Schedule Modal -->
        <div class="modal fade" id="scheduleModal" tabindex="-1" aria-labelledby="scheduleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tournament Schedule</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Round</th>
                                    <th>Match Number</th>
                                    <th>Player 1</th>
                                    <th>Player 2</th>
                                    <th>Scheduled Time</th>
                                </tr>
                            </thead>
                            <tbody id="scheduleTableBody">
                                <!-- Schedule rows will be dynamically inserted here -->
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>


    <!-- Core JS Scripts -->
    <!-- jQuery (Single Version) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 5 JS Bundle (includes Popper.js) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <!-- Material Dashboard JS -->
    <script src="./assets/js/material-dashboard.min.js?v=3.1.0"></script>
    <!-- Custom Scripts -->
    <script>
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = { damping: '0.5' }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }

        let currentTournamentId = null;
        let finalRound = 0;
        let currentMatch = null;

        document.addEventListener('DOMContentLoaded', function() {
            const showPlayersButtons = document.querySelectorAll('.show-players');
            const showScoresButtons = document.querySelectorAll('.show-scores');
            const showScheduleButtons = document.querySelectorAll('.show-schedule');
            const scheduleModal = document.getElementById('scheduleModal');
            
            // Event Listener for "Show Schedule" Buttons
            showScheduleButtons.forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    const tournamentId = this.getAttribute('data-tournament-id');
            
                    fetch(`fetch_schedule.php?tournament_id=${tournamentId}`)
                        .then(response => response.json())
                        .then(data => {
                            const scheduleTableBody = document.getElementById('scheduleTableBody');
                            scheduleTableBody.innerHTML = '';
            
                            if (data.success && data.schedule.length > 0) {
                                data.schedule.forEach(match => {
                                    // Format the scheduled date and time
                                    const scheduledDateTimeFormatted = formatDateTime(match.scheduled_time);
            
                                    const row = `
                                        <tr>
                                            <td>${match.round}</td>
                                            <td>${match.match_number}</td>
                                            <td>${match.player1_name || 'TBA'}</td>
                                            <td>${match.player2_name || 'TBA'}</td>
                                            <td>${scheduledDateTimeFormatted || 'TBA'}</td>
                                        </tr>
                                    `;
                                    scheduleTableBody.innerHTML += row;
                                });
                            } else {
                                const row = `
                                    <tr>
                                        <td colspan="5">${data.message || 'No schedule available.'}</td>
                                    </tr>
                                `;
                                scheduleTableBody.innerHTML += row;
                            }
            
                            // Show the Schedule Modal
                            const scheduleModalInstance = new bootstrap.Modal(document.getElementById('scheduleModal'));
                            scheduleModalInstance.show();
                        })
                        .catch(error => {
                            console.error('Error fetching schedule:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred while fetching the schedule.',
                            });
                        });
                });
            });
            
            // Date and Time formatting function
            function formatDateTime(datetimeStr) {
                if (!datetimeStr) return 'TBA';
            
                // Parse the datetime string
                const datetime = new Date(datetimeStr);
            
                if (isNaN(datetime)) return 'Invalid Date';
            
                // Options for formatting date and time
                const options = {
                    month: 'long',     // Full month name (e.g., "September")
                    day: 'numeric',    // Day of the month
                    year: 'numeric',   // Four-digit year
                    hour: 'numeric',   // Hour (numeric)
                    minute: 'numeric', // Minute (numeric)
                    hour12: true       // 12-hour format
                };
            
                // Format the date and time
                return datetime.toLocaleString('en-US', options);
            }

            // Event Listener for "Show Players" Buttons
            showPlayersButtons.forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    currentTournamentId = this.getAttribute('data-tournament-id');
                    
                    fetch(`fetch_players.php?tournament_id=${currentTournamentId}`)
                       .then(response => response.json())
                       .then(players => {
                          const playersTableBody = document.getElementById('playersTableBody');
                          playersTableBody.innerHTML = '';

                          if (players.success && players.players.length > 0) {
                                players.players.forEach(player => {
                                   const row = `
                                      <tr>
                                            <td>${player.user_id}</td>
                                            <td>${player.username}</td>
                                            <td>
                                                <a href="${player.proof_of_payment}" target="_blank">
                                                    <img src="${player.proof_of_payment}" alt="Proof of Payment" style="max-width: 100px; max-height: 100px;">
                                                </a>
                                            </td>
                                            <td class="text-capitalize">${player.status}</td>
                                            <td>
                                               <button class="btn btn-sm btn-success me-1 edit-confirm" data-player-id="${player.player_id}" data-status="confirmed">Confirm</button>
                                               <button class="btn btn-sm btn-danger edit-cancel" data-player-id="${player.player_id}" data-status="cancelled">Cancel</button>
                                            </td>
                                      </tr>
                                   `;
                                   playersTableBody.innerHTML += row;
                                });

                                // Add event listeners for Confirm and Cancel buttons
                                const confirmButtons = document.querySelectorAll('.edit-confirm');
                                confirmButtons.forEach(button => {
                                   button.addEventListener('click', function() {
                                      const playerId = this.getAttribute('data-player-id');
                                      const newStatus = this.getAttribute('data-status');
                                      updatePlayerStatus(playerId, newStatus, this);
                                   });
                                });

                                const cancelButtons = document.querySelectorAll('.edit-cancel');
                                cancelButtons.forEach(button => {
                                   button.addEventListener('click', function() {
                                      const playerId = this.getAttribute('data-player-id');
                                      const newStatus = this.getAttribute('data-status');
                                      updatePlayerStatus(playerId, newStatus, this);
                                   });
                                });
                          } else {
                                const row = `
                                   <tr>
                                      <td colspan="5">${players.message || 'No players found.'}</td>
                                   </tr>
                                `;
                                playersTableBody.innerHTML += row;
                          }

                          const playersModalInstance = new bootstrap.Modal(document.getElementById('playersModal'));
                          playersModalInstance.show();
                       })
                       .catch(error => {
                          console.error('Error fetching players:', error);
                          Swal.fire({
                              icon: 'error',
                              title: 'Error',
                              text: 'An error occurred while fetching players.',
                          });
                       });
                });
            });

            // Function to update player status via AJAX
            function updatePlayerStatus(playerId, newStatus, buttonElement) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: `Do you want to ${newStatus} this player?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: `Yes, ${newStatus}!`
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Send AJAX request to update_player_status.php
                        fetch('update_player_status.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ player_id: playerId, new_status: newStatus })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Update the status cell
                                const statusCell = buttonElement.closest('tr').querySelector('td:nth-child(4)');
                                statusCell.textContent = newStatus;

                                // Optionally, disable buttons after status change
                                buttonElement.closest('td').querySelectorAll('button').forEach(btn => btn.disabled = true);

                                Swal.fire(
                                    'Updated!',
                                    `Player has been ${newStatus}.`,
                                    'success'
                                );
                            } else {
                                Swal.fire(
                                    'Error!',
                                    data.message || `Failed to ${newStatus} the player.`,
                                    'error'
                                );
                            }
                        })
                        .catch(error => {
                            console.error(`Error updating player status to ${newStatus}:`, error);
                            Swal.fire(
                                'Error!',
                                `An error occurred while trying to ${newStatus} the player.`,
                                'error'
                            );
                        });
                    }
                });
            }

            // Event Listener for "Show Scores" Buttons
            showScoresButtons.forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    const tournamentId = this.getAttribute('data-tournament-id');

                    fetch(`fetch_scores.php?tournament_id=${tournamentId}`)
                        .then(response => response.json())
                        .then(data => {
                            const scoresTableBody = document.getElementById('scoresTableBody');
                            scoresTableBody.innerHTML = '';

                            if (data.success && data.scores.length > 0) {
                                data.scores.forEach(score => {
                                    const row = `
                                        <tr>
                                            <td>${score.user_id}</td>
                                            <td>${score.username}</td>
                                            <td>${score.scores}</td>
                                        </tr>
                                    `;
                                    scoresTableBody.innerHTML += row;
                                });
                            } else {
                                const row = `
                                    <tr>
                                        <td colspan="3">No scores found for this tournament.</td>
                                    </tr>
                                `;
                                scoresTableBody.innerHTML += row;
                            }

                            // Initialize and show the Bootstrap 5 modal
                            const showScoresModal = new bootstrap.Modal(document.getElementById('showScores'));
                            showScoresModal.show();
                        })
                        .catch(error => {
                            console.error('Error fetching scores:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred while fetching scores.',
                            });
                        });
                });
            });

            // Event Listener for "Create Bracket" Button
            document.getElementById('createBracketBtn').addEventListener('click', function() {
                if (currentTournamentId !== null) {
                    Swal.fire({
                        title: 'Create Bracket',
                        text: 'Are you sure you want to create a bracket for this tournament?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, create it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(`create_bracket.php?tournament_id=${currentTournamentId}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success',
                                        text: 'Bracket created successfully!',
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                    // Refresh the bracket view
                                    renderBracket(currentTournamentId);
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'Error: ' + data.message,
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error creating bracket:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'An error occurred while creating the bracket.',
                                });
                            });
                        }
                    });
                }
            });

            // Event Listener for "Show Bracket" Buttons
            document.querySelectorAll('.show-bracket').forEach(button => {
                button.addEventListener('click', function () {
                    currentTournamentId = this.getAttribute('data-tournament-id');
                    renderBracket(currentTournamentId);
                });
            });

            // Function to Render the Bracket with Scores
            function renderBracket(tournamentId, showModal = true) {
                if (!tournamentId) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Tournament ID is missing.'
                    });
                    return;
                }
            
                // Fetch Bracket and Scores Data in Parallel
                Promise.all([
                    fetch(`get_bracket.php?tournament_id=${tournamentId}`).then(response => response.json()),
                    fetch(`fetch_scores.php?tournament_id=${tournamentId}`).then(response => response.json())
                ])
                .then(([bracketData, scoresData]) => {
                    const bracketContainer = document.getElementById('bracketContainer');
                    bracketContainer.innerHTML = ''; // Clear existing bracket
            
                    if (bracketData.success && bracketData.matches.length > 0) {
                        const matches = bracketData.matches;
                        const rounds = Math.max(...matches.map(m => m.round));
                        finalRound = rounds;
            
                        // Create a mapping from user_id to scores
                        const scoresMap = {};
                        if (scoresData.success && scoresData.scores.length > 0) {
                            scoresData.scores.forEach(score => {
                                scoresMap[score.user_id] = score.scores;
                            });
                        }
            
                        // Create Bracket Structure
                        for (let round = 1; round <= rounds; round++) {
                            const roundDiv = document.createElement('div');
                            roundDiv.className = 'round';
                            roundDiv.dataset.round = round;
                            roundDiv.innerHTML = `<h2>Round ${round}</h2>`;

                            const matchesInRound = matches.filter(m => m.round == round);

                            matchesInRound.forEach(match => {
                                const matchDiv = document.createElement('div');
                                matchDiv.className = 'match';
                                matchDiv.setAttribute('data-match-number', match.match_number);

                                // Retrieve scores from scoresMap
                                const player1Score = match.player1_id && scoresMap[match.player1_id] !== undefined ? scoresMap[match.player1_id] : '-';
                                const player2Score = match.player2_id && scoresMap[match.player2_id] !== undefined ? scoresMap[match.player2_id] : '-';

                                const team1Name = match.player1_name || 'TBA';
                                const team2Name = match.player2_name || 'TBA';
                                const winnerId = match.winner_id;

                                matchDiv.innerHTML = `
                                    <div class="team ${winnerId == match.player1_id ? 'selected' : ''} ${winnerId && winnerId != match.player1_id ? 'eliminated' : ''}" data-player-id="${match.player1_id || ''}" data-bs-toggle="tooltip" title="Click to view scores">
                                        ${team1Name} <span class="score">(${player1Score})</span>
                                    </div>
                                    <div class="team ${winnerId == match.player2_id ? 'selected' : ''} ${winnerId && winnerId != match.player2_id ? 'eliminated' : ''}" data-player-id="${match.player2_id || ''}" data-bs-toggle="tooltip" title="Click to view scores">
                                        ${team2Name} <span class="score">(${player2Score})</span>
                                    </div>
                                    <button class="win-btn btn btn-success" data-round="${match.round}" data-match="${match.match_number}" ${winnerId ? 'disabled' : ''}>Select Winner</button>
                                `;

                                roundDiv.appendChild(matchDiv);
                            });

                            bracketContainer.appendChild(roundDiv);
                        }

                        // Add Winner Placeholder
                        const winnerPlaceholder = document.createElement('div');
                        winnerPlaceholder.className = 'winner-placeholder';
                        const finalMatch = matches.find(m => m.round == finalRound);
                        const winnerName = finalMatch && finalMatch.winner_id ? 
                            (finalMatch.player1_id === finalMatch.winner_id ? finalMatch.player1_name : finalMatch.player2_name) : 
                            'TBA';
                        winnerPlaceholder.innerHTML = `
                            <h2>Winner</h2>
                            <div class="team">${winnerName}</div>
                        `;
                        bracketContainer.appendChild(winnerPlaceholder);

                        // Initialize Bootstrap Tooltips
                        var tooltipTriggerList = [].slice.call(bracketContainer.querySelectorAll('[data-bs-toggle="tooltip"]'));
                        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                            return new bootstrap.Tooltip(tooltipTriggerEl);
                        });

                        if (showModal) {
                            const bracketModalElement = document.getElementById('bracketModal');
                            const bracketModal = new bootstrap.Modal(bracketModalElement);
                            bracketModal.show();
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: bracketData.message || 'No matches found for this tournament.'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error fetching bracket or scores:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while fetching bracket or scores.'
                    });
                });
            }

            // Event Listener for Selecting Winner and Inputting Scores
            document.getElementById('bracketContainer').addEventListener('click', function (event) {
                if (event.target.classList.contains('win-btn')) {
                    const round = parseInt(event.target.getAttribute('data-round'));
                    const matchNumber = parseInt(event.target.getAttribute('data-match'));

                    const matchDiv = event.target.parentElement;
                    const player1Div = matchDiv.querySelector('.team:nth-child(1)');
                    const player2Div = matchDiv.querySelector('.team:nth-child(2)');

                    const player1Id = player1Div.getAttribute('data-player-id');
                    const player2Id = player2Div.getAttribute('data-player-id');

                    const player1Name = player1Div.textContent.split('(')[0].trim();
                    const player2Name = player2Div.textContent.split('(')[0].trim();

                    let winnerId = null;
                    if (player1Div.classList.contains('selected')) {
                        winnerId = player1Id;
                    } else if (player2Div.classList.contains('selected')) {
                        winnerId = player2Id;
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'No Winner Selected',
                            text: 'Please select a winner by clicking on a player.'
                        });
                        return;
                    }

                    if (!winnerId) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Winner ID is missing.'
                        });
                        return;
                    }

                    // Populate the scores modal with player names and IDs
                    document.getElementById('tournamentId').value = currentTournamentId;
                    document.getElementById('roundNumber').value = round;
                    document.getElementById('matchNumber').value = matchNumber;
                    document.getElementById('winnerId').value = winnerId;

                    document.getElementById('player1Name').value = player1Name;
                    document.getElementById('player2Name').value = player2Name;

                    // Reset previous scores and error message
                    document.getElementById('player1Score').value = '';
                    document.getElementById('player2Score').value = '';
                    document.getElementById('scoreError').style.display = 'none';

                    // Show the scores modal
                    const scoresModal = new bootstrap.Modal(document.getElementById('scoresModal'));
                    scoresModal.show();

                    // Store current match information
                    currentMatch = {
                        tournament_id: currentTournamentId,
                        round: round,
                        match_number: matchNumber,
                        winner_id: winnerId,
                        player1_id: player1Id,
                        player2_id: player2Id
                    };
                } else if (event.target.classList.contains('team')) {
                    const winBtn = event.target.parentElement.querySelector('.win-btn');
                    if (winBtn.hasAttribute('disabled')) {
                        return; // Do not allow selection if match is already decided
                    }
                    // Remove 'selected' class from all teams
                    event.target.parentElement.querySelectorAll('.team').forEach(team => team.classList.remove('selected'));
                    // Add 'selected' class to the clicked team
                    event.target.classList.add('selected');
                }
            });

            // Handle Scores Form Submission
            document.getElementById('scoresForm').addEventListener('submit', function(event) {
                event.preventDefault();
            
                const tournamentId = document.getElementById('tournamentId').value;
                const round = document.getElementById('roundNumber').value;
                const matchNumber = document.getElementById('matchNumber').value;
                const winnerId = document.getElementById('winnerId').value;
                const player1Score = parseInt(document.getElementById('player1Score').value);
                const player2Score = parseInt(document.getElementById('player2Score').value);
            
                const player1Name = document.getElementById('player1Name').value;
                const player2Name = document.getElementById('player2Name').value;
            
                // Determine winner's score
                let winnerScore = 0;
                let loserScore = 0;
                if (currentMatch.player1_id === winnerId) {
                    winnerScore = player1Score;
                    loserScore = player2Score;
                } else {
                    winnerScore = player2Score;
                    loserScore = player1Score;
                }
            
                // Validate scores
                if (winnerScore <= loserScore) {
                    document.getElementById('scoreError').style.display = 'block';
                    return;
                } else {
                    document.getElementById('scoreError').style.display = 'none';
                }
            
                // Prepare data to send
                const data = {
                    tournament_id: tournamentId,
                    round: round,
                    match: matchNumber, // Corrected parameter name
                    winner_id: winnerId,
                    player1_id: currentMatch.player1_id,
                    player2_id: currentMatch.player2_id,
                    player1_score: player1Score,
                    player2_score: player2Score
                };
            
                // Send data via AJAX to update_bracket.php
                fetch('update_bracket.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(responseData => {
                    if (responseData.success) {
                        // Close the modal
                        const scoresModalInstance = bootstrap.Modal.getInstance(document.getElementById('scoresModal'));
                        scoresModalInstance.hide();
            
                        // Refresh the bracket view
                        renderBracket(tournamentId);
            
                        // Display a success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Scores updated and bracket advanced successfully!',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        // Display error message
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: responseData.message || 'An error occurred while updating the bracket.',
                        });
                    }
                })
                .catch(error => {
                    console.error('Error updating bracket:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while updating the bracket.',
                    });
                });
            });


            // Function to Edit Tournament
            function editTournament(tournament) {
                document.getElementById('editTournamentId').value = tournament.tournament_id;
                document.getElementById('editTournamentName').value = tournament.name;
                document.getElementById('editVenue').value = tournament.venue;
                document.getElementById('editStartDate').value = tournament.start_date;
                document.getElementById('editEndDate').value = tournament.end_date;
                document.getElementById('editMaxPlayers').value = tournament.max_player;
                document.getElementById('editPrize').value = tournament.prize;
                document.getElementById('editFee').value = tournament.fee;
                document.getElementById('editStatus').value = tournament.status;

                const editTournamentModal = new bootstrap.Modal(document.getElementById('editTournamentModal'));
                editTournamentModal.show();
            }
            
            // Ensure modals close properly without lingering backdrops
            document.addEventListener('DOMContentLoaded', function() {
                const modals = ['#playersModal', '#showScores', '#bracketModal', '#scoresModal', '#editTournamentModal', '#addTournamentModal'];
                
                modals.forEach(modalId => {
                    const modalElement = document.querySelector(modalId);
                    if (modalElement) {
                        modalElement.addEventListener('hidden.bs.modal', function () {
                            document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
                                backdrop.remove(); // Remove lingering backdrop
                            });
                        });
                    }
                });
            });
        });

        // Function to Announce the Winner (Remains Unchanged)
        function announceWinner(winnerName, tournamentId, round) {
            if (!tournamentId) {
                console.error('Tournament ID is missing.');
                return;
            }

            fetch('announce_winner.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    winner_name: winnerName,
                    tournament_id: tournamentId,
                    round: round
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Tournament Completed',
                        text: `Congratulations to ${winnerName} for winning the tournament!`,
                    });
                } else {
                    console.error('Failed to announce winner:', data.message);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Failed to announce the winner.',
                    });
                }
            })
            .catch(error => {
                console.error('Error announcing winner:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while announcing the winner.',
                });
            });
        }

        // Initialize Bootstrap Tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    </script>
</body>
</html>
