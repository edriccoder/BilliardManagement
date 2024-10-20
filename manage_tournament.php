<?php
   include 'conn.php';
   $sqlTournaments = "SELECT * FROM tournaments";
   $stmtTournaments = $conn->prepare($sqlTournaments);
   $stmtTournaments->execute();
   $tournaments = $stmtTournaments->fetchAll(PDO::FETCH_ASSOC);

   if (isset($_GET['error'])) {
      $error_message = htmlspecialchars($_GET['error']);
      echo "<script>";
      echo "alert('$error_message');";
      echo "</script>";
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

    </style>

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
                     <span class="nav-link-text ms-1">Manage User and Cashier</span>
                  </a>
               </li> 
               <li class="nav-item">
                  <a class="nav-link text-white " href="manage_tournament.php">
                     <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">flag</i>
                     </div>
                     <span class="nav-link-text ms-1">Manage Tournament</span>
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
               <a class="nav-link text-white " href="admin_booking.php">
                  <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                     <i class="material-icons opacity-10">book</i>
                  </div>
                  <span class="nav-link-text ms-1">Booking</span>
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
            <li class="nav-item">
               <a class="nav-link text-white " href="./notifications.html">
                  <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                     <i class="material-icons opacity-10">notifications</i>
                  </div>
                  <span class="nav-link-text ms-1">Notifications</span>
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
         <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Add Tournament</h1>
            <button class='btn btn-primary' data-toggle='modal' data-target='#addTournamentModal'>Add Tournament</button>
         </div>
         <!-- Table Row -->
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
                              <!-- Formatting start_date and end_date to show both date and time -->
                              <span class="mb-2 text-xs">Start Date & Time: <span class="text-dark font-weight-bold ms-sm-2">
                                 <?php echo htmlspecialchars(date("F j, Y, g:i A", strtotime($tournament['start_date']))); ?>
                              </span></span>
                              <span class="mb-2 text-xs">End Date & Time: <span class="text-dark font-weight-bold ms-sm-2">
                                 <?php echo htmlspecialchars(date("F j, Y, g:i A", strtotime($tournament['end_date']))); ?>
                              </span></span>
                              <span class="mb-2 text-xs">Max Players: <span class="text-dark font-weight-bold ms-sm-2"><?php echo htmlspecialchars($tournament['max_player']); ?></span></span>
                              <span class="mb-2 text-xs">Prizes: <span class="text-dark font-weight-bold ms-sm-2"><?php echo htmlspecialchars($tournament['prize']); ?></span></span>
                              <span class="mb-2 text-xs">Tournament Fee: <span class="text-dark font-weight-bold ms-sm-2"><?php echo htmlspecialchars($tournament['fee']); ?></span></span>
                              <span class="text-xs">Status: <span class="text-dark font-weight-bold ms-sm-2"><?php echo htmlspecialchars($tournament['status']); ?></span></span>
                           </div>
                           <div class="ms-auto text-end">
                              <button class="btn btn-link text-danger text-gradient px-3 mb-0" onclick="deleteTournament('<?php echo htmlspecialchars($tournament['tournament_id']); ?>')">
                                 <i class="material-icons text-sm me-2">delete</i>Delete
                              </button>
                              <button class="btn btn-link text-dark px-3 mb-0" onclick='editTournament(<?php echo json_encode($tournament); ?>)'>
                                 <i class="material-icons text-sm me-2">edit</i>Edit
                              </button>
                              <button class="btn btn-link text-dark px-3 mb-0 show-players" data-toggle="modal" data-target="#playersModal" data-tournament-id="<?php echo htmlspecialchars($tournament['tournament_id']); ?>">
                                 <i class="material-icons text-sm me-2">person</i>Show Players
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
                              <h6 class="mb-0">Available Bracket Tournament</h6>
                           </div>
                        </div>
                  </div>
                  <div class="card-body p-3 pb-0">
                     <ul class="list-group">
                        <?php
                        // Fetch available tournaments with their names
                        $stmt = $conn->prepare('
                           SELECT DISTINCT b.tournament_id, name 
                           FROM bracket b
                           JOIN tournaments t ON b.tournament_id = t.tournament_id
                        ');
                        $stmt->execute();
                        $tournaments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <?php if (!empty($tournaments)) : ?>
                              <?php foreach ($tournaments as $tournament) : ?>
                                 <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                                    <div class="d-flex flex-column">
                                          <h6 class="mb-1 text-dark font-weight-bold text-sm">
                                             <?php echo htmlspecialchars($tournament['name']); ?>
                                          </h6>
                                    </div>
                                    <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4 show-bracket" 
                                             data-toggle="modal" 
                                             data-target="#bracketModal" 
                                             data-tournament-id="<?php echo htmlspecialchars($tournament['tournament_id']); ?>">
                                          <i class="material-icons text-lg position-relative me-1"></i> Show Players
                                    </button>
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
         <!-- Content Row -->
         <div class="column">
         </div>
         <!-- Content Row -->
         <div class="row">
         <div class="row mt-4">
            <footer class="sticky-footer bg-white">
               <div class="container my-auto">
                     <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Your Website 2021</span>
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
      <!-- Add Tournament Modal -->
      <div class="modal fade" id="addTournamentModal" tabindex="-1" role="dialog" aria-labelledby="addTournamentModalLabel" aria-hidden="true">
         <div class="modal-dialog" role="document">
            <div class="modal-content">
                  <div class="modal-header">
                     <h5 class="modal-title" id="addTournamentModalLabel">Add Tournament</h5>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                     </button>
                  </div>
                  <div class="modal-body">
                     <form id="addTournamentForm" method="POST" action="add_tournament.php">
                        <label for="tournamentName">Tournament Name</label>
                        <div class="input-group input-group-outline my-3">
                              <input type="text" class="form-control" id="tournamentName" name="name" required>
                        </div>
                        <label for="startDate">Start Date & Time</label>
                        <div class="input-group input-group-outline my-3">
                              <input type="datetime-local" class="form-control" id="startDate" name="start_date" required>
                        </div>
                        <label for="endDate">End Date & Time</label>
                        <div class="input-group input-group-outline my-3">
                              <input type="datetime-local" class="form-control" id="endDate" name="end_date" required>
                        </div>
                        <label for="maxPlayers">Max Players</label>
                        <div class="input-group input-group-outline my-3">
                              <input type="number" class="form-control" id="maxPlayers" name="max_player" required>
                        </div>
                        <label for="prize">Prizes</label>
                        <div class="input-group input-group-outline my-3">
                              <input type="text" class="form-control" id="prize" name="prize" required>
                        </div>
                        <label for="fee">Tournament Fee</label>
                        <div class="input-group input-group-outline my-3">
                              <input type="number" step="0.01" class="form-control" id="fee" name="fee" required>
                        </div>
                        <label for="status">Status</label>
                        <div class="input-group input-group-outline my-3">
                              <select class="form-control" id="status" name="status" required>
                                 <option value="upcoming">Upcoming</option>
                                 <option value="ongoing">Ongoing</option>
                                 <option value="completed">Completed</option>
                              </select>
                        </div>
                        <label for="qualification">Qualification</label>
                        <div class="input-group input-group-outline my-3">
                              <select class="form-control" id="qualification" name="qualification" required>
                                 <option value="Class A">Class A</option>
                                 <option value="Class B">Class B</option>
                                 <option value="Class S">Class S</option>
                              </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Tournament</button>
                     </form>
                  </div>
            </div>
         </div>
      </div>

      <!-- Modal Structure -->
      <div class="modal fade" id="playersModal" tabindex="-1" aria-labelledby="playersModalLabel" aria-hidden="true">
         <div class="modal-dialog modal-lg">
            <div class="modal-content">
                  <div class="modal-header">
                     <h5 class="modal-title" id="playersModalLabel">Tournament Players</h5>
                     <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                     <table class="table">
                        <thead>
                              <tr>
                                 <th scope="col">User ID</th>
                                 <th scope="col">Username</th>
                                 <th scope="col">Proof of payment</th>
                                 <th scope="col">Status</th>
                              </tr>
                        </thead>
                        <tbody id="playersTableBody">
                              <!-- Player rows will be appended here dynamically -->
                        </tbody>
                     </table>
                  </div>
                  <div class="modal-footer">
                     <button type="button" id="createBracketBtn" class="btn btn-primary">Create Bracket</button>
                     <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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

      <!-- Edit Tournament Modal -->
      <div class="modal fade" id="editTournamentModal" tabindex="-1" role="dialog" aria-labelledby="editTournamentModalLabel" aria-hidden="true">
         <div class="modal-dialog" role="document">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title" id="editTournamentModalLabel">Edit Tournament</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                  </button>
               </div>
               <div class="modal-body">
                  <form id="editTournamentForm" method="POST" action="edit_tournament.php">
                     <input type="hidden" id="editTournamentId" name="tournament_id">
                     
                     <label for="editTournamentName">Tournament Name</label>
                     <div class="input-group input-group-outline my-3">
                        <input type="text" class="form-control" id="editTournamentName" name="name" required>
                     </div>

                     <label for="editStartDate">Start Date & Time</label> <!-- Updated to datetime-local -->
                     <div class="input-group input-group-outline my-3">
                        <input type="datetime-local" class="form-control" id="editStartDate" name="start_date" required>
                     </div>

                     <label for="editEndDate">End Date & Time</label> <!-- Updated to datetime-local -->
                     <div class="input-group input-group-outline my-3">
                        <input type="datetime-local" class="form-control" id="editEndDate" name="end_date" required>
                     </div>

                     <label for="editMaxPlayers">Max Players</label>
                     <div class="input-group input-group-outline my-3">
                        <input type="number" class="form-control" id="editMaxPlayers" name="max_player" required>
                     </div>

                     <label for="editPrize">Prize</label>
                     <div class="input-group input-group-outline my-3">
                        <input type="text" class="form-control" id="editPrize" name="prize" required>
                     </div>

                     <label for="editFee">Tournament Fee</label>
                     <div class="input-group input-group-outline my-3">
                        <input type="text" class="form-control" id="editFee" name="fee" required>
                     </div>

                     <label for="editStatus">Status</label>
                     <div class="input-group input-group-outline my-3">
                        <select class="form-control" id="editStatus" name="status" required>
                           <option value="upcoming">Upcoming</option>
                           <option value="ongoing">Ongoing</option>
                           <option value="completed">Completed</option>
                        </select>
                     </div>

                     <button type="submit" class="btn btn-primary">Save Changes</button>
                  </form>
               </div>
            </div>
         </div>
      </div>

      <!-- Modal to display proof of payment image -->
      <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
         <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                  <div class="modal-header">
                     <h5 class="modal-title" id="imageModalLabel">Proof of Payment</h5>
                     <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body" id="imageModalContent">
                     <!-- Image will be displayed here dynamically -->
                  </div>
            </div>
         </div>
      </div>
      
      <script>
         var win = navigator.platform.indexOf('Win') > -1;
            if (win && document.querySelector('#sidenav-scrollbar')) {
               var options = { damping: '0.5' }
               Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
            }

            let currentTournamentId = null;
            let finalRound = 0;
            document.addEventListener('DOMContentLoaded', function() {
               const showPlayersButtons = document.querySelectorAll('.show-players');

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
                                                <td><img src="${player.proof_of_payment}" alt="Proof of Payment" style="max-width: 200px; max-height: 300px;"></td>
                                                <td>${player.status}</td>
                                                <td>
                                                   <button class="btn btn-sm btn-primary edit-confirm" data-player-id="${player.player_id}" data-status="confirmed">Confirm</button>
                                                   <button class="btn btn-sm btn-primary edit-cancel" data-player-id="${player.player_id}" data-status="cancelled">Cancel</button>
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
                                          updatePlayerStatus(playerId, newStatus);
                                       });
                                    });

                                    const cancelButtons = document.querySelectorAll('.edit-cancel');
                                    cancelButtons.forEach(button => {
                                       button.addEventListener('click', function() {
                                          const playerId = this.getAttribute('data-player-id');
                                          const newStatus = this.getAttribute('data-status');
                                          updatePlayerStatus(playerId, newStatus);
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

                              const playersModal = new bootstrap.Modal(document.getElementById('playersModal'));
                              playersModal.show();
                           })
                           .catch(error => {
                              console.error('Error fetching players:', error);
                           });
                  });
               });

               // Function to update player status via AJAX
               function updatePlayerStatus(playerId, newStatus) {
                  fetch('update_player_status.php', {
                        method: 'POST',
                        headers: {
                           'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                           player_id: playerId,
                           new_status: newStatus
                        })
                  })
                  .then(response => response.json())
                  .then(data => {
                        if (data.success) {
                           // Optionally update UI to reflect status change
                           console.log(`Player ${playerId} status updated to ${newStatus}`);
                           // You can update the UI here if needed
                        } else {
                           console.error('Failed to update player status:', data.message);
                        }
                  })
                  .catch(error => {
                        console.error('Error updating player status:', error);
                  });
               }

               document.getElementById('createBracketBtn').addEventListener('click', function() {
                  if (currentTournamentId !== null) {
                        fetch(`create_bracket.php?tournament_id=${currentTournamentId}`)
                           .then(response => response.json())
                           .then(data => {
                              if (data.success) {
                                    alert('Bracket created successfully!');
                              } else {
                                    alert('Error: ' + data.message);
                              }
                           })
                           .catch(error => {
                              console.error('Error creating bracket:', error);
                           });
                  }
               });

               document.querySelectorAll('.show-bracket').forEach(button => {
                  button.addEventListener('click', function () {
                     currentTournamentId = this.getAttribute('data-tournament-id');

                     fetch(`get_bracket.php?tournament_id=${currentTournamentId}`)
                        .then(response => response.json())
                        .then(data => {
                              const bracketContainer = document.getElementById('bracketContainer');
                              bracketContainer.innerHTML = '';

                              if (data.success && data.players.length > 0) {
                                 const players = data.players;
                                 const rounds = Math.ceil(Math.log2(players.length));
                                 finalRound = rounds;
                                 let matchups = data.matchups || players.slice();

                                 for (let round = 1; round <= rounds; round++) {
                                    const roundDiv = document.createElement('div');
                                    roundDiv.className = 'round';
                                    roundDiv.dataset.round = round;
                                    roundDiv.innerHTML = `<h2>Round ${round}</h2>`;

                                    const matches = Math.ceil(matchups.length / 2);
                                    const newMatchups = [];

                                    for (let match = 0; match < matches; match++) {
                                          const matchDiv = document.createElement('div');
                                          matchDiv.className = 'match';

                                          const team1 = matchups[match * 2] ? matchups[match * 2].username : 'TBA';
                                          const team2 = matchups[match * 2 + 1] ? matchups[match * 2 + 1].username : 'TBA';

                                          matchDiv.innerHTML = `
                                             <div class="team" data-player-id="${matchups[match * 2] ? matchups[match * 2].user_id : ''}">${team1}</div>
                                             <div class="team" data-player-id="${matchups[match * 2 + 1] ? matchups[match * 2 + 1].user_id : ''}">${team2}</div>
                                             <button class="win-btn btn btn-success" data-round="${round}" data-match="${match}">Select Winner</button>
                                          `;

                                          roundDiv.appendChild(matchDiv);

                                          newMatchups.push({ user_id: `winner_${round}_${match}`, username: 'TBA' });
                                    }

                                    if (round > 1) {
                                          roundDiv.classList.add('vertical-center');
                                    }

                                    bracketContainer.appendChild(roundDiv);
                                    matchups = newMatchups;
                                 }

                                 if (players.length > 1) {
                                    const finalRoundDiv = document.createElement('div');
                                    finalRoundDiv.className = 'vertical-center';
                                    finalRoundDiv.innerHTML = `<h2>Winner</h2>`;

                                    const winnerPlaceholder = document.createElement('div');
                                    winnerPlaceholder.className = 'match winner-placeholder';
                                    winnerPlaceholder.innerHTML = '<div class="team">TBA</div>';

                                    finalRoundDiv.appendChild(winnerPlaceholder);
                                    bracketContainer.appendChild(finalRoundDiv);
                                 }

                                 const playersModal = new bootstrap.Modal(document.getElementById('bracketModal'));
                                 playersModal.show();
                              } else {
                                 alert(data.message);
                              }
                        })
                        .catch(error => {
                              console.error('Error fetching bracket:', error);
                        });
                  });
            });

            document.getElementById('bracketContainer').addEventListener('click', function (event) {
                  if (event.target.classList.contains('win-btn')) {
                     const round = event.target.getAttribute('data-round');
                     const match = event.target.getAttribute('data-match');
                     const winnerElement = event.target.parentElement.querySelector('.team.selected');

                     if (winnerElement) {
                        const winnerId = winnerElement.getAttribute('data-player-id');
                        fetch(`update_bracket.php`, {
                              method: 'POST',
                              headers: {
                                 'Content-Type': 'application/x-www-form-urlencoded',
                              },
                              body: new URLSearchParams({
                                 tournament_id: currentTournamentId,
                                 round: round,
                                 match: match,
                                 winner_id: winnerId,
                              })
                        })
                        .then(response => response.json())
                        .then(data => {
                              if (data.success) {
                                 winnerElement.parentElement.querySelector('.win-btn').setAttribute('disabled', 'disabled');
                                 winnerElement.parentElement.querySelectorAll('.team').forEach(team => {
                                    if (team !== winnerElement) {
                                          team.classList.add('eliminated');
                                    }
                                 });
                                 console.log('Winner updated successfully');

                                 moveWinnerToNextRound(winnerElement, round, match);
                                 announceWinner(winnerElement.textContent, currentTournamentId, round);
                                 if (parseInt(round) === parseInt(finalRound)) {
                                    announceWinner(winnerElement.textContent, currentTournamentId, round);
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
                     event.target.parentElement.querySelectorAll('.team').forEach(team => team.classList.remove('selected'));
                     event.target.classList.add('selected');
                  }
            });

            function moveWinnerToNextRound(winnerElement, round, match) {
               const nextRound = parseInt(round) + 1;
               const nextMatch = Math.floor(match / 2);

               const nextRoundDiv = document.querySelector(`.round[data-round="${nextRound}"]`);
               if (nextRoundDiv) {
                  const nextMatchDiv = nextRoundDiv.querySelectorAll('.match')[nextMatch];
                  const nextTeamContainer = nextMatchDiv.querySelectorAll('.team-container')[match % 2];
                  const nextTeamDiv = nextTeamContainer.querySelector('.team');

                  nextTeamDiv.textContent = winnerElement.textContent;
                  nextTeamDiv.setAttribute('data-player-id', winnerElement.getAttribute('data-player-id'));
               } else {
                  const winnerTeamDiv = document.querySelector('.winner .team');
                  if (winnerTeamDiv) {
                     winnerTeamDiv.textContent = winnerElement.textContent;
                     winnerTeamDiv.setAttribute('data-player-id', winnerElement.getAttribute('data-player-id'));
                  }
               }
            }


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
                        console.log('Winner announcement made successfully.');
                     } else {
                        console.error('Failed to announce winner:', data.message);
                     }
                  })
                  .catch(error => {
                     console.error('Error announcing winner:', error);
                  });
            }

               function deleteTournament(tournamentId) {
                  if (confirm('Are you sure you want to delete this tournament?')) {
                        window.location.href = `delete_tournament.php?tournament_id=${tournamentId}`;
                     }
                  }
               });

               function editTournament(tournament) {
                  console.log("editTournamentModal called");
                  console.log(tournament);

                  document.getElementById('editTournamentId').value = tournament.tournament_id;
                  document.getElementById('editTournamentName').value = tournament.name;
                  document.getElementById('editStartDate').value = tournament.start_date;
                  document.getElementById('editEndDate').value = tournament.end_date;
                  document.getElementById('editMaxPlayers').value = tournament.max_player;
                  document.getElementById('editPrize').value = tournament.prize;
                  document.getElementById('editStatus').value = tournament.status;
                  document.getElementById('editFee').value = tournament.fee;

                  $('#editTournamentModal').modal('show');
               }

               document.addEventListener('DOMContentLoaded', () => {
                  document.querySelectorAll('.show-players').forEach(button => {
                        button.addEventListener('click', function () {
                           const tournamentId = this.getAttribute('data-tournament-id');
                        });
                  });
               });
      </script>
   <style>
      .winner {
         position: absolute;
         top: 0;
         left: 50%;
         transform: translateX(-50%);
      }

         .winner .team {
         background-color: gold;
         font-weight: bold;
      }
      /* For even better alignment */
      .match {
      margin-bottom: 60px;
      }

      .round:first-child .match::after {
      right: -20px;
      }

      .round:nth-child(2) .match::after {
      right: -40px;
      }

      .round:nth-child(3) .match::after {
      right: -60px;
      }

      /* Adjust connector height based on the round */
      .round[data-round="1"] .connector {
      height: 40px;
      }

      .round[data-round="2"] .connector {
      height: 80px;
      }

      .round[data-round="3"] .connector {
      height: 160px;
      }
      /* Container for the entire bracket */
         #bracketContainer {
         display: flex;
         justify-content: center;
         align-items: flex-start;
         position: relative;
         }

         /* Styling each round */
         .round {
         display: flex;
         flex-direction: column;
         align-items: center;
         margin: 0 20px;
         position: relative;
         }

         /* Styling each match */
         .match {
         display: flex;
         flex-direction: column;
         align-items: center;
         position: relative;
         margin-bottom: 40px;
         }

         /* Team container to hold the team and connector */
         .team-container {
         position: relative;
         }

         /* Team styling */
         .team {
         padding: 10px 20px;
         background-color: #f1f1f1;
         margin: 5px 0;
         text-align: center;
         cursor: pointer;
         position: relative;
         }

         /* Connector styling */
         .connector {
         width: 2px;
         height: 20px;
         background-color: #000;
         position: absolute;
         left: 50%;
         bottom: -20px;
         transform: translateX(-50%);
         }

         /* Horizontal line to the next match */
         .match::after {
         content: '';
         width: 40px;
         height: 2px;
         background-color: #000;
         position: absolute;
         right: -40px;
         top: 50%;
         transform: translateY(-50%);
         }

         /* Vertical line connecting to the next round */
         .round:not(:last-child) .match::after {
         display: block;
         }

         .round:last-child .match::after {
         display: none;
         }




   </style>
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
      <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
      <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
      <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc --><script src="./assets/js/material-dashboard.min.js?v=3.1.0"></script>
   </body>
</html>