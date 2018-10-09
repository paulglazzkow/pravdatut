<?php

namespace Drupal\entity_tree\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Entity tree entities.
 *
 * @ingroup entity_tree
 */
interface EntityTreeInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Entity tree name.
   *
   * @return string
   *   Name of the Entity tree.
   */
  public function getName();

  /**
   * Sets the Entity tree name.
   *
   * @param string $name
   *   The Entity tree name.
   *
   * @return \Drupal\entity_tree\Entity\EntityTreeInterface
   *   The called Entity tree entity.
   */
  public function setName($name);

  /**
   * Gets the Entity tree creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Entity tree.
   */
  public function getCreatedTime();

  /**
   * Sets the Entity tree creation timestamp.
   *
   * @param int $timestamp
   *   The Entity tree creation timestamp.
   *
   * @return \Drupal\entity_tree\Entity\EntityTreeInterface
   *   The called Entity tree entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Entity tree published status indicator.
   *
   * Unpublished Entity tree are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Entity tree is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Entity tree.
   *
   * @param bool $published
   *   TRUE to set this Entity tree to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\entity_tree\Entity\EntityTreeInterface
   *   The called Entity tree entity.
   */
  public function setPublished($published);

  /**
   * Gets the Entity tree revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Entity tree revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\entity_tree\Entity\EntityTreeInterface
   *   The called Entity tree entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Entity tree revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Entity tree revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\entity_tree\Entity\EntityTreeInterface
   *   The called Entity tree entity.
   */
  public function setRevisionUserId($uid);

}
