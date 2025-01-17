<?php
// user_chat.php

session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    // Redirect to login page if session variables are not set
    header("Location: index.php");
    exit();
}
$username = htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8');
$user_id = htmlspecialchars($_SESSION['user_id'], ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="./assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="./assets/img/favicon.png">
    <title>Billiard Management - Chat with Admin</title>
    <!-- Fonts and icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700" />
    <!-- Nucleo Icons -->
    <link href="./assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="./assets/css/nucleo-svg.css" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-p1c6k5pNtIPzEZZzg/nCXe5CnCFp67nh5/1Izxlbi2G7D1BJv0iF3tyngLCfPth6D9GWu1Zmowx41xgf1JjUwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <!-- CSS Files -->
    <link id="pagestyle" href="./assets/css/material-dashboard.css?v=3.1.0" rel="stylesheet" />
    <!-- Nepcha Analytics (nepcha.com) -->
    <script defer data-site="YOUR_DOMAIN_HERE" src="https://api.nepcha.com/js/nepcha-analytics.js"></script>
    
    <!-- Custom styles for this template-->
    <style>
        /* Enhanced Chat Container */
        .chat-container {
            position: fixed;
            bottom: 80px;
            right: 20px;
            width: 350px;
            max-height: 500px;
            background-color: #ffffff;
            border: none;
            border-radius: 15px;
            display: none;
            flex-direction: column;
            box-shadow: 0 8px 16px rgba(0,0,0,0.3);
            z-index: 1050;
            font-family: 'Roboto', sans-serif;
        }

        /* Chat Header */
        .chat-header {
            background-color: #6200ea; /* Material Purple */
            color: #ffffff;
            padding: 15px;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Chat Messages */
        .chat-messages {
            padding: 15px;
            overflow-y: auto;
            flex-grow: 1;
            background-color: #f5f5f5;
        }

        /* Chat Input */
        .chat-input {
            display: flex;
            padding: 10px 15px;
            border-top: 1px solid #e0e0e0;
            background-color: #fafafa;
        }

        .chat-input input {
            flex-grow: 1;
            padding: 10px;
            border: 1px solid #cccccc;
            border-radius: 25px;
            outline: none;
            transition: border-color 0.3s;
        }

        .chat-input input:focus {
            border-color: #6200ea;
        }

        .chat-input button {
            margin-left: 10px;
            background-color: #6200ea;
            border: none;
            color: #ffffff;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .chat-input button:hover {
            background-color: #3700b3;
        }

        /* Chat Toggle Button */
        .chat-toggle {
            position: fixed;
            bottom: 20px;
            right: 20px;
            border-radius: 50%;
            width: 70px;
            height: 70px;
            z-index: 1050;
            background-color: #6200ea;
            color: #ffffff;
            border: none;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            font-size: 1.2em;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .chat-toggle:hover {
            background-color: #3700b3;
        }

        /* Message Styles */
        .message {
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 10px;
            max-width: 80%;
            word-wrap: break-word;
        }

        .user-message {
            background-color: #e1bee7; /* Light Purple */
            align-self: flex-end;
            text-align: right;
        }

        .admin-message {
            background-color: #bbdefb; /* Light Blue */
            align-self: flex-start;
            text-align: left;
        }

        .timestamp {
            font-size: 0.75em;
            color: #757575;
            margin-top: 5px;
        }

        /* Reviews Section */
        #reviewsList .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        #reviewsList .card-title {
            color: #6200ea;
        }

        #reviewsList .card-text {
            color: #424242;
        }

        #reviewsList .text-muted {
            color: #9e9e9e !important;
        }
    </style>
    <!-- Pass PHP session data to JavaScript -->
    <script>
        const userData = {
            username: "<?php echo $username; ?>",
            user_id: "<?php echo $user_id; ?>"
        };
    </script>
