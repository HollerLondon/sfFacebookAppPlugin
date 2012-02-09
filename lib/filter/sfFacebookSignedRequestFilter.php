<?php

/**
 * @package     sfFacebookAppPlugin
 * @subpackage  filter
 * @author      Jimmy Wong <jimmywong@holler.co.uk>
 */
class sfFacebookSignedRequestFilter extends sfFilter
{
  public function execute($filterChain)
  {
    if ($this->isFirstCall())
    {
      $context    = $this->getContext();
      $request    = $context->getRequest();
      $controller = $context->getController();
    
      $data = array();
      $signed_request = 0;
    
      if ($request->hasParameter('signed_request') && '' != $request->getParameter('signed_request')) 
      {
        $data           = sfFacebookAppUtil::getSignedRequest();
        $signed_request = $request->getParameter('signed_request');
      } 
      else 
      {
        // no signed_request
        // force page refresh
      }
    
      if (!is_array($data))
      {
        $data = array();
      }
    
      // get the current action instance
      $actionEntry    = $controller->getActionStack()->getLastEntry();
      $actionInstance = $actionEntry->getActionInstance();
    
      // check if like gate is enabled
      $like_gate_config = sfConfig::get('app_facebook_like_gate', array());
        
      if (isset($like_gate_config['enabled']) && true === $like_gate_config['enabled'])
      {
        // If we have no data - make them like the page :)
        if (!empty($data) && isset($data['page']))
        {
          // check if a user has liked the page & redirect if they haven't
          if (!$data['page']['liked'] && ($actionInstance->getModuleName() != $like_gate_config['module'] || $actionInstance->getActionName() != $like_gate_config['action']))
          {
            // check if they are admin and the admin_is_enabled set to false?
            $skip = (true === $data['page']['admin'] && (!isset($like_gate_config['enabled_for_admin']) || false === $like_gate_config['enabled_for_admin']));
            
            if (!$skip)
            {
              // Dynamicaly create route from the parameter in config; this means that signed_request gets sent through properly to the like page
              $context->getRouting()->appendRoute('fb_like', new sfRoute(sprintf('/%s/%s', $like_gate_config['module'], $like_gate_config['action'])));
              $controller->redirect('@fb_like?signed_request=' . $signed_request);
            }
          }
        }
      }
    
      // access token
      if (array_key_exists('oauth_token', $data))
      {
        $access_token = $data['oauth_token'];
    
        // check if user_id exist
        if (array_key_exists('user_id', $data))
        {
          $uid        = $data['user_id'];
          $user_data  = sfFacebookAppUtil::getUserData($uid, $access_token, $data);
          $actionInstance->user_data = $user_data;
        } 
      }
      else 
      {
        $access_token = false;
      }
      
      $actionInstance->signed_request = $signed_request;
      $actionInstance->data           = $data;
      $actionInstance->access_token   = $access_token;
    }
    
    $filterChain->execute();
  }
}