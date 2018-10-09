<?php

namespace Drupal\import_info;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Import Content entity.
 *
 * @see \Drupal\import_info\Entity\ImportContentEntity.
 */
class ImportContentEntityAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\import_info\Entity\ImportContentEntityInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished import content entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published import content entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit import content entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete import content entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add import content entities');
  }

}
