<?php

namespace Drupal\site_grabber\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * Provides a 'UrlToProtocol' migrate process plugin.
 *
 * @MigrateProcessPlugin(
 *  id = "url_to_protocol"
 * )
 */
class UrlToProtocol extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // Plugin logic goes here.
  }

}
