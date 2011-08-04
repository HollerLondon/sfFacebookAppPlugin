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
    
      if ($request->hasParameter('signed_request') && $request->getParameter('signed_request') != '') 
      {
        $data = sfFacebookAppUtil::parseSignedRequest($request->getParameter('signed_request'), sfConfig::get('app_facebook_app_secret'));
        $signed_request = $request->getParameter('signed_request');
      } else {
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
    
      // like gate check
      $like_gate_config = sfConfig::get('app_facebook_like_gate', array());
      // check if like gate is enabled
      if ( isset($like_gate_config['enabled']) && $like_gate_config['enabled'] === true )
      {
        if (array_key_exists('page', $data))
        {
          // check if a user has liked the page
          if (array_key_exists('liked', $data['page']))
          {
            // redirect if they haven't
            if (!$data['page']['liked'] && $actionInstance->getModuleName() != $like_gate_config['module'] && $actionInstance->getActionName() != $like_gate_config['action'])
            {
              $controller->redirect($like_gate_config['module'] . '/' . $like_gate_config['action']);
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
          $uid = $data['user_id'];
          $user_data = sfFacebookAppUtil::getUserData($uid, $access_token, $data);
        } 
      }
      else 
      {
        $access_token = false;
      }
      
      $actionInstance->data           = $data;
      $actionInstance->signed_request = $signed_request;
    }
    
    $filterChain->execute();
  }
}