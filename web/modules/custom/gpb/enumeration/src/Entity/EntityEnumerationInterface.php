<?php

namespace Drupal\enumeration\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Entity enumeration entities.
 *
 * @ingroup enumeration
 */
interface EntityEnumerationInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Entity enumeration name.
   *
   * @return string
   *   Name of the Entity enumeration.
   */
  public function getName();

  /**
   * Sets the Entity enumeration name.
   *
   * @param string $name
   *   The Entity enumeration name.
   *
   * @return \Drupal\enumeration\Entity\EntityEnumerationInterface
   *   The called Entity enumeration entity.
   */
  public function setName($name);

  /**
   * Gets the Entity enumeration creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Entity enumeration.
   */
  public function getCreatedTime();

  /**
   * Sets the Entity enumeration creation timestamp.
   *
   * @param int $timestamp
   *   The Entity enumeration creation timestamp.
   *
   * @return \Drupal\enumeration\Entity\EntityEnumerationInterface
   *   The called Entity enumeration entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Entity enumeration published status indicator.
   *
   * Unpublished Entity enumeration are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Entity enumeration is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Entity enumeration.
   *
   * @param bool $published
   *   TRUE to set this Entity enumeration to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\enumeration\Entity\EntityEnumerationInterface
   *   The called Entity enumeration entity.
   */
  public function setPublished($published);

  /**
   * Gets the Entity enumeration revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Entity enumeration revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\enumeration\Entity\EntityEnumerationInterface
   *   The called Entity enumeration entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Entity enumeration revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Entity enumeration revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\enumeration\Entity\EntityEnumerationInterface
   *   The called Entity enumeration entity.
   */
  public function setRevisionUserId($uid);

}
