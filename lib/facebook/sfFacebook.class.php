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
  
  /**
   * sfCache instance
   * 
   * @var sfCache
   */
  protected $cache = false;
  
  public function __construct($config)
  {
    parent::__construct($config);
    if(array_key_exists('cache',$config))
    {
      $this->cache = $config['cache'];
    }
  }
  
  /**
   * Gets sfCache instance
   * 
   * @return sfCache
   */
  protected function getCache()
  {
    return $this->cache instanceof sfCache ? $this->cache : new sfNoCache;
  }
  
  protected function makeRequest($url, $params, $ch=null)
  {
    $args       = func_get_args();
    $event      = new sfFacebookEvent($this,'facebook.api_call',$args);
    
    $cache_key  = sha1('fb_'.$url.serialize($params));
    
    // check params['method'] = only cache get requests
    $method     = array_key_exists('method',$params) ? strtolower($params['method']) : false;
    $lifetime   = array_key_exists('lifetime',$params) ? $params['lifetime'] : sfConfig::get('app_facebook_cache_lifetime', 3600);

    if ($this->getCache()->has($cache_key) && 'get' == $method)
    {
      $response = $this->getCache()->get($cache_key);
      $event->setCacheModified($this->getCache()->getLastModified($cache_key));
      $event->setCacheTimeout($this->getCache()->getTimeout($cache_key));
    }
    else
    {
      $response = parent::makeRequest($url,$params,$ch); 
      
      if ('get' == $method) 
      {
        $this->getCache()->set($cache_key, $response, $lifetime);
      }
      
    }

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
    $options = array(
      'appId'   => sfConfig::get('app_facebook_app_id'),
      'secret'  => sfConfig::get('app_facebook_app_secret'),
    );
    
    $cache_options = sfConfig::get('app_facebook_cache',array());
    
    if(!empty($cache_options) && array_key_exists('class',$cache_options))
    {
      $options['cache'] = new $cache_options['class']($cache_options['param']);
    }

    self::$instance = self::$instance ? self::$instance : new sfFacebook($options);
    return self::$instance;
  }
  
  /**
   * Override to get from sfUser session
   * 
   * @see Facebook::getPersistentData()
   */
  protected function getPersistentData($key, $default = false) 
  {
    $value = parent::getPersistentData($key, $default);
    
    try {
      return sfContext::getInstance()->getUser()->getAttribute($key, $value);
    }
    catch (Exception $e) {
      return $value;
    }
  }
  
  /**
   * Retrieves an access token for the given authorization code
   * (previously generated from www.facebook.com on behalf of
   * a specific user).  The authorization code is sent to graph.facebook.com
   * and a legitimate access token is generated provided the access token
   * and the user for which it was generated all match, and the user is
   * either logged in to Facebook or has granted an offline access permission.
   *
   * Get params including expiry for cacophony stuffs - includes expires
   *
   * @param string $code An authorization code.
   * @return array An access token exchanged for the authorization code, or
   *               false if an access token could not be generated.
   */
  public function getAccessTokenParamsFromCode($code, $redirect_uri = null) {
    if (empty($code)) {
      return false;
    }

    if ($redirect_uri === null) {
      $redirect_uri = $this->getCurrentUrl();
    }

    try {
      // need to circumvent json_decode by calling _oauthRequest
      // directly, since response isn't JSON format.
      $access_token_response =
        $this->_oauthRequest(
          $this->getUrl('graph', '/oauth/access_token'),
          $params = array('client_id' => $this->getAppId(),
                          'client_secret' => $this->getAppSecret(),
                          'redirect_uri' => $redirect_uri,
                          'code' => $code));
    } catch (FacebookApiException $e) {
      // most likely that user very recently revoked authorization.
      // In any event, we don't have an access token, so say so.
      return false;
    }

    if (empty($access_token_response)) {
      return false;
    }

    $response_params = array();
    parse_str($access_token_response, $response_params);
    if (!isset($response_params['access_token'])) {
      return false;
    }

    return $response_params;
  }
  
  /**
   * Extend an access token, while removing the short-lived token that might
   * have been generated via client-side flow. Thanks to http://bit.ly/b0Pt0H
   * for the workaround.
   * 
   * @return array of params
   * @throws FacebookApiException
   */
  public function getExtendedAccessToken() {
    try {
      // need to circumvent json_decode by calling _oauthRequest
      // directly, since response isn't JSON format.
      $access_token_response = $this->_oauthRequest(
        $this->getUrl('graph', '/oauth/access_token'),
        $params = array(
          'client_id' => $this->getAppId(),
          'client_secret' => $this->getAppSecret(),
          'grant_type' => 'fb_exchange_token',
          'fb_exchange_token' => $this->getAccessToken(),
        )
      );
    }
    catch (FacebookApiException $e) {
      // most likely that user very recently revoked authorization.
      // In any event, we don't have an access token, so say so.
      throw $e;
    }

    if (empty($access_token_response)) {
      return false;
    }

    $response_params = array();
    parse_str($access_token_response, $response_params);

    if (!isset($response_params['access_token'])) {
      return false;
    }

    $this->destroySession();

    $this->setPersistentData(
      'access_token', $response_params['access_token']
    );
    
    return $response_params;
  }
} // END