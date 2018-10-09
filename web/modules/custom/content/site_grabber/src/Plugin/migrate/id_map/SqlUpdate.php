<?php

namespace Drupal\site_grabber\Plugin\migrate\id_map;

use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\migrate\Event\MigrateEvents;
use Drupal\migrate\Event\MigrateMapSaveEvent;
use Drupal\migrate\Plugin\migrate\id_map\Sql;
use Drupal\migrate\Plugin\MigrateIdMapInterface;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Row;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use function is_integer;
use function preg_split;

/**
 * Defines the sql based ID map implementation.
 *
 * It creates one map and one message table per migration entity to store the
 * relevant information.
 *
 * @PluginID("sql_update")
 */
class SqlUpdate extends Sql {

  var $destination;

  /** @var EntityManagerInterface $entity_manager */
  var $entity_manager;

  /**
   * Constructs an SQL object.
   *
   * Sets up the tables and builds the maps,
   *
   * @param array $configuration
   *   The configuration.
   * @param string $plugin_id
   *   The plugin ID for the migration process to do.
   * @param mixed $plugin_definition
   *   The configuration for the plugin.
   * @param \Drupal\migrate\Plugin\MigrationInterface $migration
   *   The migration to do.
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $event_dispatcher
   *   The event dispatcher.
   */
  public function __construct(array $configuration,
                              $plugin_id,
                              $plugin_definition,
                              MigrationInterface $migration,
                              EventDispatcherInterface $event_dispatcher,
                              EntityManagerInterface $entity_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration, $event_dispatcher);
    $this->entity_manager = $entity_manager;

  }

  public function saveIdMapping(Row $row, array $destination_id_values, $source_row_status = MigrateIdMapInterface::STATUS_IMPORTED, $rollback_action = MigrateIdMapInterface::ROLLBACK_DELETE) {
    $fields = [
      'revision_id' => $row->getDestinationProperty('revision_id'),
      'source_row_status' => MigrateIdMapInterface::STATUS_NEEDS_UPDATE,
    ];
    return $this->_saveIdMapping($fields, $row, $destination_id_values, $source_row_status);
  }

  /**
   * Оригинальный метод родителя, добавлен параметр $fields
   * {@inheritdoc}
   */
  public function _saveIdMapping($fields, Row $row, array $destination_id_values, $source_row_status = MigrateIdMapInterface::STATUS_IMPORTED, $rollback_action = MigrateIdMapInterface::ROLLBACK_DELETE) {
    // Construct the source key.
    $source_id_values = $row->getSourceIdValues();
    // Construct the source key and initialize to empty variable keys.

    foreach ($this->sourceIdFields() as $field_name => $key_name) {
      // A NULL key value is usually an indication of a problem.
      if (!isset($source_id_values[$field_name])) {
        $this->message->display($this->t(
          'Did not save to map table due to NULL value for key field @field',
          ['@field' => $field_name]), 'error');
        return;
      }
      $fields[$key_name] = $source_id_values[$field_name];
    }

    if (!$fields) {
      return;
    }

    $fields += [
      'source_row_status' => (int) $source_row_status,
      'rollback_action' => (int) $rollback_action,
      'hash' => $row->getHash(),
    ];
    $count = 0;
    foreach ($destination_id_values as $dest_id) {
      $fields['destid' . ++$count] = $dest_id;
    }
    if ($count && $count != count($this->destinationIdFields())) {
      $this->message->display(t('Could not save to map table due to missing destination id values'), 'error');
      return;
    }
    if ($this->migration->getTrackLastImported()) {
      $fields['last_imported'] = time();
    }
    $keys = [static::SOURCE_IDS_HASH => $this->getSourceIdsHash($source_id_values)];
    // Notify anyone listening of the map row we're about to save.
    $this->eventDispatcher->dispatch(MigrateEvents::MAP_SAVE, new MigrateMapSaveEvent($this, $fields));
    $this->getDatabase()->merge($this->mapTableName())
      ->key($keys)
      ->fields($fields)
      ->execute();
  }

  /**
   * {@inheritdoc}
   */
  public static function create($container, array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration = NULL) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $migration,
      $container->get('event_dispatcher'),
      $container->get('entity.manager')
    );
  }

  protected function getDestinationTypeId() {
    $destination = $this->migration->getDestinationConfiguration();
    list(, $destinationEntityType) = preg_split('/\:/', $destination['plugin']);
    return $destinationEntityType;
  }

  protected function getDestinationBundleId() {
    $destination = $this->migration->getDestinationConfiguration();
    return $destination['default_bundle'];
  }

  private static function arrayFilter($arr) {
    $result = [];
    foreach ($arr as $key => $item) {
      if (empty($item)) {
        continue;
      }
      if (!is_array($item)) {
        if (is_integer($key)) {
          $result[] = $item;
        }
        else {
          $result[$key] = $item;
        }
        continue;
      }
      if (($item = self::arrayFilter($item)) && !empty($item)) {
        if (is_integer($key)) {
          $result[] = $item;
        }
        else {
          $result[$key] = $item;
        }
      }
    }
    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function lookupDestinationIds(array $source_id_values) {
    if (empty($source_id_values)) {
      return [];
    }

    $ids = self::arrayFilter(parent::lookupDestinationIds($source_id_values));

    if (!empty($ids)) {
      return $ids;
    }

    if (!isset($source_id_values['entity_id'])) {
      throw new \Error("'entity_id' for Migrate IdMap is empty");
    }

    return [[$source_id_values['entity_id']]];
  }

  function loadEntity($id) {
    try {
      return \Drupal::entityTypeManager()
        ->getStorage($this->getDestinationTypeId())
        ->loadByProperties(['id' => $id]);
    } catch (InvalidPluginDefinitionException $e) {
    } catch (PluginNotFoundException $e) {
    }
  }

  public function test() {

  }

  private function _createMapTable($source_id_schema = []) {
    // Generate appropriate schema info for the map and message tables,
    // and map from the source field names to the map/msg field names.
    $count = 1;
    $indexes = [];
    foreach ($this->migration->getSourcePlugin()->getIds() as $id_definition) {
      $mapkey = 'sourceid' . $count++;
      $indexes['source'][] = $mapkey;
      $source_id_schema[$mapkey] = $this->getFieldSchema($id_definition);
      $source_id_schema[$mapkey]['not null'] = TRUE;
    }

    $source_ids_hash[static::SOURCE_IDS_HASH] = [
      'type' => 'varchar',
      'length' => '64',
      'not null' => TRUE,
      'description' => 'Hash of source ids. Used as primary key',
    ];
    $fields = $source_ids_hash + $source_id_schema;

    // Add destination identifiers to map table.
    // @todo How do we discover the destination schema?
    $count = 1;
    foreach ($this->migration->getDestinationPlugin()->getIds() as $id_definition) {
      // Allow dest identifier fields to be NULL (for IGNORED/FAILED cases).
      $mapkey = 'destid' . $count++;
      $fields[$mapkey] = $this->getFieldSchema($id_definition);
      $fields[$mapkey]['not null'] = FALSE;
    }
    $fields['source_row_status'] = [
      'type' => 'int',
      'size' => 'tiny',
      'unsigned' => TRUE,
      'not null' => TRUE,
      'default' => MigrateIdMapInterface::STATUS_IMPORTED,
      'description' => 'Indicates current status of the source row',
    ];
    $fields['rollback_action'] = [
      'type' => 'int',
      'size' => 'tiny',
      'unsigned' => TRUE,
      'not null' => TRUE,
      'default' => MigrateIdMapInterface::ROLLBACK_DELETE,
      'description' => 'Flag indicating what to do for this item on rollback',
    ];
    $fields['last_imported'] = [
      'type' => 'int',
      'unsigned' => TRUE,
      'not null' => TRUE,
      'default' => 0,
      'description' => 'UNIX timestamp of the last time this row was imported',
    ];
    $fields['hash'] = [
      'type' => 'varchar',
      'length' => '64',
      'not null' => FALSE,
      'description' => 'Hash of source row data, for detecting changes',
    ];
    $schema = [
      'description' => 'Mappings from source identifier value(s) to destination identifier value(s).',
      'fields' => $fields,
      'primary key' => [static::SOURCE_IDS_HASH],
      'indexes' => $indexes,
    ];
    $this->getDatabase()->schema()->createTable($this->mapTableName, $schema);

    // Now do the message table.
    if (!$this->getDatabase()->schema()->tableExists($this->messageTableName())) {
      $fields = [];
      $fields['msgid'] = [
        'type' => 'serial',
        'unsigned' => TRUE,
        'not null' => TRUE,
      ];
      $fields += $source_ids_hash;

      $fields['level'] = [
        'type' => 'int',
        'unsigned' => TRUE,
        'not null' => TRUE,
        'default' => 1,
      ];
      $fields['message'] = [
        'type' => 'text',
        'size' => 'medium',
        'not null' => TRUE,
      ];
      $schema = [
        'description' => 'Messages generated during a migration process',
        'fields' => $fields,
        'primary key' => ['msgid'],
      ];
      $this->getDatabase()->schema()->createTable($this->messageTableName(), $schema);
    }
  }

  function getOverwriteFieldSchema() {
    $field_name = 'revision_id';
    $type = 'integer';
    return [$field_name => $this->getFieldSchema(['type' => $type])];
  }

  protected function ensureTables() {
    parent::ensureTables();
    $this->addRevisionField();
  }

  protected function addRevisionField() {
    if (!$this->getDatabase()->schema()->fieldExists($this->mapTableName,
      'revision_id')) {
      $this->getDatabase()->schema()->addField($this->mapTableName, 'revision_id',
        [
          'description' => 'The prev entity revision ID for rollback.',
          'type' => 'int',
          'unsigned' => TRUE,
        ]
      );
    }
  }

}