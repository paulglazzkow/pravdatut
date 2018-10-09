<?php

namespace Drupal\entity_party_programm\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Entity party programm entities.
 *
 * @ingroup entity_party_programm
 */
interface EntityPartyProgrammInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Entity party programm name.
   *
   * @return string
   *   Name of the Entity party programm.
   */
  public function getName();

  /**
   * Sets the Entity party programm name.
   *
   * @param string $name
   *   The Entity party programm name.
   *
   * @return \Drupal\entity_party_programm\Entity\EntityPartyProgrammInterface
   *   The called Entity party programm entity.
   */
  public function setName($name);

  /**
   * Gets the Entity party programm creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Entity party programm.
   */
  public function getCreatedTime();

  /**
   * Sets the Entity party programm creation timestamp.
   *
   * @param int $timestamp
   *   The Entity party programm creation timestamp.
   *
   * @return \Drupal\entity_party_programm\Entity\EntityPartyProgrammInterface
   *   The called Entity party programm entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Entity party programm published status indicator.
   *
   * Unpublished Entity party programm are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Entity party programm is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Entity party programm.
   *
   * @param bool $published
   *   TRUE to set this Entity party programm to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\entity_party_programm\Entity\EntityPartyProgrammInterface
   *   The called Entity party programm entity.
   */
  public function setPublished($published);

  /**
   * Gets the Entity party programm revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Entity party programm revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\entity_party_programm\Entity\EntityPartyProgrammInterface
   *   The called Entity party programm entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Entity party programm revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Entity party programm revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\entity_party_programm\Entity\EntityPartyProgrammInterface
   *   The called Entity party programm entity.
   */
  public function setRevisionUserId($uid);

}
