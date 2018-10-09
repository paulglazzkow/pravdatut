<?php

namespace Drupal\site_grabber\Plugin\migrate\source;

use Drupal\import_info\Entity\ImportInfoEntity;
use Drupal\migrate\MigrateMessageInterface;
use Drupal\migrate\MigrateSkipRowException;
use Drupal\migrate\Plugin\MigrateIdMapInterface;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Row;
use Drupal\migrate_plus\Plugin\migrate\source\SourcePluginExtension;
use Drupal\site_grabber\ContentCounter;
use Drupal\site_grabber\exceptions\ImportDataException;
use Drupal\site_grabber\LogTrait;
use Drupal\site_grabber\parse_settings\ParseSettings;
use Drupal\site_grabber\parse_settings\ParseSettingsFactory;
use Drupal\views\Views;
use function is_array;


abstract class ImportFromSite extends SourcePluginExtension {

  use LogTrait;

  /**
   * The source URLs to retrieve.
   *
   * @var array
   */
  protected $sourceUrls = [];

  /**
   * The data parser plugin.
   *
   * @var \Drupal\migrate_plus\DataParserPluginInterface
   */
  protected $dataParserPlugin;

  protected $sourceType;

  protected $sourceBundle;

  protected $sourceFieldName;

  protected $sourceQueryFilters;

  protected $is_test = FALSE;

  protected $type;

