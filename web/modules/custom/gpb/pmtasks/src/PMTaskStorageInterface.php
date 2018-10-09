<?php

namespace Drupal\pmtasks;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface PMTaskStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Project task revision IDs for a specific Project task.
   *
   * @param \Drupal\pmtasks\Entity\PMTaskInterface $entity
   *   The Project task entity.
   *
   * @return int[]
   *   Project task revision IDs (in ascending order).
   */
  public function revisionIds(PMTaskInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Project task author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Project task revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\pmtasks\Entity\PMTaskInterface $entity
   *   The Project task entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(PMTaskInterface $entity);

  /**
   * Unsets the language for all Project task with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
