<?php

namespace Drupal\entity_tech;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\entity_tech\Entity\EntityTechInterface;

/**
 * Defines the storage handler class for Entity tech entities.
 *
 * This extends the base storage class, adding required special handling for
 * Entity tech entities.
 *
 * @ingroup entity_tech
 */
class EntityTechStorage extends SqlContentEntityStorage implements EntityTechStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(EntityTechInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {entity_tech_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {entity_tech_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(EntityTechInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {entity_tech_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('entity_tech_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
