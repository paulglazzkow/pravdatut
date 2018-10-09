<?php

namespace Drupal\import_info\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Import Content type entity.
 *
 * @ConfigEntityType(
 *   id = "import_content_entity_type",
 *   label = @Translation("Import Content type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\import_info\ImportContentEntityTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\import_info\Form\ImportContentEntityTypeForm",
 *       "edit" = "Drupal\import_info\Form\ImportContentEntityTypeForm",
 *       "delete" = "Drupal\import_info\Form\ImportContentEntityTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\import_info\ImportContentEntityTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "import_content_entity_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "import_content_entity",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/import/import-content/{import_content_entity_type}",
 *     "add-form" = "/admin/structure/import/import-content/add",
 *     "edit-form" = "/admin/structure/import/import-content/{import_content_entity_type}/edit",
 *     "delete-form" = "/admin/structure/import/import-content/{import_content_entity_type}/delete",
 *     "collection" = "/admin/structure/import/import-content"
 *   }
 * )
 */
class ImportContentEntityType extends ConfigEntityBundleBase implements ImportContentEntityTypeInterface {

  /**
   * The Import Content type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Import Content type label.
   *
   * @var string
   */
  protected $label;

}
