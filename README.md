sfFacebookAppPlugin
=========================

@author:    Jimmy Wong (<jimmywong@holler.co.uk>)

@version:   1.0


Introduction
------------

This is a customised project creation process geared towards Facebook development.

It will create a Facebook application, a tab module, and copy files with useful code to deal with access tokens and user authentication within Facebook.


Instructions
------------

 * Install the symfony framework files (usually in the lib/vendor directory)
 * Create a plugins folder on the root of your project directory and install this plugin (this plugin can be deleted after intial installation) along with any other plugins you want
 * In terminal: 
 
        php lib/vendor/symfony/data/bin/symfony generate:project --installer=plugins/sfFacebookAppPlugin/data/installer.php PROJECTNAME