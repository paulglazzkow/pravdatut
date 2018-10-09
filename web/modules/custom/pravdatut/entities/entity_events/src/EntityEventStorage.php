<?php

namespace Drupal\entity_events;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\entity_events\Entity\EntityEventInterface;

/**
 * Defines the storage handler class for Event entities.
 *
 * This extends the base storage class, adding required special handling for
 * Event entities.
 *
 * @ingroup entity_events
 */
class EntityEventStorage extends SqlContentEntityStorage implements EntityEventStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(EntityEventInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {entity_event_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {entity_event_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(EntityEventInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {entity_event_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('entity_event_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
