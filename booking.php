<?php
require_once 'db.php';

// Get hotel and dates
$hotel_id = $_GET['hotel_id'] ?? 0;
$checkin = $_GET['checkin'] ?? '';
$checkout = $_GET['checkout'] ?? '';

// Validate hotel ID
if (!$hotel_id || !is_numeric($hotel_id)) {
    die("Invalid Hotel ID");
}

// Fetch hotel details
$stmt = $pdo->prepare("SELECT * FROM hotels WHERE id = ?");
$stmt->execute([$hotel_id]);
$hotel = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$hotel) {
    die("Hotel not found");
}

// Calculate number of nights and total price
$nights = 0;
$totalPrice = 0;
if (!empty($checkin) && !empty($checkout)) {
    $checkinDate = new DateTime($checkin);
    $checkoutDate = new DateTime($checkout);
    $nights = $checkinDate->diff($checkoutDate)->days;
    $totalPrice = $nights * $hotel['price_per_night'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Your Stay - Sheraton Hotels</title>
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

        /* Booking Page */
        .booking-header {
            background-color: var(--primary-color);
            color: white;
            padding: 40px 0;
        }

        .booking-header h1 {
            color: white;
            margin-bottom: 20px;
        }

        .booking-summary {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            align-items: center;
        }

        .booking-summary .hotel-info {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .booking-summary .hotel-info img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
        }

        .booking-summary .hotel-info h2 {
            color: white;
            margin-bottom: 5px;
        }

        .booking-summary .hotel-info p {
            opacity: 0.9;
        }

        .booking-dates {
            display: flex;
            justify-content: space-around;
        }

        .date-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
        }

        .date-item i {
            font-size: 1.5rem;
            margin-bottom: 5px;
        }

        .date-item span {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .date-item strong {
            font-size: 1.1rem;
        }

        .booking-layout {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            padding: 40px 0;
        }

        .booking-form-container {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: var(--shadow);
        }

        .booking-form-container h2 {
            margin-bottom: 25px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-size: 16px;
        }

        .payment-info {
            margin: 30px 0;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
        }

        .payment-info h3 {
            margin-bottom: 20px;
        }

        .booking-total {
            background-color: var(--light-color);
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid var(--border-color);
        }

        .total-row:last-child {
            border-bottom: none;
        }

        .total-row.total {
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--primary-color);
            padding-top: 10px;
            margin-top: 10px;
        }

        .hotel-details {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: var(--shadow);
        }

        .hotel-details .hotel-image {
            margin-bottom: 20px;
        }

        .hotel-details .hotel-image img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 8px;
        }

        .hotel-rating {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .review-count {
            color: var(--gray-color);
        }

        .hotel-description {
            margin-bottom: 25px;
            color: var(--gray-color);
        }

        .amenities-list {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .amenity-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .amenity-item i {
            color: var(--success-color);
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
            .booking-layout {
                grid-template-columns: 1fr;
            }
            
            .booking-summary {
                grid-template-columns: 1fr;
                text-align: center;
            }
            
            .booking-dates {
                justify-content: center;
                gap: 30px;
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
            
            .booking-layout {
                grid-template-columns: 1fr;
            }
            
            .booking-summary {
                grid-template-columns: 1fr;
                text-align: center;
            }
            
            .booking-dates {
                justify-content: center;
                gap: 30px;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 576px) {
            .btn {
                padding: 10px 16px;
                font-size: 14px;
            }
            
            .btn-block {
                margin-top: 10px;
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

    <!-- Booking Header -->
    <section class="booking-header">
        <div class="container">
            <h1>Book Your Stay</h1>
            <div class="booking-summary">
                <div class="hotel-info">
                    <img src="assets/images/<?php echo htmlspecialchars($hotel['image_url']); ?>" alt="<?php echo htmlspecialchars($hotel['name']); ?>">
                    <div>
                        <h2><?php echo htmlspecialchars($hotel['name']); ?></h2>
                        <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($hotel['location']); ?></p>
                    </div>
                </div>
                <div class="booking-dates">
                    <div class="date-item">
                        <i class="fas fa-calendar-check"></i>
                        <div>
                            <span>Check-in</span>
                            <strong><?php echo htmlspecialchars($checkin ?: 'Not selected'); ?></strong>
                        </div>
                    </div>
                    <div class="date-item">
                        <i class="fas fa-calendar-times"></i>
                        <div>
                            <span>Check-out</span>
                            <strong><?php echo htmlspecialchars($checkout ?: 'Not selected'); ?></strong>
                        </div>
                    </div>
                    <div class="date-item">
                        <i class="fas fa-bed"></i>
                        <div>
                            <span>Nights</span>
                            <strong><?php echo $nights; ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container">
        <div class="booking-layout">
            <!-- Booking Form -->
            <div class="booking-form-container">
                <h2>Guest Information</h2>
                <form action="confirm.php" method="POST" class="booking-form">
                    <input type="hidden" name="hotel_id" value="<?php echo $hotel_id; ?>">
                    <input type="hidden" name="checkin" value="<?php echo htmlspecialchars($checkin); ?>">
                    <input type="hidden" name="checkout" value="<?php echo htmlspecialchars($checkout); ?>">
                    <input type="hidden" name="total_price" value="<?php echo $totalPrice; ?>">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="guest_name">Full Name *</label>
                            <input type="text" id="guest_name" name="guest_name" required>
                        </div>
                        <div class="form-group">
                            <label for="guest_email">Email Address *</label>
                            <input type="email" id="guest_email" name="guest_email" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="room_type">Room Type</label>
                            <select id="room_type" name="room_type">
                                <option value="Standard">Standard Room</option>
                                <option value="Deluxe">Deluxe Room</option>
                                <option value="Executive">Executive Suite</option>
                                <option value="Presidential">Presidential Suite</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="special_requests">Special Requests</label>
                            <textarea id="special_requests" name="special_requests" rows="3"></textarea>
                        </div>
                    </div>
                    
                    <div class="payment-info">
                        <h3>Payment Information</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="card_number">Card Number *</label>
                                <input type="text" id="card_number" placeholder="1234 5678 9012 3456" required>
                            </div>
                            <div class="form-group">
                                <label for="card_expiry">Expiry Date *</label>
                                <input type="text" id="card_expiry" placeholder="MM/YY" required>
                            </div>
                            <div class="form-group">
                                <label for="card_cvv">CVV *</label>
                                <input type="text" id="card_cvv" placeholder="123" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="booking-total">
                        <div class="total-row">
                            <span>Price per night</span>
                            <span>$<?php echo number_format($hotel['price_per_night'], 2); ?></span>
                        </div>
                        <div class="total-row">
                            <span><?php echo $nights; ?> nights</span>
                            <span>$<?php echo number_format($totalPrice, 2); ?></span>
                        </div>
                        <div class="total-row total">
                            <span>Total</span>
                            <span>$<?php echo number_format($totalPrice, 2); ?></span>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">Confirm Booking</button>
                </form>
            </div>
            
            <!-- Hotel Details -->
            <div class="hotel-details">
                <h2>Hotel Details</h2>
                <div class="hotel-image">
                    <img src="assets/images/<?php echo htmlspecialchars($hotel['image_url']); ?>" alt="<?php echo htmlspecialchars($hotel['name']); ?>">
                </div>
                <div class="hotel-rating">
                    <div class="rating">
                        <i class="fas fa-star"></i>
                        <span><?php echo number_format($hotel['rating'], 1); ?></span>
                    </div>
                    <span class="review-count">(<?php echo rand(150, 500); ?> reviews)</span>
                </div>
                <p class="hotel-description"><?php echo htmlspecialchars($hotel['description']); ?></p>
                
                <h3>Amenities</h3>
                <div class="amenities-list">
                    <?php 
                    $amenities = explode(',', $hotel['amenities']);
                    foreach ($amenities as $amenity): ?>
                        <div class="amenity-item">
                            <i class="fas fa-check-circle"></i>
                            <span><?php echo trim($amenity); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
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

    <script>
        // Format card number input
        document.getElementById('card_number').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            let formattedValue = '';
            for (let i = 0; i < value.length; i++) {
                if (i > 0 && i % 4 === 0) formattedValue += ' ';
                formattedValue += value[i];
            }
            e.target.value = formattedValue.substring(0, 19);
        });
        
        // Format expiry date
        document.getElementById('card_expiry').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                e.target.value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
        });
    </script>
</body>
</html>
