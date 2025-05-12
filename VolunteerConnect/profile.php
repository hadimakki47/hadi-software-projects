<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}
require 'api/config.php';
$stmt = $pdo->prepare("SELECT first_name, last_name, email, created_at FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Your Profile | VolunteerConnect</title>
  <link rel="stylesheet" href="css/style.css"/>
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
</head>
<body>
  <div class="page-container">
    <!-- Navigation -->
    <nav class="navbar">
      <div class="container">
        <div class="navbar-content">
          <a href="index.php" class="logo">VolunteerConnect</a>
          <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="opportunities.php">Opportunities</a>
            <a href="about.php">About</a>
            <a href="contact.php">Contact</a>
          </div>
          <div class="auth-buttons">
            <a href="profile.php" class="btn btn-outline">Profile</a>
            <a href="logout.php"  class="btn btn-primary">Log Out</a>
          </div>
          <button class="mobile-menu-btn" aria-label="Toggle menu">
            <i class="fas fa-bars"></i>
          </button>
        </div>
        <div class="mobile-menu">
          <a href="index.php">Home</a>
          <a href="opportunities.php">Opportunities</a>
          <a href="about.php">About</a>
          <a href="contact.php">Contact</a>
          <div class="mobile-auth-buttons">
            <a href="profile.php" class="btn btn-outline">Profile</a>
            <a href="logout.php"  class="btn btn-primary">Log Out</a>
          </div>
        </div>
      </div>
    </nav>

    <main>
      <section class="signup-section">
        <div class="container auth-form-container">
          <h1>Your Profile</h1>
          <p><strong>First Name:</strong> <?= htmlspecialchars($user['first_name']) ?></p>
          <p><strong>Last Name:</strong> <?= htmlspecialchars($user['last_name']) ?></p>
          <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
          <p><strong>Member Since:</strong> <?= date('F j, Y', strtotime($user['created_at'])) ?></p>
        </div>
      </section>
    </main>

    <!-- Footer -->
    <footer class="footer">
      <div class="container">
        <div class="footer-grid">
          <div class="footer-col">
            <h3>VolunteerConnect</h3>
            <p>Connecting volunteers with meaningful opportunities to make a difference in their communities.</p>
            <div class="social-links">
              <a href="#"><i class="fab fa-facebook-f"></i></a>
              <a href="#"><i class="fab fa-twitter"></i></a>
              <a href="#"><i class="fab fa-instagram"></i></a>
              <a href="#"><i class="fab fa-linkedin-in"></i></a>
            </div>
          </div>
          <div class="footer-col">
            <h4>Quick Links</h4>
            <ul>
              <li><a href="index.php">Home</a></li>
              <li><a href="opportunities.php">Opportunities</a></li>
              <li><a href="about.php">About Us</a></li>
              <li><a href="contact.php">Contact</a></li>
            </ul>
          </div>
          <div class="footer-col">
            <h4>Resources</h4>
            <ul>
              <li><a href="faq.html">FAQ</a></li>
              <li><a href="blog.html">Blog</a></li>
              <li><a href="privacy.html">Privacy Policy</a></li>
              <li><a href="terms.html">Terms of Service</a></li>
            </ul>
          </div>
          <div class="footer-col">
            <h4>Contact Us</h4>
            <ul class="contact-info">
              <li><i class="fas fa-map-marker-alt"></i><span>123 Volunteer Street, Hamra, Beirut</span></li>
              <li><i class="fas fa-phone"></i><span>+961 70 956 293</span></li>
              <li><i class="fas fa-envelope"></i><span>info@volunteerconnect.com</span></li>
            </ul>
          </div>
        </div>
        <div class="footer-bottom">
          <p>&copy; <span id="current-year"></span> VolunteerConnect. All rights reserved.</p>
        </div>
      </div>
    </footer>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
  <script src="js/script.js"></script>
</body>
</html>
