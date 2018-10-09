<?php

namespace Drupal\account_menu;

use Drupal\Core\Access\AccessManagerInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Config\ConfigManagerInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Menu\DefaultMenuLinkTreeManipulators;
use Drupal\Core\Menu\MenuLinkInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Class MenuTreeManipulator.
 */
class MenuTreeManipulator extends DefaultMenuLinkTreeManipulators {

  /**
   * The configuration manager.
   *
   * @var \Drupal\Core\Config\ConfigManagerInterface
   */
  protected $configManager;

  /**
   * The entity repository.
   *
   * @var \Drupal\Core\Entity\EntityRepository
   */
  protected $entityRepository;

  /**
   * Constructs a \Drupal\Core\Menu\DefaultMenuLinkTreeManipulators object.
   *
   * @param \Drupal\Core\Access\AccessManagerInterface $access_manager
   *   The access manager.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The current user.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Config\ConfigManagerInterface $config_manager
   *   The configuration manager.
   * @param \Drupal\Core\Entity\EntityRepositoryInterface $entity_repository
   *   An implementation of the entity repository interface.
   */
  public function __construct(AccessManagerInterface $access_manager,
                              AccountInterface $account,
                              EntityTypeManagerInterface $entity_type_manager,
                              ConfigManagerInterface $config_manager,
                              EntityRepositoryInterface $entity_repository) {
    parent::__construct($access_manager, $account, $entity_type_manager);
    $this->configManager = $config_manager->getConfigFactory();
    $this->entityRepository = $entity_repository;
  }

  /**
   * Checks access for one menu link instance.
   *
   * This function adds to the checks provided by
   * DefaultMenuLinkTreeManipulators to allow us to check any roles which
   * have been added to a menu item to allow or deny access.
   *
   * @param \Drupal\Core\Menu\MenuLinkInterface $instance
   *   The menu link instance.
   *
   * @return \Drupal\Core\Access\AccessResult
   *   The access result.
   */
  protected function menuLinkCheckAccess(MenuLinkInterface $instance) {
    $access_result = parent::menuLinkCheckAccess($instance);

    $this->hideMenuLinkRegister($access_result, $instance);

    return $access_result->cachePerPermissions();
  }


  private function hideMenuLinkRegister(AccessResult &$access_result, MenuLinkInterface $instance) {
    if ($instance->getRouteName() === 'user.register') {
      if ($this->account->isAnonymous()) {
        $access_result = AccessResult::allowed();
      }
      else {
        $access_result = AccessResult::forbidden();
      }
    }
  }


}
