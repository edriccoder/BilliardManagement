<?php
session_start();
include 'conn.php'; // Ensure this includes your database connection

// Check if session variables are set
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Retrieve user data from the database
$user_id = $_SESSION['user_id'];
$query = "SELECT name, email, username, profile_pic, contact_number FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$user_id]); // Execute with the parameter

// Fetch user data
$user = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch the associative array

if ($user) {
    $name = htmlspecialchars($user['name']);
    $email = htmlspecialchars($user['email']);
    $username = htmlspecialchars($user['username']);
    $contact_number = htmlspecialchars($user['contact_number']);
    $profile_pic = htmlspecialchars($user['profile_pic'] ?? 'img/admin.png'); // Default image
} else {
    // Handle case where user data is not found
    echo "User not found.";
    exit();
}
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
            <span class="ms-1 font-weight-bold text-white"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
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
         <div class="container-fluid px-2 px-md-4">
            <div class="page-header min-height-300 border-radius-xl mt-4" style="background-image: url('https://images.unsplash.com/photo-1531512073830-ba890ca4eba2?ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80');">
                <span class="mask bg-gradient-primary opacity-6"></span>
            </div>
            <div class="card card-body mx-3 mx-md-4 mt-n6">
                <div class="row gx-4 mb-2">
                    <div class="col-auto">
                        <div class="avatar avatar-xl position-relative">
                            <!-- Display the profile picture -->
                            <img src="img/profilepicture/<?php echo htmlspecialchars($profile_pic); ?>" alt="profile_image" class="w-100 border-radius-lg shadow-sm">
                        </div>
                    </div>
                    <div class="col-auto my-auto">
                        <div class="h-100">
                            <!-- Display the user's name and username -->
                            <h5 class="mb-1"><?php echo htmlspecialchars($name); ?></h5>
                            <p class="mb-0 font-weight-normal text-sm"><?php echo htmlspecialchars($username); ?></p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-xl-4">
                        <div class="card card-plain h-100">
                            <div class="card-header pb-0 p-3">
                                <h6 class="mb-0">Profile Information</h6>
                            </div>
                            <!-- Existing Profile Information Section -->
                            <div class="card-body p-3">
                                <ul class="list-group">
                                    <li class="list-group-item border-0 ps-0 pt-0 text-sm"><strong class="text-dark">Full Name:</strong> &nbsp; <?php echo htmlspecialchars($name); ?></li>
                                    <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Email:</strong> &nbsp; <?php echo htmlspecialchars($email); ?></li>               
                                    <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Username:</strong> &nbsp; <?php echo htmlspecialchars($username); ?></li>
                                    <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Contact Number:</strong> &nbsp; <?php echo htmlspecialchars($contact_number); ?></li>
                                </ul>
                                <!-- Form for uploading profile picture -->
                                <form action="upload_profile_pic.php" method="post" enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <label for="profile_pic" class="form-label">Upload Profile Picture</label>
                                        <div class="input-group input-group-outline my-3">
                                            <input class="form-control" type="file" name="profile_pic" id="profile_pic">
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Upload</button>
                            
                                    <!-- Existing Buttons for Changing Email and Password -->
                                    <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#changeEmailModal">Change Email</button>
                                    <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#changePasswordModal">Change Password</button>
                                    
                                    <!-- New Button for Changing Contact Number -->
                                    <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#changeContactNumberModal">Change Contact Number</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
         <!-- Page Heading -->      
         <!-- Table Row -->

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
      <!-- Modal for changing email -->
      <div class="modal fade" id="changeEmailModal" tabindex="-1" role="dialog" aria-labelledby="changeEmailModalLabel" aria-hidden="true">
         <div class="modal-dialog" role="document">
            <div class="modal-content">
                  <div class="modal-header">
                     <h5 class="modal-title" id="changeEmailModalLabel">Change Email</h5>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                     </button>
                  </div>
                  <div class="modal-body">
                     <form action="change_email.php" method="post">
                        <div class="form-group">
                              <label for="new_email">New Email</label>
                              <div class="input-group input-group-outline my-3">
                                 <input type="email" class="form-control" id="new_email" name="new_email" required>
                              </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Send OTP</button>
                     </form>
                  </div>
            </div>
         </div>
      </div>
      <!-- Modal for Changing Contact Number -->
    <div class="modal fade" id="changeContactNumberModal" tabindex="-1" role="dialog" aria-labelledby="changeContactNumberModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="change_contact_number.php" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="changeContactNumberModalLabel">Change Contact Number</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- New Contact Number Input -->
                        <div class="form-group">
                            <label for="new_contact_number">New Contact Number</label>
                            <div class="input-group input-group-outline my-3">
                                <input type="text" class="form-control" id="new_contact_number" name="new_contact_number" pattern="[0-9]{10}" title="Please enter a 10-digit contact number." required>
                            </div>
                            <small class="form-text text-muted">Enter a valid 10-digit contact number.</small>
                        </div>
                        <!-- Optional: Current Password for Verification -->
                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <div class="input-group input-group-outline my-3">
                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                            </div>
                            <small class="form-text text-muted">Enter your current password to confirm changes.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Contact Number</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


      <!-- Modal for changing password -->
      <div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
         <div class="modal-dialog" role="document">
            <div class="modal-content">
                  <div class="modal-header">
                     <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                     </button>
                  </div>
                  <div class="modal-body">
                     <form action="change_password.php" method="post">
                        <div class="form-group">
                              <label for="new_password">New Password</label>
                              <div class="input-group input-group-outline my-3">
                              <input type="password" class="form-control" id="new_password" name="new_password" required>
                              </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Send OTP</button>
                     </form>
                  </div>
            </div>
         </div>
      </div>
        <script>
        
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