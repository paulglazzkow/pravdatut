<?php

namespace Drupal\entity_party_programm;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\entity_party_programm\Entity\EntityPartyProgrammInterface;

/**
 * Defines the storage handler class for Entity party programm entities.
 *
 * This extends the base storage class, adding required special handling for
 * Entity party programm entities.
 *
 * @ingroup entity_party_programm
 */
class EntityPartyProgrammStorage extends SqlContentEntityStorage implements EntityPartyProgrammStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(EntityPartyProgrammInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {entity_party_programm_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {entity_party_programm_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(EntityPartyProgrammInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {entity_party_programm_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('entity_party_programm_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
