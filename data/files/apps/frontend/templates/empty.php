<!DOCTYPE HTML>
<html>
  <head>
    <?php // for OG ?>
    <?php if (has_slot('meta')) include_slot('meta'); // open graph details ?>
  </head>
  <body>
    <?php echo $sf_content ?>
  </body>
</html>