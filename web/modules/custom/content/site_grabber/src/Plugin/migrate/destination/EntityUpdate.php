<?php

namespace Drupal\site_grabber\Plugin\migrate\destination;

use Drupal\Component\Plugin\DependentPluginInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\TranslatableInterface;
use Drupal\Core\Field\FieldTypePluginManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\TypedData\TypedDataInterface;
use Drupal\migrate\MigrateException;
use Drupal\migrate\Plugin\migrate\destination\EntityContentBase;
use Drupal\migrate\Plugin\MigrateIdMapInterface;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Row;
use Error;

/**
 * Provides a generic destination to import entities.
 *
 * Available configuration keys:
 * - translations: (optional) Boolean, if TRUE, the destination will be
 *   associated with the langcode provided by the source plugin. Defaults to
 *   FALSE.
 *
 * Examples:
 *
 * @code
 * source:
 *   plugin: d7_node
 * process:
 *   nid: tnid
 *   vid: vid
 *   langcode: language
 *   title: title
 *   ...
 *   revision_timestamp: timestamp
 * destination:
 *   plugin: entity:node
 * @endcode
 *
 * This will save the processed, migrated row as a node.
 *
 * @code
 * source:
 *   plugin: d7_node
 * process:
 *   nid: tnid
 *   vid: vid
 *   langcode: language
 *   title: title
 *   ...
 *   revision_timestamp: timestamp
 * destination:
 *   plugin: entity:node
 *   translations: true
 * @endcode
 *
 * This will save the processed, migrated row as a node with the relevant
 * langcode because the translations configuration is set to "true".
 *
 * @MigrateDestination(
 *   id = "entity_update",
 *   deriver = "Drupal\site_grabber\Plugin\Derivative\MigrateEntityUpdate"
 * )
 */
class EntityUpdate extends EntityContentBase implements ContainerFactoryPluginInterface, DependentPluginInterface {

