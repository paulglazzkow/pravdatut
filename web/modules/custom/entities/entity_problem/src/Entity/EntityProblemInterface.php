<?php

namespace Drupal\entity_problem\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Entity problem entities.
 *
 * @ingroup entity_problem
 */
interface EntityProblemInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Entity problem name.
   *
   * @return string
   *   Name of the Entity problem.
   */
  public function getName();

  /**
   * Sets the Entity problem name.
   *
   * @param string $name
   *   The Entity problem name.
   *
   * @return \Drupal\entity_problem\Entity\EntityProblemInterface
   *   The called Entity problem entity.
   */
  public function setName($name);

  /**
   * Gets the Entity problem creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Entity problem.
   */
  public function getCreatedTime();

  /**
   * Sets the Entity problem creation timestamp.
   *
   * @param int $timestamp
   *   The Entity problem creation timestamp.
   *
   * @return \Drupal\entity_problem\Entity\EntityProblemInterface
   *   The called Entity problem entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Entity problem published status indicator.
   *
   * Unpublished Entity problem are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Entity problem is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Entity problem.
   *
   * @param bool $published
   *   TRUE to set this Entity problem to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\entity_problem\Entity\EntityProblemInterface
   *   The called Entity problem entity.
   */
  public function setPublished($published);

  /**
   * Gets the Entity problem revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Entity problem revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\entity_problem\Entity\EntityProblemInterface
   *   The called Entity problem entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Entity problem revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Entity problem revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\entity_problem\Entity\EntityProblemInterface
   *   The called Entity problem entity.
   */
  public function setRevisionUserId($uid);

}
