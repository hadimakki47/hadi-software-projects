<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>About Us | VolunteerConnect</title>
  <meta name="description" content="Learn about VolunteerConnect's mission and the team behind the platform">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <div class="page-container">
    <nav class="navbar">
      <div class="container">
        <div class="navbar-content">
          <a href="index.php" class="logo">VolunteerConnect</a>
          <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="opportunities.php">Opportunities</a>
            <a href="about.php" class="active">About</a>
            <a href="contact.php">Contact</a>
          </div>
          <div class="auth-buttons">
            <?php if(isset($_SESSION['user_id'])): ?>
              <a href="profile.php" class="btn btn-outline">Profile</a>
              <a href="logout.php"  class="btn btn-primary">Log Out</a>
            <?php else: ?>
              <a href="login.php"   class="btn btn-outline">Log In</a>
              <a href="signup.php"  class="btn btn-primary">Sign Up</a>
            <?php endif; ?>
          </div>
          <button class="mobile-menu-btn"><i class="fas fa-bars"></i></button>
        </div>
        <div class="mobile-menu">
          <a href="index.php">Home</a>
          <a href="opportunities.php">Opportunities</a>
          <a href="about.php">About</a>
          <a href="contact.php">Contact</a>
          <div class="mobile-auth-buttons">
            <?php if(isset($_SESSION['user_id'])): ?>
              <a href="profile.php" class="btn btn-outline">Profile</a>
              <a href="logout.php"  class="btn btn-primary">Log Out</a>
            <?php else: ?>
              <a href="login.php"   class="btn btn-outline">Log In</a>
              <a href="signup.php"  class="btn btn-primary">Sign Up</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </nav>
    
    <main>
      <section class="about-section">
        <div class="container">
          <h1 class="text-center">About VolunteerConnect</h1>
          <div class="about-image">
            <img src="images/volunteer-connect.jpg" alt="Team of volunteers">
          </div>
          <div class="about-content">
            <h2>Our Mission</h2>
            <p>At VolunteerConnect, our mission is to bridge the gap…</p>
            <h2>Our Story</h2>
            <p>VolunteerConnect was founded in 2025 by Hadi Makki…</p>
            <p>Our platform has facilitated thousands…</p>
            <h2>Our Values</h2>
            <ul>
              <li><strong>Community Impact:</strong> …</li>
              <li><strong>Accessibility:</strong> …</li>
              <li><strong>Transparency:</strong> …</li>
              <li><strong>Diversity and Inclusion:</strong> …</li>
              <li><strong>Continuous Improvement:</strong> …</li>
            </ul>
          </div>
          <div class="how-it-works">…</div>
          <div class="team-section">…</div>
          <div class="why-choose-us">…</div>
          <div class="join-cta">…</div>
        </div>
      </section>
    </main>
    
    <footer class="footer">…</footer>
  </div>
  
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
  <script src="js/script.js"></script>
</body>
</html>
