<?php
include 'conn.php';

// Count the number of players in the players table
$sqlCountPlayers = "SELECT COUNT(*) AS total_players FROM players";
$stmtCountPlayers = $conn->prepare($sqlCountPlayers);
$stmtCountPlayers->execute();
$result = $stmtCountPlayers->fetch(PDO::FETCH_ASSOC);
$totalPlayers = $result['total_players'];

try {
    $sqlUpcoming = "SELECT tournament_id, name, max_player, start_date, end_date, status, created_at, prize, fee, qualification, venue, start_time, end_time 
                    FROM tournaments 
                    WHERE status = 'upcoming'
                    ORDER BY start_date ASC";
    $stmtUpcoming = $conn->prepare($sqlUpcoming);
    $stmtUpcoming->execute();
    $upcomingTournaments = $stmtUpcoming->fetchAll(PDO::FETCH_ASSOC);
    
    $sqlReviews = "SELECT reviews.user_id, users.name, reviews.rating_service, reviews.rating_facilities, 
                          reviews.rating_tournaments, reviews.comments, reviews.created_at 
                   FROM reviews 
                   JOIN users ON reviews.user_id = users.user_id 
                   ORDER BY reviews.created_at DESC";
    $stmtReviews = $conn->prepare($sqlReviews);
    $stmtReviews->execute();
    $reviews = $stmtReviews->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

function displayStars($rating) {
    $stars = "";
    for ($i = 0; $i < 5; $i++) {
        $stars .= $i < $rating ? '<i class="fas fa-star text-warning"></i>' : '<i class="far fa-star text-warning"></i>';
    }
    return $stars;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Billiard-Home</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="dashboard/img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&family=Playfair+Display:wght@600;700&display=swap"
        rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="dashboard/lib/animate/animate.min.css" rel="stylesheet">
    <link href="dashboard/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="dashboard/css/bootstrap.min.css" rel="stylesheet">
    <link id="pagestyle" href="assets/css/material-dashboard.css?v=3.1.0" rel="stylesheet" />

    <!-- Template Stylesheet -->
    <link href="dashboard/css/style.css" rel="stylesheet">
</head>

<style>
    /* Announcements Scrollable Styling */
    .announcements-container {
        max-height: 400px;
        /* Adjust as needed */
        overflow-y: auto;
    }

    /* Optional: Customize the scrollbar for better aesthetics */
    .announcements-container::-webkit-scrollbar {
        width: 8px;
    }

    .announcements-container::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .announcements-container::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }

    .announcements-container::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    /* Enhanced Announcement Styling */
    .announcement-card {
        background-color: #f8f9fa;
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
        padding: 20px;
    }

    .announcement-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 10px;
        color: #000;
    }

    .announcement-body {
        font-size: 1.1rem;
        margin-bottom: 10px;
        color: #000;
    }

    .announcement-meta {
        font-size: 0.9rem;
        color: #000;
    }

    .announcement-meta p {
        margin: 0;
    }

    .announcement-icon {
        font-size: 2rem;
        margin-right: 10px;
        vertical-align: middle;
        color: #000;
    }

    .alert-one-hour {
        background-color: #ffeb3b;
        /* Yellow background for within the first hour */
        color: #000;
    }

    .alert-new {
        background-color: #d4edda;
        /* Green background for within 24 hours */
        color: #000;
    }

    .alert-danger,
    .alert-warning,
    .alert-info,
    .alert-success {
        color: #000;
        /* Set text color to black */
    }

    /* Background colors for alert types */
    .alert-danger {
        background-color: #f8d7da;
    }

    .alert-warning {
        background-color: #fff3cd;
    }

    .alert-info {
        background-color: #d1ecf1;
    }

    .alert-success {
        background-color: #d4edda;
    }

    /* Adjust spacing */
    .alert {
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 15px;
    }

    /* Custom button styling */
    .btn-close {
        color: #000;
    }

    /* Upcoming Events Styling */
    #upcoming-events {
        background-color: #f1f1f1;
        /* Light gray background */
    }

    #upcoming-events .section-title h1 {
        font-size: 2.5rem;
        font-weight: 700;
        color: #333;
    }

    #upcoming-events .section-title p {
        font-size: 1.1rem;
        color: #555;
    }

    #upcoming-events .card {
        border: none;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    #upcoming-events .card:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }

    #upcoming-events .card-img-top {
        border-top-left-radius: 0.25rem;
        border-top-right-radius: 0.25rem;
    }

    #upcoming-events .card-title {
        font-size: 1.5rem;
        color: #007bff;
    }

    #upcoming-events .card-text {
        font-size: 1rem;
        color: #555;
    }

    #upcoming-events .list-group-item {
        background-color: transparent;
        border: none;
        padding: 0.5rem 1rem;
        font-size: 0.95rem;
        color: #666;
    }

    #upcoming-events .card-footer {
        background-color: transparent;
        border-top: none;
        padding: 1rem;
    }

    #upcoming-events .btn-primary {
        background-color: #007bff;
        border: none;
        transition: background-color 0.3s;
    }

    #upcoming-events .btn-primary:hover {
        background-color: #0056b3;
    }

    /* Upcoming Tournaments Scrollable Styling */
    .upcoming-tournaments-container {
        display: flex;
        flex-wrap: nowrap;
        overflow-x: auto;
        padding-bottom: 15px;
    }

    .tournament-card {
        min-width: 300px;
        /* Adjust based on desired card width */
        margin-right: 20px;
        flex: 0 0 auto;
        /* Prevent flex items from shrinking */
    }

    /* Optional: Hide scrollbar for a cleaner look (for WebKit browsers) */
    .upcoming-tournaments-container::-webkit-scrollbar {
        height: 8px;
    }

    .upcoming-tournaments-container::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .upcoming-tournaments-container::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }

    .upcoming-tournaments-container::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    /* Adjust spacing for last card */
    .tournament-card:last-child {
        margin-right: 0;
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .tournament-card {
            min-width: 250px;
        }
    }

    @media (max-width: 576px) {
        .tournament-card {
            min-width: 200px;
        }
    }

    /* Floating Buttons Styling */
    .floating-buttons {
        position: fixed;
        bottom: 20px;
        right: 20px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        z-index: 1000;
    }

    .floating-buttons .btn-floating {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .floating-buttons .btn-floating:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
    }

    .floating-buttons .btn-facebook {
        background-color: #3b5998;
        color: #fff;
    }

    .floating-buttons .btn-call {
        background-color: #28a745;
        color: #fff;
    }
