<?php

namespace Drupal\entity_party_programm;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Entity party programm entity.
 *
 * @see \Drupal\entity_party_programm\Entity\EntityPartyProgramm.
 */
class EntityPartyProgrammAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\entity_party_programm\Entity\EntityPartyProgrammInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished entity party programm entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published entity party programm entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit entity party programm entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete entity party programm entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add entity party programm entities');
  }

}
