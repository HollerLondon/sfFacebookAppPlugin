sfFacebookAppPlugin
=========================

@author:    Jimmy Wong (<jimmywong@holler.co.uk>), Ben Lancaster (<benlancaster@holler.co.uk>), Jo Carter (<jocarter@holler.co.uk>)

@version:   0.1a


Introduction
------------

This is a customised project creation process geared towards Facebook development.

It includes an installer that'll create a Facebook application, a tab module, and copy files with useful code to deal with access tokens and user authentication within Facebook.

The plugin also includes extensions to the web debug bar for profiling API calls.

To make API calls, rather than instantiating an instance of the Facebook SDK, use:

    sfFacebook::getInstance()->api('/me');

Instructions
------------

 * Install the symfony framework files (usually in the lib/vendor directory)
 * Add the Facebook SDK (https://github.com/facebook/php-sdk) to your lib/vendor folder (ideally using git submodules or svn:externals), and configure config/autoload.yml to include it:
  
        autoload:

          fb_sdk:
            name:       facebook
            path:       %SF_LIB_DIR%/vendor/facebook/src
 
 * Create a plugins folder on the root of your project directory and install this plugin along with any other plugins you want
 * To use the installer, in terminal: 
 
        php lib/vendor/symfony/data/bin/symfony generate:project --installer=plugins/sfFacebookAppPlugin/data/installer.php PROJECTNAME

 * If you already have a project, and want to include the Facebook filter to parse the signed_request and get user data.  Include in the app's filters.yml

        sfFacebookApp:
          class: sfFacebookSignedRequestFilter

 * Then, in the action where you want to authorise the app with the user add:

        if (false === $this->access_token) $this->redirect('@auth?signed_request='.$request->getParameter('signed_request'));

 * If you want to request additional permissions (not in the regular scope)

		 $additionalScope = 'email';
         if (false === $this->access_token) $this->redirect(sprintf('@auth_scope?scope=%s&signed_request=%s', $additionalScope, $request->getParameter('signed_request')));

 * If you want to be able to interact with the tab whilst using Facebook as the page (the like button disappears), you need to disable the like gate for admins only:

		prod:
		  facebook:
		    like_gate:
		      enabled:           true
		      enabled_for_admin: false   # disable for "use as page"
		      
	* If you want to Google track the source of incoming tab links, you'll want to enable the tracking - not implemented for canvas apps
	
	  # If creating a tab make sure you use /tab as the starting url - this will then implement the
    # google tracking properly
    tracking:
      enabled:            true
      prefix:             source_
      utm_campaign:       CAMPAIGN
      utm_medium:         MEDIUM
      
  * NOTE: You'll need to make sure when linking to a tab or doing any redirects you include the app_data with the required source - e.g: TAB_URL?app_data=source_SOURCE
  