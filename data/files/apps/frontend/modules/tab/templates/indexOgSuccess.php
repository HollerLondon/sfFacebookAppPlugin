<?php function opengraph_meta($key,$value)
{
  return tag('meta', array(
    'property'    => sprintf("%s:%s",
      preg_match('/^(admins|app_id)$/', $key) ? 'fb' : 'og',
      $key
    ),
    'content'     => $value
  ));
} 

slot('meta');
  foreach ($metas as $key => $value)
  {
    echo opengraph_meta($key, $value);
  }
end_slot();

echo $auth_url;