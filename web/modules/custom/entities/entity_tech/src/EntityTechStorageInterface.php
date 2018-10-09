<?php

namespace Drupal\entity_tech;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\entity_tech\Entity\EntityTechInterface;

/**
 * Defines the storage handler class for Entity tech entities.
 *
 * This extends the base storage class, adding required special handling for
 * Entity tech entities.
 *
 * @ingroup entity_tech
 */
interface EntityTechStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Entity tech revision IDs for a specific Entity tech.
   *
   * @param \Drupal\entity_tech\Entity\EntityTechInterface $entity
   *   The Entity tech entity.
   *
   * @return int[]
   *   Entity tech revision IDs (in ascending order).
   */
  public function revisionIds(EntityTechInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Entity tech author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Entity tech revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\entity_tech\Entity\EntityTechInterface $entity
   *   The Entity tech entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(EntityTechInterface $entity);

  /**
   * Unsets the language for all Entity tech with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
