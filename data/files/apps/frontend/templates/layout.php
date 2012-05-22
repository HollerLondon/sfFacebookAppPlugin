<!DOCTYPE HTML>
<html>
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <link rel="shortcut icon" href="/favicon.ico" />
    <?php include_stylesheets() ?>
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
      };
      // Load async as causing scrollbars in FF4 otherwise
      (function() {
        var e = document.createElement('script'); e.async = true;
        e.src = document.location.protocol +
          '//connect.facebook.net/en_US/all.js';
        document.getElementById('fb-root').appendChild(e);
      }());
    </script>
    
    <div id="wrapper">
      <div id="header">
        
      </div>
      
      <div id="content">
        <?php echo $sf_content ?>
      </div>
      
      <div id="footer">
        
      </div>
    </div>
      
    <?php include_javascripts() ?>
  </body>
</html>
