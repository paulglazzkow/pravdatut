<?php

namespace Drupal\entity_news;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\entity_news\Entity\EntityNewsInterface;

/**
 * Defines the storage handler class for News entities.
 *
 * This extends the base storage class, adding required special handling for
 * News entities.
 *
 * @ingroup entity_news
 */
class EntityNewsStorage extends SqlContentEntityStorage implements EntityNewsStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(EntityNewsInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {entity_news_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {entity_news_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(EntityNewsInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {entity_news_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('entity_news_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
