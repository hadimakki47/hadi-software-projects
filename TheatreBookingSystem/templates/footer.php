    </div> <!-- End of container -->
    <!-- Footer -->
    <footer class="footer mt-auto py-3 bg-dark text-white">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>Theatre Booking</h5>
                    <p>Book tickets for the best shows in town.</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="/index.php" class="text-white">Home</a></li>
                        <li><a href="/pages/shows.php" class="text-white">Shows</a></li>
                        <li><a href="/pages/about.php" class="text-white">About Us</a></li>
                        <li><a href="/pages/contact.php" class="text-white">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact</h5>
                    <address>
                        <i class="fas fa-map-marker-alt me-2"></i> 123 Theatre St, City<br>
                        <i class="fas fa-phone me-2"></i> (123) 456-7890<br>
                        <i class="fas fa-envelope me-2"></i> info@theatre.com
                    </address>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p>&copy; <?php echo date('Y'); ?> Theatre Booking. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Chat Widget -->
    <div id="chat-widget-container">
        <div class="chat-icon">
            <i class="fas fa-comments"></i>
            <span class="notification-badge"></span>
        </div>
        <div class="chat-window">
            <div class="chat-header">
                <h3>Support Chat</h3>
                <button class="chat-close"><i class="fas fa-times"></i></button>
            </div>
            <div class="chat-messages"></div>
            <div class="chat-input">
                <form>
                    <input type="text" placeholder="Type a message...">
                    <button type="submit"><i class="fas fa-paper-plane"></i></button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/js/script.js"></script>
    <link href="/css/chat-widget.css" rel="stylesheet">
    <script src="/js/chat-widget.js"></script>
</body>
</html> 