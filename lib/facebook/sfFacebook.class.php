<?php
/**
 * Symfony-specific extensions for the Facebook PHP SDK
 *
 * @package facebook
 * @author  Ben Lancaster
 */
class sfFacebook extends Facebook
{
  static $instance = false;

  protected function makeRequest($url, $params, $ch=null)
  {
    $args       = func_get_args();
    $event      = new sfFacebookEvent($this,'facebook.api_call',$args);
    $response   = parent::makeRequest($url,$params,$ch);
    sfProjectConfiguration::getActive()->getEventDispatcher()->notify($event);
    return $response;
  }

  /**
   * Retrieve an instance of the Facebook SDK
   *
   * @return sfFacebook
   */
  public static function getInstance()
  {
    self::$instance = self::$instance ?: new sfFacebook(array(
      'appId'   => sfConfig::get('app_facebook_app_id'),
      'secret'  => sfConfig::get('app_facebook_app_secret'),
    ));
    return self::$instance;
  }
} // END