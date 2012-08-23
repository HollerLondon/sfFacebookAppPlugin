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