  public function __construct(array $configuration,
                              $plugin_id,
                              $plugin_definition,
                              MigrationInterface $migration,
                              EntityStorageInterface $storage,
                              array $bundles,
                              EntityManagerInterface $entity_manager,
                              FieldTypePluginManagerInterface $field_type_manager) {

    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration, $storage, $bundles, $entity_manager, $field_type_manager);

  }

  /**
   * Creates or loads an entity.
   *
   * @param \Drupal\migrate\Row $row
   *   The row object.
   * @param array $old_destination_id_values
   *   The old destination IDs.
   *
   * @return \Drupal\Core\Entity\EntityInterface
   *   The entity we are importing into.
   */
  protected function getEntity(Row $row, array $old_destination_id_values) {
    $entity_id = $this->getEntityId($row);
    if (!empty($entity_id) && ($entity = $this->storage->load($entity_id))) {
      // Allow updateEntity() to change the entity.
      $entity = $this->updateEntity($entity, $row) ?: $entity;
    }
    else {
      // Attempt to ensure we always have a bundle.
      if ($bundle = $this->getBundle($row)) {
        $row->setDestinationProperty($this->getKey('bundle'), $bundle);
      }

      // Stubs might need some required fields filled in.
      if ($row->isStub()) {
        $this->processStubRow($row);
      }
      $entity = $this->storage->create($row->getDestination());
      $entity->enforceIsNew();
    }
    return $entity;
  }

  /**
   * Gets the entity ID of the row.
   *
   * @param \Drupal\migrate\Row $row
   *   The row of data.
   *
   * @return string
   *   The entity ID for the row that we are importing.
   */
  protected function getEntityId(Row $row) {
    return $row->getSourceProperty('entity_id');
  }


  function saveNewRevision($entity) {
    /** @var \Drupal\Core\Entity\RevisionableContentEntityBase $entity */
    if (FALSE === $entity->getEntityType()->isRevisionable()) {
      throw new \Error('Entity for migrate not Revisionable! entity_type:' . $entity->getEntityTypeId());
    }
    $entity->revision_log = 'Migrate updated';
    $entity->setNewRevision();
    $entity->save();

    return [$entity->id()];
  }

  /**
   * Updates an entity with the new values from row.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to update.
   * @param \Drupal\migrate\Row $row
   *   The row object to update from.
   *
   * @return \Drupal\Core\Entity\EntityInterface
   *   An updated entity from row values.
   */
  protected function updateEntity(EntityInterface $entity, Row $row) {

    $empty_destinations = $row->getEmptyDestinationProperties();
    // By default, an update will be preserved.
    $rollback_action = MigrateIdMapInterface::ROLLBACK_PRESERVE;

    // Make sure we have the right translation.
    if ($this->isTranslationDestination()) {
      $property = $this->storage->getEntityType()->getKey('langcode');
      if ($row->hasDestinationProperty($property)) {
        $language = $row->getDestinationProperty($property);
        if (!$entity->hasTranslation($language)) {
          $entity->addTranslation($language);

          // We're adding a translation, so delete it on rollback.
          $rollback_action = MigrateIdMapInterface::ROLLBACK_DELETE;
        }
        $entity = $entity->getTranslation($language);
      }
    }

    // If the migration has specified a list of properties to be overwritten,
    // clone the row with an empty set of destination values, and re-add only
    // the specified properties.
    if (isset($this->configuration['overwrite_properties'])) {
      $empty_destinations = array_intersect($empty_destinations, $this->configuration['overwrite_properties']);
      $clone = $row->cloneWithoutDestination();
      foreach ($this->configuration['overwrite_properties'] as $property) {
        $clone->setDestinationProperty($property, $row->getDestinationProperty($property));
      }
      $row = $clone;
    }

    foreach ($row->getDestination() as $field_name => $values) {
      $field = $entity->$field_name;
      if ($field instanceof TypedDataInterface) {
        $field->setValue($values);
      }
    }
    foreach ($empty_destinations as $field_name) {
      $entity->$field_name = NULL;
    }

    $this->setRollbackAction($row->getIdMap(), $rollback_action);

    // We might have a different (translated) entity, so return it.
    return $entity;
  }

  /**
   * {@inheritdoc}
   */
  public function import(Row $row, array $old_destination_id_values = []) {
    $this->migration->getIdMap()->prepareUpdate();

    $this->rollbackAction = MigrateIdMapInterface::ROLLBACK_PRESERVE;
    /** @var \Drupal\Core\Entity\ContentEntityInterface $entity */
    $entity = $this->getEntity($row, $old_destination_id_values);

    if (!$entity) {
      throw new MigrateException('Unable to get entity');
    }

    $row->setDestinationProperty('revision_id', $entity->getRevisionId());

    $ids = $this->save($entity, $old_destination_id_values);
    if ($this->isTranslationDestination()) {
      $ids[] = $entity->language()->getId();
    }
    return $ids;
  }

  /**
   * Saves the entity.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   The content entity.
   * @param array $old_destination_id_values
   *   (optional) An array of destination ID values. Defaults to an empty array.
   *
   * @return array
   *   An array containing the entity ID.
   */
  protected function save(ContentEntityInterface $entity, array $old_destination_id_values = []) {
    try {
      return $this->saveNewRevision($entity);
    } catch (Error $e) {
    }
  }

  /**
   * {@inheritdoc}
   */
  public function rollback(array $destination_identifier) {
    if ($this->isTranslationDestination()) {
      // Attempt to remove the translation.
      $entity = $this->storage->load(reset($destination_identifier));
      if ($entity && $entity instanceof TranslatableInterface) {
        if ($key = $this->getKey('langcode')) {
          if (isset($destination_identifier[$key])) {
            $langcode = $destination_identifier[$key];
            if ($entity->hasTranslation($langcode)) {
              // Make sure we don't remove the default translation.
              $translation = $entity->getTranslation($langcode);
              if (!$translation->isDefaultTranslation()) {
                $entity->removeTranslation($langcode);
                $entity->save();
              }
            }
          }
        }
      }
    }
    else {
      $this->_rollback($destination_identifier);
    }
  }

  /**
   * {@inheritdoc}
   */
  private function _rollback(array $destination_identifier) {
    // Delete the specified entity from Drupal if it exists.
    $entity = $this->storage->load(reset($destination_identifier));
    if ($entity) {
      $entity->delete();
    }
  }

  /**
   * Finds the entity type from configuration or plugin ID.
   *
   * @param string $plugin_id
   *   The plugin ID.
   *
   * @return string
   *   The entity type.
   */
  protected static function getEntityTypeId($plugin_id) {
    // Remove "entity_update:".
    return substr($plugin_id, 14);
  }


}