</style>

<body>
    <!-- Spinner Start -->
    <div id="spinner"
        class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" role="status"></div>
    </div>
    <!-- Spinner End -->

    <!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top py-lg-0 px-lg-5 wow fadeIn" data-wow-delay="0.1s">
        <a href="index.html" class="navbar-brand ms-4 ms-lg-0">
            <h1 class="text-primary m-0">T James Sporty Bar</h1>
        </a>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav mx-auto p-4 p-lg-0">
                <a href="index.php" class="nav-item nav-link active">Home</a>
                <a href="#about" class="nav-item nav-link">About</a>
                <a href="#announcement" class="nav-item nav-link">Announcement</a>
                <a href="register.php?form=login" class="nav-item nav-link">Login</a>
                <a href="register.php?form=registration" class="nav-item nav-link">Register</a>
                <a href="#contact" class="nav-item nav-link">Contact</a>
            </div>
            <div class=" d-none d-lg-flex">
            </div>
        </div>
    </nav>
    <!-- Navbar End -->


    <!-- Carousel Start -->
    <div class="container-fluid p-0 pb-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="owl-carousel header-carousel position-relative">
            <div class="owl-carousel-item position-relative">
                <img class="img-fluid" src="dashboard/img/carousel-1.jpg" alt="">
                <div class="owl-carousel-inner">
                    <div class="container">
                        <div class="row justify-content-start">
                            <div class="col-lg-8">
                                <p class="text-primary text-uppercase fw-bold mb-2">T-James Billiard Hall</p>
                                <h1 class="display-1 text-light mb-4 animated slideInDown">Chase the Felt, Chase the Dream.
                                </h1>
                                <a href="#" class="btn btn-primary rounded-pill py-3 px-5" data-bs-toggle="modal"
                                    data-bs-target="#readMoreModal">Read More</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Carousel End -->


    <!-- Facts Start -->

    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3 col-md-6 wow fadeIn" data-wow-delay="0.3s">
                <div class="fact-item bg-light rounded text-center h-100 p-5">
                    <i class="fa fa-users fa-4x text-primary mb-4"></i>
                    <p class="mb-2">Champions Player</p>
                    <h1 class="display-5 mb-0" data-toggle="counter-up">0</h1>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 wow fadeIn" data-wow-delay="0.5s">
                <div class="fact-item bg-light rounded text-center h-100 p-5">
                    <i class="fa fa-user-plus fa-4x text-primary mb-4"></i>
                    <p class="mb-2">Total Players</p>
                    <h1 class="display-5 mb-0" data-toggle="counter-up"><?php echo htmlspecialchars($totalPlayers); ?></h1>
                </div>
            </div>
        </div>
    </div>

    <!-- Facts End -->
    <!-- Announcement -->
    <div class="container">
        <div id="announcement" class="container-xxl py-6">
            <div class="container">
                <div class="card mt-4">
                    <div class="card-header p-3">
                        <h5 class="mb-0">Announcement</h5>
                    </div>
                    <div class="card-body p-3 pb-0 announcements-container">
                        <?php
                        include 'conn.php';

                        // Fetch announcements from the database
                        $sqlBookings = "SELECT title, body, expires_at, created_at FROM announcements";
                        $stmtBookings = $conn->prepare($sqlBookings);
                        $stmtBookings->execute();
                        $announcements = $stmtBookings->fetchAll(PDO::FETCH_ASSOC);
                        ?>

                        <?php if (!empty($announcements)): ?>
                        <?php
                            $hasValidAnnouncements = false; // Flag to check if any announcements are valid
                            foreach ($announcements as $announcement):
                                $currentTime = time(); // Current Unix timestamp
                                $expiresAtTimestamp = strtotime($announcement['expires_at']);

                                // Skip expired announcements
                                if ($currentTime > $expiresAtTimestamp) {
                                    continue;
                                }

                                $hasValidAnnouncements = true; // Found at least one valid announcement

                                // Determine alert type based on the title or content (customizable logic)
                                $alertType = "alert-primary"; // Default alert type
                                $icon = '<i class="fas fa-info-circle announcement-icon"></i>'; // Default icon

                                if (stripos($announcement['title'], 'danger') !== false) {
                                    $alertType = "alert-danger";
                                    $icon = '<i class="fas fa-exclamation-triangle announcement-icon"></i>';
                                } elseif (stripos($announcement['title'], 'warning') !== false) {
                                    $alertType = "alert-warning";
                                    $icon = '<i class="fas fa-exclamation-circle announcement-icon"></i>';
                                } elseif (stripos($announcement['title'], 'info') !== false) {
                                    $alertType = "alert-info";
                                    $icon = '<i class="fas fa-info-circle announcement-icon"></i>';
                                } elseif (stripos($announcement['title'], 'success') !== false) {
                                    $alertType = "alert-success";
                                    $icon = '<i class="fas fa-check-circle announcement-icon"></i>';
                                }

                                // Format the expiration date
                                $expiresAtFormatted = date('F j, Y, g:i a', $expiresAtTimestamp);

                                // Check if the announcement is new (created in the last 24 hours)
                                $createdAtTimestamp = strtotime($announcement['created_at']);
                                $isNewAnnouncement = ($currentTime - $createdAtTimestamp) <= (24 * 60 * 60); // Within 24 hours
                                $isWithinOneHour = ($currentTime - $createdAtTimestamp) <= (60 * 60); // Within 1 hour

                                // Apply special styling based on time since creation
                                if ($isWithinOneHour) {
                                    $alertType = "alert-one-hour"; // Special class for announcements created within the last hour
                                } elseif ($isNewAnnouncement) {
                                    $alertType = "alert-new"; // Special class for announcements created within the last 24 hours
                                }
                            ?>
                        <div
                            class="alert <?php echo $alertType; ?> announcement-card alert-dismissible fade show custom-alert"
                            role="alert">
                            <div class="d-flex align-items-start">
                                <?php echo $icon; ?>
                                <div>
                                    <strong class="announcement-title"><?php echo htmlspecialchars($announcement['title']); ?>:</strong>
                                    <div class="announcement-body"><?php echo nl2br(htmlspecialchars($announcement['body'])); ?></div>
                                    <div class="announcement-meta">
                                        <p>Expires at: <?php echo $expiresAtFormatted; ?></p>
                                        <?php if ($isWithinOneHour): ?>
                                        <p><strong>New Announcement! (Within the last hour)</strong></p>
                                        <?php elseif ($isNewAnnouncement): ?>
                                        <p><strong>New Announcement! (Within the last 24 hours)</strong></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                        <?php endforeach; ?>

                        <?php if (!$hasValidAnnouncements): ?>
                        <div class="alert alert-light alert-dismissible fade show custom-alert" role="alert">
                            <span class="text-sm">No announcements at this time.</span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                        <?php endif; ?>

                        <?php else: ?>
                        <div class="alert alert-light alert-dismissible fade show custom-alert" role="alert">
                            <span class="text-sm">No announcements at this time.</span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container mt-5">
        <div class="section-title text-center position-relative pb-3 mb-5 mx-auto" style="max-width: 600px;">
            <h1 class="display-5">User Reviews</h1>
            <p class="text-primary">See what our customers say about us!</p>
        </div>
        
        <?php if (!empty($reviews)): ?>
            <?php foreach ($reviews as $review): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($review['name']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($review['comments']); ?></p>
                        
                        <div class="d-flex">
                            <div class="me-3">
                                <strong>Service Rating:</strong> <?php echo displayStars($review['rating_service']); ?>
                            </div>
                            <div class="me-3">
                                <strong>Facilities Rating:</strong> <?php echo displayStars($review['rating_facilities']); ?>
                            </div>
                            <div>
                                <strong>Tournaments Rating:</strong> <?php echo displayStars($review['rating_tournaments']); ?>
                            </div>
                        </div>
                        <p class="card-text"><small class="text-muted">Reviewed on <?php echo date('F j, Y', strtotime($review['created_at'])); ?></small></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center">No reviews available at the moment.</p>
        <?php endif; ?>
    </div>

    <!-- About Start -->
    <div id="about" class="container-xxl py-6">
        <div class="container-xxl py-6">
            <div class="container">
                <div class="row g-5">
                    <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="row img-twice position-relative h-100">
                            <div class="col-6">
                                <img class="img-fluid rounded" src="dashboard/img/about-1.jpg" alt="">
                            </div>
                            <div class="col-6 align-self-end">
                                <img class="img-fluid rounded" src="dashboard/img/balls.jfif" alt="">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.5s">
                        <div class="h-100">
                            <h1 class="display-6 mb-4">We play for our dream</h1>
                            <p>T James Sports Bar opened its doors with a vision to create a vibrant and welcoming environment for sports enthusiasts and community members alike. The bar quickly became a local favorite, known for its lively atmosphere, extensive drink menu, and passion for sports. With numerous large-screen TVs, patrons could enjoy live broadcasts of their favorite games while savoring delicious food and drinks.</p>
                            <p>Over the years, T James Sports Bar has expanded its offerings, becoming a hub for not only watching sports but also for hosting various events and tournaments. The bar has built a reputation for excellent customer service and a strong community presence, making it a go-to destination for both casual outings and special occasions.</p>
                            <h3>Services Offered</h3>

                            <p><strong>Online Table Booking:</strong></p>
                            <p>Patrons can conveniently reserve tables online, ensuring they have a spot during busy game nights or special events.</p>

                            <p><strong>Join Tournaments:</strong></p>
                            <p>T James Sports Bar regularly hosts various tournaments, from trivia contests to sports competitions. Guests can join these events, compete for prizes, and enjoy a fun, engaging atmosphere.</p>

                            <p><strong>Special Offers:</strong></p>
                            <p>The bar frequently updates its special offers, including happy hour deals, game-day promotions, and discounts on food and drinks. These offers are designed to enhance the experience for guests and provide great value.</p>

                            <p><strong>Live Entertainment:</strong></p>
                            <p>T James Sports Bar features live music and entertainment on select nights, creating a lively atmosphere for guests to enjoy beyond sports.</p>

                            <p><strong>Catering Services:</strong></p>
                            <p>For private events or gatherings, T James Sports Bar offers catering services, allowing guests to enjoy their favorite dishes in a customized setting.</p>

                            <p><strong>Merchandise:</strong></p>
                            <p>Fans can purchase branded merchandise, including apparel and accessories, to show their support for their favorite teams and the bar itself.</p>

                            <div class="row g-2 mb-4">
                                <div class="col-sm-6">
                                    <i class="fa fa-check text-primary me-2"></i>Quality Table
                                </div>
                                <div class="col-sm-6">
                                    <i class="fa fa-check text-primary me-2"></i>Quality balls
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- About End -->
        <!-- Upcoming Events Start -->
        <div id="upcoming-events" class="container-xxl py-6">
            <div class="container">
                <div class="section-title text-center position-relative pb-3 mb-5 mx-auto"
                    style="max-width: 600px;">
                    <h1 class="display-5">Upcoming Tournaments</h1>
                    <p class="text-primary">Join our exciting upcoming events and showcase your skills!</p>
                </div>
                <div class="upcoming-tournaments-container">
                    <?php if (!empty($upcomingTournaments)): ?>
                    <?php foreach ($upcomingTournaments as $tournament): ?>
                    <div class="card h-100 shadow-sm tournament-card">
                        <img src="img/backgroud_upcoming.jpg" class="card-img-top"
                            alt="<?php echo htmlspecialchars($tournament['name']); ?>"
                            style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($tournament['name']); ?></h5>
                            <p class="card-text">
                                <strong>Prize:</strong> ₱<?php echo number_format((float)$tournament['prize'], 2); ?><br>
                                <strong>Entry Fee:</strong> ₱<?php echo number_format((float)$tournament['fee'], 2); ?><br>
                                <strong>Players:</strong> <?php echo htmlspecialchars($tournament['max_player']); ?><br>
                                <strong>Qualification:</strong> <?php echo htmlspecialchars($tournament['qualification']); ?>
                            </p>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <strong>Venue:</strong> <?php echo htmlspecialchars($tournament['venue']); ?>
                            </li>
                            <li class="list-group-item">
                                <strong>Start Date:</strong> <?php echo date('F j, Y', strtotime($tournament['start_date'])); ?>
                            </li>
                            <li class="list-group-item">
                                <strong>End Date:</strong> <?php echo date('F j, Y', strtotime($tournament['end_date'])); ?>
                            </li>
                            <li class="list-group-item">
                                <strong>Time:</strong> <?php echo date('g:i a', strtotime($tournament['start_time'])); ?> -
                                <?php echo date('g:i a', strtotime($tournament['end_time'])); ?>
                            </li>
                        </ul>
                        <div class="card-footer text-center">
                            <a href="javascript:void(0);"
                                onclick="openJoinTournamentModal(<?php echo $tournament['tournament_id']; ?>, <?php echo $tournament['fee']; ?>);"
                                class="btn btn-primary">Register Now</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <div class="alert alert-info text-center" role="alert">
                        No upcoming tournaments at this time. Please check back later!
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <!-- Upcoming Events End -->
        <!-- Product Start -->
        <div id="contact" class="container-xxl py-6">
            <div class="container-xxl bg-light my-6 py-6 pt-0">
                <div class="container">
                    <div class="bg-primary text-light rounded-bottom p-5 my-6 mt-0 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="row g-4 align-items-center">
                            <div class="col-lg-6">
                                <h1 class="display-4 text-light mb-0">The Best Quality Of Billiard Hall Tournament</h1>
                            </div>
                            <div class="col-lg-6 text-lg-end">
                                <div class="d-inline-flex align-items-center text-start">
                                    <i class="fa fa-phone-alt fa-4x flex-shrink-0"></i>
                                    <div class="ms-4">
                                        <p class="fs-5 fw-bold mb-0">Call Us</p>
                                        <p class="fs-1 fw-bold mb-0">09454055131</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product End -->

        <!-- Read More Modal -->
        <div class="modal fade" id="readMoreModal" tabindex="-1" aria-labelledby="readMoreModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="readMoreModalLabel">More About T-James Billiard Hall</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <p>T-James Billiard Hall offers state-of-the-art facilities, professional coaching, and a vibrant
                            community of billiard enthusiasts. Whether you're a beginner or a seasoned player, our hall
                            provides the perfect environment to hone your skills and enjoy the game.</p>
                        <!-- Apply Bootstrap width class here -->
                        <img src="img/logoBilliard.png" class="img-fluid mb-3 w-25" alt="Detailed Image">
                        <p>Join us today and chase your dreams on the felt!</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Join Tournament Modal -->
        <div class="modal fade" id="joinTournamentModal" tabindex="-1" aria-labelledby="joinTournamentModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="joinTournamentModalLabel">Join Tournament</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="join_tournament2.php" enctype="multipart/form-data"
                            id="joinTournamentForm">
                            <!-- Payment Option Selection -->
                            <label class="form-label">Choose Payment Method</label>
                            <div class="input-group input-group-outline my-3">
                                <select class="form-select" id="paymentMethod" name="paymentMethod" required>
                                    <option value="gcash">GCash</option>
                                    <option value="paypal">PayPal</option>
                                </select>
                            </div>

                            <!-- Username Field -->
                            <label for="username" class="form-label">Username</label>
                            <div class="input-group input-group-outline my-3">
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>

                            <!-- Total Amount -->
                            <input type="hidden" id="tournament_id" name="tournament_id">
                            <label for="totalAmount" class="form-label">Total Amount</label>
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
        
        <!-- Phone Number Modal -->
        <div class="modal fade" id="phoneModal" tabindex="-1" aria-labelledby="phoneModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="phoneModalLabel">Contact Us</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <p class="fs-4 fw-bold">09454055131</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>


        <!-- Join Tournament Script -->
        <script>
            document.getElementById('paymentMethod').addEventListener('change', function () {
                const paymentMethod = this.value;
                const gcashPayment = document.getElementById('gcashPayment');
                const paypalButtonContainer = document.getElementById('paypalButtonContainer');

                if (paymentMethod === 'paypal') {
                    gcashPayment.classList.add('d-none');
                    paypalButtonContainer.classList.remove('d-none');
                    loadPayPalSDK();
                } else {
                    gcashPayment.classList.remove('d-none');
                    paypalButtonContainer.classList.add('d-none');
                }
            });

            function loadPayPalSDK() {
                if (!document.getElementById('paypal-sdk')) {
                    const script = document.createElement('script');
                    script.id = 'paypal-sdk';
                    script.src = "https://www.paypal.com/sdk/js?client-id=Aam1fskHpg8yD-SMFL1jsV3bpVHJgBv5jr0ipYmJh2LL7Cyc_HDP4KVlJsQCQjgIyCenNy0EBD6dY-R9&currency=PHP";
                    script.onload = initializePayPalButton;
                    document.body.appendChild(script);
                } else {
                    initializePayPalButton();
                }
            }

            function initializePayPalButton() {
                paypal.Buttons({
                    createOrder: function (data, actions) {
                        const tournamentId = document.getElementById('tournament_id').value;
                        return fetch('create_paypal_tournament.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ tournament_id: tournamentId })
                        }).then(res => res.json()).then(orderData => orderData.id);
                    },
                    onApprove: function (data, actions) {
                        return fetch('paypal_success_tournament.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                orderID: data.orderID,
                                tournament_id: document.getElementById('tournament_id').value,
                                username: document.getElementById('username').value
                            })
                        }).then(res => res.json()).then(orderData => {
                            if (orderData.success) {
                                Swal.fire('Success', 'Successfully registered for the tournament!', 'success');
                                $('#joinTournamentModal').modal('hide');
                            } else {
                                Swal.fire('Error', orderData.message, 'error');
                            }
                        }).catch(err => {
                            console.error(err);
                            Swal.fire('Error', 'An unexpected error occurred.', 'error');
                        });
                    }
                }).render('#paypal-button');
            }

            function openJoinTournamentModal(tournamentId, fee) {
                fetch('pre_validate_tournament.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ 'tournament_id': tournamentId })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            $('#joinTournamentModal').modal('show');
                            document.getElementById('tournament_id').value = tournamentId;
                            document.getElementById('fee').value = fee.toFixed(2);
                            // Ensure only numeric fee is passed
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error', 'An unexpected error occurred. Please try again.', 'error');
                    });
            }
        </script>

        <!-- Floating Buttons Start -->
        <div class="floating-buttons">
            <!-- Facebook Button -->
            <a href="https://www.facebook.com/pmmgsc" class="btn btn-facebook btn-floating" target="_blank"
                title="Facebook">
                <i class="fab fa-facebook-f"></i>
            </a>
            
            <!-- Call Button -->
            <a href="javascript:void(0);" class="btn btn-call btn-floating" title="Call Us" data-bs-toggle="modal" data-bs-target="#phoneModal">
                <i class="fa fa-phone"></i>
            </a>
        </div>
        <!-- Floating Buttons End -->

        <!-- Copyright Start -->
        <div class="container-fluid copyright text-light py-4 wow fadeIn"
            data-wow-delay="0.1s">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        &copy; <a href="#">T-James Billiard Hall</a>, All Right Reserved.
                    </div>

                </div>
            </div>
        </div>
        <!-- Copyright End -->

        <script>
            document.getElementById('paymentMethod').addEventListener('change', function () {
                const paymentMethod = this.value;
                const gcashPayment = document.getElementById('gcashPayment');
                const paypalButtonContainer = document.getElementById('paypalButtonContainer');

                if (paymentMethod === 'paypal') {
                    gcashPayment.classList.add('d-none');
                    paypalButtonContainer.classList.remove('d-none');
                    loadPayPalSDK();
                } else {
                    gcashPayment.classList.remove('d-none');
                    paypalButtonContainer.classList.add('d-none');
                }
            });

            function loadPayPalSDK() {
                if (!document.getElementById('paypal-sdk')) {
                    const script = document.createElement('script');
                    script.id = 'paypal-sdk';
                    script.src = "https://www.paypal.com/sdk/js?client-id=Aam1fskHpg8yD-SMFL1jsV3bpVHJgBv5jr0ipYmJh2LL7Cyc_HDP4KVlJsQCQjgIyCenNy0EBD6dY-R9&currency=PHP";
                    script.onload = initializePayPalButton;
                    document.body.appendChild(script);
                } else {
                    initializePayPalButton();
                }
            }

            function initializePayPalButton() {
                paypal.Buttons({
                    createOrder: function (data, actions) {
                        const tournamentId = document.getElementById('tournament_id').value;
                        return fetch('create_paypal_tournament.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ tournament_id: tournamentId })
                        }).then(res => res.json()).then(orderData => orderData.id);
                    },
                    onApprove: function (data, actions) {
                        return fetch('paypal_success_tournament.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                orderID: data.orderID,
                                tournament_id: document.getElementById('tournament_id').value,
                                username: document.getElementById('username').value
                            })
                        }).then(res => res.json()).then(orderData => {
                            if (orderData.success) {
                                Swal.fire('Success', 'Successfully registered for the tournament!', 'success');
                                $('#joinTournamentModal').modal('hide');
                            } else {
                                Swal.fire('Error', orderData.message, 'error');
                            }
                        }).catch(err => {
                            console.error(err);
                            Swal.fire('Error', 'An unexpected error occurred.', 'error');
                        });
                    }
                }).render('#paypal-button');
            }

            function openJoinTournamentModal(tournamentId, fee) {
                fetch('pre_validate_tournament.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ 'tournament_id': tournamentId })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            $('#joinTournamentModal').modal('show');
                            document.getElementById('tournament_id').value = tournamentId;
                            document.getElementById('fee').value = fee.toFixed(2);
                            // Ensure only numeric fee is passed
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error', 'An unexpected error occurred. Please try again.', 'error');
                    });
            }
        </script>

        <!-- Back to To


        <!-- JavaScript Libraries -->
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="dashboard/lib/wow/wow.min.js"></script>
        <script src="dashboard/lib/easing/easing.min.js"></script>
        <script src="dashboard/lib/waypoints/waypoints.min.js"></script>
        <script src="dashboard/lib/counterup/counterup.min.js"></script>
        <script src="dashboard/lib/owlcarousel/owl.carousel.min.js"></script>

        <!-- Template Javascript -->
        <script src="dashboard/js/main.js"></script>
</body>

</html>
