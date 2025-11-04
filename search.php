<?php
require_once 'db.php';

// Get search parameters
$destination = $_GET['destination'] ?? '';
$checkin = $_GET['checkin'] ?? '';
$checkout = $_GET['checkout'] ?? '';
$minPrice = $_GET['min_price'] ?? 0;
$maxPrice = $_GET['max_price'] ?? 10000;
$minRating = $_GET['min_rating'] ?? 0;
$sort = $_GET['sort'] ?? 'default';

// Build SQL query
$sql = "SELECT * FROM hotels WHERE 1=1";
$params = [];

if (!empty($destination)) {
    $sql .= " AND (name LIKE :destination OR location LIKE :destination)";
    $params[':destination'] = "%$destination%";
}

if ($minPrice > 0) {
    $sql .= " AND price_per_night >= :min_price";
    $params[':min_price'] = $minPrice;
}

if ($maxPrice < 10000) {
    $sql .= " AND price_per_night <= :max_price";
    $params[':max_price'] = $maxPrice;
}

if ($minRating > 0) {
    $sql .= " AND rating >= :min_rating";
    $params[':min_rating'] = $minRating;
}

// Add sorting
switch ($sort) {
    case 'price_low':
        $sql .= " ORDER BY price_per_night ASC";
        break;
    case 'price_high':
        $sql .= " ORDER BY price_per_night DESC";
        break;
    case 'rating':
        $sql .= " ORDER BY rating DESC";
        break;
    default:
        $sql .= " ORDER BY id ASC";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$hotels = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - Sheraton Hotels</title>
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

        /* Search Results Page */
        .search-header {
            background-color: var(--primary-color);
            color: white;
            padding: 40px 0;
        }

        .search-header h1 {
            color: white;
            margin-bottom: 10px;
        }

        .search-summary p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .search-layout {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 30px;
            padding: 40px 0;
        }

        .filters-sidebar {
            position: sticky;
            top: 100px;
        }

        .filter-card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: var(--shadow);
        }

        .filter-card h3 {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-color);
        }

        .filter-group {
            margin-bottom: 20px;
        }

        .filter-group h4 {
            margin-bottom: 15px;
        }

        .price-range {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .price-range label {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .rating-filter label {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            cursor: pointer;
        }

        .rating-stars {
            color: #ffc107;
        }

        .sort-form select {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-size: 16px;
            margin-top: 10px;
        }

        .no-results {
            text-align: center;
            padding: 60px 20px;
        }

        .no-results i {
            font-size: 4rem;
            color: var(--light-gray);
            margin-bottom: 20px;
        }

        /* Hotel Listings */
        .hotel-card {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
            margin-bottom: 30px;
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
            .search-layout {
                grid-template-columns: 1fr;
            }
            
            .filters-sidebar {
                position: static;
            }
            
            .main-header .container {
                flex-direction: column;
                gap: 15px;
            }
            
            .main-nav ul {
                flex-wrap: wrap;
                justify-content: center;
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
            
            .search-layout {
                grid-template-columns: 1fr;
            }
            
            .filters-sidebar {
                position: static;
            }
        }

        @media (max-width: 576px) {
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
                    <li><a href="index.php">Home</a></li>
                    <li><a href="#">Destinations</a></li>
                    <li><a href="#">Offers</a></li>
                    <li><a href="#">About</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Search Results Header -->
    <section class="search-header">
        <div class="container">
            <div class="search-summary">
                <h1>Search Results</h1>
                <p>
                    <?php echo count($hotels); ?> hotels found in 
                    <strong><?php echo htmlspecialchars($destination ?: 'all locations'); ?></strong>
                    <?php if (!empty($checkin) && !empty($checkout)): ?>
                        from <?php echo htmlspecialchars($checkin); ?> to <?php echo htmlspecialchars($checkout); ?>
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </section>

    <div class="container">
        <div class="search-layout">
            <!-- Filters Sidebar -->
            <aside class="filters-sidebar">
                <div class="filter-card">
                    <h3>Filters</h3>
                    <form method="GET" class="filter-form">
                        <input type="hidden" name="destination" value="<?php echo htmlspecialchars($destination); ?>">
                        <input type="hidden" name="checkin" value="<?php echo htmlspecialchars($checkin); ?>">
                        <input type="hidden" name="checkout" value="<?php echo htmlspecialchars($checkout); ?>">
                        
                        <div class="filter-group">
                            <h4>Price Range</h4>
                            <div class="price-range">
                                <label>
                                    Min: $<input type="number" name="min_price" value="<?php echo htmlspecialchars($minPrice); ?>" min="0" max="10000">
                                </label>
                                <label>
                                    Max: $<input type="number" name="max_price" value="<?php echo htmlspecialchars($maxPrice); ?>" min="0" max="10000">
                                </label>
                            </div>
                        </div>
                        
                        <div class="filter-group">
                            <h4>Minimum Rating</h4>
                            <div class="rating-filter">
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <label>
                                        <input type="radio" name="min_rating" value="<?php echo $i; ?>" 
                                            <?php echo ($minRating == $i) ? 'checked' : ''; ?>>
                                        <span class="rating-stars">
                                            <?php for ($j = 1; $j <= $i; $j++): ?>
                                                <i class="fas fa-star"></i>
                                            <?php endfor; ?>
                                            <?php for ($j = $i + 1; $j <= 5; $j++): ?>
                                                <i class="far fa-star"></i>
                                            <?php endfor; ?>
                                        </span>
                                    </label>
                                <?php endfor; ?>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                        <a href="search.php?destination=<?php echo urlencode($destination); ?>&checkin=<?php echo urlencode($checkin); ?>&checkout=<?php echo urlencode($checkout); ?>" class="btn btn-secondary">Reset Filters</a>
                    </form>
                </div>
                
                <div class="filter-card">
                    <h3>Sort By</h3>
                    <form method="GET" class="sort-form">
                        <input type="hidden" name="destination" value="<?php echo htmlspecialchars($destination); ?>">
                        <input type="hidden" name="checkin" value="<?php echo htmlspecialchars($checkin); ?>">
                        <input type="hidden" name="checkout" value="<?php echo htmlspecialchars($checkout); ?>">
                        <input type="hidden" name="min_price" value="<?php echo htmlspecialchars($minPrice); ?>">
                        <input type="hidden" name="max_price" value="<?php echo htmlspecialchars($maxPrice); ?>">
                        <input type="hidden" name="min_rating" value="<?php echo htmlspecialchars($minRating); ?>">
                        
                        <select name="sort" onchange="this.form.submit()">
                            <option value="default" <?php echo ($sort == 'default') ? 'selected' : ''; ?>>Recommended</option>
                            <option value="price_low" <?php echo ($sort == 'price_low') ? 'selected' : ''; ?>>Price: Low to High</option>
                            <option value="price_high" <?php echo ($sort == 'price_high') ? 'selected' : ''; ?>>Price: High to Low</option>
                            <option value="rating" <?php echo ($sort == 'rating') ? 'selected' : ''; ?>>Top Rated</option>
                        </select>
                    </form>
                </div>
            </aside>

            <!-- Hotel Listings -->
            <main class="hotel-listings">
                <?php if (empty($hotels)): ?>
                    <div class="no-results">
                        <i class="fas fa-hotel"></i>
                        <h3>No hotels found</h3>
                        <p>Try adjusting your search criteria or filters</p>
                        <a href="index.php" class="btn btn-primary">Search Again</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($hotels as $hotel): ?>
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
                            <p class="description"><?php echo htmlspecialchars($hotel['description']); ?></p>
                            <div class="amenities">
                                <?php 
                                $amenities = explode(',', $hotel['amenities']);
                                foreach ($amenities as $amenity): ?>
                                    <span class="amenity-tag"><?php echo trim($amenity); ?></span>
                                <?php endforeach; ?>
                            </div>
                            <div class="hotel-footer">
                                <div class="price">From $<?php echo number_format($hotel['price_per_night'], 0); ?> / night</div>
                                <?php if ($hotel['id']): ?>
                                    <a href="booking.php?hotel_id=<?php echo $hotel['id']; ?>&checkin=<?php echo urlencode($checkin); ?>&checkout=<?php echo urlencode($checkout); ?>" class="btn btn-primary">Book Now</a>
                                <?php else: ?>
                                    <span class="btn btn-secondary">Unavailable</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </main>
        </div>
    </div>

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
                        <li><a href="index.php">Home</a></li>
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
</body>
</html>
