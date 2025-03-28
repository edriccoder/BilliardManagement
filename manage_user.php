<?php
// Include database connection
include 'conn.php';

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
   </head>
   <body class="g-sidenav-show  bg-gray-100">
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
               <a class="nav-link text-white " href="admin_dashboard.php">
                  <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                     <i class="material-icons opacity-10">dashboard</i>
                  </div>
                  <span class="nav-link-text ms-1">Dashboard</span>
               </a>
            </li>
            <li class="nav-item">
               <a class="nav-link text-white " href="billiard_table.php">
                  <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                     <i class="material-icons opacity-10">table_view</i>
                  </div>
                  <span class="nav-link-text ms-1">Billiard Tables</span>
               </a>
            </li>
            <li class="nav-item">
                  <a class="nav-link text-white " href="manage_user.php">
                     <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">person</i>
                     </div>
                     <span class="nav-link-text ms-1">User Account Management</span>
                  </a>
               </li> 
               <li class="nav-item">
                  <a class="nav-link text-white " href="manage_tournament.php">
                     <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">flag</i>
                     </div>
                     <span class="nav-link-text ms-1">Billiard Tournament Scheduling Management</span>
                  </a>
               </li> 
            <li class="nav-item">
                  <a class="nav-link text-white " href="inventory_management.php">
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
               <a class="nav-link text-white " href="admin_booking.php">
                  <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                     <i class="material-icons opacity-10">book</i>
                  </div>
                  <span class="nav-link-text ms-1">Reservation Management</span>
               </a>
            </li>
            <li class="nav-item">
               <a class="nav-link text-white " href="admin_reports.php">
                  <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                     <i class="material-icons opacity-10">bar_chart</i>
                  </div>
                  <span class="nav-link-text ms-1">Reports & Analytics</span>
               </a>
            </li>
            <li class="nav-item">
               <a class="nav-link text-white " href="admin_feedback.php">
                  <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                     <i class="material-icons opacity-10">feedback</i>
                  </div>
                  <span class="nav-link-text ms-1">Manage Feedback</span>
               </a>
            </li>
            <li class="nav-item mt-3">
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
         <!-- Page Heading -->
         <!-- Content Row -->
         <?php
            include 'conn.php';

            // Retrieve data from users with role 'user'
            $sql_users = "SELECT user_id, name, email, username FROM users WHERE role = 'user'";
            $stmt_users = $conn->prepare($sql_users);
            $stmt_users->execute();
            $users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);

            // Retrieve data from users with role 'cashier'
            $sql_cashiers = "SELECT user_id, name, email, username FROM users WHERE role = 'cashier'";
            $stmt_cashiers = $conn->prepare($sql_cashiers);
            $stmt_cashiers->execute();
            $cashiers = $stmt_cashiers->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <!-- Container for Users and Cashiers -->
            <div class="container-fluid py-4">
               <div class="d-sm-flex align-items-center justify-content-between mb-4">
                  <h1 class="h3 mb-0 text-gray-800">Manage Users and Cashiers</h1>
                  <button class='btn btn-primary editBtn' onclick="window.location.href='generate_report_users.php'">Generate User/Cashier Reports</button>
                  <button class='btn btn-primary editBtn' data-toggle='modal' data-target='#addCashier'>Add Cashier Account</button>
               </div>

               <!-- Users Table -->
               <div class="row">
                  <div class="col-12">
                     <div class="card my-4">
                           <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                              <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                 <h6 class="text-white text-capitalize ps-3">Users</h6>
                              </div>
                           </div>
                           <div class="card-body px-0 pb-2">
                              <div class="table-responsive p-0">
                                 <table class="table align-items-center mb-0">
                                       <thead>
                                          <tr>
                                             <th>Name</th>
                                             <th>Email</th>
                                             <th>Username</th>
                                             <th></th>
                                          </tr>
                                       </thead>
                                       <tbody>
                                       <?php
                                          if (!empty($users)) {
                                             foreach ($users as $user) {
                                                echo '<tr>' .
                                                '<td>' . htmlspecialchars($user["name"]) . '</td>' .
                                                '<td>' . htmlspecialchars($user["email"]) . '</td>' .
                                                '<td>' . htmlspecialchars($user["username"]) . '</td>' .
                                                // Add data attributes correctly
                                                '<td><button type="button" class="btn btn-warning btn-sm" onclick=\'editUserModal('. json_encode($user) .')\'>Edit</button><td>' .
                                                '</tr>';
                                             }
                                          } else {
                                             echo '<tr><td colspan="4">No users found</td></tr>';
                                          }
                                          ?>
                                       </tbody>
                                 </table>
                              </div>
                           </div>
                     </div>
                  </div>
               </div>

               <!-- Cashiers Table -->
               <div class="row">
                  <div class="col-12">
                     <div class="card my-4">
                           <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                              <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                 <h6 class="text-white text-capitalize ps-3">Cashiers</h6>
                              </div>
                           </div>
                           <div class="card-body px-0 pb-2">
                              <div class="table-responsive p-0">
                                 <table class="table align-items-center mb-0">
                                       <thead>
                                          <tr>
                                             <th>Name</th>
                                             <th>Email</th>
                                             <th>Username</th>
                                             <th></th>
                                          </tr>
                                       </thead>
                                       <tbody>
                                       <?php
                                          if (!empty($cashiers)) {
                                             foreach ($cashiers as $cashier) {
                                                echo '<tr>' .
                                                '<td>' . htmlspecialchars($cashier["name"]) . '</td>' .
                                                '<td>' . htmlspecialchars($cashier["email"]) . '</td>' .
                                                '<td>' . htmlspecialchars($cashier["username"]) . '</td>' .
                                                // Add data attributes correctly
                                                '<td><button type="button" class="btn btn-warning btn-sm" onclick=\'editCashierModal('. json_encode($cashier) .')\'>Edit</button><td>' .
                                                '</tr>';
                                             }
                                          } else {
                                             echo '<tr><td colspan="4">No cashiers found</td></tr>';
                                          }
                                          ?>
                                       </tbody>
                                 </table>
                              </div>
                           </div>
                     </div>
                  </div>
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
      <!-- Modal for add table -->
      <div class="modal fade" id="addTableModal" tabindex="-1" role="dialog" aria-labelledby="addTableModalLabel" aria-hidden="true">
         <div class="modal-dialog" role="document">
            <div class="modal-content">
                  <div class="modal-header">
                     <h5 class="modal-title" id="addTableModalLabel">Add Billiard Table</h5>
                     <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                     </button>
                  </div>
                  <div class="modal-body">
                     <form method="POST" action = "addTable.php" enctype="multipart/form-data">
                        <div class="form-group">
                              <label>Table Name</label>
                              <input type="text" name="tablename" class="form-control" required="required"/>
                        </div>
                        <div class="form-group">
                              <label>Table Status</label>
                              <select name="status" class="form-control" required="required">
                                 <option value="Available">Available</option>
                                 <option value="Occupied">Occupied</option>
                                 <option value="Under Maintenance">Under Maintenance</option>
                              </select>
                        </div>
                        <div class="modal-footer">
                              <button type="submit" name="save" class="btn btn-primary">Save</button>
                              <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
                        </div>
                     </form>
                  </div>
            </div>
         </div>
      </div>

      <!-- Modal for Adding Cashier Account -->
      <div class="modal fade" id="addCashier" tabindex="-1" role="dialog" aria-labelledby="addCashierLabel" aria-hidden="true">
         <div class="modal-dialog" role="document">
            <div class="modal-content">
                  <div class="modal-header">
                     <h5 class="modal-title" id="addCashierLabel">Add Cashier Account</h5>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                     </button>
                  </div>
                  <form id="addCashierForm" action="addCashier.php" method="POST">
                     <div class="modal-body">
                        <label for="cashierName">Name</label>
                        <div class="input-group input-group-outline my-3">
                              <label for="cashierName">Name</label>
                              <input type="text" class="form-control" id="cashierName" name="name" required>
                        </div>                    
                        <div class="input-group input-group-outline my-3">
                              
                              <input type="email" class="form-control" id="cashierEmail" name="email" required>
                        </div>
                        <label for="cashierUsername">Username</label>
                        <div class="input-group input-group-outline my-3">
                              
                              <input type="text" class="form-control" id="cashierUsername" name="username" required>
                        </div>
                        <label for="cashierPassword">Password</label>
                        <div class="input-group input-group-outline my-3">
                              
                              <input type="password" class="form-control" id="cashierPassword" name="password" required>
                        </div>
                        <input type="hidden" name="role" value="cashier">
                     </div>
                     <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="saveCashier">Save Account</button>
                     </div>
                  </form>
            </div>
         </div>
      </div>

      <!-- Edit User Modal -->
      <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
         <div class="modal-dialog" role="document">
            <div class="modal-content">
               <div class="modal-header">
               <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
               </div>
               <form id="editUserForm" method="POST" action="update_user.php">
               <div class="modal-body">
                  <input type="hidden" name="user_id" id="editUserId">
                  <label for="editUserName">Name</label>
                  <div class="input-group input-group-outline my-3">                    
                     <input type="text" class="form-control" id="editUserName" name="name" required>
                  </div>
                  <label for="editUserEmail">Email</label>
                  <div class="input-group input-group-outline my-3">
                     <input type="email" class="form-control" id="editUserEmail" name="email" required>
                  </div>
                  <label for="editUserUsername">Username</label>
                  <div class="input-group input-group-outline my-3">
                     <input type="text" class="form-control" id="editUserUsername" name="username" required>
                  </div>
                  <div class="input-group input-group-outline my-3">
                     <label for="editUserPassword">Password (Leave blank if unchanged)</label>
                     <input type="password" class="form-control" id="editUserPassword" name="password">
                  </div>
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary">Save changes</button>
               </div>
               </form>
            </div>
         </div>
      </div>

      <!-- Edit Cashier Modal -->
      <div class="modal fade" id="editCashierModal" tabindex="-1" role="dialog" aria-labelledby="editCashierModalLabel" aria-hidden="true">
         <div class="modal-dialog" role="document">
            <div class="modal-content">
               <div class="modal-header">
               <h5 class="modal-title" id="editCashierModalLabel">Edit Cashier</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
               </div>
               <form id="editCashierForm" method="POST" action="update_cashier.php">
               <div class="modal-body">
                  <input type="hidden" name="user_id" id="editCashierId">
                  <label for="editCashierName">Name</label>
                  <div class="input-group input-group-outline my-3">
                     <input type="text" class="form-control" id="editCashierName" name="name" required>
                  </div>
                  <label for="editCashierEmail">Email</label>
                  <div class="input-group input-group-outline my-3">
                     <input type="email" class="form-control" id="editCashierEmail" name="email" required>
                  </div>
                  <label for="editCashierUsername">Username</label>
                  <div class="input-group input-group-outline my-3p">
                     <input type="text" class="form-control" id="editCashierUsername" name="username" required>
                  </div>
                  <label for="editCashierPassword">Password (Leave blank if unchanged)</label>
                  <div class="input-group input-group-outline my-3">                
                     <input type="password" class="form-control" id="editCashierPassword" name="password">
                  </div>
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary">Save changes</button>
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

         $(document).ready(function() {
            $('.show-players').click(function() {
                  var tournamentId = $(this).data('tournament-id');
                  $.ajax({
                     url: 'get_players.php',
                     type: 'GET',
                     data: { tournament_id: tournamentId },
                     success: function(response) {
                        $('#players-list').html(response);
                     }
                  });
            });

            $('#saveCashierBtn').click(function(event) {
               event.preventDefault(); // Prevent the default button action

               // Serialize the form data
               var formData = $('#addCashierForm').serialize();

               // Make AJAX request
               $.ajax({
                  type: 'POST',
                  url: 'addCashier.php', // Ensure this points to your correct backend script
                  data: formData,
                  success: function(response) {
                     // Handle success
                     alert(response); // Alert the response from the server
                     $('#addCashier').modal('hide'); // Hide the modal
                     // Optionally, you could refresh the user list or do other actions here
                  },
                  error: function(xhr, status, error) {
                     // Handle error
                     alert('An error occurred: ' + error);
                  }
               });
            });
         });

         function editCashierModal(cashier) {
            console.log("Editing cashier:", cashier);
            
            // Assuming the modal has fields with these IDs
            $('#editCashierId').val(cashier.user_id);
            $('#editCashierName').val(cashier.name);
            $('#editCashierEmail').val(cashier.email);
            $('#editCashierUsername').val(cashier.username);

            // Show the modal
            $('#editCashierModal').modal('show');
         }

         function editUserModal(user) {
            console.log("Editing user:", user);
            
            // Assuming the modal has fields with these IDs
            $('#editUserId').val(user.user_id);
            $('#editUserName').val(user.name);
            $('#editUserEmail').val(user.email);
            $('#editUserUsername').val(user.username);

            // Show the modal
            $('#editUserModal').modal('show');
         }
         
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
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
      <!-- Bootstrap 5 JS Bundle (includes Popper.js) -->
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


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