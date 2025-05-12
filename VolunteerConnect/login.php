<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Log In | VolunteerConnect</title>
  <meta name="description" content="Log in to your VolunteerConnect account">
  <!-- Option B: CSS in css/ -->
  <link rel="stylesheet" href="css/style.css">
  <!-- Font Awesome -->
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            <?php if(isset($_SESSION['user_id'])): ?>
              <a href="profile.php" class="btn btn-outline">Profile</a>
              <a href="logout.php"  class="btn btn-primary">Log Out</a>
            <?php else: ?>
              <a href="login.php"   class="btn btn-outline" id="nav-login">Log In</a>
              <a href="signup.php"  class="btn btn-primary"  id="nav-signup">Sign Up</a>
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
      <section class="signup-section">
        <div class="container auth-form-container">
          <h1>Log In</h1>
          <form id="login-form" class="auth-form">
            <div class="form-group">
              <label for="loginEmail">Email Address</label>
              <input type="email" id="loginEmail" name="email" required>
            </div>
            <div class="form-group">
              <label for="loginPassword">Password</label>
              <input type="password" id="loginPassword" name="password" required>
            </div>
            <div class="form-group">
              <span class="error-text" id="loginError"></span>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Log In</button>
          </form>
          <div class="auth-links">
            <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
          </div>
        </div>
      </section>
    </main>

    <footer class="footer">
      <!-- your existing footer -->
    </footer>
  </div>

  <!-- Option B: JS in js/ -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
  <script src="js/script.js"></script>
</body>
</html>
