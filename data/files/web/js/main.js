var MOBILE = {
  /**
   * Init mobile auth
   */
  init : function() {
    // Scroll to hide address bar on mobile (iOS / Android) - timeout is necessary
    if ($('#wrapper').hasClass('mobile')) {
      setTimeout(function() {
        try { window.scrollTo(0, 1); } catch(e) { }
      }, 0);
    }
    
    if ($('#fb-logout')) $(document).on('click', '#fb-logout', MOBILE.logout);
    
    // If hooking into a class for auth then add here too
  },
  /**
   * Check authorisation status of app and act accordingly
   * Called from fb_js slot in indexSuccess.php (or you could add a class and hook it into an event there)
   * @param JSON response
   */
  checkLoginStatus : function(response) {
    if (response && response.status == 'connected') {
      try {
        _gaq.push(['_trackPageview', '/user/login']);
      } catch(e) { }
      
      $.ajax({
        type: 'GET',
        dataType: 'json', // if returning partial set to 'html',
        url: Settings.authUser + '?signed_request=' + response.authResponse.signedRequest + '&access_token=' + response.authResponse.accessToken + '&user_id=' + response.authResponse.userID,
        success: function(response, success, xhr) {
          // If returning JSON url then reload page
          if (response.redirect) {
            window.location = response.redirect;
          }
          
          // if returning partial
          //$('#some-id').html(response); // obv update id to match

          // if returning partial add logout button
          //$('#footer').append('<div id="fb-logout">Logout</div>');
          //$(document).on('click', '#fb-logout', MOBILE.logout);
        }
      });
    }
    // Not auth'd or not logged in (NOTE: logged out won't work if app sandboxed)
    else {
      // Change button to say Login with Facebook and add onclick event to call
      $('#facebook-login').html('Log in with Facebook'); // obv update id to match
      $(document).on('click', '#facebook-login', MOBILE.requestAuth);
      
      // Or return partial if using cacophony
      // Or just trigger MOBILE.requestAuth directly if you've hooked into a class to call this method
    }
  },
  
  /**
   * Log in with Facebook (and auth app)
   * Not used if you get a partial with Cacophony for the response.status != 'connected' section
   * @param Event e
   */
  requestAuth : function(e) {
    e.preventDefault();
    FB.login(MOBILE.checkLoginStatus, { scope: Settings.scope });
  },
  
  /**
   * Log user out from Facebook and site (as per Facebook guidelines)
   * @param Event e
   */
  logout: function(e) {
    e.preventDefault();
    
    try {
      _gaq.push(['_trackPageview', '/user/logout']);
    } catch(e) { }
    
    FB.logout(function(response) {
      // call AJAX function to do sfGuard/logout
      $.ajax({
        url :      Settings.deAuthUser,
        dataType : 'json',
        success :  function(response) {
          window.location = Settings.homepage;
        }
      });
    });
  }
};

/**
 * Set up the site when document ready
 */
$(document).ready(function() {
  // Try and hide web debug to keep out of the way on dev
  try { sfWebDebugToggleMenu(); } catch(e) { }
  
  // IE fix for errant console.log
  if (typeof console === "undefined") console = { log: function() { } };
  
  // MOBILE auth
  MOBILE.init();
  
  // ADD PROJECT CODE HERE
});