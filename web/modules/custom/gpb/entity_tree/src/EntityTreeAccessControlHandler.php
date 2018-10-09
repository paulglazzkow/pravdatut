<?php

namespace Drupal\entity_tree;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Entity tree entity.
 *
 * @see \Drupal\entity_tree\Entity\EntityTree.
 */
class EntityTreeAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\entity_tree\Entity\EntityTreeInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished entity tree entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published entity tree entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit entity tree entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete entity tree entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add entity tree entities');
  }

}
