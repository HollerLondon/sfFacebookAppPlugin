<?php

/**
 * parses facebook signed request
 * 
 * @package     sfFacebookAppPlugin
 * @subpackage  Util
 * @author      Jimmy Wong <jimmywong@holler.co.uk>
 */
class sfFacebookAppUtil
{
  /**
   * facebook signed request functions
   * 
   * @param   string  $signed_request
   * @param   string  $secret
   * @return  decoded data
   */
  public static function parseSignedRequest($signed_request, $secret)
  {
    list($encoded_sig, $payload) = explode('.', $signed_request, 2);

    // decode the data
    $sig  = self::base64UrlDecode($encoded_sig);
    $data = json_decode(self::base64UrlDecode($payload), true);

    if ('HMAC-SHA256' !== strtoupper($data['algorithm']))
    {
      sfContext::getInstance()->getLogger()->log('Unknown algorithm. Expected HMAC-SHA256', sfLogger::ERR);
      return null;
    }

    // check sig
    $expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
    
    if ($sig !== $expected_sig)
    {
      sfContext::getInstance()->getLogger()->log('Bad signed JSON signature!', sfLogger::ERR);
      return null;
    }

    return $data;
  }

  /**
   * @param   string $input
   * @return  string
   */
  public static function base64UrlDecode($input)
  {
    return base64_decode(strtr($input, '-_', '+/'));
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
    // Get user information
    $graph_api = 'https://graph.facebook.com/me?access_token='.$access_token;
  
    try 
    {
      $graph_data = json_decode(file_get_contents($graph_api), true);
    }
    catch (Exception $e) 
    {
      $graph_data = array();
    }
  
    $user_data = array(
      'first_name'  => (isset($graph_data['first_name']) ? $graph_data['first_name'] : ''),
      'last_name'   => (isset($graph_data['last_name']) ? $graph_data['last_name'] : ''),
      'email'   => (isset($graph_data['email']) ? $graph_data['email'] : ''),
      'fb_uid'      => $fb_uid
    );
    
    sfContext::getInstance()->getUser()->setAttribute('user_data', $user_data);
  
    return $user_data;
  }
}
