<?php

namespace Drupal\entity_party_programm\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Entity party programm type entity.
 *
 * @ConfigEntityType(
 *   id = "entity_party_programm_type",
 *   label = @Translation("Entity party programm type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\entity_party_programm\EntityPartyProgrammTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\entity_party_programm\Form\EntityPartyProgrammTypeForm",
 *       "edit" = "Drupal\entity_party_programm\Form\EntityPartyProgrammTypeForm",
 *       "delete" = "Drupal\entity_party_programm\Form\EntityPartyProgrammTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\entity_party_programm\EntityPartyProgrammTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "entity_party_programm_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "entity_party_programm",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/entity_party_programm_type/{entity_party_programm_type}",
 *     "add-form" = "/admin/structure/entity_party_programm_type/add",
 *     "edit-form" = "/admin/structure/entity_party_programm_type/{entity_party_programm_type}/edit",
 *     "delete-form" = "/admin/structure/entity_party_programm_type/{entity_party_programm_type}/delete",
 *     "collection" = "/admin/structure/entity_party_programm_type"
 *   }
 * )
 */
class EntityPartyProgrammType extends ConfigEntityBundleBase implements EntityPartyProgrammTypeInterface {

  /**
   * The Entity party programm type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Entity party programm type label.
   *
   * @var string
   */
  protected $label;

}
