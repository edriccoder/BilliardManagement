<?php
// Include database connection
include 'conn.php';

// Fetch notifications and unread count
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

$sql = "SELECT id, type, description, datetime, photo, name, caused_by, contact_number FROM reports ORDER BY datetime DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle Add/Edit/Delete/Inflow/Outflow form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    $itemId = $_POST['item_id'] ?? null;
    $itemName = $_POST['item_name'] ?? '';
    $quantity = $_POST['quantity'] ?? 0;
    $description = $_POST['description'] ?? '';
    $date = $_POST['date'] ?? date('Y-m-d');  // Use today's date if not provided
    $image = $_FILES['image']['name'] ?? '';
    $targetDir = "inventory_image/";
    $targetFile = $targetDir . basename($image);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    if ($action == 'add') {
        // Validate image file type for adding a new item
        if (in_array($imageFileType, ['jpg', 'jpeg', 'png'])) {
            // Upload image
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $sql = "INSERT INTO inventory (item_name, quantity, description, image, date) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$itemName, $quantity, $description, $image, $date]);
                echo "<script>alert('Item added successfully.');</script>";
            } else {
                echo "<script>alert('Failed to upload image.');</script>";
            }
        } else {
            echo "<script>alert('Invalid image format. Only JPG, JPEG, and PNG are allowed.');</script>";
        }
    } elseif ($action == 'edit') {
        // Handle edit item details
        if ($image) {
            // Validate image file type for updating the image
            if (in_array($imageFileType, ['jpg', 'jpeg', 'png'])) {
                // Upload new image
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                    $sql = "UPDATE inventory SET item_name = ?, quantity = ?, description = ?, image = ? WHERE item_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$itemName, $quantity, $description, $image, $itemId]);
                    echo "<script>alert('Item updated successfully with new image.');</script>";
                } else {
                    echo "<script>alert('Failed to upload new image.');</script>";
                }
            } else {
                echo "<script>alert('Invalid image format. Only JPG, JPEG, and PNG are allowed.');</script>";
            }
        } else {
            // Update without changing the image
            $sql = "UPDATE inventory SET item_name = ?, quantity = ?, description = ? WHERE item_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$itemName, $quantity, $description, $itemId]);
            echo "<script>alert('Item updated successfully.');</script>";
        }
    } elseif ($action == 'delete') {
        // Handle delete item
        if ($itemId) {
            // Optionally, delete the image file from the server
            $stmt = $conn->prepare("SELECT image FROM inventory WHERE item_id = ?");
            $stmt->execute([$itemId]);
            $imageToDelete = $stmt->fetchColumn();
            if ($imageToDelete && file_exists($targetDir . $imageToDelete)) {
                unlink($targetDir . $imageToDelete);
            }

            $sql = "DELETE FROM inventory WHERE item_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$itemId]);
            echo "<script>alert('Item deleted successfully.');</script>";
        }
    } elseif ($action == 'inflow') {
        // Add an inflow transaction
        if ($itemId && $quantity > 0) {
            $sql = "INSERT INTO inventory_transactions (item_id, transaction_type, quantity, description) VALUES (?, 'inflow', ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$itemId, $quantity, $description]);

            // Update the inventory quantity
            $sqlUpdate = "UPDATE inventory SET quantity = quantity + ? WHERE item_id = ?";
            $stmtUpdate = $conn->prepare($sqlUpdate);
            $stmtUpdate->execute([$quantity, $itemId]);

            echo "<script>alert('Inflow transaction added successfully.');</script>";
        } else {
            echo "<script>alert('Invalid inflow transaction data.');</script>";
        }
    } elseif ($action == 'outflow') {
        // Add an outflow transaction
        if ($itemId && $quantity > 0) {
            // Get current quantity
            $sqlGetQty = "SELECT quantity FROM inventory WHERE item_id = ?";
            $stmtGetQty = $conn->prepare($sqlGetQty);
            $stmtGetQty->execute([$itemId]);
            $currentQty = $stmtGetQty->fetchColumn();

            if ($currentQty >= $quantity) {
                $sql = "INSERT INTO inventory_transactions (item_id, transaction_type, quantity, description) VALUES (?, 'outflow', ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$itemId, $quantity, $description]);

                // Update the inventory quantity
                $sqlUpdate = "UPDATE inventory SET quantity = quantity - ? WHERE item_id = ?";
                $stmtUpdate = $conn->prepare($sqlUpdate);
                $stmtUpdate->execute([$quantity, $itemId]);

                echo "<script>alert('Outflow transaction added successfully.');</script>";
            } else {
                // Not enough stock, handle the error
                echo "<script>alert('Not enough stock for this outflow transaction.');</script>";
            }
        } else {
            echo "<script>alert('Invalid outflow transaction data.');</script>";
        }
    }
}

