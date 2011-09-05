<?php
/**
 * Facebook web debug panel
 *
 * @package facebook
 * @subpackage debug
 * @author  
 */
class sfWebDebugPanelFacebook extends sfWebDebugPanel
{
  protected static $events = array();

  /**
   * Get the title and icon for the web debug bar
   * 
   * @return string
   */
  public function getTitle()
  {
    if(!empty(self::$events)) return '<img src="/images/icon.fb.png" width="16" height="16" /> '.count(self::$events);
  }
  
  /**
   * Sets the title of the web debug panel
   * 
   * @return string
   */
  public function getPanelTitle()
  {
    return "Facebook API calls";
  }
  
  /**
   * Gets the contents of the Facebook API Web Debug panel
   * 
   * @return string
   */
  public function getPanelContent()
  {
    return '
      <div id="sfWebDebugDatabaseLogs">
        <h3>Facebook SDK Version: '.Facebook::VERSION.'</h3>
        <ol>'.implode("\n", $this->getApiLogs()).'</ol>
      </div>
    ';
  }
  
  /**
   * Registers the new Panel for the web debug bar
   * 
   * @param sfEvent $event
   */
  public static function listenToLoadDebugWebPanelEvent(sfEvent $event)
  {
    $event->getSubject()->setPanel('fbapi',new self($event->getSubject()));
  }
  
  /**
   * Listens for events fired by the sfFacebook::api() method, adds the API
   * call to the application log and saves the event in a local event register
   * 
   * @param sfEvent $event
   */
  public static function listenToApiCall(sfFacebookEvent $event)
  {
    $params   = $event->getParameters();
    $method   = $event->getMethod();
    
    switch($method)
    {
      case 'GET':
        $log_msg  = sprintf('%s: "%s"',$method, $params[0]);
        break;
      case 'fql.query':
        $log_msg  = sprintf("%s: %s",$method,$event->getQuery());
        break;
    }

    self::$events[] = $event;
    $event->setProcessed(true);

    sfProjectConfiguration::getActive()->getEventDispatcher()->notify(
      new sfEvent($event->getSubject(),'application.log',array($log_msg))
    );
  }
  
  /**
   * Returns an array of HTML List Items, one item for each item in the event
   * register
   * 
   * @return array
   */
  public function getApiLogs()
  {
    $response = array();
    foreach(self::$events as $event)
    {
      $params = $event->getParameters();
      switch($event->getMethod())
      {
        case 'fql.query':
          $log = $this->formatSql(htmlspecialchars($event->getQuery(),ENT_QUOTES,sfConfig::get('sf_charset')));
          break;
        case 'GET':
          $log = $params[0];
          break;
      }
      
      $response[] = sprintf('<li><p class="sfWebDebugDatabaseQuery">%s</p><p class="sfWebDebugDatabaseLogInfo">%ss</p></li>',
        $log,
        number_format($event->getElapsedSecs(), 2)
      );
    }
    return $response;
  }
  
} // END
