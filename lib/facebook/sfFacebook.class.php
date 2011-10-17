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
    $method = (isset($param['method']) ? strtolower($param['method']) : false);
    
    if ($this->getCache()->has($cache_key) && 'get' == $method)
    {
      $response = $this->getCache()->get($cache_key);
    }
    else
    {
      $response = parent::makeRequest($url,$params,$ch); 
       
      if ('get' == $method) 
      {
        $this->getCache()->set($cache_key, $response, sfConfig::get('app_facebook_cache_lifetime', 3600));
      }
      
      sfProjectConfiguration::getActive()->getEventDispatcher()->notify($event);
    }

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
} // END