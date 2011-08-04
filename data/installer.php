<?php

$this->logSection('Install', 'Create facebook application and tab module');
$this->runTask('generate:app', 'facebook');
$this->runTask('generate:module', 'facebook tab');

$this->installDir(dirname(__FILE__).'/files');

$this->logSection('Renaming...', 'Renaming databases.yml');
rename(sfConfig::get('sf_config_dir') . '/databases.yml', sfConfig::get('sf_config_dir') . '/databases.yml-dist');

// Overwrite these files manually as installDir doesn't overwrite existing created symfony files
$this->logSection('Overwriting...', 'facebook_dev.php');
copy(dirname(__FILE__).'/files/web/facebook_dev.php', sfConfig::get('sf_web_dir').'/facebook_dev.php');

$this->logSection('Overwriting...', 'main.css');
copy(dirname(__FILE__).'/files/web/css/main.css', sfConfig::get('sf_web_dir').'/css/main.css');

$this->logSection('Overwriting...', 'app.yml');
copy(dirname(__FILE__).'/files/apps/facebook/config/app.yml', sfConfig::get('sf_apps_dir').'/facebook/config/app.yml');

$this->logSection('Overwriting...', 'filters.yml');
copy(dirname(__FILE__).'/files/apps/facebook/config/filters.yml', sfConfig::get('sf_apps_dir').'/facebook/config/filters.yml');

$this->logSection('Overwriting...', 'settings.yml');
copy(dirname(__FILE__).'/files/apps/facebook/config/settings.yml', sfConfig::get('sf_apps_dir').'/facebook/config/settings.yml');

$this->logSection('Overwriting...', 'view.yml');
copy(dirname(__FILE__).'/files/apps/facebook/config/view.yml', sfConfig::get('sf_apps_dir').'/facebook/config/view.yml');

$this->logSection('Overwriting...', 'layout.php');
copy(dirname(__FILE__).'/files/apps/facebook/templates/layout.php', sfConfig::get('sf_apps_dir').'/facebook/templates/layout.php');