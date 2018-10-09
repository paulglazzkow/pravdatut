<?php

namespace Drupal\site_grabber\Plugin\migrate_plus\data_parser;

use Drupal\migrate_plus\DataParserPluginBase;
use Drupal\migrate_plus\Plugin\migrate_plus\data_parser\XmlTrait;
use Drupal\site_grabber\LogTrait;
use Drupal\site_grabber\parse_settings\ParseSettings;
use Drupal\site_grabber\parser\HTMLParser;
use Drupal\site_grabber\parser\JSONParser;


/**
 * Obtain XML data for migration using the SimpleXML API.
 *
 * @DataParser(
 *   id = "import_parser",
 *   title = @Translation("Html parser")
 * )
 */
class ImportParser extends DataParserPluginBase {

  use XmlTrait;
  use LogTrait;

  /**
   * Array of matches from item_selector.
   *
   * @var array
   */
  protected $matches = [];

  protected $parser = [];

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    if (empty($configuration['urls'])) {
      $n = 0;
    }
    $configuration['item_selector'] = '';
    $this->initConfigFetcherPlugin($configuration);
    parent::__construct($configuration, $plugin_id, $plugin_definition);

  }

  public function initConfigFetcherPlugin(&$configuration) {
    $entity_fetcher = [
      'process_image_inline_news' => 1,
    ];
    $source_plugin = $configuration['plugin'];
    if (!isset($configuration['data_fetcher_plugin']) && isset($entity_fetcher[$source_plugin])) {
      $configuration['data_fetcher_plugin'] = 'entity_fetcher';
    }
  }

  public function getType() {
    return $this->configuration['import_type'];
  }

  public function getParser($source_format) {
    if (empty($this->parser) || !isset($this->parser[$source_format])) {
      switch ($source_format) {
        case 'html':
        case 'xml':
          $this->parser[$source_format] = new HTMLParser();
          break;

        case 'json':
          $this->parser[$source_format] = new JSONParser();
          break;
      }
    }
    return $this->parser[$source_format];
  }


  /**
   * {@inheritdoc}
   */
  public function openSourceUrl($config) {
    /* @var $config ParseSettings */
    if (empty($config)) {
      throw  new ImportParserException('Import config is empty');
    }
    $content = $this->getDataFetcherPlugin()->getResponseContent($config);
    $content = $this->getParser($config->getSourceFormat())->onContentLoad($content);
    /* @var $parser \Drupal\site_grabber\parser\BaseParser */
    $parser = $this->getParser($config->getSourceFormat());

    switch ($this->getType()) {
      case 'link':
        $matches = $parser->parseLink($config, $content);
        break;
      case 'content':
        $matches = $parser->parseContent($config, $content);
        break;
    }

    foreach ($matches as $result) {
      $this->matches[] = ['content' => $result, 'config' => $config];
    }

    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  protected function fetchNextRow() {
    $target_element = array_shift($this->matches);

    if ($target_element !== FALSE && !is_null($target_element)) {
      $content = $target_element['content'];
      /* @var $config ParseSettings */
      $config = $target_element['config'];


      $this->currentItem = ['config' => $config, 'values' => $config->getDataAll()];

      $values = [];
      foreach ($config->getFieldSelectors() as $field_name => $xpath) {
        /* @var $parser \Drupal\site_grabber\parser\BaseParser */
        $parser = $this->getParser($config->getSourceFormat());
        $value = $parser->parseRowValue($config, $content, $xpath);
        if (empty($value)) {
          continue;
        }
        if (count($value) == 1) {
          $value = reset($value);
        }
        $values[$field_name] = $value;

      }
      $values['source_url'] = $config->getSourceUrl();

      $this->currentItem['values'] += $values;
    }
  }

  /**
   * Implementation of Iterator::next().
   */
  public function next() {
    $this->currentItem = $this->currentId = NULL;
    if (is_null($this->activeUrl)) {
      if (!$this->nextSource()) {
        // No data to import.
        return;
      }
    }
    // At this point, we have a valid open source url, try to fetch a row from
    // it.
    $this->fetchNextRow();
    // If there was no valid row there, try the next url (if any).
    if (is_null($this->currentItem)) {
      while ($this->nextSource()) {
        $this->fetchNextRow();
        if ($this->valid()) {
          break;
        }
      }
    }
    if ($this->valid()) {
      foreach ($this->configuration['ids'] as $id_field_name => $id_info) {
        $this->currentId[$id_field_name] = $this->currentItem['values'][$id_field_name];
      }
    }
  }

  /**
   * Advances the data parser to the next source url.
   *
   * @return bool
   *   TRUE if a valid source URL was opened
   */
  protected function nextSource() {
    while ($this->activeUrl === NULL || (count($this->urls) - 1) > $this->activeUrl) {
      if (is_null($this->activeUrl)) {
        $this->activeUrl = 0;
      }
      else {
        // Increment the activeUrl so we try to load the next source.
        $this->activeUrl = $this->activeUrl + 1;
        if ($this->activeUrl >= count($this->urls)) {
          return FALSE;
        }
      }

      if ($this->openSourceUrl($this->urls[$this->activeUrl])) {
        // We have a valid source.
        return TRUE;
      }
    }

    return FALSE;
  }
}
