<?php

namespace Drupal\entity_tree;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\entity_tree\Entity\EntityTreeInterface;

/**
 * Defines the storage handler class for Entity tree entities.
 *
 * This extends the base storage class, adding required special handling for
 * Entity tree entities.
 *
 * @ingroup entity_tree
 */
class EntityTreeStorage extends SqlContentEntityStorage implements EntityTreeStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(EntityTreeInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {entity_tree_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {entity_tree_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(EntityTreeInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {entity_tree_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('entity_tree_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
