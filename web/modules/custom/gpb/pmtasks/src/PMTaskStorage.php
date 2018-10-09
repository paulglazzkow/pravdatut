<?php

namespace Drupal\pmtasks;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\pmtasks\Entity\PMTaskInterface;

/**
 * Defines the storage handler class for Project task entities.
 *
 * This extends the base storage class, adding required special handling for
 * Project task entities.
 *
 * @ingroup pmtasks
 */
class PMTaskStorage extends SqlContentEntityStorage implements PMTaskStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(PMTaskInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {pmtask_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {pmtask_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(PMTaskInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {pmtask_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('pmtask_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
