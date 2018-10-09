<?php

namespace Drupal\page_title\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Title token config entity.
 *
 * @ConfigEntityType(
 *   id = "title_token_config",
 *   label = @Translation("Title token config"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\page_title\TitleTokenConfigListBuilder",
 *     "form" = {
 *       "add" = "Drupal\page_title\Form\TitleTokenConfigForm",
 *       "edit" = "Drupal\page_title\Form\TitleTokenConfigForm",
 *       "delete" = "Drupal\page_title\Form\TitleTokenConfigDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\page_title\TitleTokenConfigHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "title_token_config",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/title_token_config/{title_token_config}",
 *     "add-form" = "/admin/structure/title_token_config/add",
 *     "edit-form" = "/admin/structure/title_token_config/{title_token_config}/edit",
 *     "delete-form" = "/admin/structure/title_token_config/{title_token_config}/delete",
 *     "collection" = "/admin/structure/title_token_config"
 *   }
 * )
 */
class TitleTokenConfig extends ConfigEntityBase implements TitleTokenConfigInterface {

  /**
   * The Title token config ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Title token config label.
   *
   * @var string
   */
  protected $label;

}