</head>
<body class="g-sidenav-show bg-gray-100">
    <!-- Sidebar -->
    <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 bg-gradient-dark" id="sidenav-main">
        <div class="sidenav-header">
            <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
            <a class="navbar-brand m-0" href="#">
                <img src="./img/admin.png" class="navbar-brand-img h-100" alt="main_logo">
                <span class="ms-1 font-weight-bold text-white"><?php echo $username; ?></span>
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
                    <a class="nav-link text-white" href="feedback.php">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">feedback</i>
                        </div>
                        <span class="nav-link-text ms-1">My Feedback</span>
                    </a>
                </li>
                <li class="nav-item mt-3">
                    <!-- Additional nav items can be added here -->
                </li>
            </ul>
        </div>
    </aside>
    
    <!-- Main Content -->
    <main class="main-content border-radius-lg">
        <!-- Navbar -->
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" data-scroll="true">
            <div class="container-fluid py-1 px-3">
                <nav aria-label="breadcrumb">
                    <!-- Breadcrumb can be added here -->
                </nav>
                <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
                    <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                        <!-- Search bar or other elements can be added here -->
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
                        <li class="nav-item px-3 d-flex align-items-center">
                            <a href="javascript:;" class="nav-link text-body p-0">
                                <i class="fa fa-cog fixed-plugin-button-nav cursor-pointer"></i>
                            </a>
                        </li>
                        <li class="nav-item dropdown pe-2 d-flex align-items-center">
                            <a href="javascript:;" class="nav-link text-body p-0" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-bell cursor-pointer"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end px-2 py-3 me-sm-n4" aria-labelledby="dropdownMenuButton">
                                <li class="mb-2">
                                    <a class="dropdown-item border-radius-md" href="javascript:;">
                                        <div class="d-flex py-1">
                                            <div class="my-auto">
                                                <img src="./assets/img/team-2.jpg" class="avatar avatar-sm me-3" alt="Profile Image">
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
                                                <img src="./assets/img/small-logos/logo-spotify.svg" class="avatar avatar-sm bg-gradient-dark me-3" alt="Logo">
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="text-sm font-weight-normal mb-1">
                                                    <span class="font-weight-bold">New album</span> by Travis Scott
                                                </h6>
                                                <p class="text-xs text-secondary mb-0">
                                                    <i class="fa fa-clock me-1"></i>
                                                    1 day ago
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item border-radius-md" href="javascript:;">
                                        <div class="d-flex py-1">
                                            <div class="avatar avatar-sm bg-gradient-secondary me-3 my-auto">
                                                <svg width="12px" height="12px" viewBox="0 0 43 36" xmlns="http://www.w3.org/2000/svg">
                                                    <title>credit-card</title>
                                                    <g fill="#FFFFFF" fill-rule="nonzero">
                                                        <path d="M43,10.7482083 L43,3.58333333 C43,1.60354167 41.3964583,0 39.4166667,0 L3.58333333,0 C1.60354167,0 0,1.60354167 0,3.58333333 L0,10.7482083 L43,10.7482083 Z" opacity="0.593633743"></path>
                                                        <path d="M0,16.125 L0,32.25 C0,34.2297917 1.60354167,35.8333333 3.58333333,35.8333333 L39.4166667,35.8333333 C41.3964583,35.8333333 43,34.2297917 43,32.25 L43,16.125 L0,16.125 Z M19.7083333,26.875 L7.16666667,26.875 L7.16666667,23.2916667 L19.7083333,23.2916667 L19.7083333,26.875 Z M35.8333333,26.875 L28.6666667,26.875 L28.6666667,23.2916667 L35.8333333,23.2916667 L35.8333333,26.875 Z"></path>
                                                    </g>
                                                </svg>
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="text-sm font-weight-normal mb-1">
                                                    Payment successfully completed
                                                </h6>
                                                <p class="text-xs text-secondary mb-0">
                                                    <i class="fa fa-clock me-1"></i>
                                                    2 days ago
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

        <!-- Page Content -->
        <div class="container-fluid">
            <!-- Chat Widget -->
            <div class="chat-container">
                <div class="chat-header">
                    <h5>Chat with Admin</h5>
                    <button id="closeChat" class="btn btn-sm btn-light"><i class="fas fa-times"></i></button>
                </div>
                <div class="chat-messages" id="chatMessages">
                    <!-- Messages will appear here -->
                </div>
                <div class="chat-input">
                    <input type="text" id="chatInput" class="form-control" placeholder="Type your message..." />
                    <button id="sendChat" class="btn btn-primary"><i class="fas fa-paper-plane"></i></button>
                </div>
            </div>

            <!-- Provide Feedback Form -->
            <div class="container mt-5">
                <h2>Provide Your Feedback</h2>
                <form id="reviewForm">
                    <div class="mb-3">
                        <label for="ratingService" class="form-label">Rate Our Services:</label>
                        <select class="form-select" id="ratingService" name="rating_service" required>
                            <option value="" selected>Select rating</option>
                            <option value="1">1 - Very Poor</option>
                            <option value="2">2 - Poor</option>
                            <option value="3">3 - Average</option>
                            <option value="4">4 - Good</option>
                            <option value="5">5 - Excellent</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="ratingFacilities" class="form-label">Rate Our Facilities:</label>
                        <select class="form-select" id="ratingFacilities" name="rating_facilities" required>
                            <option value="" selected>Select rating</option>
                            <option value="1">1 - Very Poor</option>
                            <option value="2">2 - Poor</option>
                            <option value="3">3 - Average</option>
                            <option value="4">4 - Good</option>
                            <option value="5">5 - Excellent</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="ratingTournaments" class="form-label">Rate Our Tournaments:</label>
                        <select class="form-select" id="ratingTournaments" name="rating_tournaments" required>
                            <option value="" selected>Select rating</option>
                            <option value="1">1 - Very Poor</option>
                            <option value="2">2 - Poor</option>
                            <option value="3">3 - Average</option>
                            <option value="4">4 - Good</option>
                            <option value="5">5 - Excellent</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="comments" class="form-label">Additional Comments:</label>
                        <textarea class="form-control" id="comments" name="comments" rows="3" placeholder="Your comments here..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Review</button>
                </form>
                
                <button class="chat-toggle">
                    <i class="fas fa-comments"></i>
                </button>

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

    <!-- Fixed Plugin (Settings) -->
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
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap Bundle (includes Popper) -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <!-- Material Dashboard JS -->
    <script src="./assets/js/material-dashboard.min.js?v=3.1.0"></script>
    <!-- Your Custom Scripts -->
