sfFacebookAppPlugin
=========================

@author:    Jimmy Wong (<jimmywong@holler.co.uk>), Ben Lancaster (<benlancaster@holler.co.uk>), Jo Carter (<jocarter@holler.co.uk>)

@version:   0.2a


Introduction
------------

This is a customised project creation process geared towards Facebook development.

It includes an installer that'll create a Facebook application, a tab module, and copy files with useful code to deal with access tokens and user authentication within Facebook.

The plugin also includes extensions to the web debug bar for profiling API calls.

To make API calls, rather than instantiating an instance of the Facebook SDK, use:

    sfFacebook::getInstance()->api('/me');


Dependancies:
-------------

 * Facebook SDK: https://github.com/facebook/facebook-php-sdk

Instructions
------------

## All projects 

NOTE: svn:externals included below.

Add the Facebook SDK to your `lib/vendor` folder
 
    facebook              https://github.com/facebook/facebook-php-sdk.git/trunk
    
Add this plugin to your `plugins` folder
 
    sfFacebookAppPlugin   https://github.com/HollerLondon/sfFacebookAppPlugin.git/trunk


## New project

 1. Install the symfony framework files (in the `lib/vendor` directory)
 2. Generate the project using the installer:
 
        php lib/vendor/symfony/data/bin/symfony generate:project --installer=plugins/sfFacebookAppPlugin/data/installer.php PROJECTNAME

 3. Create a Facebook app and fill in your Facebook app details in the frontend `app.yml`, NOTE: If creating a Facebook tab make sure you use YOUR_URL/tab as the starting url 
 4. Then, in the action where you want to authorise the app with the user add:

        if (false === $this->access_token) $this->redirect('@auth?signed_request='.$request->getParameter('signed_request'));

 5. If you want to request additional permissions (not in the regular scope)

         $additionalScope = 'email';
         if (false === $this->access_token) $this->redirect(sprintf('@auth_scope?scope=%s&signed_request=%s', $additionalScope, $request->getParameter('signed_request')));

 6. If you want to be able to interact with the tab whilst using Facebook as the page (the like button disappears), you need to disable the like gate for admins only:

         prod:
           facebook: 
             like_gate:
               enabled:           true
               enabled_for_admin: false   # disable for "use as page"
		      
 7. If you want to Google track the source of incoming tab links, you'll want to enable the tracking - Facebook tabs only. 
 NOTE: You'll need to make sure when linking to a tab or doing any redirects you include the `?app_data` with the required source - e.g: `TAB_URL?app_data=source_SOURCE`

           tracking:
             enabled:            true
             prefix:             source_
             utm_campaign:       CAMPAIGN
             utm_medium:         MEDIUM

## Existing project 

 1. Enable plugin in ProjectConfiguration

 2. And enable the module in the app's `settings.yml`

        enabled_modules:        [ sfFacebookAppTab ]

 3. Include the signed request filter in the app's `filters.yml`

        # insert your own filters here
        sfFacebookApp:
          class: sfFacebookSignedRequestFilter
          
 4. And configure `config/autoload.yml` to include the Facebook SDK:

        autoload:
          fb_sdk:
            name:       facebook
            path:       %SF_LIB_DIR%/vendor/facebook/src
            
 5. Follow above from step 3.