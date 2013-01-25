<!DOCTYPE HTML>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="title" content="##PROJECTFNAME##" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <title>##PROJECTFNAME##</title>
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="stylesheet" type="text/css" media="screen" href="/css/main.css" />
    <!--[if lte IE 8]>
      <link rel="stylesheet" type="text/css" href="/css/ie.css" />
    <![endif]-->
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
    <script type="text/javascript" src="/js/main.js"></script>
  </head>
  <body>
    <div id="fb-root"></div>
    <script>
      window.fbAsyncInit = function() {
        FB.init({
          appId  : '<!-- LIVE APP ID -->',
          status : true,
          cookie : true,
          xfbml  : true
        });
        FB.Canvas.setAutoGrow();
        //FB.Canvas.setSize();
        //FB.Canvas.scrollTo(0,0);
        
        // To fix issues with whitespace
        // Set shortest page height (don't include error pages) - will intefere with scrollTo / # targeting
        //FB.Canvas.setSize({ width: 810, height: xxxx }); 
        //setTimeout(FB.Canvas.setAutoGrow, 250); // This is the lowest timeout possible
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
        <h1>We're just updating the page, check back soon.</h1>
      </div>
      
      <div id="footer">
        
      </div>
    </div>
  </body>
</html>