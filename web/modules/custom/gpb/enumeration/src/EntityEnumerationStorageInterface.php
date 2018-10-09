<?php

namespace Drupal\enumeration;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\enumeration\Entity\EntityEnumerationInterface;

/**
 * Defines the storage handler class for Entity enumeration entities.
 *
 * This extends the base storage class, adding required special handling for
 * Entity enumeration entities.
 *
 * @ingroup enumeration
 */
interface EntityEnumerationStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Entity enumeration revision IDs for a specific Entity enumeration.
   *
   * @param \Drupal\enumeration\Entity\EntityEnumerationInterface $entity
   *   The Entity enumeration entity.
   *
   * @return int[]
   *   Entity enumeration revision IDs (in ascending order).
   */
  public function revisionIds(EntityEnumerationInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Entity enumeration author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Entity enumeration revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\enumeration\Entity\EntityEnumerationInterface $entity
   *   The Entity enumeration entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(EntityEnumerationInterface $entity);

  /**
   * Unsets the language for all Entity enumeration with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
