<?php

namespace Drupal\entity_news\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining News entities.
 *
 * @ingroup entity_news
 */
interface EntityNewsInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the News name.
   *
   * @return string
   *   Name of the News.
   */
  public function getName();

  /**
   * Sets the News name.
   *
   * @param string $name
   *   The News name.
   *
   * @return \Drupal\entity_news\Entity\EntityNewsInterface
   *   The called News entity.
   */
  public function setName($name);

  /**
   * Gets the News creation timestamp.
   *
   * @return int
   *   Creation timestamp of the News.
   */
  public function getCreatedTime();

  /**
   * Sets the News creation timestamp.
   *
   * @param int $timestamp
   *   The News creation timestamp.
   *
   * @return \Drupal\entity_news\Entity\EntityNewsInterface
   *   The called News entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the News published status indicator.
   *
   * Unpublished News are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the News is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a News.
   *
   * @param bool $published
   *   TRUE to set this News to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\entity_news\Entity\EntityNewsInterface
   *   The called News entity.
   */
  public function setPublished($published);

  /**
   * Gets the News revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the News revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\entity_news\Entity\EntityNewsInterface
   *   The called News entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the News revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the News revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\entity_news\Entity\EntityNewsInterface
   *   The called News entity.
   */
  public function setRevisionUserId($uid);

}
