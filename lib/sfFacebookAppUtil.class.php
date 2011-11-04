<?php

/**
 * Useful Facebook functionality, used by the filter etc.
 * 
 * @package     sfFacebookAppPlugin
 * @subpackage  lib
 * @author      Jimmy Wong <jimmywong@holler.co.uk>
 * @author      Jo Carter <jocarter@holler.co.uk>
 */
class sfFacebookAppUtil
{
  /**
   * facebook signed request functions
   * 
   * @param   string  $signed_request
   * @param   string  $secret
   * @return  decoded data
   * @deprecated use sfFacebookAppUtil::getSignedRequest();
   */
  public static function parseSignedRequest($signed_request, $secret)
  {
    return self::getSignedRequest();
  }
  
  /**
   * Parse the Facebook signed request, and return the data contained
   * 
   * @return array of decoded data
   */
  public static function getSignedRequest()
  {
    try
    {
      $facebook = sfFacebook::getInstance();
      $facebook->setApiSecret(sfConfig::get('app_facebook_app_secret'));
      
      $data     = $facebook->getSignedRequest();
    }
    catch (FacebookException $e)
    {
      sfContext::getInstance()->getLogger()->log($e->getMessage(), sfLogger::ERR);
      return null;
    }
    
    return $data;
  }
  
  /**
   * Get user information from the graph api
   * 
   * @param string $fb_uid
   * @param string $access_token
   * @param array $data
   * @return array
   */
  public static function getUserData($fb_uid, $access_token, $data)
  {
    $user_data_required = sfConfig::get('app_facebook_user_data');
    
    // Get user information
    try 
    {
      $graph_data = sfFacebook::getInstance()->api('/me', array('access_token' => $access_token));
    }
    catch (Exception $e) 
    {
      sfContext::getInstance()->getLogger()->log($e->getMessage(), sfLogger::ERR);
      return array('fb_uid' => $fb_uid);
    }
    
    // Get required user data
    $user_data          = array('fb_uid' => $fb_uid);
    
    foreach ($user_data_required as $fb_field)
    {
      $user_data[$fb_field] = (isset($graph_data[$fb_field]) ? $graph_data[$fb_field] : '');
    }
    
    return $user_data;
  }
}
