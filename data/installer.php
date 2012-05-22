<?php
$this->logSection('Install', 'Create frontend application and loading files');
$this->runTask('generate:app', 'frontend');

$this->installDir(dirname(__FILE__).'/files');

$this->logSection('Deleting...', 'main.css');
unlink(sfConfig::get('sf_web_dir').'/css/main.css');

$this->logSection('Renaming...', 'Renaming databases.yml');
rename(sfConfig::get('sf_config_dir') . '/databases.yml', sfConfig::get('sf_config_dir') . '/databases.yml-dist');

// Overwrite these files manually as installDir doesn't overwrite existing created symfony files
$this->logSection('Overwriting...', 'frontend_dev.php');
copy(dirname(__FILE__).'/files/web/frontend_dev.php', sfConfig::get('sf_web_dir').'/frontend_dev.php');

$this->logSection('Overwriting...', 'ProjectConfiguration.class.php');
copy(dirname(__FILE__).'/files/config/ProjectConfiguration.class.php', sfConfig::get('sf_config_dir').'/ProjectConfiguration.class.php');

$this->logSection('Overwriting...', 'frontend config');

foreach (array('filters.yml', 'app.yml', 'view.yml', 'settings.yml', 'routing.yml') as $file)
{
  copy(dirname(__FILE__).'/files/apps/frontend/config/'.$file, sfConfig::get('sf_apps_dir').'/frontend/config/'.$file);
}

$this->logSection('Overwriting...', 'layout.php');
copy(dirname(__FILE__).'/files/apps/frontend/templates/layout.php', sfConfig::get('sf_apps_dir').'/frontend/templates/layout.php');
