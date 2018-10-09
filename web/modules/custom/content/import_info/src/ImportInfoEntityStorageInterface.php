<?php

namespace Drupal\import_info;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface ImportInfoEntityStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Import info entity revision IDs for a specific Import info entity.
   *
   * @param \Drupal\import_info\Entity\ImportInfoEntityInterface $entity
   *   The Import info entity entity.
   *
   * @return int[]
   *   Import info entity revision IDs (in ascending order).
   */
  public function revisionIds(ImportInfoEntityInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Import info entity author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Import info entity revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\import_info\Entity\ImportInfoEntityInterface $entity
   *   The Import info entity entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(ImportInfoEntityInterface $entity);

  /**
   * Unsets the language for all Import info entity with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
