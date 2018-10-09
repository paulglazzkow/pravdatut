<?php

namespace Drupal\import_info\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Import info entity entities.
 *
 * @ingroup import_info
 */
interface ImportInfoEntityInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Import info entity name.
   *
   * @return string
   *   Name of the Import info entity.
   */
  public function getName();

  /**
   * Sets the Import info entity name.
   *
   * @param string $name
   *   The Import info entity name.
   *
   * @return \Drupal\import_info\Entity\ImportInfoEntityInterface
   *   The called Import info entity entity.
   */
  public function setName($name);

  /**
   * Gets the Import info entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Import info entity.
   */
  public function getCreatedTime();

  /**
   * Sets the Import info entity creation timestamp.
   *
   * @param int $timestamp
   *   The Import info entity creation timestamp.
   *
   * @return \Drupal\import_info\Entity\ImportInfoEntityInterface
   *   The called Import info entity entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Import info entity published status indicator.
   *
   * Unpublished Import info entity are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Import info entity is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Import info entity.
   *
   * @param bool $published
   *   TRUE to set this Import info entity to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\import_info\Entity\ImportInfoEntityInterface
   *   The called Import info entity entity.
   */
  public function setPublished($published);

  /**
   * Gets the Import info entity revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Import info entity revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\import_info\Entity\ImportInfoEntityInterface
   *   The called Import info entity entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Import info entity revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Import info entity revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\import_info\Entity\ImportInfoEntityInterface
   *   The called Import info entity entity.
   */
  public function setRevisionUserId($uid);

}
