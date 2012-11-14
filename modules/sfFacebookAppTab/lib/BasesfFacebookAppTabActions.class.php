<?php

/**
 * facebook tab default actions.
 *
 * @package    sfFacebookAppPlugin
 * @subpackage sfFacebookAppTab
 * @author     Jimmy Wong
 * @version    SVN: $Id: BasesfFacebookAppTabActions.class.php 23810 2011-08-10 11:07:44Z Jimmy Wong $
 */
class BasesfFacebookAppTabActions extends sfActions
{
  /**
   * Default tab action that redirects to the @homepage route
   * 
   * @param sfWebRequest $request
   */
  public function executeTab(sfWebRequest $request)
  {
    $trackingConfig = sfConfig::get('app_facebook_tracking');
    $routeParams    = array('signed_request' => $this->signed_request);
    
    // URL?utm_source=SOURCE&utm_medium=MEDIUM&utm_campaign=CAMPAIGN
    // Do we have information to track source of link?
    if ($trackingConfig['enabled'] && isset($this->data['app_data']) && false !== strstr($this->data['app_data'], $trackingConfig['prefix']))
    {
      $routeParams['utm_medium']   = $trackingConfig['utm_medium'];
      $routeParams['utm_campaign'] = $trackingConfig['utm_campaign'];
      $routeParams['utm_source']   = str_replace($trackingConfig['prefix'], '', $this->data['app_data']);
    }
    
    $home_route = sprintf('%s?%s', sfConfig::get('app_facebook_homepage', '@homepage'), http_build_query($routeParams));
    
    $this->redirect($home_route);
  }
  
  /**
   * Authorises the app with Facebook
   * 
   * @param sfWebRequest $request
   */
  public function executeAuth(sfWebRequest $request)
  {
    $routeParams = array(
      'app_data' => $request->getParameter('app_data', sfConfig::get('app_facebook_app_data'))
    );
    $app_scope  = '&scope=' . sfConfig::get('app_facebook_app_scope');
    
    // check if we have a signed_request
    if ($request->hasParameter('signed_request'))
    {
      $routeParams['signed_request'] = $request->getParameter('signed_request');
    }
    
    $app_url    = urlencode($this->generateUrl('redirect', $routeParams, true));
    $dialog_url = 'http://www.facebook.com/dialog/oauth?client_id=' . sfConfig::get('app_facebook_app_id') . $app_scope;
    
    $auth_url   = '<script>top.location.href="' . $dialog_url . '&redirect_uri=' . $app_url . '"</script>';
    
    sfConfig::set('sf_web_debug',false);
    return $this->renderText($auth_url);
  }
  
  /**
   * Adds additional permissions to auth
   * 
   * @param sfWebRequest $request
   */
  public function executeAuthAdditional(sfWebRequest $request)
  {
    $routeParams = array(
      'app_data' => $request->getParameter('app_data', sfConfig::get('app_facebook_app_data'))
    );
    $app_scope  = '&scope=' . $request->getParameter('scope');
    
    // check if we have a signed_request
    if ($request->hasParameter('signed_request'))
    {
      $routeParams['signed_request'] = $request->getParameter('signed_request');
    }
    
    $app_url    = urlencode($this->generateUrl('redirect', $routeParams, true));
    $dialog_url = 'http://www.facebook.com/dialog/oauth?client_id=' . sfConfig::get('app_facebook_app_id') . $app_scope;
    
    $auth_url   = '<script>top.location.href="' . $dialog_url . '&redirect_uri=' . $app_url . '"</script>';
    
    sfConfig::set('sf_web_debug',false);
    return $this->renderText($auth_url);
  }
  
  /**
   * Get around FB bug that doesn't let you redirect to FB url in auth (10/8/11)
   * Also useful for share/send which doesn't let you link to a tab.
   * 
   * @param sfWebRequest $request
   */
  public function executeRedirect(sfWebRequest $request)
  {
    // set this for a url other than a facebook tab
    $app_url = sfConfig::get('app_facebook_redirect_url', sfConfig::get('app_facebook_app_url'));
    
    $app_data   = 'app_data=' . $request->getParameter('app_data', sfConfig::get('app_facebook_app_data'));
    
    // check if app_url contains a question mark or not
    $app_query = '&';
    if( false === strrpos($app_url, '?') )
    {
      $app_query = '?';
    }
    
    $redirect_url   = '<script>top.location.href="' . $app_url . $app_query . $app_data . '"</script>';
    
    sfConfig::set('sf_web_debug',false);
    return $this->renderText($redirect_url);
  }
  
  /**
   * Like gate
   * 
   * @param sfWebRequest $request
   */
  public function executeLike(sfWebRequest $request)
  {
  }
  
  /**
   * Channel 
   */
  public function executeChannel(sfWebRequest $request)
  {
    sfConfig::set('sf_web_debug', false);
    return $this->renderText('<script src="//connect.facebook.net/en_US/all.js"></script>');
  }
}