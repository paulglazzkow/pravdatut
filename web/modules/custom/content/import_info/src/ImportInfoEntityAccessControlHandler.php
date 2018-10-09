<?php

namespace Drupal\import_info;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Import info entity entity.
 *
 * @see \Drupal\import_info\Entity\ImportInfoEntity.
 */
class ImportInfoEntityAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\import_info\Entity\ImportInfoEntityInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished import info entity entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published import info entity entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit import info entity entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete import info entity entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add import info entity entities');
  }

}
