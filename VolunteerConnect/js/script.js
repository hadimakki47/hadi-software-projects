// js/script.js
$(function(){
  // --- Navbar auth button toggling ---
  const $loginBtn   = $('#nav-login'),
        $signupBtn  = $('#nav-signup'),
        $profileBtn = $('#nav-profile'),
        $logoutBtn  = $('#nav-logout');

  function updateNav(loggedIn) {
    $loginBtn.toggle(!loggedIn);
    $signupBtn.toggle(!loggedIn);
    $profileBtn.toggle(loggedIn);
    $logoutBtn.toggle(loggedIn);
  }

  // initialize as logged out
  updateNav(false);

  // --- Mobile menu toggle ---
  $('.mobile-menu-btn').click(() => {
    $('.mobile-menu').toggleClass('active');
  });

  // --- Footer year ---
  $('#current-year').text(new Date().getFullYear());

  // --- Contact form AJAX ---
  $('#contact-form').submit(function(e){
    e.preventDefault();
    const $btn     = $('#submit-btn'),
          $text    = $('#submit-text'),
          $spinner = $('#submit-spinner'),
          $success = $('#success-message');

    $btn.prop('disabled', true);
    $text.hide();
    $spinner.removeClass('hidden');

    $.post('api/contact_submit.php', $(this).serialize())
      .done(() => {
        $spinner.addClass('hidden');
        $text.show();
        $(this).addClass('hidden');
        $success.removeClass('hidden');
      })
      .fail(() => {
        alert('Error sending message.');
      })
      .always(() => {
        $btn.prop('disabled', false);
      });
  });

  // --- Newsletter subscribe ---
  $('.newsletter-form').submit(function(e){
    e.preventDefault();
    const email = $(this).find('input[type=email]').val().trim();
    if (!email) return alert('Please enter an email.');

    $.post('api/subscribe.php', { email })
      .done(() => alert('Subscribed!'))
      .fail(() => alert('Subscription failed.'));
  });

  // --- Signup form validation + AJAX ---
  $('#signup-form').submit(function(e){
    e.preventDefault();
    let valid = true;
    $('.error-text').text('');

    if (!$('#firstName').val().trim()) {
      valid = false;
      $('#firstName-error').text('First name is required');
    }
    if (!$('#lastName').val().trim()) {
      valid = false;
      $('#lastName-error').text('Last name is required');
    }
    const emailVal = $('#email').val().trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(emailVal)) {
      valid = false;
      $('#email-error').text('Please enter a valid email');
    }
    const pw = $('#password').val();
    if (pw.length < 8) {
      valid = false;
      $('#password-error').text('Password must be at least 8 characters');
    }
    const cpw = $('#confirmPassword').val();
    if (cpw !== pw) {
      valid = false;
      $('#confirmPassword-error').text('Passwords do not match');
    }
    if (!$('#agreeTerms').is(':checked')) {
      valid = false;
      $('#agreeTerms-error').text('You must agree to the terms');
    }
    if (!valid) return;

    $.post('api/signup.php', $(this).serialize())
      .done(() => {
        updateNav(true);
        window.location = 'profile.php';
      })
      .fail(() => {
        alert('Email already exists.');
      });
  });

  // --- Login form validation + AJAX ---
  $('#login-form').submit(function(e){
    e.preventDefault();
    $('#loginError').text('');

    const email = $('#loginEmail').val().trim(),
          pw    = $('#loginPassword').val().trim();
    if (!email || !pw) {
      return $('#loginError').text('Both email and password are required.');
    }

    $.post('api/login.php', { email, password: pw })
      .done(() => {
        updateNav(true);
        // Redirect to index.php after successful login
        window.location = 'index.php';
      })
      .fail(() => {
        $('#loginError').text('Invalid email or password.');
      });
  });

  // --- Logout button click ---
  $logoutBtn.click(function(){
    // Optionally call a logout endpoint here, then:
    updateNav(false);
    window.location = 'index.php';
  });

  // --- Password visibility toggle ---
  $('#password-toggle').click(function(){
    const $pw = $('#password, #confirmPassword');
    const type = $pw.attr('type') === 'password' ? 'text' : 'password';
    $pw.attr('type', type);
    $(this).find('i').toggleClass('fa-eye fa-eye-slash');
  });

  // --- Load opportunities via AJAX ---
  function loadOpps(filters = {}) {
    $.getJSON('api/get_opportunities.php', filters, function(data){
      const $grid = $('.opportunities-grid').empty();
      data.forEach(opp => {
        $grid.append(`
          <div class="opportunity-card">
            <div class="opportunity-image">
              <img src="images/${opp.image||'default.jpg'}" alt="${opp.title}">
              ${opp.is_new ? '<span class="opportunity-badge new-badge">New</span>' : ''}
            </div>
            <div class="opportunity-content">
              <div class="opportunity-header">
                <span class="badge badge-${opp.category.toLowerCase()}">${opp.category}</span>
                <button class="favorite-btn" data-id="${opp.id}">
                  <i class="${opp.favorited ? 'fas' : 'far'} fa-heart"></i>
                </button>
              </div>
              <h3>${opp.title}</h3>
              <p>${opp.description}</p>
              <div class="opportunity-details">
                <div class="detail"><i class="fas fa-calendar"></i><span>${opp.date}</span></div>
                <div class="detail"><i class="fas fa-map-marker-alt"></i><span>${opp.location}</span></div>
                <div class="detail"><i class="fas fa-users"></i><span>${opp.volunteers_needed} needed</span></div>
              </div>
            </div>
          </div>
        `);
      });
    });
  }

  loadOpps();
  $('#search-input, #category, #location, #commitment').on('keyup change', function(){
    loadOpps({
      q: $('#search-input').val(),
      category: $('#category').val(),
      location: $('#location').val(),
      commitment: $('#commitment').val()
    });
  });

  // --- Favorite toggle (AJAX) ---
  $(document).on('click', '.favorite-btn', function(){
    const oppId = $(this).data('id');
    $.post('api/toggle_favorite.php', { opp_id: oppId })
      .fail(() => alert('Please log in to favorite.'));
  });

  // --- Grid/List view toggle ---
  $('.grid-view').click(function(){
    $('.opportunities-grid').removeClass('list-view');
    $(this).addClass('active');
    $('.list-view').removeClass('active');
  });
  $('.list-view').click(function(){
    $('.opportunities-grid').addClass('list-view');
    $(this).addClass('active');
    $('.grid-view').removeClass('active');
  });
});
