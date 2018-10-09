<?php

namespace Drupal\import_info\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Import Content entities.
 *
 * @ingroup import_info
 */
interface ImportContentEntityInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Import Content name.
   *
   * @return string
   *   Name of the Import Content.
   */
  public function getName();

  /**
   * Sets the Import Content name.
   *
   * @param string $name
   *   The Import Content name.
   *
   * @return \Drupal\import_info\Entity\ImportContentEntityInterface
   *   The called Import Content entity.
   */
  public function setName($name);

  /**
   * Gets the Import Content creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Import Content.
   */
  public function getCreatedTime();

  /**
   * Sets the Import Content creation timestamp.
   *
   * @param int $timestamp
   *   The Import Content creation timestamp.
   *
   * @return \Drupal\import_info\Entity\ImportContentEntityInterface
   *   The called Import Content entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Import Content published status indicator.
   *
   * Unpublished Import Content are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Import Content is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Import Content.
   *
   * @param bool $published
   *   TRUE to set this Import Content to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\import_info\Entity\ImportContentEntityInterface
   *   The called Import Content entity.
   */
  public function setPublished($published);

  /**
   * Gets the Import Content revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Import Content revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\import_info\Entity\ImportContentEntityInterface
   *   The called Import Content entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Import Content revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Import Content revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\import_info\Entity\ImportContentEntityInterface
   *   The called Import Content entity.
   */
  public function setRevisionUserId($uid);

}
