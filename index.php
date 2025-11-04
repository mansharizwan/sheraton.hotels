<?php
require_once 'db.php';

// Fetch featured hotels
$stmt = $pdo->query("SELECT * FROM hotels ORDER BY rating DESC LIMIT 3");
$featuredHotels = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sheraton Hotels - Luxury Accommodations</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Base Styles */
        :root {
            --primary-color: #0056b3;
            --secondary-color: #e60000;
            --accent-color: #ffcc00;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --gray-color: #6c757d;
            --light-gray: #e9ecef;
            --border-color: #dee2e6;
            --success-color: #28a745;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Open Sans', sans-serif;
            line-height: 1.6;
            color: var(--dark-color);
            background-color: #fff;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--dark-color);
        }

        a {
            text-decoration: none;
            color: var(--primary-color);
        }

        ul {
            list-style: none;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-align: center;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: #004494;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color: var(--light-gray);
            color: var(--dark-color);
        }

        .btn-secondary:hover {
            background-color: #d1d5db;
        }

        .btn-outline {
            background-color: transparent;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
        }

        .btn-outline:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-block {
            display: block;
            width: 100%;
        }

        /* Header */
        .main-header {
            background-color: white;
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .main-header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
        }

        .logo h1 {
            font-size: 1.8rem;
            color: var(--primary-color);
        }

        .logo i {
            color: var(--secondary-color);
            margin-right: 8px;
        }

        .main-nav ul {
            display: flex;
            gap: 25px;
        }

        .main-nav a {
            font-weight: 600;
            color: var(--dark-color);
            position: relative;
        }

        .main-nav a:hover,
        .main-nav a.active {
            color: var(--primary-color);
        }

        .main-nav a.active::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 3px;
            background-color: var(--primary-color);
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('assets/images/hero-bg.jpg') center/cover no-repeat;
            height: 60vh;
            display: flex;
            align-items: center;
            color: white;
            text-align: center;
        }

        .hero-content {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 15px;
            color: white;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 30px;
        }

        /* Search Form */
        .search-form {
            background-color: white;
            border-radius: 10px;
            padding: 25px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            color: var(--dark-color);
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--dark-color);
        }

        .form-group input,
        .form-group select {
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-size: 16px;
        }

        /* Featured Hotels */
        .featured-hotels {
            padding: 60px 0;
            background-color: var(--light-color);
        }

        .featured-hotels h2 {
            text-align: center;
            margin-bottom: 40px;
            font-size: 2.2rem;
        }

        .hotel-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .hotel-card {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .hotel-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        .hotel-image img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .hotel-info {
            padding: 20px;
        }

        .hotel-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
        }

        .hotel-header h3 {
            font-size: 1.4rem;
            margin-bottom: 0;
        }

        .rating {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #ffc107;
        }

        .rating i {
            font-size: 1.1rem;
        }

        .location {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--gray-color);
            margin-bottom: 12px;
        }

        .description {
            color: var(--gray-color);
            margin-bottom: 15px;
            min-height: 60px;
        }

        .amenities {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 20px;
        }

        .amenity-tag {
            background-color: var(--light-gray);
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
        }

        .hotel-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .price {
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--primary-color);
        }

        /* Why Choose Us */
        .why-choose {
            padding: 60px 0;
        }

        .why-choose h2 {
            text-align: center;
            margin-bottom: 40px;
            font-size: 2.2rem;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }

        .feature-card {
            text-align: center;
            padding: 30px 20px;
            border-radius: 10px;
            background-color: white;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-card i {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        .feature-card h3 {
            margin-bottom: 15px;
        }

        /* Footer */
        .main-footer {
            background-color: var(--dark-color);
            color: white;
            padding: 60px 0 20px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-logo h2 {
            color: white;
            margin-bottom: 15px;
        }

        .footer-logo p {
            opacity: 0.8;
        }

        .footer-links h4,
        .footer-contact h4 {
            margin-bottom: 20px;
            color: white;
        }

        .footer-links ul {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .footer-links a {
            color: white;
            opacity: 0.8;
            transition: var(--transition);
        }

        .footer-links a:hover {
            opacity: 1;
            color: var(--accent-color);
        }

        .footer-contact p {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            opacity: 0.8;
        }

        .social-icons {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .social-icons a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            transition: var(--transition);
        }

        .social-icons a:hover {
            background-color: var(--primary-color);
            transform: translateY(-3px);
        }

        .copyright {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            opacity: 0.7;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .main-header .container {
                flex-direction: column;
                gap: 15px;
            }
            
            .main-nav ul {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .hero h1 {
                font-size: 2.2rem;
            }
            
            .search-form {
                grid-template-columns: 1fr;
            }
            
            .hotel-grid {
                grid-template-columns: 1fr;
            }
            
            .features {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .main-header .container {
                flex-direction: column;
                gap: 15px;
            }
            
            .main-nav ul {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .hero h1 {
                font-size: 1.8rem;
            }
            
            .search-form {
                grid-template-columns: 1fr;
            }
            
            .hotel-grid {
                grid-template-columns: 1fr;
            }
            
            .features {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 576px) {
            .hero {
                height: 50vh;
            }
            
            .hero h1 {
                font-size: 1.8rem;
            }
            
            .btn {
                padding: 10px 16px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="main-header">
        <div class="container">
            <div class="logo">
                <h1><i class="fas fa-hotel"></i> Sheraton Hotels</h1>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="#" class="active">Home</a></li>
                    <li><a href="#">Destinations</a></li>
                    <li><a href="#">Offers</a></li>
                    <li><a href="#">About</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Find Your Perfect Stay</h1>
            <p>Discover luxury accommodations in the world's most desirable destinations</p>
            
            <!-- Search Form -->
            <form action="search.php" method="GET" class="search-form">
                <div class="form-group">
                    <label for="destination">Destination</label>
                    <input type="text" id="destination" name="destination" placeholder="Where are you going?" required>
                </div>
                <div class="form-group">
                    <label for="checkin">Check-in</label>
                    <input type="date" id="checkin" name="checkin" required>
                </div>
                <div class="form-group">
                    <label for="checkout">Check-out</label>
                    <input type="date" id="checkout" name="checkout" required>
                </div>
                <button type="submit" class="btn btn-primary">Search Hotels</button>
            </form>
        </div>
    </section>

    <!-- Featured Hotels -->
    <section class="featured-hotels">
        <div class="container">
            <h2>Featured Luxury Stays</h2>
            <div class="hotel-grid">
                <?php foreach ($featuredHotels as $hotel): ?>
                <div class="hotel-card">
                    <div class="hotel-image">
                        <img src="assets/images/<?php echo htmlspecialchars($hotel['image_url']); ?>" alt="<?php echo htmlspecialchars($hotel['name']); ?>">
                    </div>
                    <div class="hotel-info">
                        <div class="hotel-header">
                            <h3><?php echo htmlspecialchars($hotel['name']); ?></h3>
                            <div class="rating">
                                <i class="fas fa-star"></i>
                                <span><?php echo number_format($hotel['rating'], 1); ?></span>
                            </div>
                        </div>
                        <p class="location"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($hotel['location']); ?></p>
                        <p class="description"><?php echo substr(htmlspecialchars($hotel['description']), 0, 100) . '...'; ?></p>
                        <div class="amenities">
                            <?php 
                            $amenities = explode(',', $hotel['amenities']);
                            foreach (array_slice($amenities, 0, 3) as $amenity): ?>
                                <span class="amenity-tag"><?php echo trim($amenity); ?></span>
                            <?php endforeach; ?>
                        </div>
                        <div class="hotel-footer">
                            <div class="price">From $<?php echo number_format($hotel['price_per_night'], 0); ?> / night</div>
                            <a href="search.php?destination=<?php echo urlencode($hotel['location']); ?>" class="btn btn-outline">View Details</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="why-choose">
        <div class="container">
            <h2>Why Choose Sheraton Hotels?</h2>
            <div class="features">
                <div class="feature-card">
                    <i class="fas fa-crown"></i>
                    <h3>Luxury Experience</h3>
                    <p>Unparalleled comfort and premium amenities in every stay</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-map-marked-alt"></i>
                    <h3>Prime Locations</h3>
                    <p>Perfectly situated in the heart of major cities and destinations</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-shield-alt"></i>
                    <h3>Secure Booking</h3>
                    <p>Safe and reliable reservation system with instant confirmation</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-headset"></i>
                    <h3>24/7 Support</h3>
                    <p>Dedicated customer service available anytime you need assistance</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <h2><i class="fas fa-hotel"></i> Sheraton Hotels</h2>
                    <p>Luxury accommodations for the discerning traveler</p>
                </div>
                <div class="footer-links">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="#">Home</a></li>
                        <li><a href="#">Destinations</a></li>
                        <li><a href="#">Special Offers</a></li>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-contact">
                    <h4>Contact Us</h4>
                    <p><i class="fas fa-phone"></i> +1 (800) 123-4567</p>
                    <p><i class="fas fa-envelope"></i> info@sheraton.com</p>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2023 Sheraton Hotels. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Set min date for check-in to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('checkin').min = today;
        
        // Set min date for check-out to tomorrow
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        document.getElementById('checkout').min = tomorrow.toISOString().split('T')[0];
        
        // Update check-out min when check-in changes
        document.getElementById('checkin').addEventListener('change', function() {
            const checkinDate = new Date(this.value);
            const checkoutMin = new Date(checkinDate);
            checkoutMin.setDate(checkoutMin.getDate() + 1);
            document.getElementById('checkout').min = checkoutMin.toISOString().split('T')[0];
        });
    </script>
</body>
</html>
