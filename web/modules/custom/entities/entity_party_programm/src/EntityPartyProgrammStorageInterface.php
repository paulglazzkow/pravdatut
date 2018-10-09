<?php

namespace Drupal\entity_party_programm;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\entity_party_programm\Entity\EntityPartyProgrammInterface;

/**
 * Defines the storage handler class for Entity party programm entities.
 *
 * This extends the base storage class, adding required special handling for
 * Entity party programm entities.
 *
 * @ingroup entity_party_programm
 */
interface EntityPartyProgrammStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Entity party programm revision IDs for a specific Entity party programm.
   *
   * @param \Drupal\entity_party_programm\Entity\EntityPartyProgrammInterface $entity
   *   The Entity party programm entity.
   *
   * @return int[]
   *   Entity party programm revision IDs (in ascending order).
   */
  public function revisionIds(EntityPartyProgrammInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Entity party programm author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Entity party programm revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\entity_party_programm\Entity\EntityPartyProgrammInterface $entity
   *   The Entity party programm entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(EntityPartyProgrammInterface $entity);

  /**
   * Unsets the language for all Entity party programm with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
