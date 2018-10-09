<?php


namespace Drupal\site_grabber;


use Drupal\migrate\Plugin\Migration;

class MigrationExt extends Migration {

  public function __construct(array $configuration, string $plugin_id, $plugin_definition, $migration_plugin_manager, $source_plugin_manager, $process_plugin_manager, $destination_plugin_manager, $idmap_plugin_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration_plugin_manager, $source_plugin_manager, $process_plugin_manager, $destination_plugin_manager, $idmap_plugin_manager);

  }

  public function getProcess() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getMigrationDependencies() {
    $this->migration_dependencies = ($this->migration_dependencies ?: []) + ['required' => [], 'optional' => []];
    $this->migration_dependencies['optional'] = array_unique(array_merge($this->migration_dependencies['optional'], $this->findMigrationDependencies($this->getProcess())));
    return $this->migration_dependencies;
  }
}