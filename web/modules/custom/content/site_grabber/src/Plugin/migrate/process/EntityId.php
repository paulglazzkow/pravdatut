<?php

namespace Drupal\site_grabber\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Provides a 'UrlToPath' migrate process plugin.
 *
 * @MigrateProcessPlugin(
 *  id = "entity_id"
 * )
 */
class EntityId extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $n = 0;
    $source = $row->getSource();
    $entity = $source[$this->configuration['entity']];
    return $entity->id();
  }

}
