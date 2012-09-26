<?php

require_once dirname(__FILE__).'/../lib/vendor/symfony/lib/autoload/sfCoreAutoload.class.php';
sfCoreAutoload::register();

class ProjectConfiguration extends sfProjectConfiguration
{
  /**
   * Enable all plugins
   */
  public function setup()
  {
    $this->enableAllPluginsExcept('sfPropelPlugin');
  }
  
  /**
   * Set nice database bits here
   * 
   * @param Doctrine_Manager $manager
   */
  public function configureDoctrine(Doctrine_Manager $manager)
  {
    $manager->setAttribute(Doctrine_Core::ATTR_USE_NATIVE_ENUM, true);
    $manager->setAttribute(Doctrine_Core::ATTR_DEFAULT_TABLE_CHARSET, 'utf8');
    $manager->setAttribute(Doctrine_Core::ATTR_DEFAULT_TABLE_COLLATE, 'utf8_general_ci');
    $manager->setAttribute(Doctrine_Core::ATTR_QUERY_LIMIT, Doctrine_Core::LIMIT_ROWS);
  }
}
