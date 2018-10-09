<?php

namespace Drupal\import_info;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\import_info\Entity\ImportContentEntityInterface;

/**
 * Defines the storage handler class for Import Content entities.
 *
 * This extends the base storage class, adding required special handling for
 * Import Content entities.
 *
 * @ingroup import_info
 */
class ImportContentEntityStorage extends SqlContentEntityStorage implements ImportContentEntityStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(ImportContentEntityInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {import_content_entity_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {import_content_entity_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(ImportContentEntityInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {import_content_entity_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('import_content_entity_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
