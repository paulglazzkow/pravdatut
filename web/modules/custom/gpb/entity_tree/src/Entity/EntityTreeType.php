<?php

namespace Drupal\entity_tree\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Entity tree type entity.
 *
 * @ConfigEntityType(
 *   id = "entity_tree_type",
 *   label = @Translation("Entity tree type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\entity_tree\EntityTreeTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\entity_tree\Form\EntityTreeTypeForm",
 *       "edit" = "Drupal\entity_tree\Form\EntityTreeTypeForm",
 *       "delete" = "Drupal\entity_tree\Form\EntityTreeTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\entity_tree\EntityTreeTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "entity_tree_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "entity_tree",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/entity_tree_type/{entity_tree_type}",
 *     "add-form" = "/admin/structure/entity_tree_type/add",
 *     "edit-form" = "/admin/structure/entity_tree_type/{entity_tree_type}/edit",
 *     "delete-form" = "/admin/structure/entity_tree_type/{entity_tree_type}/delete",
 *     "collection" = "/admin/structure/entity_tree_type"
 *   }
 * )
 */
class EntityTreeType extends ConfigEntityBundleBase implements EntityTreeTypeInterface {

  /**
   * The Entity tree type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Entity tree type label.
   *
   * @var string
   */
  protected $label;

}
