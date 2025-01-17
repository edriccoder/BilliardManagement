<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'conn.php';

// Check if user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$username = htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8');
$user_id = htmlspecialchars($_SESSION['user_id'], ENT_QUOTES, 'UTF-8');

// Fetch reports from the database
$sql = "SELECT id, type, description, datetime, photo, name, charges, contact_number FROM reports ORDER BY datetime DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.9/dist/sweetalert2.min.css">
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.9/dist/sweetalert2.all.min.js"></script>
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
            <span class="ms-1 font-weight-bold text-white">Log in as <?php echo htmlspecialchars($username); ?></span>
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
         <!-- Table Row -->
         <div class="card">
            <div class="card-header pb-0 px-3">
               <h6 class="mb-0">Reports</h6>
                    <!-- Button to Open Item Damage Report Modal -->
                    <button class="btn btn-primary" data-toggle="modal" data-target="#itemDamageModal">Create Item Damage Report</button>
                    
                    <!-- Button to Open Incident Report Modal -->
                    <button class="btn btn-primary" data-toggle="modal" data-target="#incidentReportModal">Create Incident Report</button>
            </div>
            <div class="card-body pt-4 p-3">
                <ul class="list-group">
                    <?php
                        if (!empty($reports)) {
                            foreach ($reports as $report) {
                                echo '<li class="list-group-item border-0 d-flex p-4 mb-2 bg-gray-100 border-radius-lg">';
                                echo '<div class="d-flex flex-column">';
                        
                                // Type
                                if (!empty($report["type"])) {
                                    echo '<h6 class="mb-3 text-sm">Type: <span class="text-dark font-weight-bold ms-sm-2">' . htmlspecialchars($report["type"], ENT_QUOTES, 'UTF-8') . '</span></h6>';
                                }
                        
                                // Description
                                if (!empty($report["description"])) {
                                    echo '<span class="mb-2 text-xs">Description: <span class="text-dark font-weight-bold ms-sm-2">' . htmlspecialchars($report["description"], ENT_QUOTES, 'UTF-8') . '</span></span>';
                                }
                        
                                // Date & Time
                                if (!empty($report["datetime"])) {
                                    echo '<span class="mb-2 text-xs">Date & Time: <span class="text-dark ms-sm-2 font-weight-bold">' . htmlspecialchars($report["datetime"], ENT_QUOTES, 'UTF-8') . '</span></span>';
                                }
                        
                                // Charges
                                if (isset($report["charges"]) && $report["charges"] !== '') { // Allowing '0.00' as a valid value
                                    echo '<span class="mb-2 text-xs">Charges: <span class="text-dark ms-sm-2 font-weight-bold">₱' . htmlspecialchars($report["charges"], ENT_QUOTES, 'UTF-8') . '</span></span>';
                                }
                        
                                // Photo
                                if (!empty($report["photo"])) {
                                    echo '<span class="mb-2 text-xs">Photo: <span class="text-dark ms-sm-2 font-weight-bold">';
                                    echo '<a href="#" onclick="openImageModal(\'' . htmlspecialchars($report["photo"], ENT_QUOTES, 'UTF-8') . '\'); return false;">';
                                    echo '<img src="' . htmlspecialchars($report["photo"], ENT_QUOTES, 'UTF-8') . '" alt="Report Photo" style="max-width: 100px; max-height: 100px;">';
                                    echo '</a>';
                                    echo '</span></span>';
                                }
                        
                                // Reported By
                                if (!empty($report["name"])) {
                                    echo '<span class="mb-2 text-xs">Reported By: <span class="text-dark ms-sm-2 font-weight-bold">' . htmlspecialchars($report["name"], ENT_QUOTES, 'UTF-8') . '</span></span>';
                                }
                        
                                // Contact Number
                                if (!empty($report["contact_number"])) {
                                    echo '<span class="mb-2 text-xs">Contact Number: <span class="text-dark ms-sm-2 font-weight-bold">' . htmlspecialchars($report["contact_number"], ENT_QUOTES, 'UTF-8') . '</span></span>';
                                }
                        
                                echo '</div>'; // Closing the flex column div properly
                        
                                // Actions section
                                echo '<div class="ms-auto text-end">';
                                echo '<a class="btn btn-link text-danger text-gradient px-3 mb-0" href="delete_report.php?report_id=' . htmlspecialchars($report["id"], ENT_QUOTES, 'UTF-8') . '"><i class="material-icons text-sm me-2">delete</i>Delete</a>';
                                echo '<a class="btn btn-link text-dark px-3 mb-0" data-bs-toggle="modal" data-bs-target="#reportModal" onclick=\'openEditModal(' . htmlspecialchars(json_encode($report), ENT_QUOTES, 'UTF-8') . ')\'><i class="material-icons text-sm me-2">edit</i>Edit</a>';
                                echo '</div>';
                                echo '</li>';
                            }
                        } else {
                            echo '<li class="list-group-item border-0 d-flex p-4 mb-2 bg-gray-100 border-radius-lg">';
                            echo '<div class="d-flex flex-column">';
                            echo '<h6 class="mb-3 text-sm">No reports found.</h6>';
                            echo '</div>';
                            echo '</li>';
                        }
                        ?>

                </ul>
               <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered" role="document">
                     <div class="modal-content">
                        <div class="modal-header">
                        <h5 class="modal-title" id="imageModalLabel">Proof of Payment</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                           <span aria-hidden="true">&times;</span>
                        </button>
                        </div>
                        <div class="modal-body text-center">
                        <img id="imageModalContent" src="" alt="Proof of Payment" style="max-width: 100%; max-height: 500px;">
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

