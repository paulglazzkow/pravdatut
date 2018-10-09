<?php

namespace Drupal\entity_problem;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\entity_problem\Entity\EntityProblemInterface;

/**
 * Defines the storage handler class for Entity problem entities.
 *
 * This extends the base storage class, adding required special handling for
 * Entity problem entities.
 *
 * @ingroup entity_problem
 */
interface EntityProblemStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Entity problem revision IDs for a specific Entity problem.
   *
   * @param \Drupal\entity_problem\Entity\EntityProblemInterface $entity
   *   The Entity problem entity.
   *
   * @return int[]
   *   Entity problem revision IDs (in ascending order).
   */
  public function revisionIds(EntityProblemInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Entity problem author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Entity problem revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\entity_problem\Entity\EntityProblemInterface $entity
   *   The Entity problem entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(EntityProblemInterface $entity);

  /**
   * Unsets the language for all Entity problem with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
