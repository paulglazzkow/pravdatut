<?php

namespace Drupal\pravdatut_party\Controller;

use Drupal\Core\Entity\EntityFormBuilderInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\group\Controller\GroupMembershipController;
use Drupal\group\Entity\GroupInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides group membership route controllers.
 *
 * This only controls the routes that are not supported out of the box by the
 * plugin base \Drupal\group\Plugin\GroupContentEnablerBase.
 */
class PartyGroupMembershipController extends GroupMembershipController {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The entity form builder.
   *
   * @var \Drupal\Core\Entity\EntityFormBuilderInterface
   */
  protected $entityFormBuilder;

  /**
   * Constructs a new GroupMembershipController.
   *
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   * @param \Drupal\Core\Entity\EntityFormBuilderInterface $entity_form_builder
   *   The entity form builder.
   */
  public function __construct(AccountInterface $current_user, EntityFormBuilderInterface $entity_form_builder) {
    parent::__construct($current_user, $entity_form_builder);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return parent::create($container);
  }

  /**
   * Provides the form for joining a group.
   *
   * @param \Drupal\group\Entity\GroupInterface $group
   *   The group to join.
   *
   * @return array
   *   A group join form.
   */
  public function join(GroupInterface $group) {
    return parent::join($group);
  }

  /**
   * The _title_callback for the join form route.
   *
   * @param \Drupal\group\Entity\GroupInterface $group
   *   The group to join.
   *
   * @return string
   *   The page title.
   */
  public function joinTitle(GroupInterface $group) {
    return $this->t('Join group %label', ['%label' => $group->label()]);
  }

  /**
   * Provides the form for leaving a group.
   *
   * @param \Drupal\group\Entity\GroupInterface $group
   *   The group to leave.
   *
   * @return array
   *   A group leave form.
   */
  public function leave(GroupInterface $group) {
    $group_content = $group->getMember($this->currentUser)->getGroupContent();
    return $this->entityFormBuilder->getForm($group_content, 'group-leave');
  }

}
