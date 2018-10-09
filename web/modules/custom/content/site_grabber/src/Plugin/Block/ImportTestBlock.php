<?php

namespace Drupal\site_grabber\Plugin\Block;

use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Component\Render\FormattableMarkup;
use Drupal\Console\Utils\MigrateExecuteMessageCapture;
use Drupal\Core\Block\BlockBase;
use Drupal\migrate\Plugin\Migration;
use Drupal\migrate\Row;
use Drupal\migrate_drupal_ui\Batch\MigrateMessageCapture;
use Drupal\site_grabber\ConfigTrait;
use Drupal\site_grabber\MigrateExecutableDebug;
use function array_keys;
use function is_scalar;


/**
 * Provides a 'ImportTestBlock' block.
 *
 * @Block(
 *  id = "import_test_block",
 *  admin_label = @Translation("Import test block"),
 * )
 */
class ImportTestBlock extends BlockBase {

  use ConfigTrait;

  const IMPORT_ENTITY_TYPE = 'import_info_entity';

  const IMPORT_ENTITY_BUNDLE = 'party_news_source_link';


  var $migration;

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
//    $build['import_test_block'] = $this->buildImportTest();
              $build['import_test_block']['#markup'] = 'Import Test Block';

    //    $build['import_test_block'] = $this->test();

