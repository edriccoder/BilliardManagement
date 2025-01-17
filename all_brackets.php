<?php
   include 'conn.php';
   
   // Fetch all tournaments
   $sqlTournaments = "SELECT * FROM tournaments ORDER BY start_date DESC";
   $stmtTournaments = $conn->prepare($sqlTournaments);
   $stmtTournaments->execute();
   $tournaments = $stmtTournaments->fetchAll(PDO::FETCH_ASSOC);

   // Handle error messages if any
   if (isset($_GET['error'])) {
      $error_message = htmlspecialchars($_GET['error']);
      echo "<script>alert('$error_message');</script>";
   }

   // Fetch notifications as in your existing code
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
      <title>All Brackets - Billiard Management</title>
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
         /* Additional styling for brackets */
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
         }
      </style>
   </head>
   <body class="g-sidenav-show bg-gray-100">
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
         <div class="container-fluid">
            <!-- Page Heading -->      
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
               <h1 class="h3 mb-0 text-gray-800">All Tournament Brackets</h1>
               <a href="manage_tournament.php" class="btn btn-secondary">Manage Tournaments</a>
            </div>
            <!-- Accordion for Tournaments -->
            <div class="accordion" id="tournamentsAccordion">
               <?php if (!empty($tournaments)) : ?>
                  <?php foreach ($tournaments as $index => $tournament) : ?>
                     <div class="accordion-item">
                        <h2 class="accordion-header" id="heading<?php echo $tournament['tournament_id']; ?>">
                           <button class="accordion-button <?php echo $index !== 0 ? 'collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $tournament['tournament_id']; ?>" aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>" aria-controls="collapse<?php echo $tournament['tournament_id']; ?>">
                              <?php echo htmlspecialchars($tournament['name']); ?> 
                              <span class="badge bg-<?php 
                                 switch($tournament['status']) {
                                    case 'upcoming':
                                       echo 'info';
                                       break;
                                    case 'ongoing':
                                       echo 'success';
                                       break;
                                    case 'completed':
                                       echo 'secondary';
                                       break;
                                    default:
                                       echo 'primary';
                                 }
                              ?> ms-2">
                                 <?php echo ucfirst(htmlspecialchars($tournament['status'])); ?>
                              </span>
                           </button>
                        </h2>
                        <div id="collapse<?php echo $tournament['tournament_id']; ?>" class="accordion-collapse collapse <?php echo $index === 0 ? 'show' : ''; ?>" aria-labelledby="heading<?php echo $tournament['tournament_id']; ?>" data-bs-parent="#tournamentsAccordion">
                           <div class="accordion-body">
                              <!-- Tournament Details -->
                              <div class="row mb-3">
                                 <div class="col-md-6">
                                    <h5>Details</h5>
                                    <ul class="list-group">
                                       <li class="list-group-item"><strong>Start Date:</strong> <?php echo htmlspecialchars($tournament['start_date']); ?></li>
                                       <li class="list-group-item"><strong>End Date:</strong> <?php echo htmlspecialchars($tournament['end_date']); ?></li>
                                       <li class="list-group-item"><strong>Max Players:</strong> <?php echo htmlspecialchars($tournament['max_player']); ?></li>
                                       <li class="list-group-item"><strong>Prizes:</strong> <?php echo htmlspecialchars($tournament['prize']); ?></li>
                                       <li class="list-group-item"><strong>Venue:</strong> <?php echo htmlspecialchars($tournament['venue']); ?></li>
                                       <li class="list-group-item"><strong>Fee:</strong> <?php echo htmlspecialchars($tournament['fee']); ?></li>
                                       <li class="list-group-item"><strong>Qualification:</strong> <?php echo htmlspecialchars($tournament['qualification']); ?></li>
                                    </ul>
                                 </div>
                                 <div class="col-md-6">
                                    <h5>Bracket Actions</h5>
                                    <button class="btn btn-primary mb-2 view-bracket-btn" data-tournament-id="<?php echo $tournament['tournament_id']; ?>" data-tournament-name="<?php echo htmlspecialchars($tournament['name']); ?>">View Bracket</button>
                                    <button class="btn btn-success mb-2 create-bracket-btn" data-tournament-id="<?php echo $tournament['tournament_id']; ?>">Create Bracket</button>
                                    <button class="btn btn-warning mb-2 export-bracket-btn" data-tournament-id="<?php echo $tournament['tournament_id']; ?>">Tournament Reports</button>
                                 </div>
                              </div>
                              <!-- Placeholder for Bracket -->
                              <div id="bracket<?php echo $tournament['tournament_id']; ?>" class="bracket-container mb-4">
                                 <!-- Bracket will be loaded here via AJAX -->
                                 <p>Click "View Bracket" to see the tournament bracket.</p>
                              </div>
                           </div>
                        </div>
                     </div>
                  <?php endforeach; ?>
               <?php else : ?>
                  <div class="alert alert-warning" role="alert">
                     No tournaments found.
                  </div>
               <?php endif; ?>
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
      
      <!-- Bracket Modal -->
      <div class="modal fade" id="bracketModal" tabindex="-1" aria-labelledby="bracketModalLabel" aria-hidden="true">
         <div class="modal-dialog modal-lg">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title" id="bracketModalLabel">Tournament Bracket</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
               </div>
               <div class="modal-body">
                  <div class="bracket" id="bracketContainer">
                     <!-- Bracket content will be loaded here dynamically -->
                  </div>
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

          document.addEventListener('DOMContentLoaded', function() {
              // Event Listener for View Bracket Buttons
              document.querySelectorAll('.view-bracket-btn').forEach(button => {
                  button.addEventListener('click', function() {
                      const tournamentId = this.getAttribute('data-tournament-id');
                      const tournamentName = this.getAttribute('data-tournament-name');
                      loadBracket(tournamentId, tournamentName);
                  });
              });

              // Event Listener for Create Bracket Buttons
              document.querySelectorAll('.create-bracket-btn').forEach(button => {
                  button.addEventListener('click', function() {
                      const tournamentId = this.getAttribute('data-tournament-id');
                      createBracket(tournamentId);
                  });
              });

              // Event Listener for Export Bracket Buttons
              document.querySelectorAll('.export-bracket-btn').forEach(button => {
                  button.addEventListener('click', function() {
                      const tournamentId = this.getAttribute('data-tournament-id');
                      exportBracket(tournamentId);
                  });
              });

              // Function to Load Bracket via AJAX
              function loadBracket(tournamentId, tournamentName) {
                  fetch(`get_bracket.php?tournament_id=${tournamentId}`)
                      .then(response => response.json())
                      .then(data => {
                          if (data.success) {
                              const bracketContainer = document.getElementById('bracketContainer');
                              bracketContainer.innerHTML = ''; // Clear existing content

                              const matches = data.matches;
                              const rounds = Math.max(...matches.map(m => m.round));
                              
                              // Create a mapping from match_number to match details
                              const matchMap = {};
                              matches.forEach(match => {
                                  matchMap[match.match_number] = match;
                              });

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

                                      const player1 = match.player1_name || 'TBA';
                                      const player2 = match.player2_name || 'TBA';
                                      const winner = match.winner_id ? (match.player1_id === match.winner_id ? match.player1_name : match.player2_name) : 'TBA';

                                      matchDiv.innerHTML = `
                                          <div class="team ${winner === player1 ? 'selected' : ''} ${winner !== 'TBA' && winner !== player1 ? 'eliminated' : ''}">
                                              ${player1} ${match.player1_score !== null ? `(${match.player1_score})` : ''}
                                          </div>
                                          <div class="team ${winner === player2 ? 'selected' : ''} ${winner !== 'TBA' && winner !== player2 ? 'eliminated' : ''}">
                                              ${player2} ${match.player2_score !== null ? `(${match.player2_score})` : ''}
                                          </div>
                                      `;

                                      roundDiv.appendChild(matchDiv);
                                  });

                                  bracketContainer.appendChild(roundDiv);
                              }

                              // Initialize and show the Bracket Modal
                              const bracketModal = new bootstrap.Modal(document.getElementById('bracketModal'));
                              document.getElementById('bracketModalLabel').innerText = `${tournamentName} - Bracket`;
                              bracketModal.show();
                          } else {
                              Swal.fire({
                                  icon: 'error',
                                  title: 'Error',
                                  text: data.message || 'Unable to load bracket.'
                              });
                          }
                      })
                      .catch(error => {
                          console.error('Error fetching bracket:', error);
                          Swal.fire({
                              icon: 'error',
                              title: 'Error',
                              text: 'An error occurred while fetching the bracket.'
                          });
                      });
              }

              // Function to Create Bracket via AJAX
              function createBracket(tournamentId) {
                  Swal.fire({
                      title: 'Are you sure?',
                      text: "This will create a new bracket for the selected tournament.",
                      icon: 'warning',
                      showCancelButton: true,
                      confirmButtonColor: '#3085d6',
                      cancelButtonColor: '#d33',
                      confirmButtonText: 'Yes, create it!'
                  }).then((result) => {
                      if (result.isConfirmed) {
                          fetch(`create_bracket.php?tournament_id=${tournamentId}`)
                              .then(response => response.json())
                              .then(data => {
                                  if (data.success) {
                                      Swal.fire(
                                          'Created!',
                                          'The bracket has been created.',
                                          'success'
                                      ).then(() => {
                                          // Optionally, reload the page or update the UI
                                          location.reload();
                                      });
                                  } else {
                                      Swal.fire({
                                          icon: 'error',
                                          title: 'Error',
                                          text: data.message || 'Unable to create bracket.'
                                      });
                                  }
                              })
                              .catch(error => {
                                  console.error('Error creating bracket:', error);
                                  Swal.fire({
                                      icon: 'error',
                                      title: 'Error',
                                      text: 'An error occurred while creating the bracket.'
                                  });
                              });
                      }
                  });
              }

              // Function to Export Bracket (e.g., as PDF)
              function exportBracket(tournamentId) {
                  // Assuming you have a script to handle exporting brackets, e.g., export_bracket.php
                  window.open(`export_bracket.php?tournament_id=${tournamentId}`, '_blank');
              }

              // Initialize Bootstrap Tooltips (if any)
              var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
              var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                  return new bootstrap.Tooltip(tooltipTriggerEl);
              });
          });
      </script>
   </body>
</html>
