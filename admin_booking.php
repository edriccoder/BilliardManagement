<?php
// admin_booking.php

// Enable error logging
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log'); // Ensure this path is writable
error_reporting(E_ALL);

session_start();
include 'conn.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Generate CSRF token if not present
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle SweetAlert messages
if (isset($_SESSION['alert'])) {
    $alertType = $_SESSION['alert']['type'];
    $alertMessage = $_SESSION['alert']['message'];
    unset($_SESSION['alert']);
}

// Fetch all tables
try {
    $sqlTables = "SELECT table_number, status, table_id FROM tables";
    $stmtTables = $conn->prepare($sqlTables);
    $stmtTables->execute();
    $tables = $stmtTables->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("SQL Error (Fetching Tables): " . $e->getMessage());
    $_SESSION['alert'] = [
        'type' => 'error',
        'message' => "Error fetching tables."
    ];
    header("Location: admin_booking.php");
    exit();
}

// Fetch all bookings (active and archived)
try {
    // Active bookings
    $sqlActiveBookings = "SELECT 
                        b.booking_id, 
                        b.user_id, 
                        u.username, 
                        b.customer_name,
                        b.contact_number,
                        b.table_id, 
                        b.table_name, 
                        b.start_time, 
                        b.end_time, 
                        b.status, 
                        b.num_matches, 
                        b.num_players, 
                        t.amount, 
                        t.payment_method
                     FROM bookings b
                     LEFT JOIN transactions t ON b.booking_id = t.booking_id
                     LEFT JOIN users u ON b.user_id = u.user_id
                     WHERE b.archive = 0
                     ORDER BY b.booking_id DESC";
    $stmtActive = $conn->prepare($sqlActiveBookings);
    $stmtActive->execute();
    $activeBookings = $stmtActive->fetchAll(PDO::FETCH_ASSOC);

    // Archived bookings
    $sqlArchivedBookings = "SELECT 
                            b.booking_id, 
                            b.user_id, 
                            u.username, 
                            b.customer_name,
                            b.table_id, 
                            b.table_name, 
                            b.start_time, 
                            b.end_time, 
                            b.status, 
                            b.contact_number,
                            b.num_matches, 
                            t.amount, 
                            t.payment_method
                         FROM bookings b
                         LEFT JOIN transactions t ON b.booking_id = t.booking_id
                         LEFT JOIN users u ON b.user_id = u.user_id
                         WHERE b.archive = 1
                         ORDER BY b.booking_id DESC";
    $stmtArchived = $conn->prepare($sqlArchivedBookings);
    $stmtArchived->execute();
    $archivedBookings = $stmtArchived->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['alert'] = [
        'type' => 'error',
        'message' => "Error fetching bookings: " . $e->getMessage()
    ];
    header("Location: admin_booking.php");
    exit();
}

// Fetch all users for mapping
try {
    $sqlUsers = "SELECT user_id, username FROM users";
    $stmtUsers = $conn->prepare($sqlUsers);
    $stmtUsers->execute();
    $users = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

    // Create a user map for easy reference
    $userMap = [];
    foreach ($users as $user) {
        $userMap[$user['user_id']] = $user['username'];
    }
} catch (PDOException $e) {
    $_SESSION['alert'] = [
        'type' => 'error',
        'message' => "Error fetching users: " . $e->getMessage()
    ];
    header("Location: admin_booking.php");
    exit();
}

// Fetch notifications
try {
    $stmtNotifications = $conn->prepare("SELECT notification_id, user_id, message, created_at, is_read FROM admin_notifications ORDER BY created_at DESC");
    $stmtNotifications->execute();
    $notifications = $stmtNotifications->fetchAll(PDO::FETCH_ASSOC);

    // Fetch unread notifications count
    $stmtUnread = $conn->prepare("SELECT COUNT(*) AS unread_count FROM admin_notifications WHERE is_read = 0");
    $stmtUnread->execute();
    $unreadCountResult = $stmtUnread->fetch(PDO::FETCH_ASSOC);
    $unreadCount = $unreadCountResult['unread_count'] ?? 0;
} catch (PDOException $e) {
    $_SESSION['alert'] = [
        'type' => 'error',
        'message' => "Error fetching notifications: " . $e->getMessage()
    ];
    header("Location: admin_booking.php");
    exit();
}

