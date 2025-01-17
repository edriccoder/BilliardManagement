<?php
session_start();
include 'conn.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Sanitize user ID
$user_id = htmlspecialchars($_SESSION['user_id']);

// Fetch users (if needed for additional functionality)
$sqlUsers = "SELECT user_id, username FROM users";
$stmtUsers = $conn->prepare($sqlUsers);
$stmtUsers->execute();
$users = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

// Fetch notifications
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
    exit();
}

// Create user map
$userMap = [];
foreach ($users as $user) {
    $userMap[$user['user_id']] = $user['username'];
}
?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <link rel="apple-touch-icon" sizes="76x76" href="./assets/img/apple-icon.png">
      <link rel="icon" type="image/png" href="./assets/img/favicon.png">
      <title>Billiard Management</title>
      <!-- Fonts and icons -->
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
      <!-- Nepcha Analytics (nepcha.com) -->
      <script defer data-site="YOUR_DOMAIN_HERE" src="https://api.nepcha.com/js/nepcha-analytics.js"></script>
      <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
      <link
         href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
         rel="stylesheet">
      <!-- FullCalendar CSS -->
      <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
      <!-- Custom styles -->
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
      </style>
   </head>
   <body class="g-sidenav-show bg-gray-100">
      <!-- Sidebar -->
      <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 bg-gradient-dark" id="sidenav-main">
         <div class="sidenav-header">
            <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
            <a class="navbar-brand m-0" href="#" target="_blank">
            <img src="./img/admin.png" class="navbar-brand-img h-100" alt="main_logo">
            <span class="ms-1 font-weight-bold text-white"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
            </a>
         </div>
         <hr class="horizontal light mt-0 mb-2">
         <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
            <ul class="navbar-nav">
               <li class="nav-item">
                  <a class="nav-link text-white" href="user_profile.php">
                     <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">person</i>
                     </div>
                     <span class="nav-link-text ms-1">My Profile</span>
                  </a>
               </li>
               <li class="nav-item">
                  <a class="nav-link text-white" href="user_dashboard.php">
                     <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">dashboard</i>
                     </div>
                     <span class="nav-link-text ms-1">My Dashboard</span>
                  </a>
               </li>
               <li class="nav-item">
                  <a class="nav-link text-white" href="user_table.php">
                     <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">table_view</i>
                     </div>
                     <span class="nav-link-text ms-1">Billiard Tables</span>
                  </a>
               </li>
               <li class="nav-item">
                  <a class="nav-link text-white" href="booking_user.php">
                     <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">book</i>
                     </div>
                     <span class="nav-link-text ms-1">My Booking</span>
                  </a>
               </li>
               <li class="nav-item">
                  <a class="nav-link text-white" href="user_tournament.php">
                     <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">flag</i>
                     </div>
                     <span class="nav-link-text ms-1">My Tournament</span>
                  </a>
               </li> 
               <li class="nav-item">
                  <a class="nav-link text-white" href="user_feedback.php">
                     <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">feedback</i>
                     </div>
                     <span class="nav-link-text ms-1">My Feedback</span>
                  </a>
               </li>
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
                        <a href="javascript:;" class="nav-link text-body p-0" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-bell cursor-pointer"></i>
                            <?php if ($unreadCount > 0): ?>
                                <span class="badge bg-danger text-white position-absolute top-0 start-100 translate-middle p-1 rounded-circle" style="font-size: 0.75rem;">
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
             <!-- Booking Information -->
                   <!-- Calendar Section -->
                   <div class="card mt-4">
                      <div class="card-header pb-0 px-3">
                         <h6 class="mb-0">Booking Calendar</h6>
                      </div>
                      <div class="card-body pt-4 p-3">
                          <div id='bookingCalendar'></div>
                      </div>
                   </div>
               </div>
             </div>
             
             <!-- Image Modal -->
             <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                   <div class="modal-content">
                      <div class="modal-header">
                      <h5 class="modal-title" id="imageModalLabel">Proof of Payment</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body text-center">
                      <img id="imageModalContent" src="" alt="Proof of Payment" style="max-width: 100%; max-height: 500px;">
                      </div>
                   </div>
                </div>
             </div>
             
             <!-- Edit Booking Modal -->
            <div class="modal fade" id="bookingModal" tabindex="-1" role="dialog" aria-labelledby="bookingModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="bookingModalLabel">Manage Booking</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="edit_booking.php">
                                <input type="hidden" id="bookingId" name="booking_id">
                                <input type="hidden" id="userId" name="user_id">
                                <label for="username">User</label>
                                <div class="input-group input-group-outline my-3">
                                    <input type="text" class="form-control" id="username" name="username" value="" readonly>
                                </div>
                                <label>Start Time</label>
                                <div class="input-group input-group-outline my-3">
                                    <input type="datetime-local" name="start_time" id="editStartTime" class="form-control" required>
                                </div>
                                <label>End Time</label>
                                <div class="input-group input-group-outline my-3">
                                    <input type="datetime-local" name="end_time" id="editEndTime" class="form-control" required>
                                </div>
                                <label>Number of Players</label>
                                <div class="input-group input-group-outline my-3">
                                    <input type="number" name="num_players" id="editNumPlayers" class="form-control" required>
                                </div>
                                <label>Amount</label>
                                <div class="input-group input-group-outline my-3">
                                    <input type="text" name="amount" id="editAmount" class="form-control" readonly>
                                </div>
                                <label>Payment Method</label>
                                <div class="input-group input-group-outline my-3">
                                    <input type="text" name="payment_method" id="editPaymentMethod" class="form-control" readonly>
                                </div>
                                <!-- Receipt Button -->
                                <div class="input-group input-group-outline my-3">
                                    <button type="button" id="showReceiptButton" class="btn btn-info w-100">Show Receipt</button>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" name="confirm" class="btn btn-primary">Confirm Edit</button>
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
      
      <!-- Fixed Plugin -->
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
                // Initialize Scrollbar if applicable
                var win = navigator.platform.indexOf('Win') > -1;
                if (win && document.querySelector('#sidenav-scrollbar')) {
                    var options = {
                        damping: '0.5'
                    }
                    Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
                }

                const userData = <?php echo json_encode($userMap); ?>;
                const currentUserId = <?php echo json_encode($user_id); ?>; // Define currentUserId

                document.addEventListener('DOMContentLoaded', function() {
                    var calendarEl = document.getElementById('bookingCalendar');

                    var calendar = new FullCalendar.Calendar(calendarEl, {
                        initialView: 'timeGridWeek',
                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'timeGridWeek,timeGridDay,dayGridMonth'
                        },
                        events: 'get_bookings.php',  // Loads all bookings from a separate PHP file
                        eventClick: function(info) {
                            var booking = info.event.extendedProps;
                            if (booking.user_id == currentUserId) {
                                openEditModal({
                                    booking_id: info.event.id,
                                    username: info.event.title.split(' - ')[0],  // Get username from title
                                    table_name: info.event.title.split(' - ')[1],
                                    start_time: formatDateTime(info.event.start),
                                    end_time: formatDateTime(info.event.end),
                                    status: booking.status,
                                    num_players: booking.num_players,
                                    amount: booking.amount,
                                    payment_method: booking.payment_method,
                                    proof_of_payment: booking.proof_of_payment
                                });
                            } else {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Unauthorized',
                                    text: 'You are not authorized to edit this booking.'
                                });
                            }
                        },
                        height: 'auto',
                    });

                    calendar.render();
                });

                function formatDateTime(date) {
                    if (!date) return '';
                    var year = date.getFullYear();
                    var month = ('0' + (date.getMonth() + 1)).slice(-2);
                    var day = ('0' + date.getDate()).slice(-2);
                    var hours = ('0' + date.getHours()).slice(-2);
                    var minutes = ('0' + date.getMinutes()).slice(-2);
                    return `${year}-${month}-${day}T${hours}:${minutes}`;
                }

                function openEditModal(booking) {
                    document.getElementById('bookingId').value = booking.booking_id;
                    document.getElementById('userId').value = booking.user_id;
                    document.getElementById('username').value = booking.username || userData[booking.user_id];
                    document.getElementById('editStartTime').value = booking.start_time;
                    document.getElementById('editEndTime').value = booking.end_time;
                    document.getElementById('editAmount').value = booking.amount || '';
                    document.getElementById('editPaymentMethod').value = booking.payment_method || '';
                    document.getElementById('editNumPlayers').value = booking.num_players || '';

                    // Store booking ID for receipt generation
                    document.getElementById('showReceiptButton').setAttribute('data-booking-id', booking.booking_id);

                    var bookingModal = new bootstrap.Modal(document.getElementById('bookingModal'));
                    bookingModal.show();
                }

                // Handle "Show Receipt" button click
                document.getElementById('showReceiptButton').addEventListener('click', function () {
                    var bookingId = this.getAttribute('data-booking-id');
                    if (bookingId) {
                        // Open the receipt in a new tab
                        window.open('generate_receipt.php?booking_id=' + bookingId, '_blank');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Booking ID not found.'
                        });
                    }
                });

                function openImageModal(imageUrl) {
                    var imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
                    document.getElementById('imageModalContent').src = imageUrl;
                    imageModal.show();
                }

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
                            })
                            .catch(error => console.error('Error:', error));
                        });
                    });
                });
            </script>
      
      <!-- Include necessary JS libraries -->
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
