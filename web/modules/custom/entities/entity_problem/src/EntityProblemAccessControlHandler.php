<?php

namespace Drupal\entity_problem;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Entity problem entity.
 *
 * @see \Drupal\entity_problem\Entity\EntityProblem.
 */
class EntityProblemAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\entity_problem\Entity\EntityProblemInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished entity problem entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published entity problem entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit entity problem entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete entity problem entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add entity problem entities');
  }

}
