<?php
/**
 * Facebook-specific extension of the Symfony Event class
 *
 * @package facebook
 * @subpackage debug
 * @author Ben Lancaster
 */
class sfFacebookEvent extends sfEvent
{
  protected $start_time = 0;
  protected $end_time   = 0;
  protected $timer      = false;

  /**
   * Starts timers when initialised
   * 
   * @see parent::__construct
   */
  public function __construct($subject, $name, $parameters = array())
  {
    $this->timer = sfTimerManager::getTimer('Facebook SDK');
    $this->start_time = microtime(true);
    return parent::__construct($subject, $name, $parameters);    
  }
  
  public function getElapsedSecs()
  {
    return $this->end_time - $this->start_time;
  }
  
  public function setProcessed($flag)
  {
    parent::setProcessed($flag);
    $this->end_time = microtime(true);
    $this->timer->addTime();
  }
  
  public function getMethod()
  {
    $params = $this->getParameters();
    return isset($params[1]) && isset($params[1]['method']) ? $params[1]['method'] : 'GET';
  }
  
  /**
   * Gets the FQL query if present with linebreaks stripped and whitespace normalised
   * 
   * @return string
   */
  public function getQuery()
  {
    if($this->getMethod() === 'fql.query')
    {
      $params = $this->getParameters();
      return preg_replace('/\s+/',' ',$params[1]['query']);
    }
    return '';
  }
} // END