<?php

namespace Drupal\entity_tech;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Entity tech entity.
 *
 * @see \Drupal\entity_tech\Entity\EntityTech.
 */
class EntityTechAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\entity_tech\Entity\EntityTechInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished entity tech entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published entity tech entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit entity tech entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete entity tech entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add entity tech entities');
  }

}
