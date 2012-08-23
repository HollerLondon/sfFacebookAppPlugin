<!DOCTYPE HTML>
<html>
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <link rel="shortcut icon" href="/favicon.ico" />
    <?php include_stylesheets() ?>
    <!--[if lte IE 8]>
      <link rel="stylesheet" type="text/css" href="/css/ie.css" />
    <![endif]-->
    <?php include_javascripts() ?>
  </head>
  <body>
    <div id="fb-root"></div>
    <script>
      window.fbAsyncInit = function() {
        FB.init({
          appId  : '<?php echo sfConfig::get('app_facebook_app_id'); ?>',
          status : true,
          cookie : true,
          xfbml  : true
        });
        FB.Canvas.setAutoGrow();
        FB.Canvas.setSize();
        //FB.Canvas.scrollTo(0,0);
        
        // To fix issues with whitespace
        // Set shortest page height (don't include error pages) - will intefere with scrollTo / # targeting
        //FB.Canvas.setSize({ width: 810, height: xxxx }); 
        //setTimeout(FB.Canvas.setAutoGrow, 250); // This is the lowest timeout possible
        
        <?php if (has_slot('fb_js')) include_slot('fb_js'); ?>
      };
      // Load async as causing scrollbars in FF4 otherwise
      (function() {
        var e = document.createElement('script'); e.async = true;
        e.src = document.location.protocol +
          '//connect.facebook.net/en_US/all.js';
        document.getElementById('fb-root').appendChild(e);
      }());
    </script>
    
    <div id="wrapper"<?php if ($sf_user->isMobile()) echo ' class="mobile"'; ?>>
      <div id="header">
        
      </div>
      
      <div id="content">
        <?php echo $sf_content ?>
      </div>
      
      <div id="footer">
        
      </div>
    </div>
  </body>
</html>
