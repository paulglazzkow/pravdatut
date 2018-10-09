<?php

namespace Drupal\pravdatut_party\Controller;

use Drupal\group\Entity\Controller\GroupContentController;
use Drupal\group\Entity\GroupContentType;
use Drupal\group\Entity\GroupInterface;

class PartyGroupContentController extends GroupContentController {

  const CONTENT_ENTITY_TYPE = 'group_node:party_programm';

  /**
   * Provides the group content submission form.
   *
   * @param \Drupal\group\Entity\GroupInterface $group
   *   The group to add the group content to.
   * @param string $plugin_id
   *   The group content enabler to add content with.
   *
   * @return array
   *   A group submission form.
   */
  public function addForm(GroupInterface $group, $plugin_id = self::CONTENT_ENTITY_TYPE) {
    return parent::addForm($group, $plugin_id);
  }

  /**
   * The _title_callback for the entity.group_content.add_form route.
   *
   * @param \Drupal\group\Entity\GroupInterface $group
   *   The group to add the group content to.
   * @param string $plugin_id
   *   The group content enabler to add content with.
   *
   * @return string
   *   The page title.
   */
  public function addFormTitle(GroupInterface $group, $plugin_id = self::CONTENT_ENTITY_TYPE) {
    /** @var \Drupal\group\Plugin\GroupContentEnablerInterface $plugin */
    $plugin = $group->getGroupType()->getContentPlugin($plugin_id);
    $group_content_type = GroupContentType::load($plugin->getContentTypeConfigId());
    return $this->t('Create @name', ['@name' => $group_content_type->label()]);
  }

}