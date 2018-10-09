<?php

namespace Drupal\import_info;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\import_info\Entity\ImportInfoEntityInterface;

/**
 * Defines the storage handler class for Import info entity entities.
 *
 * This extends the base storage class, adding required special handling for
 * Import info entity entities.
 *
 * @ingroup import_info
 */
class ImportInfoEntityStorage extends SqlContentEntityStorage implements ImportInfoEntityStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(ImportInfoEntityInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {import_info_entity_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {import_info_entity_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(ImportInfoEntityInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {import_info_entity_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('import_info_entity_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
