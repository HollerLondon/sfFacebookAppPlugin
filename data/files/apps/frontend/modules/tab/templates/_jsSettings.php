<script type="text/javascript">
  var Settings = {
    scope           : '<?php echo sfConfig::get('app_facebook_app_scope'); ?>',
    authUser        : '<?php echo url_for('@authUser'); ?>',
    deAuthUser      : '<?php echo url_for('@deAuthUser'); ?>',
    homepage        : '<?php echo url_for('@homepage', true); ?>',
  };
</script>