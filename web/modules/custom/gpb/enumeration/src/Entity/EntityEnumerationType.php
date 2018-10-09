<?php

namespace Drupal\enumeration\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Entity enumeration type entity.
 *
 * @ConfigEntityType(
 *   id = "entity_enumeration_type",
 *   label = @Translation("Entity enumeration type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\enumeration\EntityEnumerationTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\enumeration\Form\EntityEnumerationTypeForm",
 *       "edit" = "Drupal\enumeration\Form\EntityEnumerationTypeForm",
 *       "delete" = "Drupal\enumeration\Form\EntityEnumerationTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\enumeration\EntityEnumerationTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "entity_enumeration_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "entity_enumeration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/entity_enumeration_type/{entity_enumeration_type}",
 *     "add-form" = "/admin/structure/entity_enumeration_type/add",
 *     "edit-form" = "/admin/structure/entity_enumeration_type/{entity_enumeration_type}/edit",
 *     "delete-form" = "/admin/structure/entity_enumeration_type/{entity_enumeration_type}/delete",
 *     "collection" = "/admin/structure/entity_enumeration_type"
 *   }
 * )
 */
class EntityEnumerationType extends ConfigEntityBundleBase implements EntityEnumerationTypeInterface {

  /**
   * The Entity enumeration type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Entity enumeration type label.
   *
   * @var string
   */
  protected $label;

}