<script>

const chatContainer = document.querySelector('.chat-container');
    const chatToggle = document.querySelector('.chat-toggle');
    const closeChat = document.getElementById('closeChat');
    const sendChatBtn = document.getElementById('sendChat');
    const chatInput = document.getElementById('chatInput');
    const chatMessages = document.getElementById('chatMessages');

    // Show Chat
    chatToggle.addEventListener('click', () => {
        chatContainer.style.display = 'flex';
        fetchMessages(); // Fetch messages when chat is opened
    });

    // Close Chat
    closeChat.addEventListener('click', () => {
        chatContainer.style.display = 'none';
    });

    // Send Message Function
    function sendMessage() {
        const message = chatInput.value.trim();
        if (message === '') return;

        // Disable button to prevent multiple clicks
        sendChatBtn.disabled = true;

        // Send AJAX request to send_message.php
        $.ajax({
            url: 'send_message.php',
            type: 'POST',
            data: { message },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    appendMessage(userData.username, message, new Date().toISOString(), false);
                    chatInput.value = '';
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message,
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to send message.',
                });
            },
            complete: function() {
                sendChatBtn.disabled = false;
            }
        });
    }

    // Fetch Messages Function
    function fetchMessages() {
        $.ajax({
            url: 'get_messages.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    chatMessages.innerHTML = ''; // Clear existing messages
                    response.messages.forEach(msg => {
                        const isAdmin = msg.receiver_id === <?php echo json_encode($user_id); ?>;
                        const senderName = isAdmin ? 'Admin' : msg.username;
                        appendMessage(senderName, msg.message, msg.timestamp, isAdmin);
                    });
                } else {
                    console.error(response.message);
                }
            },
            error: function() {
                console.error('Failed to fetch messages.');
            }
        });
    }

    // Event Listener for Send Button
    sendChatBtn.addEventListener('click', sendMessage);

    // Event Listener for Enter Key
    chatInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            sendMessage();
        }
    });

    // Append Message to Chat
    function appendMessage(sender, message, time, isAdmin) {
        const msgDiv = document.createElement('div');
        msgDiv.classList.add('message', isAdmin ? 'admin-message' : 'user-message');
        msgDiv.innerHTML = `
            <strong>${sanitizeHTML(sender)}:</strong> ${sanitizeHTML(message)}
            <div class="timestamp">${formatTimestamp(time)}</div>
        `;
        chatMessages.appendChild(msgDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Format Timestamp to a readable format
    function formatTimestamp(timestamp) {
        const date = new Date(timestamp);
        return date.toLocaleString();
    }

    // Poll for new messages every 5 seconds
    setInterval(() => {
        if (chatContainer.style.display === 'flex') {
            fetchMessages();
        }
    }, 5000);

    // Handle Review Form Submission
    const reviewForm = document.getElementById('reviewForm');
    const reviewsList = document.getElementById('reviewsList');

    reviewForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(reviewForm);
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'submit_review.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function () {
            if (this.status === 200) {
                try {
                    const response = JSON.parse(this.responseText);
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                        });
                        reviewForm.reset();
                        loadReviews(); // Reload reviews after submission
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: response.message,
                        });
                    }
                } catch (e) {
                    console.error('Invalid JSON response:', e);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An unexpected error occurred.',
                    });
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to submit review.',
                });
            }
        };
        xhr.onerror = function () {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Network error.',
            });
        };
        xhr.send(new URLSearchParams(formData).toString());
    });

    // Load Reviews
    function loadReviews() {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'get_reviews.php', true);
        xhr.onload = function () {
            if (this.status === 200) {
                try {
                    const response = JSON.parse(this.responseText);
                    if (response.status === 'success') {
                        displayReviews(response.reviews);
                    } else {
                        reviewsList.innerHTML = '<p>No reviews found.</p>';
                    }
                } catch (e) {
                    console.error('Invalid JSON response:', e);
                    reviewsList.innerHTML = '<p>Failed to load reviews.</p>';
                }
            } else {
                reviewsList.innerHTML = '<p>Failed to load reviews.</p>';
            }
        };
        xhr.onerror = function () {
            reviewsList.innerHTML = '<p>Network error.</p>';
        };
        xhr.send();
    }

    // Display Reviews
    function displayReviews(reviews) {
        if (reviews.length === 0) {
            reviewsList.innerHTML = '<p>No reviews yet.</p>';
            return;
        }

        let html = '';
        reviews.forEach(review => {
            html += `
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">${sanitizeHTML(review.username)}</h5>
                        <p class="card-text">
                            <strong>Services:</strong> ${renderStars(review.rating_service)}<br>
                            <strong>Facilities:</strong> ${renderStars(review.rating_facilities)}<br>
                            <strong>Tournaments:</strong> ${renderStars(review.rating_tournaments)}
                        </p>
                        ${review.comments ? `<p class="card-text"><em>${sanitizeHTML(review.comments)}</em></p>` : ''}
                        <p class="card-text"><small class="text-muted">Reviewed on ${new Date(review.created_at).toLocaleDateString()}</small></p>
                    </div>
                </div>
            `;
        });
        reviewsList.innerHTML = html;
    }

    // Render Stars based on rating
    function renderStars(rating) {
        let stars = '';
        for (let i = 1; i <= 5; i++) {
            stars += i <= rating ? '<i class="fas fa-star text-warning"></i> ' : '<i class="far fa-star text-warning"></i> ';
        }
        return stars;
    }

    // Sanitize HTML to prevent XSS
    function sanitizeHTML(str) {
        var temp = document.createElement('div');
        temp.textContent = str;
        return temp.innerHTML;
    }

    // Format Timestamp to a readable format
    function formatTimestamp(timestamp) {
        const date = new Date(timestamp);
        return date.toLocaleString();
    }

    // **New Functions: sendMessage and fetchMessages**

    /**
     * Function to send a message to the admin.
     */
    document.getElementById('sendChat').addEventListener('click', sendMessage);


</script>

</body>
</html>
