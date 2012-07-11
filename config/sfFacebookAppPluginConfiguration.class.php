<?php
class sfFacebookAppPluginConfiguration extends sfPluginConfiguration
{
  public function initialize()
  {
    if (sfConfig::get('sf_debug', false))
    {
      $this->dispatcher->connect('facebook.api_call',     array('sfWebDebugPanelFacebook', 'listenToApiCall'));
    }

    if (sfConfig::get('sf_web_debug', false))
    {
      $this->dispatcher->connect('debug.web.load_panels', array('sfWebDebugPanelFacebook', 'listenToLoadDebugWebPanelEvent'));
    }
  }
}