// Retrieve inventory items
$sqlInventory = "SELECT item_id, item_name, quantity, description, image FROM inventory";
$stmtInventory = $conn->prepare($sqlInventory);
$stmtInventory->execute();
$inventoryItems = $stmtInventory->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Head content -->
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Inventory Management - Admin</title>

    <!-- Fonts and Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700" />
    <link href="./assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="./assets/css/nucleo-svg.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Material Dashboard CSS -->
    <link id="pagestyle" href="./assets/css/material-dashboard.css?v=3.1.0" rel="stylesheet" />

    <!-- FullCalendar CSS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        .modal .form-control, 
        .modal .form-control-file, 
        .modal .custom-select {
            border: 1px solid #ccc;
        }
        .modal .form-control:focus, 
        .modal .custom-select:focus {
            border-color: #80bdff;
            box-shadow: none;
        }

        /* Custom z-index adjustments to ensure modals appear above navigation */
        /* Bootstrap 4 default modal z-index is 1050, adjust if necessary */
        .modal {
            z-index: 2000; /* Higher than any other element */
        }
        .modal-backdrop {
            z-index: 1999; /* Just below the modal */
        }

        /* If Material Dashboard or other CSS sets higher z-index for navbar, ensure modals are above */
        .navbar, .sidenav {
            z-index: 1000; /* Ensure navbar and sidenav have lower z-index */
        }
    </style>
