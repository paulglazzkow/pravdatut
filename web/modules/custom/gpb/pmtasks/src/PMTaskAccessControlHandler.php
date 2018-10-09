<?php

namespace Drupal\pmtasks;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Project task entity.
 *
 * @see \Drupal\pmtasks\Entity\PMTask.
 */
class PMTaskAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\pmtasks\Entity\PMTaskInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished project task entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published project task entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit project task entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete project task entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add project task entities');
  }

}
