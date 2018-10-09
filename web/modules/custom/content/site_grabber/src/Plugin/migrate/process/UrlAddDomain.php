<?php

namespace Drupal\site_grabber\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Drupal\site_grabber\utils\UtilsLinks;

/**
 * Provides a 'UrlToDomain' migrate process plugin.
 *
 * @MigrateProcessPlugin(
 *  id = "url_add_domain"
 * )
 */
class UrlAddDomain extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {

    $source = $row->getSource();

    $uri = $source[$this->configuration['url']];
    $domain = UtilsLinks::getDomain($source[$this->configuration['domain']]);

    return UtilsLinks::addDomain($domain, $uri);
  }

}
