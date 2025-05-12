<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VolunteerConnect | Find Volunteer Opportunities</title>
    <meta name="description" content="Find and connect with volunteer opportunities in your community">
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
                  <a href="index.php" class="active">Home</a>
                  <a href="opportunities.php">Opportunities</a>
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
            <!-- Hero Section -->
            <section class="hero">
                <div class="container">
                    <div class="hero-content">
                        <div class="hero-text">
                            <h1>Make a <span class="text-primary">Difference</span> in Your Community</h1>
                            <p>Find meaningful volunteer opportunities that match your skills and interests. Connect with organizations making a positive impact.</p>
                            <div class="hero-buttons">
                                <a href="opportunities.php" class="btn btn-primary">Find Opportunities</a>
                                <a href="about.php" class="btn btn-outline">Learn More</a>
                            </div>
                        </div>
                        <div class="hero-image">
                            <img src="images/WhatsApp Image 2025-03-02 at 20.28.23_23b9b7db.jpg" alt="Volunteers working together" class="object-cover w-full h-full">
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Search Section -->
            <section class="search-section">
                <div class="container">
                    <div class="search-box">
                        <h2>Find Volunteer Opportunities</h2>
                        <div class="search-form">
                            <div class="search-input">
                                <i class="fas fa-search"></i>
                                <input type="text" placeholder="Search for opportunities...">
                            </div>
                            <select>
                                <option value="">All Categories</option>
                                <option value="education">Education</option>
                                <option value="environment">Environment</option>
                                <option value="health">Health</option>
                                <option value="animals">Animals</option>
                                <option value="community">Community</option>
                            </select>
                            <select>
                                <option value="">All Locations</option>
                                <option value="remote">Remote</option>
                                <option value="local">Local</option>
                                <option value="international">International</option>
                            </select>
                            <button class="btn btn-primary">Search</button>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Featured Opportunities -->
            <section id="opportunities" class="opportunities">
                <div class="container">
                    <div class="section-header">
                        <h2>Featured Opportunities</h2>
                        <a href="opportunities.php" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
                    </div>
                    <div class="opportunities-grid">
                        <!-- Opportunity cards will be loaded here -->
                    </div>
                </div>
            </section>
            
            <!-- Impact Section -->
            <section class="impact-section">
                <div class="container">
                    <h2 class="text-center">Our Impact</h2>
                    <div class="impact-stats">
                        <div class="stat-card">
                            <div class="stat-number">5,000+</div>
                            <p>Volunteers Connected</p>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number">350+</div>
                            <p>Partner Organizations</p>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number">25,000+</div>
                            <p>Volunteer Hours</p>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Testimonials -->
            <section class="testimonials-section">
                <div class="container">
                    <h2 class="text-center">What Our Volunteers Say</h2>
                    <div class="testimonials-grid">
                        <div class="testimonial-card">
                            <div class="testimonial-header">
                                <div class="testimonial-avatar">
                                    <img src="https://placehold.co/100x100" alt="Volunteer">
                                </div>
                                <div>
                                    <h4>Sarah Johnson</h4>
                                    <p>Beach Cleanup Volunteer</p>
                                </div>
                            </div>
                            <p>"VolunteerConnect made it so easy to find opportunities that matched my interests. The beach cleanup was well-organized and I felt like I really made a difference."</p>
                        </div>
                        
                        <div class="testimonial-card">
                            <div class="testimonial-header">
                                <div class="testimonial-avatar">
                                    <img src="https://placehold.co/100x100" alt="Volunteer">
                                </div>
                                <div>
                                    <h4>Michael Chen</h4>
                                    <p>Tutor</p>
                                </div>
                            </div>
                            <p>"Tutoring through VolunteerConnect has been incredibly rewarding. Seeing the students' progress each week makes all the difference, and the platform made it easy to find a position that worked with my schedule."</p>
                        </div>
                        
                        <div class="testimonial-card">
                            <div class="testimonial-header">
                                <div class="testimonial-avatar">
                                    <img src="https://placehold.co/100x100" alt="Volunteer">
                                </div>
                                <div>
                                    <h4>Jessica Rodriguez</h4>
                                    <p>Food Bank Volunteer</p>
                                </div>
                            </div>
                            <p>"I've been volunteering at the food bank for six months now, and it's been an amazing experience. VolunteerConnect made the application process simple, and I've met so many wonderful people."</p>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- CTA Section -->
            <section class="cta-section">
                <div class="container">
                    <h2>Ready to Make a Difference?</h2>
                    <p>Join thousands of volunteers who are creating positive change in their communities.</p>
                    <div class="cta-buttons">
                        <a href="signup.php" class="btn btn-white">Sign Up Now</a>
                        <a href="opportunities.php" class="btn btn-outline-white">Browse Opportunities</a>
                    </div>
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
