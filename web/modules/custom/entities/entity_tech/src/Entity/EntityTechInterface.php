<?php

namespace Drupal\entity_tech\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Entity tech entities.
 *
 * @ingroup entity_tech
 */
interface EntityTechInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Entity tech name.
   *
   * @return string
   *   Name of the Entity tech.
   */
  public function getName();

  /**
   * Sets the Entity tech name.
   *
   * @param string $name
   *   The Entity tech name.
   *
   * @return \Drupal\entity_tech\Entity\EntityTechInterface
   *   The called Entity tech entity.
   */
  public function setName($name);

  /**
   * Gets the Entity tech creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Entity tech.
   */
  public function getCreatedTime();

  /**
   * Sets the Entity tech creation timestamp.
   *
   * @param int $timestamp
   *   The Entity tech creation timestamp.
   *
   * @return \Drupal\entity_tech\Entity\EntityTechInterface
   *   The called Entity tech entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Entity tech published status indicator.
   *
   * Unpublished Entity tech are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Entity tech is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Entity tech.
   *
   * @param bool $published
   *   TRUE to set this Entity tech to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\entity_tech\Entity\EntityTechInterface
   *   The called Entity tech entity.
   */
  public function setPublished($published);

  /**
   * Gets the Entity tech revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Entity tech revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\entity_tech\Entity\EntityTechInterface
   *   The called Entity tech entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Entity tech revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Entity tech revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\entity_tech\Entity\EntityTechInterface
   *   The called Entity tech entity.
   */
  public function setRevisionUserId($uid);

}