// Encode userMap for JavaScript
$userMapJson = json_encode($userMap);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- [Your existing head content remains unchanged] -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Billiard Management - Admin Booking</title>

    <!-- Fonts and Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700" />
    <link href="./assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="./assets/css/nucleo-svg.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Material Dashboard CSS -->
    <link id="pagestyle" href="./assets/css/material-dashboard.css?v=3.1.0" rel="stylesheet" />

    <!-- FullCalendar CSS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />

    <!-- Custom Styles -->
    <style>
        /* Adjust calendar height and appearance */
        #bookingCalendar {
            max-width: 100%;
            margin: 40px auto;
            padding: 10px;
        }

        /* Style events for better visibility */
        .fc-event {
            font-size: 0.9em;
            padding: 2px 4px;
            border-radius: 4px;
            color: #00000;
        }

        /* Notification styles */
        .notification.read {
            background-color: #f8f9fa;
            color: #6c757d;
        }

        /* Active navigation link */
        .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
        }

        /* Make month filter text black */
        .fc-daygrid-day-top, .fc-daygrid-day-number {
            color: black !important;
        }

        /* Ensure the badge is positioned correctly */
        .badge {
            position: absolute;
            top: 0;
            right: 0;
            transform: translate(50%, -50%);
        }

        /* Responsive adjustments for notifications */
        @media (max-width: 767px) {
            .badge {
                transform: translate(30%, -50%);
            }
        }
    </style>
