<?php

namespace Drupal\entity_events;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\entity_events\Entity\EntityEventInterface;

/**
 * Defines the storage handler class for Event entities.
 *
 * This extends the base storage class, adding required special handling for
 * Event entities.
 *
 * @ingroup entity_events
 */
interface EntityEventStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Event revision IDs for a specific Event.
   *
   * @param \Drupal\entity_events\Entity\EntityEventInterface $entity
   *   The Event entity.
   *
   * @return int[]
   *   Event revision IDs (in ascending order).
   */
  public function revisionIds(EntityEventInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Event author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Event revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\entity_events\Entity\EntityEventInterface $entity
   *   The Event entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(EntityEventInterface $entity);

  /**
   * Unsets the language for all Event with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
