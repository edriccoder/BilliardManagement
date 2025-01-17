<?php
// admin_dashboard.php
session_start();
include 'conn.php';
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
$username = htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8');
$admin_id = htmlspecialchars($_SESSION['user_id'], ENT_QUOTES, 'UTF-8');

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
    <title>Billiard Management - Admin Chat</title>
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
    <!-- Custom styles -->
    <style>
    #user-list {
    height: 80vh;
    overflow-y: auto;
}

#messages {
    height: 60vh;
}

.message {
    margin-bottom: 15px;
}

.message.sent {
    text-align: right;
}

.message.received {
    text-align: left;
}

.message .text {
    display: inline-block;
    padding: 10px 15px;
    border-radius: 20px;
    max-width: 70%;
}

.message.sent .text {
    background-color: #0d6efd;
    color: white;
}

.message.received .text {
    background-color: #f1f0f0;
    color: black;
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
                <span class="ms-1 font-weight-bold text-white">Admin</span>
            </a>
        </div>
        <hr class="horizontal light mt-0 mb-2">
        <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
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
                <!-- Add more nav items as needed -->
            </ul>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content border-radius-lg">
        <!-- Navbar -->
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" data-scroll="true">
            <div class="container-fluid py-1 px-3">
                <nav aria-label="breadcrumb">
                    <!-- Breadcrumb if needed -->
                </nav>
                <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
                    <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                        <!-- Search bar or other elements -->


                    </div>
                    <ul class="navbar-nav justify-content-end">
                        <!-- Navbar items -->
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
        <button class='btn btn-primary editBtn' data-toggle='modal' data-target='#showReviewsModal'>Show Reviews</button>
        <!-- Page Content -->
        <div class="container-fluid py-4">
            <!-- Chat Interface -->
            <div class="row">
                <!-- User List Sidebar -->
                <div class="col-md-4 col-lg-3 border-end" id="user-list">
                    <h5 class="mt-3">Chats</h5>
                    <div class="list-group" id="users">
                        <!-- Dynamically populated user list -->
                    </div>
                </div>
            
                <!-- Chat Window -->
                <div class="col-md-8 col-lg-9 d-flex flex-column" id="chat-window">
                    <div class="d-flex justify-content-between align-items-center border-bottom p-3">
                        <h5 id="chat-with">Select a user to chat</h5>
                        <span id="chat-timestamp"></span>
                    </div>
                    <div class="flex-grow-1 overflow-auto p-3" id="messages">
                        <!-- Chat messages will appear here -->
                    </div>
                    <div class="p-3 border-top">
                        <form id="message-form">
                            <div class="input-group">
                                <input type="text" id="message-input" class="form-control" placeholder="Type your message..." required>
                                <button class="btn btn-primary" type="submit">Send</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


        </div>
        <!-- /.container-fluid -->

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
    </main>
    <!-- Modal Structure -->
    <div class="modal fade" id="showReviewsModal" tabindex="-1" role="dialog" aria-labelledby="showReviewsModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="showReviewsModalLabel">Reviews</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <!-- Table wrapper for responsiveness -->
            <div class="table-responsive">
              <table class="table table-bordered table-striped" id="reviewsTable">
                <thead class="thead-light">
                  <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Service Rating</th>
                    <th>Facilities Rating</th>
                    <th>Tournaments Rating</th>
                    <th>Comments</th>
                    <th>Created At</th>
                  </tr>
                </thead>
                <tbody>
                  <!-- Dynamic content will be loaded here -->
                </tbody>
              </table>
            </div>
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
    <!-- Material Dashboard JS -->
    <script src="./assets/js/material-dashboard.min.js?v=3.1.0"></script>
    <!-- Custom Scripts -->
    <script>
    
    
    $(document).ready(function() {
    const adminId = <?php echo json_encode($admin_id); ?>;
    let currentChatUser = null;

    // Function to fetch and display user list
    function loadUsers() {
        $.ajax({
            url: 'fetch_users.php',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#users').empty();
                    response.users.forEach(function(user) {
                        const profilePic = user.profile_pic ? `img/profilepicture/${user.profile_pic}` : 'img/default_avatar.png';
                        const userItem = `
                            <a href="#" class="list-group-item list-group-item-action d-flex align-items-center" data-user-id="${user.id}">
                                <img src="${profilePic}" alt="${user.username}" class="rounded-circle me-2" width="40" height="40">
                                <div>
                                    <strong>${user.username}</strong>
                                </div>
                            </a>
                        `;
                        $('#users').append(userItem);
                    });
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Failed to fetch users.', 'error');
            }
        });
    }

    // Function to fetch and display messages
    function loadMessages(userId, username) {
        $.ajax({
            url: 'fetch_messages.php',
            method: 'POST',
            data: { chat_with: userId },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#messages').empty();
                    response.messages.forEach(function(msg) {
                        const messageClass = msg.sender_id == adminId ? 'sent' : 'received';
                        const messageElement = `
                            <div class="message ${messageClass}">
                                <div class="text">
                                    ${msg.message}
                                </div>
                                <div class="text-muted" style="font-size: 0.8em;">
                                    ${msg.timestamp}
                                </div>
                            </div>
                        `;
                        $('#messages').append(messageElement);
                    });
                    $('#chat-with').text(username);
                    currentChatUser = userId;
                    // Scroll to bottom
                    $('#messages').scrollTop($('#messages')[0].scrollHeight);
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Failed to fetch messages.', 'error');
            }
        });
    }

    // Function to send a new message
    $('#message-form').submit(function(e) {
        e.preventDefault();
        const message = $('#message-input').val().trim();
        if (!message || !currentChatUser) {
            return;
        }

        $.ajax({
            url: 'sendMessage.php',
            method: 'POST',
            data: { receiver_id: currentChatUser, message: message },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#message-input').val('');
                    loadMessages(currentChatUser, $('#chat-with').text());
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Failed to send message.', 'error');
            }
        });
    });

    // Event delegation for user list clicks
    // Event delegation for user list clicks
    $('#users').on('click', 'a', function(e) {
        e.preventDefault();
        const userId = $(this).data('user-id');
        const username = $(this).find('strong').text();
    
        if (userId > 0) { // Ensure userId is valid
            loadMessages(userId, username);
        } else {
            Swal.fire('Error', 'Invalid user selected.', 'error');
        }
    });


    // Initial load
    loadUsers();

    // Optional: Refresh user list and messages every few seconds for real-time effect
    setInterval(function() {
        loadUsers();
        if (currentChatUser) {
            loadMessages(currentChatUser, $('#chat-with').text());
        }
    }, 5000); // Refresh every 5 seconds
});

document.querySelector('.editBtn').addEventListener('click', function() {
  fetch('get_reviews.php')
    .then(response => response.json())
    .then(data => {
      const reviewsTableBody = document.querySelector('#reviewsTable tbody');
      reviewsTableBody.innerHTML = ''; // Clear existing content

      data.forEach(review => {
        const row = `
          <tr>
            <td>${review.id}</td>
            <td>${review.username}</td>
            <td>${review.rating_service}</td>
            <td>${review.rating_facilities}</td>
            <td>${review.rating_tournaments}</td>
            <td>${review.comments}</td>
            <td>${review.created_at}</td>
          </tr>
        `;
        reviewsTableBody.innerHTML += row;
      });
    })
    .catch(error => console.error('Error fetching reviews:', error));
});

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
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
      <!-- Bootstrap 5 JS Bundle (includes Popper.js) -->
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
