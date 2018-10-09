<?php

namespace Drupal\entity_tech\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Entity tech type entity.
 *
 * @ConfigEntityType(
 *   id = "entity_tech_type",
 *   label = @Translation("Entity tech type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\entity_tech\EntityTechTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\entity_tech\Form\EntityTechTypeForm",
 *       "edit" = "Drupal\entity_tech\Form\EntityTechTypeForm",
 *       "delete" = "Drupal\entity_tech\Form\EntityTechTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\entity_tech\EntityTechTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "entity_tech_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "entity_tech",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/tech-type/{entity_tech_type}",
 *     "add-form" = "/admin/structure/tech-type/add",
 *     "edit-form" = "/admin/structure/tech-type/{entity_tech_type}/edit",
 *     "auto-label" = "/admin/structure/tech-type/{entity_tech_type}/edit/auto-label",
 *     "delete-form" = "/admin/structure/tech-type/{entity_tech_type}/delete",
 *     "collection" = "/admin/structure/tech-type"
 *   }
 * )
 */
class EntityTechType extends ConfigEntityBundleBase implements EntityTechTypeInterface {

  /**
   * The Entity tech type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Entity tech type label.
   *
   * @var string
   */
  protected $label;

}
