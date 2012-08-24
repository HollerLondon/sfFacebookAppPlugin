<?php

/**
 * tab actions.
 *
 * @package    revlon_lipbutter
 * @subpackage tab
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class tabActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    // If in Facebook
    if (!empty($this->data))
    {
      // If on app canvas page - not tab - redirect to tab
      if (!isset($this->data['page']))
      {
        sfConfig::set('sf_web_debug', false);
        return $this->renderText(sprintf('<script>top.location.href="%s"</script>', sfConfig::get('app_facebook_app_url')));
      }
      else if (isset($this->data['app_data']))
      {
        // Do something with app_data
      }
    }
    // If come directly to site - / url
    else
    {
      // Mobile user - take to mobile site
      if ($this->getUser()->isMobile())
      {
        $this->redirect('@mobile');
      }
      // Take to tab
      else
      {
        $this->redirect(sfConfig::get('app_facebook_app_url'));
      }
    }
  }
  
  /**
  * Executes mobile action
  * 
  * IMPORTANT: We need to have a mobile page - even if it's just a landing page directing them to the tab 
  *
  * @param sfRequest $request A request object
  */
  public function executeMobile(sfWebRequest $request)
  {
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
