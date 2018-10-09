<?php


namespace Drupal\site_grabber;


use Drupal\migrate\Plugin\MigrationPluginManager;

class ___MigrationPluginManagerExt extends MigrationPluginManager {

  /**
   * {@inheritdoc}
   */
  public function createInstances($migration_id, array $configuration = []) {
    if (empty($migration_id)) {
      $migration_id = array_keys($this->getDefinitions());
    }

    $factory = $this->getFactory();
    $migration_ids = (array) $migration_id;
    $plugin_ids = $this->expandPluginIds($migration_ids);

    $instances = [];
    foreach ($plugin_ids as $plugin_id) {
      $instances[$plugin_id] = $factory->createInstance($plugin_id, isset($configuration[$plugin_id]) ? $configuration[$plugin_id] : []);
    }

    foreach ($instances as $migration) {
      $migration->set('migration_dependencies', array_map([
        $this,
        'expandPluginIds',
      ], $migration->getMigrationDependencies()));
    }

    // Sort the migrations based on their dependencies.
    return $this->buildDependencyMigration($instances, []);
  }

}