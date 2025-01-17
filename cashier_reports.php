<?php
// Include database connection
include 'conn.php';

try {
    // Handle date filtering
    $startDate = $_POST['start_date'] ?? null;
    $endDate = $_POST['end_date'] ?? null;

    // Prepare parameters array
    $params = [];
    $countParams = [];
    if ($startDate && $endDate) {
        $params[':start_date'] = $startDate . ' 00:00:00';
        $params[':end_date'] = $endDate . ' 23:59:59';
        $countParams = $params;
    }

    // Base query for bookings with username
    $query = "
        SELECT b.booking_id, u.username, b.table_name, b.start_time, b.end_time, b.status, b.amount, b.num_players
        FROM bookings b
        JOIN users u ON b.user_id = u.user_id
    ";

    // Apply date filters if provided
    if ($startDate && $endDate) {
        $query .= " WHERE b.start_time BETWEEN :start_date AND :end_date";
    }

    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Total Bookings Count
    if ($startDate && $endDate) {
        $stmtTotalBookings = $conn->prepare("SELECT COUNT(*) AS total_bookings FROM bookings WHERE start_time BETWEEN :start_date AND :end_date");
        $stmtTotalBookings->execute($countParams);
    } else {
        $stmtTotalBookings = $conn->prepare("SELECT COUNT(*) AS total_bookings FROM bookings");
        $stmtTotalBookings->execute();
    }
    $totalBookingsResult = $stmtTotalBookings->fetch(PDO::FETCH_ASSOC);
    $totalBookings = $totalBookingsResult['total_bookings'] ?? 0;

    // Transactions with username
    $queryTransactions = "
        SELECT t.transaction_id, u.username, t.amount, t.payment_method, t.status, t.timestamp
        FROM transactions t
        JOIN bookings b ON t.booking_id = b.booking_id
        JOIN users u ON b.user_id = u.user_id
    ";

    // Apply same date filters
    if ($startDate && $endDate) {
        $queryTransactions .= " WHERE t.timestamp BETWEEN :start_date AND :end_date";
    }

    $stmtTransactions = $conn->prepare($queryTransactions);
    $stmtTransactions->execute($params);
    $transactions = $stmtTransactions->fetchAll(PDO::FETCH_ASSOC);

    // Total Transactions Count
    if ($startDate && $endDate) {
        $stmtTotalTransactions = $conn->prepare("SELECT COUNT(*) AS total_transactions FROM transactions WHERE timestamp BETWEEN :start_date AND :end_date");
        $stmtTotalTransactions->execute($countParams);
    } else {
        $stmtTotalTransactions = $conn->prepare("SELECT COUNT(*) AS total_transactions FROM transactions");
        $stmtTotalTransactions->execute();
    }
    $totalTransactionsResult = $stmtTotalTransactions->fetch(PDO::FETCH_ASSOC);
    $totalTransactions = $totalTransactionsResult['total_transactions'] ?? 0;

    // Total Transaction Amount
    if ($startDate && $endDate) {
        $stmtTotalTransactionAmount = $conn->prepare("SELECT SUM(amount) AS total_transaction_amount FROM transactions WHERE timestamp BETWEEN :start_date AND :end_date");
        $stmtTotalTransactionAmount->execute($countParams);
    } else {
        $stmtTotalTransactionAmount = $conn->prepare("SELECT SUM(amount) AS total_transaction_amount FROM transactions");
        $stmtTotalTransactionAmount->execute();
    }
    $totalTransactionAmountResult = $stmtTotalTransactionAmount->fetch(PDO::FETCH_ASSOC);
    $totalTransactionAmount = $totalTransactionAmountResult['total_transaction_amount'] ?? 0;

    // Inventory Transactions
    $queryInventoryTransactions = "
        SELECT it.transaction_id, i.item_name, it.transaction_type, it.quantity, it.date_time, it.description
        FROM inventory_transactions it
        JOIN inventory i ON it.item_id = i.item_id
    ";

    if ($startDate && $endDate) {
        $queryInventoryTransactions .= " WHERE it.date_time BETWEEN :start_date AND :end_date";
    }

    $stmtInventoryTransactions = $conn->prepare($queryInventoryTransactions);
    $stmtInventoryTransactions->execute($params);
    $inventoryTransactions = $stmtInventoryTransactions->fetchAll(PDO::FETCH_ASSOC);

    // Total Inventory Transactions Count
    if ($startDate && $endDate) {
        $stmtTotalInventoryTransactions = $conn->prepare("SELECT COUNT(*) AS total_inventory_transactions FROM inventory_transactions WHERE date_time BETWEEN :start_date AND :end_date");
        $stmtTotalInventoryTransactions->execute($countParams);
    } else {
        $stmtTotalInventoryTransactions = $conn->prepare("SELECT COUNT(*) AS total_inventory_transactions FROM inventory_transactions");
        $stmtTotalInventoryTransactions->execute();
    }
    $totalInventoryTransactionsResult = $stmtTotalInventoryTransactions->fetch(PDO::FETCH_ASSOC);
    $totalInventoryTransactions = $totalInventoryTransactionsResult['total_inventory_transactions'] ?? 0;

    // Tournaments
    $queryTournaments = "
        SELECT tournament_id, name, max_player, start_date, end_date, status, created_at, prize, fee, qualification, venue, start_time, end_time
        FROM tournaments
    ";

    if ($startDate && $endDate) {
        $queryTournaments .= " WHERE start_date BETWEEN :start_date AND :end_date";
    }

    $stmtTournaments = $conn->prepare($queryTournaments);
    $stmtTournaments->execute($params);
    $tournaments = $stmtTournaments->fetchAll(PDO::FETCH_ASSOC);

    // Total Tournaments Count
    if ($startDate && $endDate) {
        $stmtTotalTournaments = $conn->prepare("SELECT COUNT(*) AS total_tournaments FROM tournaments WHERE start_date BETWEEN :start_date AND :end_date");
        $stmtTotalTournaments->execute($countParams);
    } else {
        $stmtTotalTournaments = $conn->prepare("SELECT COUNT(*) AS total_tournaments FROM tournaments");
        $stmtTotalTournaments->execute();
    }
    $totalTournamentsResult = $stmtTotalTournaments->fetch(PDO::FETCH_ASSOC);
    $totalTournaments = $totalTournamentsResult['total_tournaments'] ?? 0;

} catch (PDOException $e) {
    echo "Error retrieving reports: " . $e->getMessage();
    exit();
}

