<?php

namespace Drupal\entity_news;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the News entity.
 *
 * @see \Drupal\entity_news\Entity\EntityNews.
 */
class EntityNewsAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\entity_news\Entity\EntityNewsInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished news entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published news entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit news entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete news entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add news entities');
  }

}