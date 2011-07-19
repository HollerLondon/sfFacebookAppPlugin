sfFacebookProjectGeneratorPlugin
=========================

@author:    Jimmy Wong (<jimmywong@holler.co.uk>)

@version:   1.0


Introduction
------------

This is a customised project creation process geared towards Facebook development.


Instructions
------------

 * Install the symfony framework files (usually in the lib/vendor project directory)
 * Create a plugins folder on the root of your project directory and install this plugin along with any other plugins you want
 * In terminal: 
	php lib/vendor/symfony/data/bin/symfony generate:project --installer=plugins/sfFacebookProjectGeneratorPlugin/data/installer.php PROJECTNAME
