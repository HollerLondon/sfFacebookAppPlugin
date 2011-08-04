<?php

/**
 * default modules
 */

class BasesfFacebookAppTabActions extends sfActions
{
  /**
   * default tab action that redirects to the @homepage route
   */
  public function executeTab(sfWebRequest $request)
  {
    $home_route = sfConfig::get('app_facebook_homepage', '@homepage');
    $this->redirect($home_route . '?signed_request=' . $this->signed_request);
  }
  
  /**
   * authorises the app
   */
  public function executeAuth(sfWebRequest $request)
  {
    $app_data   = '&app_data=' . sfConfig::get('app_facebook_app_data');
    $app_scope  = '&scope=' . sfConfig::get('app_facebook_app_scope');
    
    $app_url    = urlencode(sfConfig::get('app_facebook_app_url') . $app_data);
    $dialog_url = 'http://www.facebook.com/dialog/oauth?client_id=' . sfConfig::get('app_facebook_app_id') . $app_scope;
    
    $auth_url   = '<script>top.location.href="' . $dialog_url . '&redirect_uri=' . $app_url . '"</script>';
    
    return $this->renderText($auth_url);
  }
  
  public function executeLike(sfWebRequest $request)
  {
  }
}