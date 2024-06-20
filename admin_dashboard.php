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
               <a class="nav-link text-white " href="./notifications.html">
                  <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                     <i class="material-icons opacity-10">notifications</i>
                  </div>
                  <span class="nav-link-text ms-1">Notifications</span>
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
         <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
         </div>
         <!-- Content Row -->
         <div class="row">

            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card  mb-2">
                <div class="card-header p-3 pt-2">
                   <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-xl mt-n4 position-absolute">
                      <i class="material-icons opacity-10">person</i>
                   </div>
                   <div class="text-end pt-1">
                      <p class="text-sm mb-0 text-capitalize">Total Users</p>
                      <h4 class="mb-0">0</h4>
                   </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                   <p class="mb-0"><span class="text-success text-sm font-weight-bolder">+55% </span>than last week</p>
                </div>
             </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card  mb-2">
                <div class="card-header p-3 pt-2">
                   <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary shadow text-center border-radius-xl mt-n4 position-absolute">
                      <i class="material-icons opacity-10">leaderboard</i>
                   </div>
                   <div class="text-end pt-1">
                      <p class="text-sm mb-0 text-capitalize">Billiard Table</p>
                      <h4 class="mb-0">10</h4>
                   </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                   <p class="mb-0"><span class="text-success text-sm font-weight-bolder">+3% </span>than last month</p>
                </div>
             </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card  mb-2">
                <div class="card-header p-3 pt-2 bg-transparent">
                   <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                      <i class="material-icons opacity-10">book</i>
                   </div>
                   <div class="text-end pt-1">
                      <p class="text-sm mb-0 text-capitalize ">Bookings</p>
                      <h4 class="mb-0 ">0</h4>
                   </div>
                </div>
                <hr class="horizontal my-0 dark">
                <div class="card-footer p-3">
                   <p class="mb-0 "><span class="text-success text-sm font-weight-bolder">+1% </span>than yesterday</p>
                </div>
             </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card ">
                <div class="card-header p-3 pt-2 bg-transparent">
                   <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                      <i class="material-icons opacity-10">report</i>
                   </div>
                   <div class="text-end pt-1">
                      <p class="text-sm mb-0 text-capitalize ">Reports</p>
                      <h4 class="mb-0 ">0</h4>
                   </div>
                </div>
                <hr class="horizontal my-0 dark">
                <div class="card-footer p-3">
                   <p class="mb-0 ">Just updated</p>
                </div>
             </div>
            </div>
         </div>

         <div class="row mt-4">
          <div class="col-lg-5 mb-lg-0 mb-4">
             <div class="card z-index-2 mt-4">
                <div class="card-body mt-n5 px-3">
                   <div class="bg-gradient-dark shadow-dark border-radius-lg py-3 pe-1 mb-3">
                      <div class="chart">
                         <canvas id="chart-bars" class="chart-canvas" height="170"></canvas>
                      </div>
                   </div>
                   <h6 class="ms-2 mt-4 mb-0"> Active Users </h6>
                   <p class="text-sm ms-2"> (<span class="font-weight-bolder">+11%</span>) than last week </p>
                   <div class="container border-radius-lg">
                      <div class="row">
                         <div class="col-3 py-3 ps-0">
                            <div class="d-flex mb-2">
                               <div class="icon icon-shape icon-xxs shadow border-radius-sm bg-gradient-primary text-center me-2 d-flex align-items-center justify-content-center">
                                  <i class="material-icons opacity-10">groups</i>
                               </div>
                               <p class="text-xs my-auto font-weight-bold">Users</p>
                            </div>
                            <h4 class="font-weight-bolder">42K</h4>
                            <div class="progress w-75">
                               <div class="progress-bar bg-dark w-60" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                         </div>
                         <div class="col-3 py-3 ps-0">
                            <div class="d-flex mb-2">
                               <div class="icon icon-shape icon-xxs shadow border-radius-sm bg-gradient-info text-center me-2 d-flex align-items-center justify-content-center">
                                  <i class="material-icons opacity-10">ads_click</i>
                               </div>
                               <p class="text-xs mt-1 mb-0 font-weight-bold">Clicks</p>
                            </div>
                            <h4 class="font-weight-bolder">1.7m</h4>
                            <div class="progress w-75">
                               <div class="progress-bar bg-dark w-90" role="progressbar" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                         </div>
                         <div class="col-3 py-3 ps-0">
                            <div class="d-flex mb-2">
                               <div class="icon icon-shape icon-xxs shadow border-radius-sm bg-gradient-warning text-center me-2 d-flex align-items-center justify-content-center">
                                  <i class="material-icons opacity-10">receipt</i>
                               </div>
                               <p class="text-xs mt-1 mb-0 font-weight-bold">Sales</p>
                            </div>
                            <h4 class="font-weight-bolder">399$</h4>
                            <div class="progress w-75">
                               <div class="progress-bar bg-dark w-30" role="progressbar" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                         </div>
                         <div class="col-3 py-3 ps-0">
                            <div class="d-flex mb-2">
                               <div class="icon icon-shape icon-xxs shadow border-radius-sm bg-gradient-danger text-center me-2 d-flex align-items-center justify-content-center">
                                  <i class="material-icons opacity-10">category</i>
                               </div>
                               <p class="text-xs mt-1 mb-0 font-weight-bold">Items</p>
                            </div>
                            <h4 class="font-weight-bolder">74</h4>
                            <div class="progress w-75">
                               <div class="progress-bar bg-dark w-50" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                         </div>
                      </div>
                   </div>
                </div>
             </div>
          </div>
          <div class="col-lg-7">
             <div class="card z-index-2">
                <div class="card-header pb-0">
                   <h6>Sales overview</h6>
                   <p class="text-sm">
                      <i class="fa fa-arrow-up text-success"></i>
                      <span class="font-weight-bold">4% more</span> in 2021
                   </p>
                </div>
                <div class="card-body p-3">
                   <div class="chart">
                      <canvas id="chart-line" class="chart-canvas" height="300"></canvas>
                   </div>
                </div>
             </div>
          </div>
       </div>
       <div class="row mt-4">
        <div class="col-lg-4 col-md-6 mt-4 mb-4">
          <div class="card z-index-2 ">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
              <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1">
                <div class="chart">
                  <canvas id="chart-bars" class="chart-canvas" height="170"></canvas>
                </div>
              </div>
            </div>
            <div class="card-body">
              <h6 class="mb-0 ">Website Views</h6>
              <p class="text-sm ">Last Campaign Performance</p>
              <hr class="dark horizontal">
              <div class="d-flex ">
                <i class="material-icons text-sm my-auto me-1">schedule</i>
                <p class="mb-0 text-sm"> campaign sent 2 days ago </p>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6 mt-4 mb-4">
          <div class="card z-index-2  ">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
              <div class="bg-gradient-success shadow-success border-radius-lg py-3 pe-1">
                <div class="chart">
                  <canvas id="chart-line" class="chart-canvas" height="170"></canvas>
                </div>
              </div>
            </div>
            <div class="card-body">
              <h6 class="mb-0 "> Daily Sales </h6>
              <p class="text-sm "> (<span class="font-weight-bolder">+15%</span>) increase in today sales. </p>
              <hr class="dark horizontal">
              <div class="d-flex ">
                <i class="material-icons text-sm my-auto me-1">schedule</i>
                <p class="mb-0 text-sm"> updated 4 min ago </p>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4 mt-4 mb-3">
          <div class="card z-index-2 ">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
              <div class="bg-gradient-dark shadow-dark border-radius-lg py-3 pe-1">
                <div class="chart">
                  <canvas id="chart-line-tasks" class="chart-canvas" height="170"></canvas>
                </div>
              </div>
            </div>
            <div class="card-body">
              <h6 class="mb-0 ">Completed Tasks</h6>
              <p class="text-sm ">Last Campaign Performance</p>
              <hr class="dark horizontal">
              <div class="d-flex ">
                <i class="material-icons text-sm my-auto me-1">schedule</i>
                <p class="mb-0 text-sm">just updated</p>
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
      <script>
         var win = navigator.platform.indexOf('Win') > -1;
         if (win && document.querySelector('#sidenav-scrollbar')) {
           var options = {
             damping: '0.5'
           }
           Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
         }
      </script>
      <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc --><script src="./assets/js/material-dashboard.min.js?v=3.1.0"></script>
   </body>
</html>