<?php

class myUser extends sfMobileGuardUser
{
  /**
   * Target mobile devices - but not if within Facebook tab (special case)
   * 
   * @param null|string $user_agent
   * @return boolean
   */
  public function isMobile($user_agent = null)
  {
    // Check page data / signed request - so that iPad users don't get mobile styles
    if (sfContext::getInstance()->getRequest()->hasParameter('signed_request')) return false;
    else return parent::isMobile();
  }
}
