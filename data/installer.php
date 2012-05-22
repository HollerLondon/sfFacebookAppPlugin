<?php
$this->logSection('Install', 'Create frontend application and tab module');
$this->runTask('generate:app', 'frontend');
$this->runTask('generate:module', 'frontend tab');

$this->installDir(dirname(__FILE__).'/files');

$this->logSection('Deleting...', 'main.css');
unlink(sfConfig::get('sf_web_dir').'/css/main.css');

$this->logSection('Renaming...', 'Renaming databases.yml');
rename(sfConfig::get('sf_config_dir') . '/databases.yml', sfConfig::get('sf_config_dir') . '/databases.yml-dist');

// Overwrite these files manually as installDir doesn't overwrite existing created symfony files
$this->logSection('Overwriting...', 'frontend_dev.php');
copy(dirname(__FILE__).'/files/web/frontend_dev.php', sfConfig::get('sf_web_dir').'/frontend_dev.php');

$this->logSection('Overwriting...', 'filters.yml');
copy(dirname(__FILE__).'/files/apps/frontend/config/filters.yml', sfConfig::get('sf_apps_dir').'/frontend/config/filters.yml');

$this->logSection('Overwriting...', 'settings.yml');
copy(dirname(__FILE__).'/files/apps/frontend/config/settings.yml', sfConfig::get('sf_apps_dir').'/frontend/config/settings.yml');

$this->logSection('Overwriting...', 'view.yml');
copy(dirname(__FILE__).'/files/apps/frontend/config/view.yml', sfConfig::get('sf_apps_dir').'/frontend/config/view.yml');

$this->logSection('Overwriting...', 'layout.php');
copy(dirname(__FILE__).'/files/apps/frontend/templates/layout.php', sfConfig::get('sf_apps_dir').'/frontend/templates/layout.php');