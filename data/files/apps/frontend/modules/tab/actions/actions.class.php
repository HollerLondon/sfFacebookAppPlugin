<?php

/**
 * tab actions.
 *
 * @package    sfFacebookAppPlugin
 * @subpackage tab
 * @author     Jo Carter <jocarter@holler.co.uk>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class tabActions extends sfActions
{
  /**
   * Prepare user object for use in tab (assuming use of sfGuardUser)
   */
  public function preExecute()
  {
    $this->user      = null;

    // On Facebook (tab) - if need app, then ! the ['page'] data
    if (!empty($this->data) && isset($this->data['page'])) 
    {
      // Auth'd app on Facebook and we have user data
      if (false !== $this->access_token && isset($this->user_data['fb_uid'])) 
      {
        $this->user = sfGuardUserTable::getInstance()->findOneById($this->user_data['fb_uid']);
        $this->user = $this->checkUser($this->user, $this->user_data, 'facebook', $this->access_token);
      }
    }
    // Logged in (mobile)
    else if ($this->getUser()->isAuthenticated() && !$this->getUser()->isSuperAdmin()) 
    {
      $this->user = $this->getUser()->getGuardUser();
    }
  }
  
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    // IMPORTANT: So Facebook can pick up the meta data for the app / site
    $metas           = sfConfig::get('app_facebook_default_og_data', array());
    $metas['app_id'] = sfConfig::get('app_facebook_app_id');
    $metas['image']  = 'http://placehold.it/200x200'; // @TODO: Proper image
    $metas['url']    = sfConfig::get('app_facebook_canvas_url');
    
    $this->setVar('metas', $metas, true);
    
    // If in Facebook
    if (!empty($this->data))
    {
      // If on app canvas page - not tab - redirect to tab
      if (!isset($this->data['page']))
      {
        sfConfig::set('sf_web_debug', false);
        $app_url = sfConfig::get('app_facebook_app_url');
        
        // Check whether have app_data and send through to tab if do (especially if app set as redirect_url)
        if ($request->hasParameter('app_data')) $app_url = sprintf('%s%sapp_data=%s', $app_url, (false === strrpos($app_url, '?') ? '?' : '&'), $request->getParameter('app_data'));
        
        $this->renderText(sprintf('<script>top.location.href="%s"</script>', $app_url));
      }
      else if (isset($this->data['app_data']))
      {
        // Do something with app_data
      }
    }
    // If come directly to site - / url
    else
    {
      if (!$this->getUser()->isMobile()) 
      {
        // So that Facebook can pick up the meta data for an app if the app redirects only - we redirect with JS
        // If you're using a canvas app - just remove this redirect (and make sure you use /canvas/ in the app settings)
        $this->setLayout('empty');
        $this->setTemplate('indexOg');
        $this->setVar('auth_url', sprintf('<script>top.location.href="%s"</script>', sfConfig::get('app_facebook_app_url')), true); // Take to tab
      }
      // else Mobile - stay on page and supply mobile styles using media queries
      // if need separate mobile landing page create a new action and add a redirect
      // In app settings use / as the mobile URL
    }
  }
  
	/**
   * Ajax request logs user in - creating new user if doesn't exist
   * 
   * @param sfWebRequest $request
   */
  public function executeAuthUser(sfWebRequest $request)
  {
    // Set access token in session
    $this->access_token = $request->getParameter('access_token');
    $this->getUser()->setAttribute('access_token', $this->access_token);
    $this->user         = sfGuardUserTable::getInstance()->findOneById($request->getParameter('user_id'));
    $user_data          = array();
    
    // New user, save into DB - or no data - so update
    if (!$this->user || !$this->user->email_address) 
    {
      // Get /me from Facebook plugin with access token
      $fb = sfFacebook::getInstance();
      $fb->setAccessToken($this->access_token);
      
      try {
        $user_data           = $fb->api('/me');
        $user_data['fb_uid'] = $user_data['id'];
        unset($user_data['id']);   
      }
      // Facebook gone wonky
      catch (FacebookApiException $e) {
        $user_data = array('fb_uid' => $request->getParameter('user_id'));
      }
    }
    
    $this->user = $this->checkUser($this->user, $user_data, ($this->getUser()->isMobile() ? 'mobile' : 'facebook'), $this->access_token);
        
    // If $this->user - log in
    if ($this->user) $this->getUser()->signin($this->user);
    
    // Return URL to redirect to / or partial - depending on situation
    return $this->renderText(json_encode(array('redirect' => $this->generateUrl('homepage', array(), true))));
  }
  
	/**
   * Check, validate and save/ update user (assuming use of sfGuardUser with email address)
   *
   * @param sfGuardUser $user
   * @param array $user_data
   * @param string $source
   * @param string $access_token
   * @return sfGuardUser
   */
  private function checkUser($user, $user_data, $source, $access_token = null)
  {
    $new = false; 
    
    // New user, save into DB - or update missing info
    // If there was an open graph error then may not have user data - this ensures if missing it gets updated
    if ((!$user || !$user->email_address) && !empty($user_data)) 
    {
      if (isset($user_data['email'])) $user_data['email_address'] = $user_data['email']; 
      if (empty($user_data['email_address']) && !empty($user_data['username'])) $user_data['email_address'] = $user_data['username'].'@facebook.com';
     
      if (!$user) 
      {
        $new  = true;
        $user = new sfGuardUser();
        $user_data['source'] = $source;
        $user_data['id']     = $user_data['fb_uid'];
        if (empty($user_data['username'])) $user_data['username'] = 'facebook_' . $user_data['fb_uid'];
      }
     
      // Validate email address
      try {
        $validator = new sfValidatorEmail(array('required'=>true));
        $user_data['email_address'] = $validator->clean($user_data['email_address']);
      }
      catch (sfValidatorError $e) {
        $user_data['email_address'] = null; // invalid email address
      }
     
      $user->fromArray($user_data);
      // if ($new) $user->upgradeAccessToken($access_token); // If using Cacophony and tokens get upgraded access token here
      $user->save();
    }
    
    // If using Cacophony and tokens upgrade access token here (see Who's on Heart for upgraded Cacophony module)
    // Check access token
    /*if ($user && !$new)
    {
      // Check access token retrieved
      $token = $user->getTokenFor('facebook');
      
      if (!$token || $token->isExpired()) 
      {
        $user->upgradeAccessToken($access_token);
        $user->save();
      }
    }*/
   
    return $user;
  }
  
	/**
   * AJAX action logs user out of site, after being logged out of Facebook
   * 
   * @param sfWebRequest $request
   */
  public function executeDeAuthUser(sfWebRequest $request)
  {
    $this->getUser()->signout();
    
    return $this->renderText(json_encode(array('success'=>true)));
  }
  
 /**
  * Executes like action
  *
  * @param sfRequest $request A request object
  */
  public function executeLike(sfWebRequest $request)
  {
  }
}