<!-- Item Damage Report Modal -->
<div class="modal fade" id="itemDamageModal" tabindex="-1" aria-labelledby="itemDamageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="itemDamageModalLabel">Item Damage Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="itemDamageForm" action="submit_item_damage.php" method="POST" enctype="multipart/form-data">
                    <label for="item_damage_description" class="form-label">Description of the damage:</label>
                    <div class="input-group input-group-outline my-3">
                        <textarea name="item_damage_description" id="item_damage_description" class="form-control" rows="3" placeholder="Describe the damage..." required></textarea>
                    </div>

                    <label for="item_damage_datetime" class="form-label">Date & Time:</label>
                    <div class="input-group input-group-outline my-3">
                        <input type="datetime-local" name="item_damage_datetime" id="item_damage_datetime" class="form-control" required>
                    </div>

                    <label for="item_damage_photo" class="form-label">Upload Photo:</label>
                    <div class="input-group input-group-outline my-3">
                        <input type="file" name="item_damage_photo" id="item_damage_photo" class="form-control" accept="image/*" required>
                    </div>

                    <label for="item_reported_by" class="form-label">Reported By:</label>
                    <div class="input-group input-group-outline my-3">
                        <input type="text" name="item_reported_by" id="item_reported_by" class="form-control" value="<?php echo htmlspecialchars($username); ?>" readonly>
                    </div>

                    <label for="item_damage_caused_by" class="form-label">Damage By:</label>
                    <div class="input-group input-group-outline my-3">
                        <input type="text" name="item_damage_caused_by" id="item_damage_caused_by" class="form-control" placeholder="Name of the person who caused the damage" required>
                    </div>

                    <label for="item_damage_charges" class="form-label">Charges (in ₱):</label>
                    <div class="input-group input-group-outline my-3">
                        <input type="number" name="item_damage_charges" id="item_damage_charges" class="form-control" placeholder="Enter charges" min="0" step="0.01" required>
                    </div>

                    <!-- New Contact Number Field -->
                    <label for="item_damage_contact_number" class="form-label">Contact Number:</label>
                    <div class="input-group input-group-outline my-3">
                        <input type="tel" name="item_damage_contact_number" id="item_damage_contact_number" class="form-control" placeholder="Enter contact number" pattern="[0-9]{10,15}" required>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Submit Item Damage Report</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



