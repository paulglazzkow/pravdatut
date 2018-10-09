<?php

namespace Drupal\import_info;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface ImportContentEntityStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Import Content revision IDs for a specific Import Content.
   *
   * @param \Drupal\import_info\Entity\ImportContentEntityInterface $entity
   *   The Import Content entity.
   *
   * @return int[]
   *   Import Content revision IDs (in ascending order).
   */
  public function revisionIds(ImportContentEntityInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Import Content author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Import Content revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\import_info\Entity\ImportContentEntityInterface $entity
   *   The Import Content entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(ImportContentEntityInterface $entity);

  /**
   * Unsets the language for all Import Content with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
