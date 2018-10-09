<?php

namespace Drupal\entity_problem\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Entity problem type entity.
 *
 * @ConfigEntityType(
 *   id = "entity_problem_type",
 *   label = @Translation("Entity problem type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\entity_problem\EntityProblemTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\entity_problem\Form\EntityProblemTypeForm",
 *       "edit" = "Drupal\entity_problem\Form\EntityProblemTypeForm",
 *       "delete" = "Drupal\entity_problem\Form\EntityProblemTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\entity_problem\EntityProblemTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "entity_problem_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "entity_problem",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/entity_problem_type/{entity_problem_type}",
 *     "add-form" = "/admin/structure/entity_problem_type/add",
 *     "edit-form" = "/admin/structure/entity_problem_type/{entity_problem_type}/edit",
 *     "delete-form" = "/admin/structure/entity_problem_type/{entity_problem_type}/delete",
 *     "collection" = "/admin/structure/entity_problem_type"
 *   }
 * )
 */
class EntityProblemType extends ConfigEntityBundleBase implements EntityProblemTypeInterface {

  /**
   * The Entity problem type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Entity problem type label.
   *
   * @var string
   */
  protected $label;

}
