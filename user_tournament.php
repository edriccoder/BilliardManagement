<?php
session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    // Redirect to login page if session variables are not set
    header("Location: index.php");
    exit();
}
$username = htmlspecialchars($_SESSION['username']);
$user_id = htmlspecialchars($_SESSION['user_id']);

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
      <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">

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
         <div class="container-fluid">
         <!-- Page Heading -->      
         <!-- Table Row -->
         <?php
            include 'conn.php';

            // Fetch all tournaments
            $sqlTournaments = "SELECT * FROM tournaments";
            $stmtTournaments = $conn->prepare($sqlTournaments);
            $stmtTournaments->execute();
            $tournaments = $stmtTournaments->fetchAll(PDO::FETCH_ASSOC);

            $sqlJoinTournaments = "SELECT players.*, tournaments.name AS tournament_name 
                       FROM players 
                       INNER JOIN tournaments ON players.tournament_id = tournaments.tournament_id
                       WHERE players.user_id = :user_id";
            $stmtJoinTournaments = $conn->prepare($sqlJoinTournaments);
            $stmtJoinTournaments->execute(['user_id' => $user_id]);
            $joinedTournaments = $stmtJoinTournaments->fetchAll(PDO::FETCH_ASSOC);
         ?>
         <div class="row">
            <div class="col-lg-8">
               <div class="card">
                     <div class="card-header pb-0 px-3">
                        <h6 class="mb-0">Tournament</h6>
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
                                             <span class="mb-2 text-xs">Fee: <span class="text-dark font-weight-bold ms-sm-2"><?php echo htmlspecialchars($tournament['fee']); ?></span></span>
                                             <span class="mb-2 text-xs">Prize: <span class="text-dark font-weight-bold ms-sm-2"><?php echo htmlspecialchars($tournament['prize']); ?></span></span>
                                             <span class="text-xs">Status: <span class="text-dark font-weight-bold ms-sm-2"><?php echo htmlspecialchars($tournament['status']); ?></span></span>
                                       </div>
                                       <div class="ms-auto text-end">
                                             <!-- Inside Tournament List Item -->
                                                <button type="button" 
                                                        class="btn btn-primary join-tournament" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#joinTournamentModal" 
                                                        data-tournament-id="<?php echo htmlspecialchars($tournament['tournament_id']); ?>"
                                                        data-total-amount="<?php echo htmlspecialchars($tournament['fee']); ?>">
                                                    Join Tournament
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
                              <h6 class="mb-0">Joined Tournaments</h6>
                           </div>
                           <div class="col-6 text-end">
                           </div>
                        </div>
                  </div>
                  <div class="card-body p-3 pb-0">
                        <ul class="list-group">
                           <?php
                           if (!empty($joinedTournaments)) {
                              foreach ($joinedTournaments as $tournament) {
                           ?>
                                    <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                                       <div class="d-flex flex-column">
                                          <h6 class="mb-1 text-dark font-weight-bold text-sm"><?php echo htmlspecialchars($tournament['username']); ?></h6>
                                          <span class="text-xs"><?php echo htmlspecialchars($tournament['tournament_name']); ?></span>
                                       </div>
                                       <div class="d-flex align-items-center text-sm">
                                          Status: <?php echo htmlspecialchars($tournament['status']); ?>
                                          <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4 show-bracket" data-toggle="modal" data-target="#bracketModal" data-tournament-id="<?php echo htmlspecialchars($tournament['tournament_id']); ?>"><i class="material-icons text-lg position-relative me-1"></i> Show Bracket</button>
                                          <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4 show-scores" 
                                                 data-toggle="modal" 
                                                 data-target="#showScores" 
                                                 data-tournament-id="<?php echo htmlspecialchars($tournament['tournament_id']); ?>">
                                              <i class="material-icons text-lg position-relative me-1"></i> Show Scores
                                        </button>
                                       </div>
                                    </li>
                           <?php
                              }
                           } else {
                           ?>
                              <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                                    <div class="d-flex flex-column">
                                       <h6 class="text-dark mb-1 font-weight-bold text-sm">No tournaments joined yet.</h6>
                                    </div>
                              </li>
                           <?php
                           }
                           ?>
                        </ul>
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
      <!-- Modal -->
    <!-- Join Tournament Modal -->
        <div class="modal fade" id="joinTournamentModal" tabindex="-1" aria-labelledby="joinTournamentModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="joinTournamentModalLabel">Join Tournament</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="join_tournament.php" enctype="multipart/form-data" id="joinTournamentForm">
                            <!-- Payment Option Selection -->
                            <label class="form-label">Choose Payment Method</label>
                            <div class="input-group input-group-outline my-3">
                                <select class="form-select" id="paymentMethod" name="paymentMethod" required>
                                    <option value="gcash">GCash</option>
                                </select>
                            </div>

                            <!-- Total Amount -->
                            <input type="hidden" id="tournament_id" name="tournament_id">
                            <label for="fee" class="form-label">Total Amount</label>
                            <div class="input-group input-group-outline my-3">
                                <input type="text" id="fee" name="totalAmount" class="form-control" readonly>
                            </div>

                            <!-- Payment Containers -->
                            <div id="gcashPayment" class="d-block">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>

                            <div id="paypalButtonContainer" class="d-none">
                                <div id="paypal-button"></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>



      <!-- Modal for showing bracket -->
      <div class="modal fade" id="bracketModal" tabindex="-1" aria-labelledby="bracketModalLabel" aria-hidden="true">
         <div class="modal-dialog modal-lg">
            <div class="modal-content">
                  <div class="modal-header">
                     <h5 class="modal-title" id="bracketModalLabel">Single Elimination Tournament Bracket</h5>
                     <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                  </div>
                     <div class="bracket" id="bracketContainer">
                        <!-- Bracket content will be loaded here -->
                     </div>
                  <div class="modal-footer">
                     <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  </div>
            </div>
         </div>
      </div>
      <!-- Scores Modal -->
    <div class="modal fade" id="showScores" tabindex="-1" role="dialog" aria-labelledby="showScoresLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="showScoresLabel">Tournament Scores</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table">
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = { damping: '0.5' }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }

        let currentTournamentId = null;
        let finalRound = 0;
        const showScheduleButtons = document.querySelectorAll('.show-schedule');
        
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
                        const scheduleModal = new bootstrap.Modal(document.getElementById('scheduleModal'));
                        scheduleModal.show();
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
        
        // Add the date and time formatting function
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

        // Handle Join Tournament Buttons
        document.querySelectorAll('.join-tournament').forEach(button => {
            button.addEventListener('click', function () {
                const tournamentId = this.getAttribute('data-tournament-id');
                const totalAmount = parseFloat(this.getAttribute('data-total-amount'));
        
                // Validate the total amount before proceeding
                if (totalAmount <= 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Fee',
                        text: 'The tournament fee must be greater than zero. Please contact support.',
                    });
                    return; // Exit if the fee is invalid
                }
        
                // Set the tournament_id and fee in the modal form
                document.getElementById('tournament_id').value = tournamentId;
                document.getElementById('fee').value = `PHP ${totalAmount.toFixed(2)}`;
        
                // Reset payment method selection
                document.getElementById('paymentMethod').value = 'gcash';
                document.getElementById('gcashPayment').classList.remove('d-none');
                document.getElementById('paypalButtonContainer').classList.add('d-none');
            });
        });

                // Handle Payment Method Change
                document.getElementById('paymentMethod').addEventListener('change', function () {
                    if (this.value === 'paypal') {
                        document.getElementById('gcashPayment').classList.add('d-none');
                        document.getElementById('paypalButtonContainer').classList.remove('d-none');
                        // Initialize PayPal button if using PayPal SDK
                    } else {
                        document.getElementById('gcashPayment').classList.remove('d-none');
                        document.getElementById('paypalButtonContainer').classList.add('d-none');
                    }
                });


        // Handle Show Bracket Buttons
        document.querySelectorAll('.show-bracket').forEach(button => {
            button.addEventListener('click', function () {
                currentTournamentId = this.getAttribute('data-tournament-id');
                renderBracket(currentTournamentId);
            });
        });

        // Handle Notifications
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

        // Handle Show Scores Buttons
        document.querySelectorAll('.show-scores').forEach(button => {
            button.addEventListener('click', function () {
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

                        const scoresModal = new bootstrap.Modal(document.getElementById('showScores'));
                        scoresModal.show();
                    })
                    .catch(error => {
                        console.error('Error fetching scores:', error);
                    });
            });
        });

        // Function to Render the Bracket with Scores
        function renderBracket(tournamentId, showModal = true) {
            if (!tournamentId) {
                alert('Tournament ID is missing.');
                return;
            }

            // Fetch Bracket and Scores Data in Parallel
            Promise.all([
                fetch(`get_bracket.php?tournament_id=${tournamentId}`).then(response => response.json()),
                fetch(`fetch_scores.php?tournament_id=${tournamentId}`).then(response => response.json())
            ])
            .then(([bracketData, scoresData]) => {
                const bracketContainer = document.getElementById('bracketContainer');
                bracketContainer.innerHTML = '';

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
                                <div class="team ${winnerId == match.player1_id ? 'selected' : ''} ${winnerId && winnerId != match.player1_id ? 'eliminated' : ''}" data-player-id="${match.player1_id || ''}" data-toggle="tooltip" title="Click to view scores">
                                    ${team1Name} <span class="score">(${player1Score})</span>
                                </div>
                                <div class="team ${winnerId == match.player2_id ? 'selected' : ''} ${winnerId && winnerId != match.player2_id ? 'eliminated' : ''}" data-player-id="${match.player2_id || ''}" data-toggle="tooltip" title="Click to view scores">
                                    ${team2Name} <span class="score">(${player2Score})</span>
                                </div>
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
                    $('[data-toggle="tooltip"]').tooltip();

                    if (showModal) {
                        const bracketModal = new bootstrap.Modal(document.getElementById('bracketModal'));
                        bracketModal.show();
                    }
                } else {
                    alert(bracketData.message);
                }
            })
            .catch(error => {
                console.error('Error fetching bracket or scores:', error);
            });
        }

        // Event Listener for Selecting Winner in the Bracket
        document.getElementById('bracketContainer').addEventListener('click', function (event) {
            if (event.target.classList.contains('win-btn')) {
                const round = parseInt(event.target.getAttribute('data-round'));
                const matchNumber = parseInt(event.target.getAttribute('data-match'));

                if (event.target.hasAttribute('disabled')) {
                    return;
                }

                const selectedTeam = event.target.parentElement.querySelector('.team.selected');

                if (selectedTeam) {
                    const winnerId = selectedTeam.getAttribute('data-player-id');
                    const winnerName = selectedTeam.textContent.split(' (')[0]; // Extract name without score

                    fetch(`update_bracket.php`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: new URLSearchParams({
                            tournament_id: currentTournamentId,
                            round: round,
                            match: matchNumber,
                            winner_id: winnerId,
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Disable the win button
                            event.target.setAttribute('disabled', 'disabled');

                            // Update UI to reflect the winner
                            selectedTeam.classList.add('selected');
                            event.target.parentElement.querySelectorAll('.team').forEach(team => {
                                if (team !== selectedTeam) {
                                    team.classList.add('eliminated');
                                }
                            });

                            console.log('Winner updated successfully');

                            // Move winner to the next round
                            moveWinnerToNextRound(selectedTeam, round, matchNumber);

                            // Re-render the bracket without closing the modal
                            renderBracket(currentTournamentId, false);

                            // If it's the final round, announce the winner
                            if (round === finalRound) {
                                announceWinner(winnerName, currentTournamentId, round);
                            }
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error updating winner:', error);
                    });
                } else {
                    alert('Please select a winner.');
                }
            } else if (event.target.classList.contains('team')) {
                if (event.target.parentElement.querySelector('.win-btn').hasAttribute('disabled')) {
                    return; // Do not allow selection if match is already decided
                }
                event.target.parentElement.querySelectorAll('.team').forEach(team => team.classList.remove('selected'));
                event.target.classList.add('selected');
            }
        });

        // Function to Announce the Winner
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
                    alert(`Congratulations to ${winnerName} for winning the tournament!`);
                } else {
                    console.error('Failed to announce winner:', data.message);
                }
            })
            .catch(error => {
                console.error('Error announcing winner:', error);
            });
        }

        $('[data-toggle="tooltip"]').tooltip();
    });
</script>

      <style>
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
}

    </style>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
      <!-- Bootstrap 5 JS Bundle (includes Popper.js) -->
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
      <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.min.js"></script>
      <!-- Bootstrap core JavaScript-->
      <script src="vendor/jquery/jquery.min.js"></script>
      <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
      <!-- jQuery -->
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
      <!-- Bootstrap JS -->


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