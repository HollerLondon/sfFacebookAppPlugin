<?php

/**
 * sfGuardUser
 * 
 * @package    sfFacebookAppPlugin
 * @subpackage model
 * @author     Jo Carter <jocarter@holler.co.uk>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class sfGuardUser extends PluginsfGuardUser
{
  private $fb = null;

  /**
   * Get a Facebook instance
   *
   * @return sfFacebook
   */
  public function getFacebook()
  {
    if (!$this->fb) 
    {
      $this->fb = sfFacebook::getInstance();
    }
    
    // access token for mobiles, no signed_request - it'll pull from FB otherwise
    if (sfContext::getInstance()->getUser()->hasAttribute('access_token')) 
    {
      $this->fb->setAccessToken($this->getUser()->getAttribute('access_token'));
    }
    
    // OR If using cacophony and tokens use this (also check out Cacophony module upgrade in Who's On Heart)
    /*// Set access token from DB - if not expired
    $token    = $this->getTokenFor('facebook');
    
    if ($token && !$token->isExpired())
    {
      $tokenData = $token->getContent();
      $this->fb->setAccessToken($tokenData['access_token']);
    }*/

    return $this->fb;
  }

	/**
   * If using cacophony and tokens use this (also check out Cacophony module upgrade in Who's On Heart)
   * Query the user's tokens for the selected provider
   * 
   * @param string $provider
   * @return Token
   */
  /*public function getTokenFor($provider)
  {
    // Check for existing token in DB
    $tokens = $this->getTokens();
    
    if ($tokens) 
    {
      foreach ($tokens as $token) 
      {
        if ($token['provider'] == $provider) return $token;
      }
    }
    
    return null;
  }*/
  
  /**
   * If using cacophony and tokens use this (also check out Cacophony module upgrade in Who's On Heart)
   * Upgrade to longer life access token so that app created users can
   * Log in using mobile / microsite and Cacophony (it relies on Token)
   * 
   * @return void
   */
  /*public function upgradeAccessToken($access_token = null)
  {
    if ($access_token) $this->getFacebook()->setAccessToken($access_token); // If existing user and token expired then use the one sent through from JS/FB to upgrade
    $token = $this->getTokenFor('facebook');
    
    // Check expiry if have existing token - and end here if do
    if ($token && $token->getDateTimeObject('expires_at')->getTimestamp() > time()) return; // Check elsewhere if need force upgrade and delete broken token before calling
    
    // Upgrade access token and store in DB.
    try {
      $output = $this->getFacebook()->getExtendedAccessToken();
    }
    catch (Exception $e) {
      if ($token) $token->delete(); // delete old token if error
      return;
    }
    
    if (!$output) {
      if ($token) $token->delete(); // delete old token if error
      return;
    }
    
    if (!$token) $token = new Token();
    
    $token->fromArray(array(
      'provider'          => 'facebook',
      'sf_guard_user_id'  => $this->id,
      'providers_user_id' => $this->id,
      'content'           => $output,
      'expires_at'        => date('Y-m-d H:i:s', (time() + $output['expires']))
    ));
    
    $this['Tokens']->add($token);
    // Now save user (in method this is called from)
  }*/
  
  /**
   * Example open graph post of object
   * 
   * @param Doctrine_Record $record
   * @param string $ogUrl
   * @return void
   */
  /*public function ogExample(Doctrine_Record $record, $ogUrl = null)
  {
    if ('test' == sfConfig::get('sf_environment')) return; 
    
    if (!$ogUrl) $ogUrl = ('dev' != sfConfig::get('sf_environment') 
                        ? sfContext::getInstance()->getController()->genUrl('@og_url?slug='.$record->slug, true) // replace with og_url route for the model (use empty layout)
                        : sfConfig::get('app_facebook_sample_example_object', null));
    
    if (!$ogUrl) return;
    
    try {
      // Post to wall - post id returned - nothing happens if no permission or error
      $type   = sprintf('%s:%s', sfConfig::get('app_facebook_namespace'), 'example'); // replace 'example' with action name
      $result = $this->getFacebook()->api('/me/'.$type, 'POST', array('event' => $ogUrl));
      
      if ($result && isset($result['id'])) $this->logOpenGraphAction($result['id'], $type, $ogUrl);
    }
    // Something went wrong on Facebook's side.
    catch (Exception $e) { }
  }*/
  
  /**
   * Log open graph actions for reporting
   * 
   * @param string $type
   * @param string $url
   * @param array $result
   * @return void
   */
  /*private function logOpenGraphAction($resultId, $type, $url)
  {
    $og = new OpenGraph();
    $og->fromArray(array(
      'id'    => $resultId,
      'type'  => $type,
      'url'   => $url
    ));
    $og->setUser($this);
    $og->save();
  }*/
}
