<?php
function _exec()
{
  global $filesystem;
  $args = func_get_args();
  $command = array_shift($args);
  return $filesystem->execute(vsprintf($command, array_map('escapeshellarg', $args)));
}

global $filesystem;
$filesystem = $this->getFilesystem();

$properties   = parse_ini_file(sfConfig::get('sf_config_dir').'/properties.ini', true);
$isSubversion = file_exists('.svn');

$plugins = array(
  'sfFacebookAppPlugin'     => 'https://github.com/HollerLondon/sfFacebookAppPlugin.git/trunk',
  'sfMobilePlugin'          => 'https://github.com/HollerLondon/sfMobilePlugin.git/trunk',
  'sfDoctrineGuardPlugin'   => 'http://svn.symfony-project.com/plugins/sfDoctrineGuardPlugin/trunk/',
  'sfGoogleAnalyticsPlugin' => 'http://svn.symfony-project.com/plugins/sfGoogleAnalyticsPlugin/trunk/'
);

$vendors = array(
  'symfony'  => 'http://svn.symfony-project.org/branches/1.4',
  'facebook' => 'https://github.com/facebook/facebook-php-sdk.git/trunk'
);

$this->logSection('Install', 'Create frontend application');
$this->runTask('generate:app', 'frontend');

$this->logSection('Install', 'Loading and configuring project files');

if ($isSubversion)
{
  $this->logSection('file+', $tmp = sfConfig::get('sf_cache_dir').'/svnprop.tmp');
  
  _exec('svn add apps/*');
  
  // app.local.yml ignore
  file_put_contents($tmp, 'app.local.yml');
  _exec('svn ps svn:ignore --file=%s %s', $tmp, sfConfig::get('sf_apps_dir').'/frontend/config');
}

// Removing files before installing so we can just overwrite
$filesystem->remove(sfConfig::get('sf_upload_dir').'/assets');
$filesystem->remove(sfConfig::get('sf_config_dir').'/ProjectConfiguration.class.php');
$filesystem->remove(sfConfig::get('sf_config_dir').'/doctrine/schema.yml');
$filesystem->remove(sfConfig::get('sf_web_dir').'/frontend_dev.php');
$filesystem->remove(sfConfig::get('sf_web_dir').'/css/main.css');
$filesystem->remove(sfConfig::get('sf_apps_dir').'/frontend/templates/layout.php');
$filesystem->remove(sfConfig::get('sf_apps_dir').'/frontend/lib/myUser.class.php');

foreach (array('filters.yml', 'app.yml', 'view.yml', 'settings.yml', 'routing.yml', 'factories.yml') as $file)
{
  $filesystem->remove(sfConfig::get('sf_apps_dir').'/frontend/config/'.$file);
}

// Installing base files
$this->installDir(dirname(__FILE__).'/files');

// Sort out databases.yml
rename(sfConfig::get('sf_config_dir') . '/databases.yml', sfConfig::get('sf_config_dir') . '/databases.yml-dist');

// Project name
foreach (array(
  sfConfig::get('sf_apps_dir').'/frontend/config/factories.yml',
  sfConfig::get('sf_apps_dir').'/frontend/config/view.yml',
  sfConfig::get('sf_apps_dir').'/frontend/config/app.yml',
  sfConfig::get('sf_apps_dir').'/frontend/config/unavailable.php',
  sfConfig::get('sf_apps_dir').'/frontend/config/error/error.html'
) as $file)
{
  $filesystem->replaceTokens($file, '##', '##', array('PROJECTNAME' => str_replace(' ', '_', strtolower($properties['symfony']['name']))));
  $filesystem->replaceTokens($file, '##', '##', array('PROJECTFNAME' => str_replace('_', ' ', ucwords($properties['symfony']['name']))));
}

if ($isSubversion)
{
  // Add files
  _exec('svn add symfony test/* apps/frontend/modules/* apps/frontend/config/error apps/frontend/config/unavailable.php config/* lib/form/* web/* web/.htaccess data/fixtures lib/model/doctrine/sfDoctrineGuardPlugin apps/frontend/templates/empty.php');
  
  // Global ignore
  file_put_contents($tmp, '.sass-cache');
  _exec('svn ps svn:ignore --file=%s %s', $tmp, sfConfig::get('sf_web_dir'));
  file_put_contents($tmp, '*');
  _exec('svn ps svn:ignore --file=%s %s', $tmp, sfConfig::get('sf_upload_dir'));
  
  // Sort out base files
  _exec('svn delete --force lib/*/doctrine/base');
  file_put_contents($tmp, 'base');
  _exec('svn ps -R svn:ignore --file=%s lib/*/doctrine', $tmp);
}

// Externals
$this->logSection('Install', 'Installing plugins and vendors');

if ($isSubversion)
{
  // install plugins as svn externals
  $externals = '';
  foreach ($plugins as $name => $path)
  {
    $externals .= $name.' '.$path.PHP_EOL;
  }
  _exec('svn ps svn:externals %s %s', trim($externals), sfConfig::get('sf_plugins_dir'));

  // install vendors as svn externals
  $externals = '';
  foreach ($vendors as $name => $path)
  {
    $externals .= $name.' '.$path.PHP_EOL;
  }
  _exec('svn ps svn:externals %s %s', trim($externals), sfConfig::get('sf_lib_dir').'/vendor');
}

$this->logSection('Finished', 'ALL DONE - now svn up and commit');
