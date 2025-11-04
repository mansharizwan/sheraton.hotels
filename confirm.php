<?php
require_once 'db.php';

// Process booking
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hotel_id = $_POST['hotel_id'] ?? 0;
    $guest_name = $_POST['guest_name'] ?? '';
    $guest_email = $_POST['guest_email'] ?? '';
    $checkin = $_POST['checkin'] ?? '';
    $checkout = $_POST['checkout'] ?? '';
    $room_type = $_POST['room_type'] ?? 'Standard';
    $total_price = $_POST['total_price'] ?? 0;
    
    // Validate inputs
    if (!$hotel_id || !is_numeric($hotel_id)) {
        die("Invalid Hotel ID");
    }
    
    if (empty($guest_name) || empty($guest_email) || empty($checkin) || empty($checkout)) {
        die("Required fields are missing");
    }
    
    // Insert booking into database
    $stmt = $pdo->prepare("INSERT INTO bookings (hotel_id, guest_name, guest_email, check_in, check_out, room_type, total_price) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$hotel_id, $guest_name, $guest_email, $checkin, $checkout, $room_type, $total_price]);
    $booking_id = $pdo->lastInsertId();
    
    // Fetch hotel details for confirmation
    $stmt = $pdo->prepare("SELECT * FROM hotels WHERE id = ?");
    $stmt->execute([$hotel_id]);
    $hotel = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$hotel) {
        die("Hotel not found");
    }
} else {
    // Redirect if accessed directly
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed - Sheraton Hotels</title>
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

        /* Confirmation Page */
        .confirmation-section {
            padding: 80px 0;
            background-color: var(--light-color);
        }

        .confirmation-card {
            background-color: white;
            border-radius: 10px;
            padding: 50px;
            text-align: center;
            max-width: 700px;
            margin: 0 auto;
            box-shadow: var(--shadow);
        }

        .confirmation-icon {
            width: 100px;
            height: 100px;
            background-color: rgba(40, 167, 69, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
        }

        .confirmation-icon i {
            font-size: 4rem;
            color: var(--success-color);
        }

        .confirmation-card h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: var(--success-color);
        }

        .booking-details {
            text-align: left;
            margin: 40px 0;
            padding: 20px;
            background-color: var(--light-color);
            border-radius: 8px;
        }

        .booking-details h2 {
            margin-bottom: 20px;
            text-align: center;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid var(--border-color);
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .confirmation-actions {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 30px 0;
        }

        .confirmation-note {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
            color: var(--gray-color);
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
            
            .confirmation-card {
                padding: 30px 20px;
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
            
            .confirmation-card {
                padding: 30px 20px;
            }
            
            .booking-details {
                padding: 15px;
            }
        }

        @media (max-width: 576px) {
            .confirmation-card {
                padding: 20px 15px;
            }
            
            .btn {
                padding: 10px 16px;
                font-size: 14px;
            }
            
            .confirmation-actions {
                flex-direction: column;
                gap: 10px;
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

    <!-- Confirmation Section -->
    <section class="confirmation-section">
        <div class="container">
            <div class="confirmation-card">
                <div class="confirmation-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h1>Booking Confirmed!</h1>
                <p>Thank you for choosing Sheraton Hotels. Your reservation has been successfully processed.</p>
                
                <div class="booking-details">
                    <h2>Booking Details</h2>
                    <div class="detail-row">
                        <span>Confirmation Number:</span>
                        <strong>#<?php echo str_pad($booking_id, 6, '0', STR_PAD_LEFT); ?></strong>
                    </div>
                    <div class="detail-row">
                        <span>Hotel:</span>
                        <strong><?php echo htmlspecialchars($hotel['name']); ?></strong>
                    </div>
                    <div class="detail-row">
                        <span>Location:</span>
                        <strong><?php echo htmlspecialchars($hotel['location']); ?></strong>
                    </div>
                    <div class="detail-row">
                        <span>Check-in:</span>
                        <strong><?php echo htmlspecialchars($checkin); ?></strong>
                    </div>
                    <div class="detail-row">
                        <span>Check-out:</span>
                        <strong><?php echo htmlspecialchars($checkout); ?></strong>
                    </div>
                    <div class="detail-row">
                        <span>Room Type:</span>
                        <strong><?php echo htmlspecialchars($room_type); ?></strong>
                    </div>
                    <div class="detail-row">
                        <span>Guest Name:</span>
                        <strong><?php echo htmlspecialchars($guest_name); ?></strong>
                    </div>
                    <div class="detail-row">
                        <span>Total Amount:</span>
                        <strong>$<?php echo number_format($total_price, 2); ?></strong>
                    </div>
                </div>
                
                <div class="confirmation-actions">
                    <a href="index.php" class="btn btn-primary">Book Another Stay</a>
                    <a href="#" class="btn btn-outline">Download Confirmation</a>
                </div>
                
                <div class="confirmation-note">
                    <p>A confirmation email has been sent to <strong><?php echo htmlspecialchars($guest_email); ?></strong></p>
                    <p>If you have any questions, please contact our customer service at +1 (800) 123-4567</p>
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
