<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Volunteer Opportunities | VolunteerConnect</title>
  <meta name="description" content="Browse volunteer opportunities in your community">
  <link rel="stylesheet" href="css/style.css">
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <div class="page-container">
    <!-- Navigation -->
    <nav class="navbar">
      <div class="container">
        <div class="navbar-content">
          <a href="index.php" class="logo">VolunteerConnect</a>
          
          <!-- Desktop Navigation -->
          <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="opportunities.php" class="active">Opportunities</a>
            <a href="about.php">About</a>
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
          
          <!-- Mobile menu button -->
          <button class="mobile-menu-btn" aria-label="Toggle menu">
            <i class="fas fa-bars"></i>
          </button>
        </div>
        
        <!-- Mobile Navigation -->
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
      <section class="opportunities-section">
        <div class="container">
          <h1 class="text-center">Volunteer Opportunities</h1>
          <p class="text-center section-description">
            Browse through our curated list of volunteer opportunities and find the perfect way to make a difference in your community.
          </p>
          
          <!-- Search and Filter Section -->
          <div class="search-filter-box">
            <div class="search-row">
              <div class="search-input-container">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search for opportunities..." id="search-input">
              </div>
              <button class="btn btn-primary search-btn">
                <i class="fas fa-search"></i> Search
              </button>
            </div>
            
            <div class="filter-row">
              <div class="filter-group">
                <label for="category">Category</label>
                <select id="category">
                  <option value="">All Categories</option>
                  <option value="education">Education</option>
                  <option value="environment">Environment</option>
                  <option value="health">Health</option>
                  <option value="animals">Animals</option>
                  <option value="community">Community</option>
                  <option value="arts">Arts & Culture</option>
                  <option value="seniors">Senior Care</option>
                </select>
              </div>
              
              <div class="filter-group">…</div>
              <div class="filter-group">…</div>
              <div class="filter-group">…</div>
            </div>
            
            <div class="filter-actions">
              <button class="advanced-filter-btn"><i class="fas fa-filter"></i> Advanced Filters</button>
              <button class="clear-filter-btn">Clear All Filters</button>
            </div>
          </div>
          
          <!-- Results Section -->
          <div class="results-section">
            <div class="results-header">
              <p class="results-count">Showing <span class="font-semibold">24</span> opportunities</p>
              <div class="view-toggle">
                <span>View:</span>
                <button class="view-btn grid-view active"><i class="fas fa-th"></i></button>
                <button class="view-btn list-view"><i class="fas fa-list"></i></button>
              </div>
            </div>
            
            <div class="opportunities-grid">
              <!-- Opportunity Card 1 -->
              <div class="opportunity-card">…</div>
              <!-- Opportunity Card 2 -->
              <div class="opportunity-card">…</div>
              <!-- Opportunity Card 3 -->
              <div class="opportunity-card">…</div>
            </div>
            
            <!-- Pagination -->
            <div class="pagination">…</div>
          </div>
          
          <!-- Newsletter Section -->
          <div class="newsletter-box">…</div>
        </div>
      </section>
    </main>
    
    <!-- Footer -->
    <footer class="footer">…</footer>
  </div>
  
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
  <script src="js/script.js"></script>
</body>
</html>
