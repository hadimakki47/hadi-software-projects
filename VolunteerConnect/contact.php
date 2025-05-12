<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Contact Us | VolunteerConnect</title>
  <meta name="description" content="Get in touch with the VolunteerConnect team">
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
            <a href="about.php">About</a>
            <a href="contact.php" class="active">Contact</a>
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
      <section class="contact-section">
        <div class="container">
          <h1 class="text-center">Contact Us</h1>
          <p class="text-center section-description">
            Have questions about volunteering or need help with your account? We're here to assist you. Fill out the form below or use our contact information.
          </p>
          <div class="contact-container">
            <div class="contact-form-container">
              <div id="success-message" class="success-message hidden">
                <h3>Message Sent Successfully!</h3>
                <p>Thank you for reaching out. We've received your message and will get back to you as soon as possible.</p>
              </div>
              <form id="contact-form" class="contact-form">
                <div class="form-row">
                  <div class="form-group">
                    <label for="name">Your Name</label>
                    <input type="text" id="name" name="name" required>
                  </div>
                  <div class="form-group">
                    <label for="email">Your Email</label>
                    <input type="email" id="email" name="email" required>
                  </div>
                </div>
                <div class="form-group">
                  <label for="subject">Subject</label>
                  <select id="subject" name="subject" required>
                    <option value="">Select a subject</option>
                    <option value="General Inquiry">General Inquiry</option>
                    <option value="Volunteer Opportunities">Volunteer Opportunities</option>
                    <option value="Organization Registration">Organization Registration</option>
                    <option value="Technical Support">Technical Support</option>
                    <option value="Feedback">Feedback</option>
                    <option value="Other">Other</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="message">Your Message</label>
                  <textarea id="message" name="message" rows="6" required></textarea>
                </div>
                <div id="error-message" class="error-message hidden"></div>
                <button type="submit" class="btn btn-primary btn-block" id="submit-btn">
                  <span id="submit-text">Send Message</span>
                  <span id="submit-spinner" class="spinner hidden"><i class="fas fa-spinner fa-spin"></i></span>
                  <i class="fas fa-paper-plane"></i>
                </button>
              </form>
            </div>
            <div class="contact-info-container">
              <div class="contact-info-box">…</div>
              <div class="contact-info-box">…</div>
            </div>
          </div>
        </div>
      </section>
    </main>
    
    <footer class="footer">…</footer>
  </div>
  
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
  <script src="js/script.js"></script>
</body>
</html>