// Fetch notifications
try {
    $stmtNotifications = $conn->prepare("SELECT notification_id, user_id, message, created_at, is_read FROM admin_notifications ORDER BY created_at DESC");
    $stmtNotifications->execute();
    $notifications = $stmtNotifications->fetchAll(PDO::FETCH_ASSOC);

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
    <!-- Meta Tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Title -->
    <title>Billiard Management - Reports & Analytics</title>

    <!-- Fonts and icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons+Round">

    <!-- CSS Files -->
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Custom CSS -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700" />
      <!-- Nucleo Icons -->
      <link href="./assets/css/nucleo-icons.css" rel="stylesheet" />
      <link href="./assets/css/nucleo-svg.css" rel="stylesheet" />
      <!-- Font Awesome Icons -->
      <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
      <!-- Material Icons -->
      <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
      <!-- CSS Files -->
      <link id="pagestyle" href="./assets/css/material-dashboard.css?v=3.1.0" rel="stylesheet" />
      <!-- Nepcha Analytics (nepcha.com) -->
      <!-- Nepcha is a easy-to-use web analytics. No cookies and fully compliant with GDPR, CCPA and PECR. -->
      <script defer data-site="YOUR_DOMAIN_HERE" src="https://api.nepcha.com/js/nepcha-analytics.js"></script>
      <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
      <link
         href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
         rel="stylesheet">
    <!-- Your Custom CSS (if any) -->
    <!-- <link href="css/custom.css" rel="stylesheet"> -->

    <!-- Custom styles for this template -->
    <style>
        /* Add any custom CSS here */
        body {
            font-family: 'Roboto', sans-serif;
        }
        .navbar-brand img {
            max-height: 50px;
        }
        .sidenav {
            background-color: #1a1a1a;
        }
        .sidenav .nav-link {
            color: #ffffff;
        }
        .sidenav .nav-link.active {
            background-color: #4e73df;
        }
        .card-header h5 {
            margin: 0;
        }
        .table-responsive {
            margin-top: 20px;
        }
        .footer {
            margin-top: 40px;
        }
    </style>
</head>
<body class="g-sidenav-show bg-gray-100">
    <!-- Sidebar -->
    <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3   bg-gradient-dark" id="sidenav-main">
         <div class="sidenav-header">
            <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
            <a class="navbar-brand m-0" href=" https://demos.creative-tim.com/material-dashboard/pages/dashboard " target="_blank">
            <img src="./img/admin.png" class="navbar-brand-img h-100" alt="main_logo">
            <span class="ms-1 font-weight-bold text-white">Admin</span>
            </a>
         </div>
         <hr class="horizontal light mt-0 mb-2">
         <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
         <ul class="navbar-nav">
         <li class="nav-item">
               <a class="nav-link text-white " href="cashier_dashboard.php">
                  <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                     <i class="material-icons opacity-10">dashboard</i>
                  </div>
                  <span class="nav-link-text ms-1">Dashboard</span>
               </a>
            </li>
            <li class="nav-item">
               <a class="nav-link text-white " href="cashier_billing.php">
                  <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                     <i class="material-icons opacity-10">payment</i>
                  </div>
                  <span class="nav-link-text ms-1">Billing and payments</span>
               </a>
            </li>
            <li class="nav-item">
               <a class="nav-link text-white " href="reports.php">
                  <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                     <i class="material-icons opacity-10">report</i>
                  </div>
                  <span class="nav-link-text ms-1">Reports</span>
               </a>
            </li>
            <li class="nav-item">
               <a class="nav-link text-white " href="cashier_reports.php">
                  <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                     <i class="material-icons opacity-10">bar_chart</i>
                  </div>
                  <span class="nav-link-text ms-1">Reports & Analytics</span>
               </a>
            </li>
            <li class="nav-item mt-3">
            <li class="nav-item mt-3">
         </ul>
      </aside>
    <!-- End Sidebar -->

    <!-- Main Content -->
    <main class="main-content border-radius-lg">
        <!-- Navbar -->
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur">
            <div class="container-fluid py-1 px-3">
                <div class="collapse navbar-collapse" id="navbar">
                    <ul class="navbar-nav ms-auto">
                        <!-- Notification Icon with Unread Count Badge -->
                        <li class="nav-item dropdown pe-2 d-flex align-items-center">
                            <a href="#" class="nav-link text-body p-0" id="dropdownMenuButton" data-bs-toggle="dropdown">
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
                                           href="#" 
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
                        <!-- Logout -->
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

        <!-- Page Content -->
        <div class="container-fluid py-4">
            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Reports & Analytics</h1>
            </div>
            
            <!-- Totals Summary Cards -->
            <div class="row">
                <!-- Total Bookings -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card h-100 border-left-primary shadow">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <i class="fas fa-calendar fa-2x text-primary"></i>
                                </div>
                                <div class="col ms-2">
                                    <div class="text-xs font-weight-bold text-uppercase mb-1">Total Bookings</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo htmlspecialchars($totalBookings); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Total Transactions -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card h-100 border-left-success shadow">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <i class="fas fa-dollar-sign fa-2x text-success"></i>
                                </div>
                                <div class="col ms-2">
                                    <div class="text-xs font-weight-bold text-uppercase mb-1">Total Transactions</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo htmlspecialchars($totalTransactions); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Total Transaction Amount -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card h-100 border-left-success shadow">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <i class="fas fa-money-bill-wave fa-2x text-success"></i>
                                </div>
                                <div class="col ms-2">
                                    <div class="text-xs font-weight-bold text-uppercase mb-1">Total Transaction Amount</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo htmlspecialchars(number_format($totalTransactionAmount, 2)); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Total Inventory Transactions -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card h-100 border-left-info shadow">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <i class="fas fa-boxes fa-2x text-info"></i>
                                </div>
                                <div class="col ms-2">
                                    <div class="text-xs font-weight-bold text-uppercase mb-1">Total Inventory Transactions</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo htmlspecialchars($totalInventoryTransactions); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <!-- Total Tournaments -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card h-100 border-left-warning shadow">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <i class="fas fa-trophy fa-2x text-warning"></i>
                                </div>
                                <div class="col ms-2">
                                    <div class="text-xs font-weight-bold text-uppercase mb-1">Total Tournaments</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo htmlspecialchars($totalTournaments); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- You can add more cards here if needed -->
            </div>

            <!-- Date Filter Form -->
            <form method="POST" action="" class="row g-3 mb-4">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Start Date</label>
                    <div class="input-group input-group-outline my-3">
                        <input type="date" class="form-control" id="start_date" name="start_date" placeholder="YYYY-MM-DD" value="<?php echo htmlspecialchars($startDate ?? ''); ?>" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">End Date</label>
                    <div class="input-group input-group-outline my-3">
                        <input type="date" class="form-control" id="end_date" name="end_date" placeholder="YYYY-MM-DD" value="<?php echo htmlspecialchars($endDate ?? ''); ?>" required>
                    </div>
                </div>
                <div class="col-md-4 align-self-end">
                    <button type="submit" name="filter" class="btn btn-primary">Filter</button>
                    <a href="admin_reports.php" class="btn btn-secondary">Reset</a>
                </div>
            </form>


            <!-- Reports Section -->
            <div class="row">
                <!-- Bookings Report -->
                <div class="col-lg-12 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5>Bookings</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="bookingsTable">
                                    <thead>
                                        <tr>
                                            <th>Booking ID</th>
                                            <th>Username</th>
                                            <th>Table Name</th>
                                            <th>Start Time</th>
                                            <th>End Time</th>
                                            <th>Status</th>
                                            <th>Amount</th>
                                            <th>Number of Players</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($bookings as $booking): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($booking['booking_id']); ?></td>
                                                <td><?php echo htmlspecialchars($booking['username']); ?></td>
                                                <td><?php echo htmlspecialchars($booking['table_name']); ?></td>
                                                <td><?php echo htmlspecialchars($booking['start_time']); ?></td>
                                                <td><?php echo htmlspecialchars($booking['end_time']); ?></td>
                                                <td><?php echo htmlspecialchars($booking['status']); ?></td>
                                                <td><?php echo htmlspecialchars($booking['amount']); ?></td>
                                                <td><?php echo htmlspecialchars($booking['num_players']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transactions Report -->
                <div class="col-lg-12 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5>Transactions</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="transactionsTable">
                                    <thead>
                                        <tr>
                                            <th>Transaction ID</th>
                                            <th>Username</th>
                                            <th>Amount</th>
                                            <th>Payment Method</th>
                                            <th>Status</th>
                                            <th>Timestamp</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($transactions as $transaction): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($transaction['transaction_id']); ?></td>
                                                <td><?php echo htmlspecialchars($transaction['username']); ?></td>
                                                <td><?php echo htmlspecialchars($transaction['amount']); ?></td>
                                                <td><?php echo htmlspecialchars($transaction['payment_method']); ?></td>
                                                <td><?php echo htmlspecialchars($transaction['status']); ?></td>
                                                <td><?php echo htmlspecialchars($transaction['timestamp']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Inventory Transactions Report -->
                <div class="col-lg-12 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5>Inventory Transactions</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="inventoryTransactionsTable">
                                    <thead>
                                        <tr>
                                            <th>Transaction ID</th>
                                            <th>Item Name</th>
                                            <th>Transaction Type</th>
                                            <th>Quantity</th>
                                            <th>Date & Time</th>
                                            <th>Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($inventoryTransactions as $invTrans): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($invTrans['transaction_id']); ?></td>
                                                <td><?php echo htmlspecialchars($invTrans['item_name']); ?></td>
                                                <td><?php echo htmlspecialchars(ucfirst($invTrans['transaction_type'])); ?></td>
                                                <td><?php echo htmlspecialchars($invTrans['quantity']); ?></td>
                                                <td><?php echo htmlspecialchars($invTrans['date_time']); ?></td>
                                                <td><?php echo htmlspecialchars($invTrans['description']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tournaments Report -->
                <div class="col-lg-12 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5>Tournaments</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="tournamentsTable">
                                    <thead>
                                        <tr>
                                            <th>Tournament ID</th>
                                            <th>Name</th>
                                            <th>Max Players</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Status</th>
                                            <th>Created At</th>
                                            <th>Prize</th>
                                            <th>Fee</th>
                                            <th>Qualification</th>
                                            <th>Venue</th>
                                            <th>Start Time</th>
                                            <th>End Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($tournaments as $tournament): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($tournament['tournament_id']); ?></td>
                                                <td><?php echo htmlspecialchars($tournament['name']); ?></td>
                                                <td><?php echo htmlspecialchars($tournament['max_player']); ?></td>
                                                <td><?php echo htmlspecialchars($tournament['start_date']); ?></td>
                                                <td><?php echo htmlspecialchars($tournament['end_date']); ?></td>
                                                <td><?php echo htmlspecialchars($tournament['status']); ?></td>
                                                <td><?php echo htmlspecialchars($tournament['created_at']); ?></td>
                                                <td><?php echo htmlspecialchars($tournament['prize']); ?></td>
                                                <td><?php echo htmlspecialchars($tournament['fee']); ?></td>
                                                <td><?php echo htmlspecialchars($tournament['qualification']); ?></td>
                                                <td><?php echo htmlspecialchars($tournament['venue']); ?></td>
                                                <td><?php echo htmlspecialchars($tournament['start_time']); ?></td>
                                                <td><?php echo htmlspecialchars($tournament['end_time']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Add More Reports as Needed -->
            </div>

            <!-- Graphical Representations -->
            <div class="row">
                <!-- Bookings Over Time -->
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5>Bookings Over Time</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="bookingsChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Transactions Over Time -->
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5>Transactions Over Time</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="transactionsChart"></canvas>
                        </div>
                    </div>
                </div>
                <!-- Add More Charts as Needed -->
            </div>

            <!-- Footer -->
            <div class="row mt-4">
                <footer class="sticky-footer bg-white">
                    <div class="container my-auto">
                        <div class="copyright text-center my-auto">
                            <span>T James Sporty Bar</span>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <!-- End Page Content -->
    </main>
    <!-- End Main Content -->

    <!-- Fixed Plugin -->
    <!-- ... (Your existing fixed plugin code, if any) -->

    <!-- Scripts -->
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS Bundle (includes Popper.js) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom scripts -->
    <script>
        // Initialize date pickers
        flatpickr("#start_date", {
            dateFormat: "Y-m-d"
        });
        flatpickr("#end_date", {
            dateFormat: "Y-m-d"
        });

        // Initialize DataTables
        $(document).ready(function() {
            $('#bookingsTable').DataTable();
            $('#transactionsTable').DataTable();
            $('#inventoryTransactionsTable').DataTable();
            $('#tournamentsTable').DataTable();
        });

        // Fetch data for charts using PHP
        <?php
        // Fetch bookings data for chart
        $bookingsChartData = [];
        $transactionsChartData = [];

        // Query to get bookings count per day
        $queryBookingsChart = "
            SELECT DATE(start_time) as date, COUNT(*) as count
            FROM bookings
        ";
        if ($startDate && $endDate) {
            $queryBookingsChart .= " WHERE start_time BETWEEN :start_date AND :end_date";
        }
        $queryBookingsChart .= " GROUP BY DATE(start_time) ORDER BY DATE(start_time) ASC";

        $stmtBookingsChart = $conn->prepare($queryBookingsChart);
        $stmtBookingsChart->execute($params);
        $bookingsChartResult = $stmtBookingsChart->fetchAll(PDO::FETCH_ASSOC);

        foreach ($bookingsChartResult as $row) {
            $bookingsChartData[] = ['date' => $row['date'], 'count' => $row['count']];
        }

        // Query to get transactions sum per day
        $queryTransactionsChart = "
            SELECT DATE(timestamp) as date, SUM(amount) as total
            FROM transactions
        ";
        if ($startDate && $endDate) {
            $queryTransactionsChart .= " WHERE timestamp BETWEEN :start_date AND :end_date";
        }
        $queryTransactionsChart .= " GROUP BY DATE(timestamp) ORDER BY DATE(timestamp) ASC";

        $stmtTransactionsChart = $conn->prepare($queryTransactionsChart);
        $stmtTransactionsChart->execute($params);
        $transactionsChartResult = $stmtTransactionsChart->fetchAll(PDO::FETCH_ASSOC);

        foreach ($transactionsChartResult as $row) {
            $transactionsChartData[] = ['date' => $row['date'], 'total' => $row['total']];
        }
        ?>

        // Bookings Chart
        const bookingsLabels = <?php echo json_encode(array_column($bookingsChartData, 'date')); ?>;
        const bookingsData = <?php echo json_encode(array_column($bookingsChartData, 'count')); ?>;

        const bookingsCtx = document.getElementById('bookingsChart').getContext('2d');
        const bookingsChart = new Chart(bookingsCtx, {
            type: 'bar',
            data: {
                labels: bookingsLabels,
                datasets: [{
                    label: 'Number of Bookings',
                    data: bookingsData,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        precision: 0
                    }
                }
            }
        });

        // Transactions Chart
        const transactionsLabels = <?php echo json_encode(array_column($transactionsChartData, 'date')); ?>;
        const transactionsData = <?php echo json_encode(array_column($transactionsChartData, 'total')); ?>;

        const transactionsCtx = document.getElementById('transactionsChart').getContext('2d');
        const transactionsChartInstance = new Chart(transactionsCtx, {
            type: 'line',
            data: {
                labels: transactionsLabels,
                datasets: [{
                    label: 'Total Transactions Amount',
                    data: transactionsData,
                    fill: false,
                    borderColor: 'rgba(255, 99, 132, 1)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Notification Handling
        document.addEventListener("DOMContentLoaded", function() {
            const unreadBadge = document.querySelector("#dropdownMenuButton .badge");
        
            document.querySelectorAll(".notification").forEach(notification => {
                notification.addEventListener("click", function() {
                    const notificationId = this.dataset.notificationId;
                    fetch("admin_notifications.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: `action=mark_as_read&notification_id=${notificationId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.classList.add("read");
                            this.style.pointerEvents = "none"; // Disable further clicks
        
                            // Update the badge count
                            if (unreadBadge) {
                                let currentCount = parseInt(unreadBadge.innerText);
                                if (currentCount > 0) {
                                    unreadBadge.innerText = currentCount - 1;
                                    if (currentCount - 1 === 0) {
                                        unreadBadge.style.display = 'none'; // Hide badge if count is zero
                                    }
                                }
                            }
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>