  /** @var MigrateMessageInterface $message */
  protected $message;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration) {


    $configuration['import_type'] = $this->getType();
    $configuration['ids'] = $this->getIds();
    $configuration['fields'] = $this->fields();
    $configuration['process'] = $migration->getProcess();

    $migration->set('idMap', ['plugin' => 'sql_update']);

    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration);

    $all_fields = array_merge($this->destinationFields(), $this->getStatusFieldsList());

    $this->updateDestinationFieldsConfig($all_fields);
  }

  public function setMessage($message) {
    $this->message = $message;
  }

  protected function messageDisplay($message, $context = [], $type = 'status') {
    $this->message->display($this->t($message, $context), $type);
  }

  public function hasImportData() {
    return isset($this->configuration['urls']) &&
      is_array($this->configuration['urls']) &&
      FALSE === empty($this->configuration['urls']);
  }

  public function fields() {
    return $this->getStatusFieldsList() + $this->destinationFields();
  }

  protected function getType() {
    return $this->type;
  }

  protected function setType($type) {
    $this->type = $type;
  }

  public function updateDestinationFieldsConfig($fields) {

    $destination = $this->migration->getDestinationConfiguration();
    if (!isset($destination['overwrite_properties'])) {
      $destination['overwrite_properties'] = [];
    }
    $destination['overwrite_properties'] = $destination['overwrite_properties'] + array_keys($fields);
    $this->migration->set('destination', $destination);
  }

  public function getDataFields() {
    $dataFields = $this->destinationFields();
    return array_merge($dataFields, $this->getStatusFieldsList());
  }

  abstract public function getStatusConfig();

  abstract public function destinationFields();

  protected function getStatusProcess() {
    $process = [];
    $config = $this->getStatusConfig();
    foreach ($this->statusFields() as $name => $info) {
      $field_name = $info['field'];
      $process[$field_name] = [
        'plugin' => 'default_value',
        'default_value' => $config[$name],
      ];
    }
    return $process;
  }

  public function statusFields() {
    return [
      'state' => ['field' => 'field_state', 'title' => t('State field')],
      'status' => ['field' => 'field_status', 'title' => t('State status field')],
    ];
  }

  public function getStatusFieldsList() {
    $list = [];
    foreach ($this->statusFields() as $item) {
      $list[$item['field']] = $item['title'];
    }
    return $list;
  }

  /**
   * Get the result of a view.
   *
   * @param string $name
   *   The name of the view to retrieve the data from.
   * @param string $display_id
   *   The display id. On the edit page for the view in question, you'll find
   *   a list of displays at the left side of the control area. "Master"
   *   will be at the top of that list. Hover your cursor over the name of the
   *   display you want to use. A URL will appear in the status bar of your
   *   browser. This is usually at the bottom of the window, in the chrome.
   *   Everything after #views-tab- is the display ID, e.g. page_1.
   * @param ...
   *   Any additional parameters will be passed as arguments.
   *
   * @return array
   *   An array containing an object for each view item.
   */
  protected static function views_get_view_result($name, $display_id = NULL) {
    $args = func_get_args();
    // Remove $name and $display_id from the arguments.
    unset($args[0], $args[1]);

    $view = Views::getView($name);
    if (is_object($view)) {
      if (is_array($args)) {
        $view->setArguments($args);
      }
      if (is_string($display_id)) {
        $view->setDisplay($display_id);
      }
      else {
        $view->initDisplay();
      }
      $view->preExecute();
      $view->execute();
      return $view->result;
    }
    else {
      return [];
    }
  }

  public function setIsTest($status) {
    $this->is_test = $status;
  }

  public function count($refresh = FALSE) {

    return ContentCounter::count('import_content_entity', 'news');


  }

  public function loadData() {
    /* @var $this \Drupal\site_grabber\Plugin\migrate\source\ImportLinks */

    $this->rewind();
    $rows = [];
    $log = [];
    while ($this->valid()) {
      $rows[] = $this->current();
      $log[] = $this->getLog();
      $this->next();
    }
    return $rows;
  }

  /**
   * {@inheritdoc}
   *
   * The migration iterates over rows returned by the source plugin. This
   * method determines the next row which will be processed and imported into
   * the system.
   *
   * The method tracks the source and destination IDs using the ID map plugin.
   *
   * This also takes care about highwater support. Highwater allows to reimport
   * rows from a previous migration run, which got changed in the meantime.
   * This is done by specifying a highwater field, which is compared with the
   * last time, the migration got executed (originalHighWater).
   */
  public function next() {
    $this->currentSourceIds = NULL;
    $this->currentRow = NULL;

    // In order to find the next row we want to process, we ask the source
    // plugin for the next possible row.
    while (!isset($this->currentRow) && $this->getIterator()->valid()) {
      /* @var $iterator \Drupal\site_grabber\Plugin\migrate_plus\data_parser\ImportParser */
      $iterator = $this->getIterator();

      $row_data = $iterator->current();


      //      foreach ($row_data['config']->getProcess() as $property => $process) {
      //        $this->migration->setProcessOfProperty($property, $process);
      //      }

      //      $this->addStatusData($row_data);

      $this->fetchNextRow();

      $ids_source = $this->migration->getSourcePlugin()->getIds();
      $ids_destination = $this->migration->getDestinationIds();

      $row = new Row($row_data['values'], $ids_source, $ids_destination);

      // Populate the source key for this row.
      $this->currentSourceIds = $row->getSourceIdValues();

      // Pick up the existing map row, if any, unless fetchNextRow() did it.
      if (!$this->mapRowAdded && ($id_map = $this->idMap->getRowBySource($this->currentSourceIds))) {
        if ($this->getType() === 'content') {
          $id_map['source_row_status'] = MigrateIdMapInterface::STATUS_NEEDS_UPDATE;
        }
        $row->setIdMap($id_map);
      }

      // Clear any previous messages for this row before potentially adding
      // new ones.
      if (!empty($this->currentSourceIds)) {
        $this->idMap->delete($this->currentSourceIds, TRUE);
      }

      // Preparing the row gives source plugins the chance to skip.
      if ($this->prepareRow($row) === FALSE) {
        continue;
      }

      // Check whether the row needs processing.
      // 1. This row has not been imported yet.
      $not_imported = !$row->getIdMap();
      // 2. Explicitly set to update.
      $need_update = $row->needsUpdate();
      // 3. The row is newer than the current highwater mark.
      $row_newer = $this->aboveHighwater($row);
      // 4. If no such property exists then try by checking the hash of the row.
      $row_changed = $this->rowChanged($row);
      if ($not_imported || $need_update || $row_newer || $row_changed) {
        $this->currentRow = $row->freezeSource();
      }

      if ($this->getHighWaterProperty()) {
        $this->saveHighWater($row->getSourceProperty($this->highWaterProperty['name']));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $result = TRUE;
    try {
      $result_hook = $this->getModuleHandler()->invokeAll('migrate_prepare_row', [$row, $this, $this->migration]);
      $result_named_hook = $this->getModuleHandler()
        ->invokeAll('migrate_' . $this->migration->id() . '_prepare_row', [$row, $this, $this->migration]);


      // We will skip if any hook returned FALSE.
      $skip = ($result_hook && in_array(FALSE, $result_hook)) ||
        ($result_named_hook && in_array(FALSE, $result_named_hook));


      $save_to_map = TRUE;
    } catch (MigrateSkipRowException $e) {
      $skip = TRUE;
      $save_to_map = $e->getSaveToMap();
      if ($message = trim($e->getMessage())) {
        $this->idMap->saveMessage($row->getSourceIdValues(), $message, MigrationInterface::MESSAGE_INFORMATIONAL);
      }
    }

    // We're explicitly skipping this row - keep track in the map table.
    if ($skip) {
      // Make sure we replace any previous messages for this item with any
      // new ones.
      if ($save_to_map) {
        $this->idMap->saveIdMapping($row, [], MigrateIdMapInterface::STATUS_IGNORED);
        $this->currentRow = NULL;
        $this->currentSourceIds = NULL;
      }
      $result = FALSE;
    }
    elseif ($this->trackChanges) {
      // When tracking changed data, We want to quietly skip (rather than
      // "ignore") rows with changes. The caller needs to make that decision,
      // so we need to provide them with the necessary information (before and
      // after hashes).
      $row->rehash();
    }
    return $result;
  }

  /**
   * Checks if the incoming row has changed since our last import.
   *
   * @param \Drupal\migrate\Row $row
   *   The row we're importing.
   *
   * @return bool
   *   TRUE if the row has changed otherwise FALSE.
   */
  protected function rowChanged(Row $row) {
    return ($this->trackChanges && $row->changed()) || $this->is_test;
  }

  /**
   * Return a string representing the source URLs.
   *
   * @return string
   *   Comma-separated list of URLs being imported.
   */
  public function __toString() {
    // This could cause a problem when using a lot of urls, may need to hash.
    $urls = implode(', ', $this->sourceUrls);
    return $urls;
  }

  //  public static function initSettings(ParseSettings $settings) {
  //    $settings
  //      ->setSourceFormat('field_source_format', 'value')
  //      ->setContainerSelector('field_container_selector', 'value')
  //      ->setItemSelector('field_item_selector', 'value')
  //      ->setData('party.target_id', 'field_term_party', 'target_id');
  //
  //  }

  /**
   * Creates and returns a filtered Iterator over the documents.
   *
   * @return \Iterator
   *   An iterator over the documents providing source rows that match the
   *   configured item_selector.
   */
  protected function initializeIterator() {
    $this->initializeParserData();

    return $this->getDataParserPlugin();
  }


  /**
   * @throws \Drupal\site_grabber\exceptions\ImportDataException
   */
  protected function initializeParserData() {

    $viewsResult = $this->getImportConfigs();
    $import_data = $this->createImportData($viewsResult);

    if (empty($import_data)) {
      throw new ImportDataException('Import data is empty');
    }
    $this->messageDisplay('Sources count: @count', ['@count' => count($import_data)]);

    $this->addImportData($import_data);
  }

  abstract public function getImportConfigs();

  public function createImportData($settings) {
    $import_data = [];

    foreach ($settings as $entity) {
      foreach ($this->createSettings($entity) as $entity_data) {
        /** @var ParseSettings $entity_data   */
        foreach ($entity_data->getProcess() as $property => $process) {
          $this->migration->setProcessOfProperty($property, $process);
        }

        foreach ($this->getStatusProcess() as $property => $process) {
          $this->migration->setProcessOfProperty($property, $process);
        }

        $import_data[] = $entity_data;
      }
    }
    return $import_data;
  }

  public function createSettings($entity) {
    $settings = [];
    foreach (self::getUrls($entity, self::getUrlConfig()) as $source_link) {
      $config = ParseSettingsFactory::create($this->getType(), $entity, $source_link, $this->configuration);
      $settings[] = $config;
    }
    return $settings;

  }

  protected static function getUrls(ImportInfoEntity $entity, $url_config) {


    $field = $entity->get($url_config['field']);

    $values = [];
    foreach ($field as $item) {
      $value = $item->getValue();
      if (empty($value[$url_config['column']])) {
        continue;
      }
      $values[] = $value[$url_config['column']];
    }
    return $values;
  }

  public static function getUrlConfig() {
    return ['field' => 'field_link_source_links', 'column' => 'uri'];
  }

  public function addImportData($import_data) {
    if (!isset($this->configuration['urls'])) {
      $this->configuration['urls'] = [];
    }
    foreach ($import_data as $data) {
      $this->configuration['urls'][] = $data;
    }
  }

  /**
   * Returns the initialized data parser plugin.
   *
   * @return \Drupal\migrate_plus\DataParserPluginInterface
   *   The data parser plugin.
   */
  public function getDataParserPlugin() {
    if (!isset($this->dataParserPlugin)) {
      $this->dataParserPlugin = \Drupal::service('plugin.manager.migrate_plus.data_parser')
        ->createInstance($this->configuration['data_parser_plugin'], $this->configuration);
    }
    return $this->dataParserPlugin;
  }

}
