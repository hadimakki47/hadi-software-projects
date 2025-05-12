<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Sign Up | VolunteerConnect</title>
  <meta name="description" content="Create an account to find volunteer opportunities">
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
          <h1>Create Your Account</h1>
          
          <form id="signup-form" class="auth-form">
            <div class="form-row">
              <div class="form-group">
                <label for="firstName">First Name</label>
                <input type="text" id="firstName" name="firstName">
                <span class="error-text" id="firstName-error"></span>
              </div>
              <div class="form-group">
                <label for="lastName">Last Name</label>
                <input type="text" id="lastName" name="lastName">
                <span class="error-text" id="lastName-error"></span>
              </div>
            </div>
            
            <div class="form-group">
              <label for="email">Email Address</label>
              <input type="email" id="email" name="email">
              <span class="error-text" id="email-error"></span>
            </div>
            
            <div class="form-group">
              <label for="password">Password</label>
              <div class="password-input">
                <input type="password" id="password" name="password">
                <button type="button" class="password-toggle" id="password-toggle">
                  <i class="far fa-eye"></i>
                </button>
              </div>
              <span class="error-text" id="password-error"></span>
              <p class="password-hint">Password must be at least 8 characters long</p>
            </div>
            
            <div class="form-group">
              <label for="confirmPassword">Confirm Password</label>
              <div class="password-input">
                <input type="password" id="confirmPassword" name="confirmPassword">
              </div>
              <span class="error-text" id="confirmPassword-error"></span>
            </div>
            
            <div class="form-group checkbox-group">
              <input type="checkbox" id="agreeTerms" name="agreeTerms">
              <label for="agreeTerms">
                I agree to the <a href="terms.html">Terms and Conditions</a> and <a href="privacy.html">Privacy Policy</a>
              </label>
              <span class="error-text" id="agreeTerms-error"></span>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Create Account</button>
          </form>
          
          <div class="auth-links">
            <p>Already have an account? <a href="login.php">Log In</a></p>
          </div>
          
          <div class="social-auth">
            <p>Or sign up with</p>
            <div class="social-buttons">
              <button class="social-btn google-btn"><i class="fab fa-google"></i> Google</button>
              <button class="social-btn facebook-btn"><i class="fab fa-facebook-f"></i> Facebook</button>
            </div>
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