</head>
<body class="g-sidenav-show bg-gray-100">
    <!-- Sidebar -->
    <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 bg-gradient-dark" id="sidenav-main">
        <div class="sidenav-header">
            <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-xl-none" aria-hidden="true" id="iconSidenav"></i>
            <a class="navbar-brand m-0" href="#">
                <img src="./img/admin.png" class="navbar-brand-img h-100" alt="main_logo">
                <span class="ms-1 font-weight-bold text-white">Admin</span>
            </a>
        </div>
        <hr class="horizontal light mt-0 mb-2">
        <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
            <ul class="navbar-nav">
                <?php
                // Define navigation links with active state
                $navItems = [
                    'admin_dashboard.php' => ['icon' => 'dashboard', 'text' => 'Dashboard'],
                    'billiard_table.php' => ['icon' => 'table_view', 'text' => 'Billiard Tables'],
                    'manage_user.php' => ['icon' => 'person', 'text' => 'User Account Management'],
                    'manage_tournament.php' => ['icon' => 'flag', 'text' => 'Billiard Tournament Scheduling Management'],
                    'inventory_management.php' => ['icon' => 'inventory', 'text' => 'Inventory Management'],
                    'admin_announcement.php' => ['icon' => 'campaign', 'text' => 'Announcement Management'],
                    'admin_booking.php' => ['icon' => 'book', 'text' => 'Reservation Management'],
                    'admin_reports.php' => ['icon' => 'bar_chart', 'text' => 'Reports & Analytics'],
                    'admin_feedback.php' => ['icon' => 'feedback', 'text' => 'Manage Feedback'],
                ];

                $currentPage = basename($_SERVER['PHP_SELF']);

                foreach ($navItems as $href => $item) {
                    $activeClass = ($currentPage == $href) ? 'active' : '';
                    echo '<li class="nav-item">
                            <a class="nav-link text-white ' . $activeClass . '" href="' . $href . '">
                                <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="material-icons opacity-10">' . $item['icon'] . '</i>
                                </div>
                                <span class="nav-link-text ms-1">' . $item['text'] . '</span>
                            </a>
                          </li>';
                }
                ?>
            </ul>
        </div>
    </aside>
    <!-- End Sidebar -->

    <!-- Main Content -->
    <main class="main-content border-radius-lg">
        <!-- Navbar -->
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
            <div class="container-fluid py-1 px-3">
                <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
                    <ul class="navbar-nav ms-md-auto pe-md-3 d-flex align-items-center">
                        <!-- Notifications Dropdown -->
                        <li class="nav-item dropdown pe-2">
                            <a href="#" class="nav-link text-white" id="notificationsDropdown" role="button" data-toggle="dropdown" aria-expanded="false">
                                <i class="material-icons">notifications</i>
                                <?php if ($unreadCount > 0) { ?>
                                    <span class="badge badge-sm bg-danger"><?php echo $unreadCount; ?></span>
                                <?php } ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end px-2 py-3 me-sm-n4" aria-labelledby="notificationsDropdown">
                                <?php if (!empty($notifications)) {
                                    foreach ($notifications as $notification) {
                                        $badge = $notification['is_read'] ? '' : '<span class="badge badge-sm bg-danger">New</span>';
                                        echo '<li class="mb-2">
                                                <a class="dropdown-item border-radius-md" href="#">
                                                    <div class="d-flex py-1">
                                                        <div class="my-auto">
                                                            <i class="material-icons text-warning me-2">notifications</i>
                                                        </div>
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="text-sm font-weight-normal mb-1">
                                                                <span class="font-weight-bold">' . htmlspecialchars($notification['message']) . '</span>
                                                            </h6>
                                                            <p class="text-xs text-secondary mb-0">
                                                                ' . htmlspecialchars($notification['created_at']) . '
                                                            </p>
                                                        </div>
                                                        ' . $badge . '
                                                    </div>
                                                </a>
                                              </li>';
                                    }
                                } else {
                                    echo '<li><a class="dropdown-item border-radius-md" href="#">No notifications</a></li>';
                                } ?>
                            </ul>
                        </li>
                        <!-- User Profile Dropdown -->
                        <li class="nav-item dropdown pe-2">
                            <a href="#" class="nav-link text-white" id="userDropdown" role="button" data-toggle="dropdown" aria-expanded="false">
                                <i class="material-icons">person</i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end px-2 py-3 me-sm-n4" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item border-radius-md" href="#">Profile</a></li>
                                <li><a class="dropdown-item border-radius-md" href="#">Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item border-radius-md" href="#">Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- End Navbar -->

        <!-- Page Content -->
        <div class="container-fluid py-4">
            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Inventory Management</h1>
                <div>
                    <button class='btn btn-primary' data-toggle='modal' data-target='#addItemModal'>Add Item</button>
                    <button class='btn btn-info' data-toggle='modal' data-target='#showReportModal'>Show Report</button>
                    <form method="GET" action="generate_inventory_report.php" class="form-inline d-inline-block ml-3">
                        <div class="form-group mr-2">
                            <label for="start_date" class="mr-2">Start Date:</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" required>
                        </div>
                        <div class="form-group mr-2">
                            <label for="end_date" class="mr-2">End Date:</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-info">Filter and Generate Report</button>
                    </form>
                </div>
            </div>
            <!-- Inventory Items Table -->
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">Item ID</th>
                                    <th scope="col">Item Name</th>
                                    <th scope="col">Quantity</th>
                                    <th scope="col">Description</th>
                                    <th scope="col">Image</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($inventoryItems)) {
                                    foreach ($inventoryItems as $item) {
                                        echo '<tr>';
                                        echo '<th scope="row">' . htmlspecialchars($item["item_id"]) . '</th>';
                                        echo '<td>' . htmlspecialchars($item["item_name"]) . '</td>';
                                        echo '<td>' . htmlspecialchars($item["quantity"]) . '</td>';
                                        echo '<td>' . htmlspecialchars($item["description"]) . '</td>';
                                        echo '<td>';
                                        if (!empty($item["image"]) && file_exists('inventory_image/' . $item["image"])) {
                                            echo '<img src="inventory_image/' . htmlspecialchars($item["image"]) . '" alt="Item image" class="img-fluid shadow border-radius-xl" style="max-width: 100px;">';
                                        } else {
                                            echo 'No Image';
                                        }
                                        echo '</td>';
                                        echo '<td class="align-middle">';
                                        echo '<button type="button" class="btn btn-outline-primary btn-sm mb-1" data-toggle="modal" onclick=\'openEditModal(' . json_encode($item) . ')\'>Edit</button> ';
                                        echo '<form method="POST" style="display:inline-block;" onsubmit="return confirm(\'Are you sure you want to delete this item?\');">';
                                        echo '<input type="hidden" name="action" value="delete">';
                                        echo '<input type="hidden" name="item_id" value="' . $item["item_id"] . '">';
                                        echo '<button type="submit" class="btn btn-outline-danger btn-sm mb-1">Delete</button>';
                                        echo '</form> ';
                                        echo '<button type="button" class="btn btn-outline-success btn-sm mb-1" onclick=\'openInflowModal(' . json_encode($item) . ')\'>Inflow</button> ';
                                        echo '<button type="button" class="btn btn-outline-warning btn-sm mb-1" onclick=\'openOutflowModal(' . json_encode($item) . ')\'>Outflow</button> ';
                                        echo '<button type="button" class="btn btn-outline-info btn-sm mb-1" onclick=\'openTransactionsModal(' . json_encode($item) . ')\'>View Transactions</button>';
                                        echo '</td>';
                                        echo '</tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="6" class="text-center">No items found</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Footer (optional) -->
            <!-- ... -->
        </div>
    </main>
    <!-- End Main Content -->

    <!-- Modals -->
    <!-- Add Item Modal -->
    <div class="modal fade" id="addItemModal" tabindex="-1" role="dialog" aria-labelledby="addItemModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addItemModalLabel">Add New Item</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addItemForm" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="add">
                        <div class="form-group">
                            <label for="item_name">Item Name<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="item_name" name="item_name" required>
                        </div>
                        <div class="form-group">
                            <label for="quantity">Quantity<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="quantity" name="quantity" min="0" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description<span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="image">Image<span class="text-danger">*</span></label>
                            <input type="file" class="form-control-file" id="image" name="image" accept=".jpg, .jpeg, .png" required>
                        </div>
                        <div class="form-group">
                            <label for="date">Date<span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="date" name="date" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Item</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Add Item Modal -->

    <!-- Edit Item Modal -->
    <div class="modal fade" id="editItemModal" tabindex="-1" role="dialog" aria-labelledby="editItemModalLabel" aria-hidden="true">
        <!-- Edit Item Modal Content -->
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <!-- Edit Item Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title" id="editItemModalLabel">Edit Item</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <!-- Edit Item Modal Body -->
                <div class="modal-body">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="item_id" id="edit_item_id">
                        <div class="form-group">
                            <label for="edit_item_name">Item Name<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_item_name" name="item_name" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_quantity">Quantity<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="edit_quantity" name="quantity" min="0" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_description">Description<span class="text-danger">*</span></label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="edit_image">Image (Leave blank to keep current)</label>
                            <input type="file" class="form-control-file" id="edit_image" name="image" accept=".jpg, .jpeg, .png">
                        </div>
                        <button type="submit" class="btn btn-primary">Update Item</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Edit Item Modal -->

    <!-- Inflow Modal -->
    <div class="modal fade" id="inflowModal" tabindex="-1" role="dialog" aria-labelledby="inflowModalLabel" aria-hidden="true">
        <!-- Inflow Modal Content -->
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <!-- Inflow Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title" id="inflowModalLabel">Add Inflow</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <!-- Inflow Modal Body -->
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="inflow">
                        <input type="hidden" name="item_id" id="inflow_item_id">
                        <div class="form-group">
                            <label for="inflow_quantity">Quantity<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="inflow_quantity" name="quantity" min="1" required>
                        </div>
                        <div class="form-group">
                            <label for="inflow_description">Description</label>
                            <textarea class="form-control" id="inflow_description" name="description" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">Add Inflow</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Inflow Modal -->

    <!-- Outflow Modal -->
    <div class="modal fade" id="outflowModal" tabindex="-1" role="dialog" aria-labelledby="outflowModalLabel" aria-hidden="true">
        <!-- Outflow Modal Content -->
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <!-- Outflow Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title" id="outflowModalLabel">Add Outflow</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <!-- Outflow Modal Body -->
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="outflow">
                        <input type="hidden" name="item_id" id="outflow_item_id">
                        <div class="form-group">
                            <label for="outflow_quantity">Quantity<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="outflow_quantity" name="quantity" min="1" required>
                        </div>
                        <div class="form-group">
                            <label for="outflow_description">Description</label>
                            <textarea class="form-control" id="outflow_description" name="description" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-warning">Add Outflow</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Outflow Modal -->

    <!-- Transactions Modal -->
    <div class="modal fade" id="transactionsModal" tabindex="-1" role="dialog" aria-labelledby="transactionsModalLabel" aria-hidden="true">
        <!-- Transactions Modal Content -->
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <!-- Transactions Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title" id="transactionsModalLabel">Transaction History</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <!-- Transactions Modal Body -->
                <div class="modal-body">
                    <!-- Transaction table will be loaded here via JavaScript/Ajax -->
                    <div id="transactionsTableContainer"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Transactions Modal -->

    <!-- Show Report Modal -->
    <div class="modal fade" id="showReportModal" tabindex="-1" role="dialog" aria-labelledby="showReportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document"> <!-- Changed modal-lg to modal-xl -->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="showReportModalLabel">Reports</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php if (!empty($reports)) { ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th>Date & Time</th>
                                    <th>Photo</th>
                                    <th>Reported By</th>
                                    <th>Caused By</th>
                                    <th>Contact Number</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reports as $report) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($report["id"], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($report["type"], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($report["description"], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($report["datetime"], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>
                                        <?php if (!empty($report["photo"])) { ?>
                                        <a href="#" onclick="openImageModal('<?php echo htmlspecialchars($report["photo"], ENT_QUOTES, 'UTF-8'); ?>'); return false;">
                                            <img src="<?php echo htmlspecialchars($report["photo"], ENT_QUOTES, 'UTF-8'); ?>" alt="Report Photo" style="max-width: 150px; max-height: 150px;"> <!-- Made photo larger -->
                                        </a>
                                        <?php } else { echo 'No Photo'; } ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($report["name"], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($report["caused_by"], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($report["contact_number"], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>
                                        <button type="button" class="btn btn-info btn-sm" onclick="viewUserDetails('<?php echo htmlspecialchars($report["caused_by"], ENT_QUOTES, 'UTF-8'); ?>')">View Details</button>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <?php } else { ?>
                    <p>No reports found.</p>
                    <?php } ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Show Report Modal -->

    <!-- User Details Modal -->
    <div class="modal fade" id="userDetailsModal" tabindex="-1" role="dialog" aria-labelledby="userDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userDetailsModalLabel">User Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="userDetailsContent">
                    <!-- Content will be dynamically added via JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End User Details Modal -->

    <!-- Image Modal (Optional: To view larger images) -->
    <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <img src="" id="largeImage" alt="Large Image" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
    <!-- End Image Modal -->

    <!-- Scripts -->
    <!-- Include jQuery and Bootstrap JS (ensure this is after jQuery) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 4 JS Bundle -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
    <!-- Custom scripts -->
    <script>
        function openEditModal(item) {
            // Populate the form with the item details
            document.getElementById('edit_item_id').value = item.item_id;
            document.getElementById('edit_item_name').value = item.item_name;
            document.getElementById('edit_quantity').value = item.quantity;
            document.getElementById('edit_description').value = item.description;

            // Clear the file input field
            document.getElementById('edit_image').value = '';

            // Show the modal
            $('#editItemModal').modal('show');
        }
        
        function viewUserDetails(username) {
            // Close the Reports Modal
            $('#showReportModal').modal('hide');

            // Fetch the user details via AJAX
            $.ajax({
                url: 'fetch_user_details.php',
                type: 'POST',
                data: { username: username },
                success: function(response) {
                    // Populate the content of the User Details Modal
                    $('#userDetailsContent').html(response);
                    // Show the User Details Modal
                    $('#userDetailsModal').modal('show');
                },
                error: function() {
                    alert('Error fetching user details. Please try again.');
                }
            });
        }

        function openInflowModal(item) {
            // Set the item ID
            document.getElementById('inflow_item_id').value = item.item_id;

            // Clear previous values
            document.getElementById('inflow_quantity').value = '';
            document.getElementById('inflow_description').value = '';

            // Show the modal
            $('#inflowModal').modal('show');
        }

        function openOutflowModal(item) {
            // Set the item ID
            document.getElementById('outflow_item_id').value = item.item_id;

            // Clear previous values
            document.getElementById('outflow_quantity').value = '';
            document.getElementById('outflow_description').value = '';

            // Show the modal
            $('#outflowModal').modal('show');
        }

        function openTransactionsModal(item) {
            // Fetch transaction history via Ajax
            $.ajax({
                url: 'fetch_transactions.php',
                method: 'GET',
                data: { item_id: item.item_id },
                success: function(response) {
                    $('#transactionsTableContainer').html(response);
                    // Show the modal
                    $('#transactionsModal').modal('show');
                },
                error: function() {
                    alert('Error fetching transactions.');
                }
            });
        }

        // Function to open large image in Image Modal
        function openImageModal(imageSrc) {
            $('#largeImage').attr('src', imageSrc);
            $('#imageModal').modal('show');
        }

        // Optional: Automatically mark notifications as read when dropdown is opened
        $(document).ready(function(){
            $('#notificationsDropdown').on('show.bs.dropdown', function () {
                $.ajax({
                    url: 'mark_notifications_read.php',
                    method: 'POST',
                    success: function(response) {
                        // Update the unread count badge
                        if(response.unread_count !== undefined){
                            if(response.unread_count > 0){
                                $('span.badge').text(response.unread_count);
                            } else {
                                $('span.badge').remove();
                            }
                        }
                    },
                    error: function() {
                        console.log('Failed to mark notifications as read.');
                    }
                });
            });
        });
    </script>
</body>
</html>
