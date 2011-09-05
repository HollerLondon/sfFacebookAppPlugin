sfFacebookAppPlugin
=========================

@author:    Jimmy Wong (<jimmywong@holler.co.uk>), Ben Lancaster (benlancaster@holler.co.uk)

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
 * Add the Facebook SDK to your lib/vendor folder (ideally using git submodules or svn:externals), and configure config/autoload.yml to include it:
  
        autoload:

          fb_sdk:
            name:       Facebook
            path:       %SF_LIB_DIR%/vendor/facebook/src
 
 * Create a plugins folder on the root of your project directory and install this plugin (this plugin can be deleted after intial installation) along with any other plugins you want
 * To use the installer, in terminal: 
 
        php lib/vendor/symfony/data/bin/symfony generate:project --installer=plugins/sfFacebookAppPlugin/data/installer.php PROJECTNAME