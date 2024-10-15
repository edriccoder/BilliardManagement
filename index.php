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
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet"> 

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

<body>  
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" role="status"></div>
    </div>
    <!-- Spinner End -->

    <!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top py-lg-0 px-lg-5 wow fadeIn" data-wow-delay="0.1s">
        <a href="index.html" class="navbar-brand ms-4 ms-lg-0">
            <h1 class="text-primary m-0">Billiard</h1>
        </a>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav mx-auto p-4 p-lg-0">
                <a href="index.php" class="nav-item nav-link active">Home</a>
                <a href="#about" class="nav-item nav-link">About</a>
                <a href="#announcement" class="nav-item nav-link">Announcement</a>
                <a href="register.php" class="nav-item nav-link">Register</a>
                <a href="#contact" class="nav-item nav-link">Contact</a>
            </div>
            <div class=" d-none d-lg-flex">
                </div>  
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
                                <h1 class="display-1 text-light mb-4 animated slideInDown">Chase the Felt, Chase the Dream.</h1>    
                                <a href="#" class="btn btn-primary rounded-pill py-3 px-5" data-bs-toggle="modal" data-bs-target="#readMoreModal">Read More</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--<div class="owl-carousel-item position-relative">
                <img class="img-fluid" src="img/carousel-2.jpg" alt="">
                <div class="owl-carousel-inner">
                    <div class="container">
                        <div class="row justify-content-start">
                            <div class="col-lg-8">
                                <p class="text-primary text-uppercase fw-bold mb-2">// The Best Bakery</p>
                                <h1 class="display-1 text-light mb-4 animated slideInDown">We Bake With Passion</h1>
                                <p class="text-light fs-5 mb-4 pb-3">Vero elitr justo clita lorem. Ipsum dolor sed stet sit diam rebum ipsum.</p>
                                <a href="" class="btn btn-primary rounded-pill py-3 px-5">Read More</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>-->
        </div>
    </div>
    <!-- Carousel End -->


    <!-- Facts Start -->
    <div class="container-xxl py-6">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-3 col-md-6 wow fadeIn" data-wow-delay="0.3s">
                    <div class="fact-item bg-light rounded text-center h-100 p-5">
                        <i class="fa fa-users fa-4x text-primary mb-4"></i>
                        <p class="mb-2">Champions Player</p>
                        <h1 class="display-5 mb-0" data-toggle="counter-up">10</h1>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 wow fadeIn" data-wow-delay="0.5s">
                    <div class="fact-item bg-light rounded text-center h-100 p-5">
                        <i class="fa fa-user-plus fa-4x text-primary mb-4"></i>
                        <p class="mb-2">Total Players</p>
                        <h1 class="display-5 mb-0" data-toggle="counter-up">135</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Facts End -->
    <!-- announcement -->
    <div id="announcement" class="container-xxl py-6">
        <div class="container-xxl py-6">
            <div class="container">
                <div class="card mt-4">
                    <div class="card-header p-3">
                        <h5 class="mb-0">Announcement</h5>
                    </div>
                    <div class="card-body p-3 pb-0">
                    <?php
                        include 'conn.php';

                        // Fetch announcements from the database
                        $sqlBookings = "SELECT title, body, expires_at FROM announcements";
                        $stmtBookings = $conn->prepare($sqlBookings);
                        $stmtBookings->execute();
                        $announcements = $stmtBookings->fetchAll(PDO::FETCH_ASSOC);

                        // Check if there are any announcements
                        if (!empty($announcements)) {
                            foreach ($announcements as $announcement) {
                                // Determine alert type based on the title or content (you can customize this logic)
                                $alertType = "alert-primary"; // Default alert type
                                if (stripos($announcement['title'], 'danger') !== false) {
                                    $alertType = "alert-danger";
                                } elseif (stripos($announcement['title'], 'warning') !== false) {
                                    $alertType = "alert-warning";
                                } elseif (stripos($announcement['title'], 'info') !== false) {
                                    $alertType = "alert-info";
                                } elseif (stripos($announcement['title'], 'success') !== false) {
                                    $alertType = "alert-success";
                                }

                                // Format the expiration date to include both date and time
                                $expiresAt = date('F j, Y, g:i a', strtotime($announcement['expires_at']));

                                // Display the alert with title, body, and expiration time each on a new line
                                echo '<div class="alert ' . $alertType . ' alert-dismissible text-white" role="alert">';
                                echo '<span class="text-sm"><strong>' . htmlspecialchars($announcement['title']) . '</strong></span><br>'; // Title on a new line
                                echo '<span class="text-sm">' . nl2br(htmlspecialchars($announcement['body'])) . '</span><br>'; // Body with new line support
                                echo '<small class="text">Expires at: ' . $expiresAt . '</small><br>'; // Expiration date on a new line
                                echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                                echo '</div>';
                            }
                        } else {
                            // No announcements found
                            echo '<div class="alert alert-light alert-dismissible text-white" role="alert">';
                            echo '<span class="text-sm">No announcements at this time.</span>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
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
                                <p class="fs-1 fw-bold mb-0">+012 345 6789</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
                <h1 class="display-6 mb-4">Our Top 3 Player's</h1>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="product-item d-flex flex-column bg-white rounded overflow-hidden h-100">
                        <div class="text-center p-4">
                            <h3 class="mb-3">Carlo Biado</h3>
                            <span>Tempor erat elitr rebum at clita dolor diam ipsum sit diam amet diam et eos</span>
                        </div>
                        <div class="position-relative mt-auto">
                            <img class="img-fluid" src="dashboard/img/product1.jpg" alt="">
                            <div class="product-overlay">
                                <a class="btn btn-lg-square btn-outline-light rounded-circle" href=""><i class="fa fa-eye text-primary"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="product-item d-flex flex-column bg-white rounded overflow-hidden h-100">
                        <div class="text-center p-4">
                            <h3 class="mb-3">AJ Manas</h3>
                            <span>Tempor erat elitr rebum at clita dolor diam ipsum sit diam amet diam et eos</span>
                        </div>
                        <div class="position-relative mt-auto">
                            <center><img class="img-fluid" src="dashboard/img/ajmanas.jfif" alt=""></center>
                            <div class="product-overlay">
                                <a class="btn btn-lg-square btn-outline-light rounded-circle" href=""><i class="fa fa-eye text-primary"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="product-item d-flex flex-column bg-white rounded overflow-hidden h-100">
                        <div class="text-center p-4">
                            <h4 class="mb-3">Johan Chua</h4>
                            <span>Tempor erat elitr rebum at clita dolor diam ipsum sit diam amet diam et eos</span>
                        </div>
                        <div class="position-relative mt-auto">
                            <img class="img-fluid" src="dashboard/img/johannchua.jpg" alt="">
                            <div class="product-overlay">
                                <a class="btn btn-lg-square btn-outline-light rounded-circle" href=""><i class="fa fa-eye text-primary"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Product End -->



    <!-- Team Start -->
    <div id="team" class="container-xxl py-6">
        <div class="container-xxl py-6">
        <div class="container">
            <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
                <p class="text-primary text-uppercase mb-2">// Our Referre Team</p>
                <h1 class="display-6 mb-4">We're Super Professional </h1>
            </div>
            <div class="row g-4">
                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="team-item text-center rounded overflow-hidden">
                        <img class="img-fluid" src="dashboard/img/reffere1.jfif" alt="">
                        <div class="team-text">
                            <div class="team-title">
                                <h5>Full Name</h5>
                                <span>Designation</span>
                            </div>
                            <div class="team-social">
                                <a class="btn btn-square btn-light rounded-circle" href=""><i class="fab fa-facebook-f"></i></a>
                                <a class="btn btn-square btn-light rounded-circle" href=""><i class="fab fa-twitter"></i></a>
                                <a class="btn btn-square btn-light rounded-circle" href=""><i class="fab fa-instagram"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="team-item text-center rounded overflow-hidden">
                        <img class="img-fluid" src="dashboard/img/reffere2.jfif" alt="">
                        <div class="team-text">
                            <div class="team-title">
                                <h5>Full Name</h5>
                                <span>Designation</span>
                            </div>
                            <div class="team-social">
                                <a class="btn btn-square btn-light rounded-circle" href=""><i class="fab fa-facebook-f"></i></a>
                                <a class="btn btn-square btn-light rounded-circle" href=""><i class="fab fa-twitter"></i></a>
                                <a class="btn btn-square btn-light rounded-circle" href=""><i class="fab fa-instagram"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="team-item text-center rounded overflow-hidden">
                        <img class="img-fluid" src="dashboard/img/reffere3.jfif" alt="">
                        <div class="team-text">
                            <div class="team-title">
                                <h5>Full Name</h5>
                                <span>Designation</span>
                            </div>
                            <div class="team-social">
                                <a class="btn btn-square btn-light rounded-circle" href=""><i class="fab fa-facebook-f"></i></a>
                                <a class="btn btn-square btn-light rounded-circle" href=""><i class="fab fa-twitter"></i></a>
                                <a class="btn btn-square btn-light rounded-circle" href=""><i class="fab fa-instagram"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Read More Modal -->
    <div class="modal fade" id="readMoreModal" tabindex="-1" aria-labelledby="readMoreModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="readMoreModalLabel">More About T-James Billiard Hall</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p>T-James Billiard Hall offers state-of-the-art facilities, professional coaching, and a vibrant community of billiard enthusiasts. Whether you're a beginner or a seasoned player, our hall provides the perfect environment to hone your skills and enjoy the game.</p>
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

    <!-- Copyright Start -->
    <div class="container-fluid copyright text-light py-4 wow fadeIn" data-wow-delay="0.1s">
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
        // Get all the nav links
        const navLinks = document.querySelectorAll('.navbar-nav .nav-link');

        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                // Remove 'active' class from all links
                navLinks.forEach(nav => nav.classList.remove('active'));
                // Add 'active' class to the clicked link
                this.classList.add('active');
            });
        });

        function toggleReadMore() {
            var dots = document.getElementById("dots");
            var moreText = document.getElementById("more");
            var btnText = document.getElementById("readMoreBtn");

            if (dots.style.display === "none") {
            dots.style.display = "inline";
            btnText.innerHTML = "Read More";
            moreText.style.display = "none";
            } else {
            dots.style.display = "none";
            btnText.innerHTML = "Read Less";
            moreText.style.display = "inline";
            }
        }

    </script>



    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top"><i class="bi bi-arrow-up"></i></a>


    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
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