<?php

error_log('Booking Type: ' . $bookingType);
error_log('Number of Matches: ' . $numMatches);
error_log('Number of Players: ' . $numPlayers);
session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    // Redirect to login page if session variables are not set
    header("Location: index.php");
    exit();
}
$username = htmlspecialchars($_SESSION['username']);
$user_id = htmlspecialchars($_SESSION['user_id']);

if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']); // Clear error after displaying
}

if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']); // Clear success message after displaying
}

// Output JSON encoded user data for JavaScript to use
echo "<script>
        const userData = {
            username: '{$username}',
            user_id: '{$user_id}'
        };
      </script>";

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
               <a class="nav-link text-white " href="feedback.php">
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
                     <li class="nav-item px-3 d-flex align-items-center">
                        <a href="javascript:;" class="nav-link text-body p-0">
                        <i class="fa fa-cog fixed-plugin-button-nav cursor-pointer"></i>
                        </a>
                     </li>
                     <li class="nav-item dropdown pe-2 d-flex align-items-center">
                        <a href="javascript:;" class="nav-link text-body p-0" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-bell cursor-pointer"></i>
                        </a>
                        <ul class="dropdown-menu  dropdown-menu-end  px-2 py-3 me-sm-n4" aria-labelledby="dropdownMenuButton">
                           <li class="mb-2">
                              <a class="dropdown-item border-radius-md" href="javascript:;">
                                 <div class="d-flex py-1">
                                    <div class="my-auto">
                                       <img src="./assets/img/team-2.jpg" class="avatar avatar-sm  me-3 ">
                                    </div>
                                    <div class="d-flex flex-column justify-content-center">
                                       <h6 class="text-sm font-weight-normal mb-1">
                                          <span class="font-weight-bold">New message</span> from Laur
                                       </h6>
                                       <p class="text-xs text-secondary mb-0">
                                          <i class="fa fa-clock me-1"></i>
                                          13 minutes ago
                                       </p>
                                    </div>
                                 </div>
                              </a>
                           </li>
                           <li class="mb-2">
                              <a class="dropdown-item border-radius-md" href="javascript:;">
                                 <div class="d-flex py-1">
                                    <div class="my-auto">
                                       <img src="./assets/img/small-logos/logo-spotify.svg" class="avatar avatar-sm bg-gradient-dark  me-3 ">
                                    </div>
                                    <div class="d-flex flex-column justify-content-center">
                                       <h6 class="text-sm font-weight-normal mb-1">
                                          <span class="font-weight-bold">New album</span> by Travis Scott
                                       </h6>
                                       <p class="text-xs text-secondary mb-0">
                                          <i class="fa fa-clock me-1"></i>
                                          1 day
                                       </p>
                                    </div>
                                 </div>
                              </a>
                           </li>
                           <li>
                              <a class="dropdown-item border-radius-md" href="javascript:;">
                                 <div class="d-flex py-1">
                                    <div class="avatar avatar-sm bg-gradient-secondary  me-3  my-auto">
                                       <svg width="12px" height="12px" viewBox="0 0 43 36" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                          <title>credit-card</title>
                                          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                             <g transform="translate(-2169.000000, -745.000000)" fill="#FFFFFF" fill-rule="nonzero">
                                                <g transform="translate(1716.000000, 291.000000)">
                                                   <g transform="translate(453.000000, 454.000000)">
                                                      <path class="color-background" d="M43,10.7482083 L43,3.58333333 C43,1.60354167 41.3964583,0 39.4166667,0 L3.58333333,0 C1.60354167,0 0,1.60354167 0,3.58333333 L0,10.7482083 L43,10.7482083 Z" opacity="0.593633743"></path>
                                                      <path class="color-background" d="M0,16.125 L0,32.25 C0,34.2297917 1.60354167,35.8333333 3.58333333,35.8333333 L39.4166667,35.8333333 C41.3964583,35.8333333 43,34.2297917 43,32.25 L43,16.125 L0,16.125 Z M19.7083333,26.875 L7.16666667,26.875 L7.16666667,23.2916667 L19.7083333,23.2916667 L19.7083333,26.875 Z M35.8333333,26.875 L28.6666667,26.875 L28.6666667,23.2916667 L35.8333333,23.2916667 L35.8333333,26.875 Z"></path>
                                                   </g>
                                                </g>
                                             </g>
                                          </g>
                                       </svg>
                                    </div>
                                    <div class="d-flex flex-column justify-content-center">
                                       <h6 class="text-sm font-weight-normal mb-1">
                                          Payment successfully completed
                                       </h6>
                                       <p class="text-xs text-secondary mb-0">
                                          <i class="fa fa-clock me-1"></i>
                                          2 days
                                       </p>
                                    </div>
                                 </div>
                              </a>
                           </li>
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
         <!-- Content Row -->
         <div class="card shadow mb-4">
               <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-primary">Billiard Table</h6>
                     </div>
                        <div class="card-body">
                           <div class="row">
                              <?php
                                 include 'conn.php';

                                 $sqlTables = "SELECT table_number, status, table_id FROM tables";
                                 $stmtTables = $conn->prepare($sqlTables);
                                 $stmtTables->execute();
                                 $tables = $stmtTables->fetchAll(PDO::FETCH_ASSOC);
                                 ?>

                                 <div class="container">
                                    <div class="row">
                                       <?php
                                       if (!empty($tables)) {
                                             foreach ($tables as $row) {
                                                echo '<div class="col-md-3 mb-4">';
                                                echo '<div class="card">';
                                                echo '<img class="card-img-top" src="./img/billiardtable.png" alt="Card image cap">';
                                                echo '<div class="card-body">';
                                                echo '<h5 class="card-title">'. htmlspecialchars($row["table_number"]) . '</h5>';
                                                echo '<p class="card-text">Status: ' . htmlspecialchars($row["status"]) . '</p>';
                                                echo '<div class="btn-group">';
                                                echo '<button type="button" class="btn btn-primary" onclick=\'openBookingModal('. json_encode($row) .')\'>Book</button>';
                                                echo '</div>';
                                                echo '</div>';
                                                echo '</div>';
                                                echo '</div>';
                                             }
                                       } else {
                                             echo "0 results";
                                       }
                                       ?>
                                    </div>
                                 </div>
                              </div>
                           </div>
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
      <!-- Booking Modal -->
        <div class="modal fade" id="bookingModal" tabindex="-1" role="dialog" aria-labelledby="bookingModalLabel" aria-hidden="true">
           <div class="modal-dialog" role="document">
              <div class="modal-content">
                 <div class="modal-header">
                    <h5 class="modal-title" id="bookingModalLabel">Book Billiard Table</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                       <span aria-hidden="true">×</span>
                    </button>
                 </div>
                 <div class="modal-body">
                    <form method="POST" action="bookTable.php" enctype="multipart/form-data">
                       <input type="hidden" id="bookingTableId" name="table_id">
                       <input type="hidden" id="bookingTableName" name="table_name">
                       <input type="hidden" id="userId" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
                       <input type="hidden" id="amount" name="amount">
        
                       <label for="username">User</label>
                       <div class="input-group input-group-outline my-3">
                          <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" readonly>
                       </div>
        
                       <label for="bookingType">Booking Type</label>
                       <div class="input-group input-group-outline my-3">
                          <select name="booking_type" id="bookingType" class="form-control" onchange="toggleBookingType()">
                             <option value="hour">Per Hour</option>
                             <option value="match">Per Match</option>
                          </select>
                       </div>
        
                       <!-- Number of Players Dropdown -->
                       <label for="numPlayers">Number of Players</label>
                       <div class="input-group input-group-outline my-3">
                          <input type="number" name="num_players" id="numPlayers" class="form-control" required>
                       </div>
        
                       <!-- Per Hour Fields -->
                       <div id="perHourFields">
                          <label for="startTime">Start Time</label>
                          <div class="input-group input-group-outline my-3">
                             <input type="datetime-local" id="startTime" name="start_time" class="form-control" onchange="calculateAmount()" />
                          </div>
                          <label for="endTime">End Time</label>
                          <div class="input-group input-group-outline my-3">
                             <input type="datetime-local" id="endTime" name="end_time" class="form-control" onchange="calculateAmount()" />
                          </div>
                       </div>
        
                       <!-- Per Match Fields -->
                       <div id="perMatchFields" style="display: none;">
                          <label for="numMatches">Number of Matches</label>
                          <div class="input-group input-group-outline my-3">
                             <input type="number" id="numMatches" name="num_matches" class="form-control" onchange="calculateAmount()">
                          </div>
                       </div>
        
                       <label for="paymentMethod">Payment Method</label>
                        <div class="input-group input-group-outline my-3">
                           <select name="payment_method" id="paymentMethod" class="form-control" onchange="togglePaymentFields()">
                              <option value="cash">Cash</option>
                              <option value="gcash">GCash</option>
                              <option value="paypal">PayPal</option> <!-- Added PayPal Option -->
                           </select>
                        </div>

                        
                        <!-- GCash Payment Notice -->
                        <div id="gcashFields" style="display: none;">
                           <p>You will be redirected to PayMongo to complete your payment.</p>
                        </div>

                       <label for="totalAmount">Total Amount</label>
                       <div class="input-group input-group-outline my-3">
                          <input type="text" id="totalAmount" class="form-control" readonly>
                       </div>
        
                       <div class="modal-footer">
                          <button type="submit" name="save" class="btn btn-primary">Book & Pay</button>
                          <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
                       </div>
                    </form>
                 </div>
              </div>
           </div>
        </div>


      <script>
         // Initialize any required plugins or functionalities
         var win = navigator.platform.indexOf('Win') > -1;
         if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
               damping: '0.5'
            }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
         }
         
         // Toggle Payment Fields
        function togglePaymentFields() {
           var paymentMethod = document.getElementById('paymentMethod').value;
           var gcashFields = document.getElementById('gcashFields');
           var paypalFields = document.getElementById('paypalFields'); // New PayPal Fields
        
           if (paymentMethod === 'gcash') {
              gcashFields.style.display = 'block';
           } else {
              gcashFields.style.display = 'none';
           }
        
           if (paymentMethod === 'paypal') {
              paypalFields.style.display = 'block';
           } else {
              paypalFields.style.display = 'none';
           }
        }


         // Toggle booking type fields
         
   // Toggle booking type fields
       function toggleBookingType() {
            var bookingType = document.getElementById('bookingType').value;
            var perHourFields = document.getElementById('perHourFields');
            var perMatchFields = document.getElementById('perMatchFields');
            var startTime = document.getElementById('startTime');
            var endTime = document.getElementById('endTime');
            var numMatches = document.getElementById('numMatches');
        
            if (bookingType === 'hour') {
                // Show hour-based fields, hide match-based fields
                perHourFields.style.display = 'block';
                perMatchFields.style.display = 'none';
        
                // Set required attributes for hour-based booking
                startTime.setAttribute('required', 'required');
                endTime.setAttribute('required', 'required');
        
                // Enable the time fields
                startTime.removeAttribute('disabled');
                endTime.removeAttribute('disabled');
        
                // Disable and remove required attribute for numMatches
                numMatches.removeAttribute('required');
                numMatches.setAttribute('disabled', 'disabled');
                numMatches.value = ''; // Clear the numMatches value
            } else if (bookingType === 'match') {
                // Hide hour-based fields, show match-based fields
                perHourFields.style.display = 'none';
                perMatchFields.style.display = 'block';
        
                // Remove required and disable the time fields
                startTime.removeAttribute('required');
                endTime.removeAttribute('required');
                startTime.setAttribute('disabled', 'disabled');
                endTime.setAttribute('disabled', 'disabled');
                startTime.value = ''; // Clear the startTime value
                endTime.value = ''; // Clear the endTime value
        
                // Set required attribute for numMatches and enable it
                numMatches.setAttribute('required', 'required');
                numMatches.removeAttribute('disabled');
            }
        
            // Reset fields and recalculate the amount when switching booking types
            resetFields(bookingType);
        }
        
        function resetFields(bookingType) {
            if (bookingType === 'hour') {
                // Clear match-related field
                document.getElementById('numMatches').value = '';
            } else {
                // Clear time-related fields
                document.getElementById('startTime').value = '';
                document.getElementById('endTime').value = '';
            }
            calculateAmount(); // Recalculate the amount after resetting fields
        }
        
        function calculateAmount() {
            var bookingType = document.getElementById('bookingType').value;
            var amount = 0;
            if (bookingType === 'hour') {
                var startTime = document.getElementById('startTime').value;
                var endTime = document.getElementById('endTime').value;
                if (startTime && endTime) {
                    var start = new Date(startTime);
                    var end = new Date(endTime);
                    var diff = (end - start) / (1000 * 60 * 60); // Difference in hours
                    if (diff > 0) {
                        amount = diff * 100; // Assuming 100 currency units per hour
                    } else {
                        alert("End time must be after the start time.");
                        return;
                    }
                } else {
                    alert("Please select valid start and end times.");
                    return;
                }
            } else if (bookingType === 'match') {
                var numMatches = document.getElementById('numMatches').value;
                if (numMatches && numMatches > 0) {
                    amount = numMatches * 20; // Assuming 20 currency units per match
                } else {
                    alert("Please enter a valid number of matches.");
                    return;
                }
            }
        
            document.getElementById('totalAmount').value = amount.toFixed(2);
            document.getElementById('amount').value = amount.toFixed(2);
        }
         // Open booking modal with pre-filled data
         function openBookingModal(table) {
            document.getElementById('bookingTableId').value = table.table_id;
            document.getElementById('bookingTableName').value = table.table_number;
            document.getElementById('username').value = userData.username;
            document.getElementById('userId').value = userData.user_id;
            document.getElementById('totalAmount').value = ''; 
            $('#bookingModal').modal('show');
         }

         // Toggle GCash payment fields
         function toggleGcashFields() {
            var paymentMethod = document.getElementById('paymentMethod').value;
            var gcashFields = document.getElementById('gcashFields');
            if (paymentMethod === 'gcash') {
               gcashFields.style.display = 'block';
            } else {
               gcashFields.style.display = 'none';
            }
         }

         // Initialize booking type on modal show
         $('#bookingModal').on('show.bs.modal', function (e) {
            // Optionally, set the default booking type here
            toggleBookingType();
         });



      </script>

      <!-- jQuery -->
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
      <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

      <!-- Bootstrap core JavaScript-->
      <script src="vendor/jquery/jquery.min.js"></script>
      <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

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