</head>
<body class="g-sidenav-show bg-gray-100">
    <!-- Sidebar -->
    <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 bg-gradient-dark" id="sidenav-main">
        <div class="sidenav-header">
            <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-xl-none" aria-hidden="true" id="iconSidenav"></i>
            <a class="navbar-brand m-0" href="#">
                <img src="./img/admin.png" class="navbar-brand-img h-100" alt="main_logo">
                <span class="ms-1 font-weight-bold text-white">Admin</span>
            </a>
        </div>
        <hr class="horizontal light mt-0 mb-2">
        <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
            <ul class="navbar-nav">
                <?php
                // Define navigation links with active state
                $navItems = [
                    'admin_dashboard.php' => ['icon' => 'dashboard', 'text' => 'Dashboard'],
                    'billiard_table.php' => ['icon' => 'table_view', 'text' => 'Billiard Tables'],
                    'manage_user.php' => ['icon' => 'person', 'text' => 'User Account Management'],
                    'manage_tournament.php' => ['icon' => 'flag', 'text' => 'Billiard Tournament Scheduling Management'],
                    'inventory_management.php' => ['icon' => 'inventory', 'text' => 'Inventory Management'],
                    'admin_announcement.php' => ['icon' => 'campaign', 'text' => 'Announcement Management'],
                    'admin_booking.php' => ['icon' => 'book', 'text' => 'Reservation Management'],
                    'admin_reports.php' => ['icon' => 'bar_chart', 'text' => 'Reports & Analytics'],
                    'admin_feedback.php' => ['icon' => 'feedback', 'text' => 'Manage Feedback'],
                ];

                $currentPage = basename($_SERVER['PHP_SELF']);

                foreach ($navItems as $href => $item) {
                    $activeClass = ($currentPage == $href) ? 'active' : '';
                    echo '<li class="nav-item">
                            <a class="nav-link text-white ' . $activeClass . '" href="' . $href . '">
                                <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="material-icons opacity-10">' . $item['icon'] . '</i>
                                </div>
                                <span class="nav-link-text ms-1">' . $item['text'] . '</span>
                            </a>
                          </li>';
                }
                ?>
            </ul>
        </div>
    </aside>
    <!-- End Sidebar -->

    <!-- Main Content -->
    <main class="main-content border-radius-lg">
        <!-- Navbar -->
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" data-scroll="true">
            <div class="container-fluid py-1 px-3">
                <nav aria-label="breadcrumb">
                </nav>
                <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
                    <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                        <!-- Placeholder for future search or controls -->
                    </div>
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
                        <!-- Notification Icon with Unread Count Badge -->
                        <li class="nav-item dropdown pe-2 d-flex align-items-center">
                            <a href="javascript:;" class="nav-link text-body p-0 position-relative" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-bell cursor-pointer"></i>
                                <?php if ($unreadCount > 0): ?>
                                    <span class="badge bg-danger text-white position-absolute top-0 end-0 translate-middle p-1 rounded-circle" style="font-size: 0.75rem;">
                                        <?php echo $unreadCount; ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end px-2 py-3 me-sm-n4" aria-labelledby="dropdownMenuButton" style="max-height: 400px; overflow-y: auto;">
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
                                <?php if (empty($notifications)): ?>
                                    <li class="text-center text-muted">No notifications found.</li>
                                <?php endif; ?>
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

        <div class="container-fluid">
            <!-- Alert Messages -->
            <?php if (isset($alertType) && isset($alertMessage)): ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: '<?php echo $alertType; ?>',
                            title: '<?php echo ($alertType === 'success') ? 'Success!' : 'Error!'; ?>',
                            text: '<?php echo addslashes($alertMessage); ?>',
                            confirmButtonText: 'OK'
                        });
                    });
                </script>
            <?php endif; ?>

            <!-- Archived Bookings and Add Walk-In Buttons -->
            <div class="d-sm-flex align-items-center">
                <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#showArchiveModal">Show Archive</button>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bookingwalkin">Add Walk-In</button>
            </div>


            <!-- Booking Calendar -->
            <div class="card mt-4">
                <div class="card-header pb-0 px-3">
                    <h6 class="mb-0">Booking Calendar</h6>
                </div>
                <div class="card-body pt-4 p-3">
                    <div id='bookingCalendar'></div>
                </div>
            </div>

            <!-- Archived Bookings Modal -->
            <div class="modal fade" id="showArchiveModal" tabindex="-1" role="dialog" aria-labelledby="archiveModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="archiveModalLabel">Archived Bookings</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <ul class="list-group" id="archiveList">
                                <?php
                                if (!empty($archivedBookings)) {
                                    foreach ($archivedBookings as $archivedBooking) {
                                        // Determine the display name
                                        if (isset($userMap[$archivedBooking["user_id"]])) {
                                            $displayName = htmlspecialchars($userMap[$archivedBooking["user_id"]]);
                                        } elseif (!empty($archivedBooking["customer_name"])) {
                                            $displayName = htmlspecialchars($archivedBooking["customer_name"]);
                                        } else {
                                            $displayName = 'Walk-In';
                                        }

                                        echo '<li class="list-group-item border-0 d-flex p-4 mb-2 bg-gray-100 border-radius-lg">';
                                        echo '<div class="d-flex flex-column">';
                                        echo '<h6 class="mb-3 text-sm">' . $displayName . '</h6>';
                                        echo '<span class="mb-2 text-xs">Table Name: <span class="text-dark font-weight-bold ms-sm-2">' . htmlspecialchars($archivedBooking["table_name"]) . '</span></span>';
                                        echo '<span class="mb-2 text-xs">Start Time: <span class="text-dark ms-sm-2 font-weight-bold">' . htmlspecialchars($archivedBooking["start_time"]) . '</span></span>';
                                        echo '<span class="mb-2 text-xs">End Time: <span class="text-dark ms-sm-2 font-weight-bold">' . htmlspecialchars($archivedBooking["end_time"]) . '</span></span>';
                                        echo '<span class="mb-2 text-xs">Status: <span class="text-dark ms-sm-2 font-weight-bold">' . htmlspecialchars($archivedBooking["status"]) . '</span></span>';
                                        echo '<span class="mb-2 text-xs">Number of Matches: <span class="text-dark ms-sm-2 font-weight-bold">' . htmlspecialchars($archivedBooking["num_matches"]) . '</span></span>';
                                        echo '<span class="mb-2 text-xs">Amount: <span class="text-dark ms-sm-2 font-weight-bold">' . htmlspecialchars($archivedBooking["amount"]) . '</span></span>';
                                        echo '</div>';
                                        echo '</li>';
                                    }
                                } else {
                                    echo '<li class="list-group-item border-0 d-flex p-4 mb-2 bg-gray-100 border-radius-lg">';
                                    echo '<div class="d-flex flex-column">';
                                    echo '<h6 class="mb-3 text-sm">No archived bookings found.</h6>';
                                    echo '</div>';
                                    echo '</li>';
                                }
                                ?>
                            </ul>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking Management Modal -->
            <div class="modal fade" id="bookingModal" tabindex="-1" role="dialog" aria-labelledby="bookingModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Manage Booking</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="manageBookingForm">
                                <input type="hidden" id="bookingId" name="booking_id">
                                <input type="hidden" id="userId" name="user_id">
                                <input type="hidden" id="csrfTokenManage" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                                <label for="username" class="form-label">User</label>
                                <div class="input-group input-group-outline my-3">
                                    <input type="text" class="form-control" id="username" name="username" readonly>
                                </div>

                                <label for="tableName" class="form-label">Table Name</label>
                                <div class="input-group input-group-outline my-3">
                                    <input type="text" class="form-control" id="tableName" name="table_name" readonly>
                                </div>

                                <label for="startTimeManage" class="form-label">Start Time</label>
                                <div class="input-group input-group-outline my-3">
                                    <input type="datetime-local" class="form-control" id="startTimeManage" name="start_time" required>
                                </div>

                                <label for="endTimeManage" class="form-label">End Time</label>
                                <div class="input-group input-group-outline my-3">
                                    <input type="datetime-local" class="form-control" id="endTimeManage" name="end_time" required>
                                </div>
                                
                                <label for="contactNumberManage" class="form-label">Contact Number</label>
                                <div class="input-group input-group-outline my-3">
                                    <input type="tel" class="form-control" id="contactNumberManage" name="contact_number" readonly>
                                </div>


                                <label for="numPlayersManage" class="form-label">Number of Players</label>
                                <div class="input-group input-group-outline my-3">
                                    <input type="number" class="form-control" id="numPlayersManage" name="num_players" required>
                                </div>

                                <label for="amountManage" class="form-label">Amount</label>
                                <div class="input-group input-group-outline my-3">
                                    <input type="text" class="form-control" id="amountManage" name="amount" readonly>
                                </div>

                                <label for="paymentMethodManage" class="form-label">Payment Method</label>
                                <div class="input-group input-group-outline my-3">
                                    <input type="text" class="form-control" id="paymentMethodManage" name="payment_method" readonly>
                                </div>

                                <!-- Removed Proof of Payment Section -->

                                <div class="modal-footer">
                                    <button type="button" id="confirmBookingBtn" class="btn btn-success">Confirm Booking</button>
                                    <button type="button" id="cancelBookingBtn" class="btn btn-danger">Cancel Booking</button>
                                    <button type="button" id="archiveBookingBtn" class="btn btn-warning">Archive Booking</button>
                                    <!-- New Check Out Button -->
                                    <button type="button" id="checkoutBookingBtn" class="btn btn-primary">Check Out</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking Walk-In Modal (Ensured CSRF Token Inclusion) -->
            <div class="modal fade" id="bookingwalkin" tabindex="-1" role="dialog" aria-labelledby="bookingwalkinLabel" aria-hidden="true">
               <div class="modal-dialog" role="document">
                  <div class="modal-content">
                     <div class="modal-header">
                        <h5 class="modal-title" id="bookingwalkinLabel">Book Billiard Table</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                     </div>
                     <div class="modal-body">
                        <form method="POST" action="booktable_walkin.php" enctype="multipart/form-data">
                           <!-- CSRF Token -->
                           <input type="hidden" id="csrf_token_walkin" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                           
                           <!-- Amount Field -->
                           <input type="hidden" id="amountWalkIn" name="amount">

                           <!-- Customer Name Input -->
                           <label for="customerNameWalkIn">Customer Name</label>
                           <div class="input-group input-group-outline my-3">
                              <input type="text" name="customer_name" id="customerNameWalkIn" class="form-control" required>
                           </div>

                           <label for="tableSelectWalkIn">Table</label>
                           <div class="input-group input-group-outline my-3">
                              <select name="table_id" id="tableSelectWalkIn" class="form-control" required>
                                  <option value="">Select Table</option>
                                  <?php if (!empty($tables)): ?>
                                     <?php foreach ($tables as $table): ?>
                                        <option value="<?php echo htmlspecialchars($table['table_id']); ?>">
                                           <?php echo htmlspecialchars($table['table_number']); ?>
                                        </option>
                                     <?php endforeach; ?>
                                  <?php else: ?>
                                     <option value="">No tables available</option>
                                  <?php endif; ?>
                               </select>
                           </div>

                           <label for="bookingTypeWalkIn">Booking Type</label>
                           <div class="input-group input-group-outline my-3">
                              <select name="booking_type" id="bookingTypeWalkIn" class="form-control" onchange="calculateAmount('walkin')" disabled>
                                 <option value="hour">Per Hour</option>
                              </select>
                           </div>
                           
                           <!-- Contact Number Input -->
                            <label for="contactNumberWalkIn">Contact Number</label>
                            <div class="input-group input-group-outline my-3">
                               <input type="tel" name="contact_number" id="contactNumberWalkIn" class="form-control" required>
                            </div>

                           <!-- Number of Players Input -->
                           <label for="numPlayersWalkIn">Number of Players</label>
                           <div class="input-group input-group-outline my-3">
                              <input type="number" name="num_players" id="numPlayersWalkIn" class="form-control" required>
                           </div>

                           <!-- Per Hour Fields -->
                           <div id="perHourFieldsWalkIn">
                              <label for="startTimeWalkIn">Start Time</label>
                              <div class="input-group input-group-outline my-3">
                                 <input type="datetime-local" id="startTimeWalkIn" name="start_time" class="form-control" onchange="calculateAmount('walkin')" required />
                              </div>
                              <label for="endTimeWalkIn">End Time</label>
                              <div class="input-group input-group-outline my-3">
                                 <input type="datetime-local" id="endTimeWalkIn" name="end_time" class="form-control" onchange="calculateAmount('walkin')" required />
                              </div>
                           </div>

                           <!-- Payment Method - Only Cash -->
                           <label for="paymentMethodWalkIn">Payment Method</label>
                            <div class="input-group input-group-outline my-3">
                               <select name="payment_method" id="paymentMethodWalkIn" class="form-control" disabled>
                                  <option value="cash">Cash</option>
                               </select>
                            </div>

                           <label for="totalAmountWalkIn">Total Amount</label>
                           <div class="input-group input-group-outline my-3">
                              <input type="text" id="totalAmountWalkIn" class="form-control" readonly>
                           </div>

                           <div class="modal-footer">
                              <button type="submit" name="save" class="btn btn-primary" id="submitWalkInBtn" disabled>Book & Pay</button>
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                           </div>
                        </form>
                     </div>
                  </div>
               </div>
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
    </main>

    <!-- Fixed Plugin (Optional) -->
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
    </div>


    <!-- Scripts -->
    <script>
    // Pass PHP variables to JavaScript
    const userMap = <?php echo $userMapJson; ?>;
    const csrfToken = "<?php echo $_SESSION['csrf_token']; ?>";

    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('bookingCalendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'timeGridWeek',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'timeGridWeek,timeGridDay,dayGridMonth'
            },
            events: 'get_bookings_admin.php', // Endpoint to fetch bookings
            eventClick: function(info) {
                var booking = info.event.extendedProps;
                populateBookingModal(booking, info.event.id, info.event.title);
            },
            eventDidMount: function(info) {
                // Customize event colors based on status
                const statusColorMapping = {
                    'confirmed': '#28a745', // Green
                    'pending': '#ffc107',   // Yellow
                    'cancelled': '#6c757d', // Grey
                    'checked out': '#dc3545' // Red
                };
                info.el.style.backgroundColor = statusColorMapping[info.event.extendedProps.status.toLowerCase()] || '#007bff'; // Default to blue
            },
            height: 'auto',
        });
        
        calendar.render();
        
        // Function to populate the booking modal with data
        function populateBookingModal(booking, eventId, eventTitle) {
            console.log('Populating modal with booking data:', booking); // Debugging log
        
            // Verify the modal element exists before proceeding
            const bookingModalEl = document.getElementById('bookingModal');
            if (!bookingModalEl) {
                console.error("Booking modal element not found.");
                return;
            }
        
            // Populate modal fields, applying fallback values as needed
            document.getElementById('bookingId').value = booking.booking_id || '';
            document.getElementById('userId').value = booking.user_id || '';
            
            // Determine display name based on available data
            const displayName = booking.username || booking.customer_name || 'Walk-In';
            document.getElementById('username').value = displayName;
        
            document.getElementById('tableName').value = booking.table_name || 'N/A';
            document.getElementById('startTimeManage').value = formatDateTimeLocal(booking.start_time || '');
            document.getElementById('endTimeManage').value = formatDateTimeLocal(booking.end_time || '');
            document.getElementById('numPlayersManage').value = booking.num_players || 'N/A';
            document.getElementById('amountManage').value = booking.amount ? parseFloat(booking.amount).toFixed(2) : '0.00';
            document.getElementById('paymentMethodManage').value = booking.payment_method || 'Cash';
            
            // Set contact number, checking for null or empty values
            document.getElementById('contactNumberManage').value = booking.contact_number || 'N/A';
        
            // Debug output for each field
            console.log('Fields populated:');
            console.log('Booking ID:', document.getElementById('bookingId').value);
            console.log('User ID:', document.getElementById('userId').value);
            console.log('Display Name:', document.getElementById('username').value);
            console.log('Table Name:', document.getElementById('tableName').value);
            console.log('Start Time:', document.getElementById('startTimeManage').value);
            console.log('End Time:', document.getElementById('endTimeManage').value);
            console.log('Num Players:', document.getElementById('numPlayersManage').value);
            console.log('Amount:', document.getElementById('amountManage').value);
            console.log('Payment Method:', document.getElementById('paymentMethodManage').value);
            console.log('Contact Number:', document.getElementById('contactNumberManage').value);
        
            // Show the booking management modal
            const bookingModal = new bootstrap.Modal(bookingModalEl);
            bookingModal.show();
        }

        // Function to format datetime to input's datetime-local format
        function formatDateTimeLocal(datetimeStr) {
            if (!datetimeStr) return '';
            var date = new Date(datetimeStr);
            var year = date.getFullYear();
            var month = ('0' + (date.getMonth() + 1)).slice(-2);
            var day = ('0' + date.getDate()).slice(-2);
            var hours = ('0' + date.getHours()).slice(-2);
            var minutes = ('0' + date.getMinutes()).slice(-2);
            return `${year}-${month}-${day}T${hours}:${minutes}`;
        }

        // Handle Confirm Booking Button Click
        document.getElementById('confirmBookingBtn').addEventListener('click', function() {
            var bookingId = document.getElementById('bookingId').value;

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to confirm this booking?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, confirm it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Send AJAX request to confirm the booking
                    $.ajax({
                        url: 'update_booking_status.php', // Endpoint to handle status update
                        type: 'POST',
                        data: {
                            booking_id: bookingId,
                            action: 'confirm',
                            csrf_token: csrfToken
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Confirmed!',
                                    text: response.message,
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    calendar.refetchEvents(); // Refresh the calendar events
                                    var bookingModal = bootstrap.Modal.getInstance(document.getElementById('bookingModal'));
                                    bookingModal.hide(); // Hide the modal
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: response.message,
                                    confirmButtonText: 'OK'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'An unexpected error occurred.',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });
        });
        
        // Handle Check Out Booking Button Click
        document.getElementById('checkoutBookingBtn').addEventListener('click', function() {
            var bookingId = document.getElementById('bookingId').value;
        
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to check out this booking?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd', // Bootstrap primary color
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, check out!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Send AJAX request to check out the booking
                    $.ajax({
                        url: 'update_booking_status.php', // Endpoint to handle status update
                        type: 'POST',
                        data: {
                            booking_id: bookingId,
                            action: 'checkout',
                            csrf_token: csrfToken
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Checked Out!',
                                    text: response.message,
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    calendar.refetchEvents(); // Refresh the calendar events
                                    var bookingModal = bootstrap.Modal.getInstance(document.getElementById('bookingModal'));
                                    bookingModal.hide(); // Hide the modal
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: response.message,
                                    confirmButtonText: 'OK'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'An unexpected error occurred.',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });
        });

        // Handle Cancel Booking Button Click
        document.getElementById('cancelBookingBtn').addEventListener('click', function() {
            var bookingId = document.getElementById('bookingId').value;

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to cancel this booking?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, cancel it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Send AJAX request to cancel the booking
                    $.ajax({
                        url: 'update_booking_status.php', // Endpoint to handle status update
                        type: 'POST',
                        data: {
                            booking_id: bookingId,
                            action: 'cancel',
                            csrf_token: csrfToken
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Cancelled!',
                                    text: response.message,
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    calendar.refetchEvents(); // Refresh the calendar events
                                    var bookingModal = bootstrap.Modal.getInstance(document.getElementById('bookingModal'));
                                    bookingModal.hide(); // Hide the modal
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: response.message,
                                    confirmButtonText: 'OK'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'An unexpected error occurred.',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });
        });

        // Handle Archive Booking Button Click
        document.getElementById('archiveBookingBtn').addEventListener('click', function() {
            var bookingId = document.getElementById('bookingId').value;

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to archive this booking? This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, archive it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Send AJAX request to archive the booking
                    $.ajax({
                        url: 'delete_booking.php', // The endpoint we modified
                        type: 'POST',
                        data: {
                            booking_id: bookingId,
                            csrf_token: csrfToken
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Archived!',
                                    text: response.message,
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    calendar.refetchEvents(); // Refresh the calendar events
                                    var bookingModal = bootstrap.Modal.getInstance(document.getElementById('bookingModal'));
                                    bookingModal.hide(); // Hide the modal
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: response.message,
                                    confirmButtonText: 'OK'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'An unexpected error occurred.',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });
        });

        // Handle Notifications Click
        document.querySelectorAll(".notification").forEach(notification => {
            notification.addEventListener("click", function () {
                const notificationId = this.dataset.notificationId;

                // Send request to mark notification as read
                fetch("admin_notifications.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `action=mark_as_read&notification_id=${notificationId}&csrf_token=${csrfToken}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Mark as read and disable further clicks
                        this.classList.add("read");
                        this.style.pointerEvents = "none";

                        // Update the badge countadmin_booking
                        if (unreadCount > 0) {
                            unreadCount -= 1;
                            if (unreadCount > 0) {
                                // Update the badge number
                                document.querySelector(".badge").innerText = unreadCount;
                            } else {
                                // Hide the badge if count reaches zero
                                document.querySelector(".badge").style.display = 'none';
                            }
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: data.message,
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        html: `
                            <div style="text-align: center;">
                                <img src="./assets/images/logo.png" alt="Logo" width="100" height="100" style="margin-bottom: 20px;">
                                <p>An unexpected error occurred while processing your request.</p>
                            </div>
                        `,
                        confirmButtonText: 'OK'
                    });
                });
            });
        });
    });
    
    // Function to calculate amount based on booking type and modal
    function calculateAmount(modalType) {
        var amount = 0;
        if (modalType === 'walkin') {
            var startTime = document.getElementById('startTimeWalkIn').value;
            var endTime = document.getElementById('endTimeWalkIn').value;
            var submitBtn = document.getElementById('submitWalkInBtn');

            if (startTime && endTime) {
                var start = new Date(startTime);
                var end = new Date(endTime);
                var diff = (end - start) / (1000 * 60 * 60); // Difference in hours

                if (diff > 0) {
                    amount = diff * 100; // Assuming 100 currency units per hour
                    document.getElementById('totalAmountWalkIn').value = amount.toFixed(2);
                    document.getElementById('amountWalkIn').value = amount.toFixed(2);
                    submitBtn.disabled = false; // Enable the button
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Time',
                        text: 'End time must be after the start time.',
                        confirmButtonText: 'OK'
                    });
                    // Clear the amount fields if invalid
                    document.getElementById('totalAmountWalkIn').value = '';
                    document.getElementById('amountWalkIn').value = '';
                    submitBtn.disabled = true; // Disable the button
                }
            } else {
                // Clear the amount fields if one of the times is missing
                document.getElementById('totalAmountWalkIn').value = '';
                document.getElementById('amountWalkIn').value = '';
                submitBtn.disabled = true; // Disable the button
                // Do not show any Swal alert here to avoid confusion
            }
        }
        // If you plan to add more modal types in the future, handle them here
    }

</script>


    <!-- Include necessary JS libraries -->
    <!-- jQuery (Only once) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 5 JS Bundle (includes Popper.js) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <!-- FullCalendar JS -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>
    <!-- Page level plugins -->
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <!-- Page level custom scripts -->
    <script src="js/demo/datatables-demo.js"></script>
    <!-- Control Center for Material Dashboard -->
    <script src="./assets/js/material-dashboard.min.js?v=3.1.0"></script>
</body>
</html>
