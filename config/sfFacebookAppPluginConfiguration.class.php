<?php
class sfFacebookAppPluginConfiguration extends sfPluginConfiguration
{
  public function initialize()
  {
    $this->dispatcher->connect('facebook.no_name',array('FacebookUtil','fetchUserName'));
    $this->dispatcher->connect('facebook.api_call',array('sfWebDebugPanelFacebook','listenToApiCall'));
    $this->dispatcher->connect('debug.web.load_panels', array('sfWebDebugPanelFacebook', 'listenToLoadDebugWebPanelEvent'));
  }
}