    return $build;
  }

  protected function test() {
    $migration_id = 'import_content_news';
    /** @var \Drupal\migrate\Plugin\Migration $migration */
    $migration = $this->getMigration($migration_id);
    $source = $migration->getSourcePlugin();
    //    $destination = $migration->getDestinationPlugin();
    $idMap = $migration->getIdMap();
    $idMap->test();
    return ['#markup' => 'Test components'];
  }

  //  private function loadLinks($source, array $config_entities) {
  //    /* @var $source \Drupal\site_grabber\Plugin\migrate\source\ImportLinks */
  //    if (!is_array($config_entities)) {
  //      $config_entities = [$config_entities];
  //    }
  //    foreach ($config_entities as $config) {
  //      $source->addImportData($source->createSettings($config));
  //    }
  //
  //    $source->rewind();
  //    $rows = [];
  //    $log = [];
  //    while ($source->valid()) {
  //      $rows[] = $source->current();
  //      $log[] = $source->getLog();
  //      $source->next();
  //    }
  //    return $rows;
  //  }

  private function testLink() {

  }

  private function testContent() {

  }

  /* @var $source \Drupal\site_grabber\Plugin\migrate\source\ImportLinks */
  private function getTestData($config_entity) {
    $data['type'] = self::getFieldValue($config_entity, 'field_test_import', 'value');

    if (empty($data['type'])) {
      return NULL;
    }

    switch ($data['type']) {
      case 'link':
        $data['source_url'] = NULL;
        break;
      case 'content':
        $data['source_url'] = self::getFieldValue($config_entity, 'field_test_import_content_url', 'uri');
        break;
    }
    return $data;
  }

  private function _log($msg, $context = [], $style = '') {
    $output = new FormattableMarkup($msg, $context);

    return [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#value' => $output,
      '#attributes' => [
        'style' => [$style],
      ],
    ];
  }

  private function errorMessage($msg, $context = []) {
    $style = 'color:#770000;background-color:#ffddaa;padding:5px';
    return $this->_log($msg, $context, $style);
  }

  private function resultMessage($msg, $context = []) {
    $style = 'color:#000033;background-color:#aaddff;padding:5px';
    return $this->_log($msg, $context, $style);
  }


  private function getMigrationId($type) {

    switch ($type) {
      case 'link':
        $migration_id = 'import_links_news';
        break;
      case 'content':
        $migration_id = 'import_content_news';
        break;
      default:
    }

    return $migration_id;
  }

  private function getDataEntity($config, $config_entity) {

    if ($config['type'] === 'content') {
      $data_entity = $this->getContentEntity($config_entity);
    }

    return $data_entity;
  }

  private function buildImportTest() {
    $config_entity = $this->getCurrentConfig();
    $test_config = $this->getTestData($config_entity);

    if (empty($test_config)) {
      return $this->errorMessage('Migrate test mode not selected');
    }

    $data_entity = $this->getDataEntity($test_config, $config_entity);

    if (empty($data_entity)) {
      return $this->errorMessage('Migrate Config entity not found.</br> type: :type ', [':type' => $test_config['type']]);
    }

    $migration_id = $this->getMigrationId($test_config['type']);

//    $source_link = NULL;
    /** @var \Drupal\site_grabber\Plugin\migrate\source\ImportFromSite $source */
//    $source = $this->getSource($migration_id);

    /* @var $config \Drupal\site_grabber\parse_settings\ParseSettings */
//    $config = $source->createSettings($config_entity);

//    $source->addImportData($config);
    //    $rows = $source->loadData();


    $migration = $this->getMigration($migration_id);
    $messages = new MigrateExecuteMessageCapture();
    $executable = new MigrateExecutableDebug($migration, $messages);

    return $this->resultMessage($executable->import());

    //    foreach ($rows as $row) {
    //      /* @var $row Row */
    //      try {
    //        $source = $row->getSource();
    //        $executable->processRow($row, $source['process']);
    //      } catch (MigrateException $e) {
    //      }
    //    }
    //    //    $filters = new FilterPluginCollection(\Drupal::service('plugin.manager.filter'));
    //    //    $ace = $filters->get('ace_filter');
    //    return $this->createTable($rows);
  }

  private function createTable($rows) {

    $values = array_map(function (Row $row) {
      return $row->getDestination();
    }, $rows);

    if (empty($values)) {
      return [
        '#markup' => 'Import data is empty',
      ];
    }


    $first = reset($values);
    $header = array_keys($first);
    $table_rows = [];

    foreach ($values as $data) {
      $table_row = [];
      foreach ($header as $field) {
        $value = $data[$field];
        if (FALSE === is_scalar($value)) {
          $value = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }
        $table_row[] = ['#markup' => $value];
      }
      $table_rows[] = $table_row;
    }

    return [
        '#type' => 'table',
        '#caption' => t('Import result'),
        '#header' => $header,
      ] + $table_rows;
  }

  private function getContentEntity($config_entity, $offset = 0) {
    $entity_type = 'import_content_entity';
    $bundle = 'news';
    try {

      $query = \Drupal::entityQuery($entity_type)
        ->condition('type', $bundle)
        ->condition('field_import_config', $config_entity->id())
        ->range($offset, 1);

      $ids = $query->execute();

      if (empty($ids)) {
        return NULL;
      }

      $entity = \Drupal::entityTypeManager()->getStorage($entity_type)
        ->loadByProperties(['id' => $ids]);
      return reset($entity);
    } catch (InvalidPluginDefinitionException $e) {
    } catch (PluginNotFoundException $e) {
    }


  }

  private function getCurrentConfig() {
    return \Drupal::routeMatch()->getParameter(self::IMPORT_ENTITY_TYPE);
  }

  private function getMigrate(Migration $migration) {
    return new MigrateExecutableDebug($migration, new MigrateMessageCapture());
  }

  private function import($migration_id) {
    $migration = $this->getMigration($migration_id);
    $migrate = $this->getMigrate($migration);
  }


  private function getMigration($migration_id) {
    if (!$this->migration) {
      //      $migration_id = 'import_links_news';
      $manager = \Drupal::service('plugin.manager.migration');
      $plugins = $manager->createInstances([$migration_id]);
      /* @var $migration \Drupal\migrate\Plugin\Migration */
      $this->migration = $plugins[$migration_id];
      //      $source = $migration->getSourcePlugin();
    }
    return $this->migration;
  }

  private function getSource($migration_id) {
    /* @var $migration \Drupal\migrate\Plugin\Migration */
    $migration = $this->getMigration($migration_id);
    $source = $migration->getSourcePlugin();
    $source->setIsTest(TRUE);
    return $source;
  }

}

//import_content_entity