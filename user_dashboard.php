<?php
include 'conn.php'; 
session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    // Redirect to login page if session variables are not set
    header("Location: index.php");
    exit();
}

$username = htmlspecialchars($_SESSION['username']);
$user_id = htmlspecialchars($_SESSION['user_id']);

// Fetch recent transactions
try {
    $stmt = $conn->prepare("
        SELECT t.amount, t.timestamp, b.table_name 
        FROM transactions t 
        JOIN bookings b ON t.booking_id = b.booking_id 
        WHERE b.user_id = :user_id
        ORDER BY t.timestamp DESC
        LIMIT 5
    ");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Failed " . $e->getMessage();
}

$sqlBookings = "SELECT title, body, created_at, expires_at 
                FROM announcements 
                ORDER BY created_at DESC";
$stmtBookings = $conn->prepare($sqlBookings);
$stmtBookings->execute();
$announcements = $stmtBookings->fetchAll(PDO::FETCH_ASSOC);

try {
    $stmt = $conn->prepare("SELECT notification_id, user_id, message, created_at, is_read FROM notifications ORDER BY created_at DESC");
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




// Fetch tournaments for the logged-in user
try {
    $stmt = $conn->prepare("
        SELECT t.tournament_id, t.name, t.start_date, t.end_date, t.status, t.prize, t.fee
        FROM tournaments t
        JOIN players p ON t.tournament_id = p.tournament_id
        WHERE p.user_id = :user_id
    ");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $tournaments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Failed " . $e->getMessage();
}

// Output JSON encoded user data for JavaScript to use
echo "<script>
        const userData = {
            username: '" . addslashes($username) . "',
            user_id: '" . addslashes($user_id) . "'
        };
      </script>";

echo "<script>
        const tournaments = " . json_encode($tournaments) . ";
      </script>";
?>
<style>
    /* Enhanced Announcement Styling */
    .announcement-card {
        background-color: #f8f9fa;
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
        padding: 20px;
    }

    .announcement-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .announcement-body {
        font-size: 1.1rem;
        margin-bottom: 10px;
        color: #495057;
    }

    .announcement-meta {
        font-size: 0.9rem;
        color: #6c757d;
    }

    .announcement-meta p {
        margin: 0;
    }

    .announcement-icon {
        font-size: 2rem;
        margin-right: 10px;
        vertical-align: middle;
    }

    .alert-one-hour {
        background-color: #ffeb3b; /* Yellow background for within the first hour */
        color: #000;
    }

    .alert-new {
        background-color: #d4edda; /* Green background for within 24 hours */
        color: #155724;
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
    }

    .alert-warning {
        background-color: #fff3cd;
        color: #856404;
    }

    .alert-info {
        background-color: #d1ecf1;
        color: #0c5460;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
    }

    /* Adjust spacing */
    .alert {
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 15px;
    }

    /* Custom button styling */
    .btn-close {
        color: #000;
    }
</style>
<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <link rel="apple-touch-icon" sizes="76x76" href="./assets/img/apple-icon.png">
      <link rel="icon" type="image/png" href="./assets/img/favicon.png">
      <title>
        Billiard Management
      </title>
      <!--     Fonts and icons     -->
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

      <!-- Custom styles for this template-->
      <style>
        .custom-alert {
            background-color: #17a2b8; /* Customize as needed */
            color: white; /* Text color set to white */
        }
      </style>

   </head>
   <body class="g-sidenav-show  bg-gray-100">
      <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3   bg-gradient-dark" id="sidenav-main">
         <div class="sidenav-header">
            <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
            <a class="navbar-brand m-0" href=" https://demos.creative-tim.com/material-dashboard/pages/dashboard " target="_blank">
            <img src="./img/admin.png" class="navbar-brand-img h-100" alt="main_logo">
            <span class="ms-1 font-weight-bold text-white"><?php echo htmlspecialchars($username); ?></span>
            </a>
         </div>
         <hr class="horizontal light mt-0 mb-2">
         <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
         <ul class="navbar-nav">
            <li class="nav-item">
               <a class="nav-link text-white " href="user_profile.php">
                  <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                     <i class="material-icons opacity-10">person</i>
                  </div>
                  <span class="nav-link-text ms-1">My Profile</span>
               </a>
            </li>
         <ul class="navbar-nav">
            <li class="nav-item">
               <a class="nav-link text-white " href="user_dashboard.php">
                  <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                     <i class="material-icons opacity-10">dashboard</i>
                  </div>
                  <span class="nav-link-text ms-1">My Dashboard</span>
               </a>
            </li>
            <li class="nav-item">
               <a class="nav-link text-white " href="user_table.php">
                  <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                     <i class="material-icons opacity-10">table_view</i>
                  </div>
                  <span class="nav-link-text ms-1">Billiard Tables</span>
               </a>
            </li>
            <li class="nav-item">
               <a class="nav-link text-white " href="booking_user.php">
                  <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                     <i class="material-icons opacity-10">book</i>
                  </div>
                  <span class="nav-link-text ms-1">My Booking</span>
               </a>
            </li>
            <li class="nav-item">
                  <a class="nav-link text-white " href="user_tournament.php">
                     <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">flag</i>
                     </div>
                     <span class="nav-link-text ms-1">My Tournament</span>
                  </a>
               </li> 
            <li class="nav-item">
               <a class="nav-link text-white " href="user_feedback.php">
                  <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                     <i class="material-icons opacity-10">feedback</i>
                  </div>
                  <span class="nav-link-text ms-1">My Feedback</span>
               </a>
            </li>
            <li class="nav-item mt-3">
         </ul>
      </aside>
      <main class="main-content border-radius-lg ">
         <!-- Navbar -->
         <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" data-scroll="true">
            <div class="container-fluid py-1 px-3">
               <nav aria-label="breadcrumb">
               </nav>
               <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
                  <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                  </div>
                  <ul class="navbar-nav  justify-content-end">
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
         <div class="container-fluid">
         <!-- Page Heading -->
         <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
         </div>
         <!-- Content Row -->
         <div class="card shadow mb-4">
            <img class="card-img-top" src="./img/tjamesLOGO.jpg" alt="Card image cap">
         </div>
         <!-- Table Row -->
         <div class="card shadow mb-4">
            <div class="card-header pb-0 px-3">
               <div class="row">
                     <div class="col-md-6">
                        <h6 class="mb-0">Your Transactions</h6>
                     </div>
                     <div class="col-md-6 d-flex justify-content-start justify-content-md-end align-items-center">
                        <i class="material-icons me-2 text-lg">date_range</i>
                        <small>Recent Transactions</small>
                     </div>
               </div>
            </div>
            <div class="card-body pt-4 p-3">
               <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Newest</h6>
               <ul class="list-group" style="max-height: 200px; overflow-y: auto;">
                     <?php foreach ($transactions as $transaction): ?>
                     <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                        <div class="d-flex align-items-center">
                           <button class="btn btn-icon-only btn-rounded btn-outline-success mb-0 me-3 p-3 btn-sm d-flex align-items-center justify-content-center">
                                 <i class="material-icons text-lg">expand_more</i>
                           </button>
                           <div class="d-flex flex-column">
                                 <h6 class="mb-1 text-dark text-sm"><?php echo htmlspecialchars($transaction['table_name']); ?></h6>
                                 <span class="text-xs"><?php echo date("d F Y, at h:i A", strtotime($transaction['timestamp'])); ?></span>
                           </div>
                        </div>
                        <div class="d-flex align-items-center text-success text-gradient text-sm font-weight-bold">
                           ₱<?php echo htmlspecialchars($transaction['amount']); ?>
                        </div>
                     </li>
                     <?php endforeach; ?>
               </ul>
            </div>
         </div>
         <div class="card mt-4">
            <div class="card-header p-3">
               <h5 class="mb-0">Announcements</h5>
            </div>
            <div class="card-body p-3 pb-0">
               <?php if (!empty($announcements)): ?>
                    <?php
                    $hasValidAnnouncements = false; // Flag to check if there are any valid announcements
                    foreach ($announcements as $announcement):
                        $currentTime = time();
                        $expiresAtTimestamp = strtotime($announcement['expires_at']);
                
                        // Skip expired announcements
                        if ($currentTime > $expiresAtTimestamp) {
                            continue;
                        }
                
                        $hasValidAnnouncements = true; // Set to true if at least one valid announcement exists
                
                        // Determine alert type based on the title or content (customizable logic)
                        $alertType = "alert-primary";
                        $icon = '<i class="fas fa-info-circle announcement-icon"></i>';
                
                        if (stripos($announcement['title'], 'danger') !== false) {
                            $alertType = "alert-danger";
                            $icon = '<i class="fas fa-exclamation-triangle announcement-icon"></i>';
                        } elseif (stripos($announcement['title'], 'warning') !== false) {
                            $alertType = "alert-warning";
                            $icon = '<i class="fas fa-exclamation-circle announcement-icon"></i>';
                        } elseif (stripos($announcement['title'], 'info') !== false) {
                            $alertType = "alert-info";
                            $icon = '<i class="fas fa-info-circle announcement-icon"></i>';
                        } elseif (stripos($announcement['title'], 'success') !== false) {
                            $alertType = "alert-success";
                            $icon = '<i class="fas fa-check-circle announcement-icon"></i>';
                        }
                
                        // Format the expiration date
                        $expiresAt = date('F j, Y, g:i a', $expiresAtTimestamp);
                
                        // Check if the announcement is new (created in the last 24 hours)
                        $createdAtTimestamp = strtotime($announcement['created_at']);
                        $isNewAnnouncement = ($currentTime - $createdAtTimestamp) <= (24 * 60 * 60);
                        $isWithinOneHour = ($currentTime - $createdAtTimestamp) <= (60 * 60);
                
                        // Apply special styling for recent announcements
                        if ($isWithinOneHour) {
                            $alertType = "alert-one-hour";
                        } elseif ($isNewAnnouncement) {
                            $alertType = "alert-new";
                        }
                        ?>
                        <div class="alert <?php echo $alertType; ?> alert-dismissible fade show custom-alert" role="alert">
                            <div class="d-flex align-items-start">
                                <?php echo $icon; ?>
                                <div>
                                    <strong class="announcement-title"><?php echo htmlspecialchars($announcement['title']); ?>:</strong>
                                    <div class="announcement-body"><?php echo nl2br(htmlspecialchars($announcement['body'])); ?></div>
                                    <div class="announcement-meta">
                                        <p>Expires at: <?php echo $expiresAt; ?></p>
                                        <?php if ($isWithinOneHour): ?>
                                            <p><strong>New Announcement! (Within the last hour)</strong></p>
                                        <?php elseif ($isNewAnnouncement): ?>
                                            <p><strong>New Announcement! (Within the last 24 hours)</strong></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endforeach; ?>
                
                    <?php if (!$hasValidAnnouncements): ?>
                        <div class="alert alert-light alert-dismissible fade show custom-alert" role="alert">
                            <span class="text-sm">No announcements at this time.</span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="alert alert-light alert-dismissible fade show custom-alert" role="alert">
                        <span class="text-sm">No announcements at this time.</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
            </div>
         <div class="card mt-4">
            <div class="card-header p-3">
                  <h5 class="mb-0">Tournaments</h5>
            </div>
            <div class="card-body p-3 pb-0">
                  <?php if (!empty($tournaments)): ?>
                     <table id="tournamentTable" class="table table-striped">
                        <thead>
                              <tr>
                                 <th>Name</th>
                                 <th>Start Date</th>
                                 <th>Status</th>
                              </tr>
                        </thead>
                        <tbody>
                              <?php foreach ($tournaments as $tournament): ?>
                                 <tr data-start-date="<?php echo htmlspecialchars($tournament['start_date']); ?>" data-end-date="<?php echo htmlspecialchars($tournament['end_date']); ?>">
                                    <td><?php echo htmlspecialchars($tournament['name']); ?></td>
                                    <td class="start-time"></td>
                                    <td><?php echo htmlspecialchars($tournament['status']); ?></td>
                                 </tr>
                              <?php endforeach; ?>
                        </tbody>
                     </table>
                  <?php else: ?>
                     <div class="alert alert-warning" role="alert">
                        No tournaments found for the user.
                     </div>
                  <?php endif; ?>
            </div>
         </div>                
         <!-- Content Row -->
         <div class="column">
         </div>
         <!-- Content Row -->
         <div class="row">
         <div class="row mt-4">
            <footer class="sticky-footer bg-white">
               <div class="container my-auto">
                     <div class="copyright text-center my-auto">
                        <span>T James Sporty Bar</span>
                      </div>
                </div>
            </footer>
         </div>
      </main>
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
               <!-- End Toggle Button -->
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
      <!-- Booking Modal -->
      <div class="modal fade" id="bookingModal" tabindex="-1" role="dialog" aria-labelledby="bookingModalLabel" aria-hidden="true">
         <div class="modal-dialog" role="document">
            <div class="modal-content">
                  <form method="POST" action="">
                     <div class="modal-header">
                        <h5 class="modal-title" id="bookingModalLabel">Book Table</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                        </button>
                     </div>
                     <div class="modal-body">
                        <input type="hidden" id="bookingTableId" name="table_id">
                        <input type="hidden" id="bookingTableName" name="table_name">
                        <input type="hidden" id="userId" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">

                        <label for="username">User</label>
                        <div class="input-group input-group-outline my-3">
                              <!-- Corrected the way username is displayed -->
                              <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" readonly>
                        </div>

                        <label for="startTime">Start Time</label>
                        <div class="input-group input-group-outline my-3">
                              <input type="datetime-local" class="form-control" id="startTime" name="start_time" required>
                        </div>
                        <label for="endTime">End Time</label>
                        <div class="input-group input-group-outline my-3">
                              <input type="datetime-local" class="form-control" id="endTime" name="end_time" required>
                        </div>
                     </div>
                     <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Book</button>
                     </div>
                  </form>
            </div>
         </div>
      </div>

      <script>
         var win = navigator.platform.indexOf('Win') > -1;
         if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                  damping: '0.5'
            }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
         }

         function openBookingModal(table) {
            console.log("openBookingModal called");
            console.log(table);

            document.getElementById('bookingTableId').value = table.table_id;
            document.getElementById('bookingTableName').value = table.table_number;
            document.getElementById('username').value = userData.username; 
            document.getElementById('userId').value = userData.user_id;    
            $('#bookingModal').modal('show');
         }

         document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                  return new bootstrap.Tooltip(tooltipTriggerEl)
            })
         });

         document.addEventListener('DOMContentLoaded', function () {
            function updateTournamentTimes() {
                const rows = document.querySelectorAll('#tournamentTable tbody tr');
                const now = new Date();

                rows.forEach(row => {
                    const startDate = new Date(row.dataset.startDate);
                    const endDate = new Date(row.dataset.endDate);
                    const startTimeDiff = startDate - now;
                    const endTimeDiff = endDate - now;

                    if (endTimeDiff <= 0) {
                        row.querySelector('.start-time').textContent = 'Ended';
                    } else if (startTimeDiff <= 0) {
                        row.querySelector('.start-time').textContent = 'Started';
                    } else if (startTimeDiff <= 3 * 60 * 60 * 1000) {
                        const hours = Math.floor(startTimeDiff / (1000 * 60 * 60));
                        const minutes = Math.floor((startTimeDiff % (1000 * 60 * 60)) / (1000 * 60));
                        row.querySelector('.start-time').textContent = `Starts in ${hours}h ${minutes}m`;
                    } else {
                        row.querySelector('.start-time').textContent = startDate.toLocaleString();
                    }
                });
            }

            if (tournaments.length > 0) {
                setInterval(updateTournamentTimes, 60000);
                updateTournamentTimes();
            }
        });
        
        document.addEventListener("DOMContentLoaded", function () {
           const unreadBadge = document.querySelector("#dropdownMenuButton .badge");
    
           // Hide badge if unread count is zero
           if (unreadBadge && parseInt(unreadBadge.innerText) === 0) {
               unreadBadge.style.display = 'none';
           }
    
           // Add click event to each notification item
           document.querySelectorAll(".notification").forEach(notification => {
               notification.addEventListener("click", function () {
                   const notificationId = this.dataset.notificationId;
    
                   // Send request to mark notification as read
                   fetch("user_notifications.php", {
                       method: "POST",
                       headers: { "Content-Type": "application/x-www-form-urlencoded" },
                       body: `action=mark_as_read&notification_id=${notificationId}`
                   })
                   .then(response => response.json())
                   .then(data => {
                       if (data.success) {
                           // Mark as read and disable further clicks
                           this.classList.add("read");
                           this.style.pointerEvents = "none";
    
                           // Update the unread badge count
                           if (unreadBadge) {
                               let currentCount = parseInt(unreadBadge.innerText);
                               if (currentCount > 0) {
                                   unreadBadge.innerText = currentCount - 1;
                                   // Hide badge if count reaches zero
                                   if (currentCount - 1 === 0) {
                                       unreadBadge.style.display = 'none';
                                   }
                               }
                           }
                       }
                   });
               });
           });
       });
      </script>
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
      <!-- Bootstrap 5 JS Bundle (includes Popper.js) -->
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
      <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
      <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
      <!-- Bootstrap core JavaScript-->
      <script src="vendor/jquery/jquery.min.js"></script>
      <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
      <!-- jQuery -->
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
      <!-- Bootstrap JS -->
      <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>


      <!-- Core plugin JavaScript-->
      <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

      <!-- Custom scripts for all pages-->
      <script src="js/sb-admin-2.min.js"></script>

      <!-- Page level plugins -->
      <script src="vendor/datatables/jquery.dataTables.min.js"></script>
      <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
         
      <!-- Page level custom scripts -->
      <script src="js/demo/datatables-demo.js"></script>

      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
      <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
      <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc --><script src="./assets/js/material-dashboard.min.js?v=3.1.0"></script>
   </body>
</html>