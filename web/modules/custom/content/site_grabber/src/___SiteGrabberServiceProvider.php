<?php


namespace Drupal\site_grabber;


use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Symfony\Component\DependencyInjection\Reference;

class SiteGrabberServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {

//    $container->get('plugin.manager.migration'),
//      $container->get('plugin.manager.migrate.source'),
//      $container->get('plugin.manager.migrate.process'),
//      $container->get('plugin.manager.migrate.destination'),
//      $container->get('plugin.manager.migrate.id_map')

    $definition = $container->getDefinition('plugin.manager.migration');
    $definition->setClass('\Drupal\site_grabber\___MigrationPluginManagerExt');
  }
}