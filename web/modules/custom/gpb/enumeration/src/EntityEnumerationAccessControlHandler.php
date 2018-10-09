<?php

namespace Drupal\enumeration;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Entity enumeration entity.
 *
 * @see \Drupal\enumeration\Entity\EntityEnumeration.
 */
class EntityEnumerationAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\enumeration\Entity\EntityEnumerationInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished entity enumeration entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published entity enumeration entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit entity enumeration entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete entity enumeration entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add entity enumeration entities');
  }

}
