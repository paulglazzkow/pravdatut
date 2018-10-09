<?php

namespace Drupal\entity_tree;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface EntityTreeStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Entity tree revision IDs for a specific Entity tree.
   *
   * @param \Drupal\entity_tree\Entity\EntityTreeInterface $entity
   *   The Entity tree entity.
   *
   * @return int[]
   *   Entity tree revision IDs (in ascending order).
   */
  public function revisionIds(EntityTreeInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Entity tree author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Entity tree revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\entity_tree\Entity\EntityTreeInterface $entity
   *   The Entity tree entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(EntityTreeInterface $entity);

  /**
   * Unsets the language for all Entity tree with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
