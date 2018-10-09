<?php

namespace Drupal\pmtasks\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Project task type entity.
 *
 * @ConfigEntityType(
 *   id = "pmtask_type",
 *   label = @Translation("Project task type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\pmtasks\PMTaskTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\pmtasks\Form\PMTaskTypeForm",
 *       "edit" = "Drupal\pmtasks\Form\PMTaskTypeForm",
 *       "delete" = "Drupal\pmtasks\Form\PMTaskTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\pmtasks\PMTaskTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "pmtask_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "pmtask",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/pmtask_type/{pmtask_type}",
 *     "add-form" = "/admin/structure/pmtask_type/add",
 *     "edit-form" = "/admin/structure/pmtask_type/{pmtask_type}/edit",
 *     "delete-form" = "/admin/structure/pmtask_type/{pmtask_type}/delete",
 *     "collection" = "/admin/structure/pmtask_type"
 *   }
 * )
 */
class PMTaskType extends ConfigEntityBundleBase implements PMTaskTypeInterface {

  /**
   * The Project task type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Project task type label.
   *
   * @var string
   */
  protected $label;

}
