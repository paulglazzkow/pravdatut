<?php

namespace Drupal\import_info\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Import info entity type entity.
 *
 * @ConfigEntityType(
 *   id = "import_info_entity_type",
 *   label = @Translation("Import info entity type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\import_info\ImportInfoEntityTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\import_info\Form\ImportInfoEntityTypeForm",
 *       "edit" = "Drupal\import_info\Form\ImportInfoEntityTypeForm",
 *       "delete" = "Drupal\import_info\Form\ImportInfoEntityTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\import_info\ImportInfoEntityTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "import_info_entity_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "import_info_entity",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/import/info/{import_info_entity_type}",
 *     "add-form" = "/admin/structure/import/info/add",
 *     "edit-form" = "/admin/structure/import/info/{import_info_entity_type}/edit",
 *     "auto-label" = "/admin/structure/import/info/{import_info_entity_type}/edit/auto-label",
 *     "delete-form" = "/admin/structure/import/info/{import_info_entity_type}/delete",
 *     "collection" = "/admin/structure/import/info"
 *   }
 * )
 */
class ImportInfoEntityType extends ConfigEntityBundleBase implements ImportInfoEntityTypeInterface {

  /**
   * The Import info entity type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Import info entity type label.
   *
   * @var string
   */
  protected $label;

}