<!-- Incident Report Modal -->
<div class="modal fade" id="incidentReportModal" tabindex="-1" aria-labelledby="incidentReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="incidentReportModalLabel">Incident Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="incidentReportForm" action="submit_incident_report.php" method="POST" enctype="multipart/form-data">
                    <!-- Hidden Field to Specify Report Type -->
                    <input type="hidden" name="report_type" value="incident_report">

                    <label for="incident_report_name" class="form-label">Name of the person to report:</label>
                    <div class="input-group input-group-outline my-3">
                        <input type="text" name="incident_report_name" id="incident_report_name" class="form-control" placeholder="Name" required>
                    </div>

                    <label for="incident_report_description" class="form-label">Description of the incident:</label>
                    <div class="input-group input-group-outline my-3">
                        <textarea name="incident_report_description" id="incident_report_description" class="form-control" rows="3" placeholder="Describe the incident..." required></textarea>
                    </div>

                    <label for="incident_report_datetime" class="form-label">Date & Time:</label>
                    <div class="input-group input-group-outline my-3">
                        <input type="datetime-local" name="incident_report_datetime" id="incident_report_datetime" class="form-control" required>
                    </div>

                    <label for="reported_by" class="form-label">Reported By:</label>
                    <div class="input-group input-group-outline my-3">
                        <input type="text" name="reported_by" id="reported_by" class="form-control" value="<?php echo htmlspecialchars($username); ?>" readonly>
                    </div>
                    
                    <label for="item_damage_caused_by" class="form-label">Person Involved in The Accident:</label>
                    <div class="input-group input-group-outline my-3">
                        <input type="text" name="damaged_by" id="damaged_by" class="form-control" placeholder="Your Name" required>
                    </div>
                    
                    <label for="incident_contact_number" class="form-label">Contact Number:</label>
                    <div class="input-group input-group-outline my-3">
                        <input type="tel" name="incident_contact_number" id="incident_contact_number" class="form-control" placeholder="Enter contact number" pattern="[0-9]{10,15}" required>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Submit Incident Report</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    function showSection(reportType) {
        const itemDamageSection = document.getElementById('itemDamageSection');
        const incidentReportSection = document.getElementById('incidentReportSection');

        // Get the form fields that need to be required or not
        const itemDamageDescription = document.getElementById('item_damage_description');
        const itemDamageDateTime = document.getElementById('item_damage_datetime');
        const itemDamagePhoto = document.getElementById('item_damage_photo');
        const itemReportedBy = document.getElementById('item_reported_by');

        const incidentReportDescription = document.getElementById('incident_report_description');
        const incidentReportDateTime = document.getElementById('incident_report_datetime');
        const reportedBy = document.getElementById('reported_by');

        // Hide both sections initially
        itemDamageSection.style.display = 'none';
        incidentReportSection.style.display = 'none';

        // Remove the 'required' attribute from all fields
        itemDamageDescription.removeAttribute('required');
        itemDamageDateTime.removeAttribute('required');
        itemDamagePhoto.removeAttribute('required');
        itemReportedBy.removeAttribute('required');

        incidentReportDescription.removeAttribute('required');
        incidentReportDateTime.removeAttribute('required');
        reportedBy.removeAttribute('required');

        // Show the selected section and add 'required' attributes
        if (reportType === 'item_damage') {
            itemDamageSection.style.display = 'block';
            itemDamageDescription.setAttribute('required', 'required');
            itemDamageDateTime.setAttribute('required', 'required');
            itemDamagePhoto.setAttribute('required', 'required');
            itemReportedBy.setAttribute('required', 'required');
        } else if (reportType === 'incident_report') {
            incidentReportSection.style.display = 'block';
            incidentReportName.setAttribute('required', 'required');
            incidentReportDescription.setAttribute('required', 'required');
            incidentReportDateTime.setAttribute('required', 'required');
            reportedBy.setAttribute('required', 'required');
        }
    }
</script>

<?php
    // Display the SweetAlert modal if there's an alert set in the session
    if (!empty($_SESSION['alert'])) {
        echo "<script>
            Swal.fire({
                title: '" . $_SESSION['alert']['title'] . "',
                text: '" . $_SESSION['alert']['text'] . "',
                icon: '" . $_SESSION['alert']['icon'] . "'
            });
        </script>";
        unset($_SESSION['alert']); // Clear the alert after displaying it
    }
    ?>


      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
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