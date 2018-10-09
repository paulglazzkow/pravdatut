<?php

namespace Drupal\enumeration;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\enumeration\Entity\EntityEnumerationInterface;

/**
 * Defines the storage handler class for Entity enumeration entities.
 *
 * This extends the base storage class, adding required special handling for
 * Entity enumeration entities.
 *
 * @ingroup enumeration
 */
class EntityEnumerationStorage extends SqlContentEntityStorage implements EntityEnumerationStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(EntityEnumerationInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {entity_enumeration_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {entity_enumeration_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(EntityEnumerationInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {entity_enumeration_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('entity_enumeration_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
