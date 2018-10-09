<?php

namespace Drupal\site_grabber\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Drupal\site_grabber\utils\UtilsLinks;
use function libxml_get_errors;
use function simplexml_load_string;
use function substr;

/**
 * Provides a 'TextFromLink' migrate process plugin.
 *
 * @MigrateProcessPlugin(
 *  id = "links_external_prepare"
 * )
 */
class LinksExternalPrepare extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {

    $source_field = $this->configuration['source'];
    $source = $row->getSource();
    if ($source_field[0] === '@') {
      $source_field = substr($source_field, 1);
      $source_content = $row->getDestinationProperty($source_field);
    }
    else {
      $source_content = $row->getSourceProperty($source_field);
    }


    $domain = UtilsLinks::getDomain($source[$this->configuration['domain']]);
    $xml = simplexml_load_string('<root>' . $source_content . '</root>');
    libxml_get_errors();
    UtilsLinks::prepareExternalLinks($xml, $domain);
    $type = isset($this->configuration['type']) ? $this->configuration['type'] : 'html';
    $output = [];
    switch ($type) {
      case 'html':
        foreach ($xml->xpath('/root/*') as $item) {
          $output[] = $item->asXML();
        }
        break;
      case 'text':
        foreach ($xml->xpath('/root/*') as $item) {
          $output[] = (string) $item;
        }
        break;
    }
    return join("\n", $output);
  }

}
