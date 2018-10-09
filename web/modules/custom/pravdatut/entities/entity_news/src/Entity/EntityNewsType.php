<?php

namespace Drupal\entity_news\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the News type entity.
 *
 * @ConfigEntityType(
 *   id = "entity_news_type",
 *   label = @Translation("News type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\entity_news\EntityNewsTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\entity_news\Form\EntityNewsTypeForm",
 *       "edit" = "Drupal\entity_news\Form\EntityNewsTypeForm",
 *       "delete" = "Drupal\entity_news\Form\EntityNewsTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\entity_news\EntityNewsTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "entity_news_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "entity_news",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/content-types/entity_news_type/{entity_news_type}",
 *     "add-form" = "/admin/structure/content-types/entity_news_type/add",
 *     "edit-form" = "/admin/structure/content-types/entity_news_type/{entity_news_type}/edit",
 *     "delete-form" = "/admin/structure/content-types/entity_news_type/{entity_news_type}/delete",
 *     "collection" = "/admin/structure/content-types/entity_news_type"
 *   }
 * )
 */
class EntityNewsType extends ConfigEntityBundleBase implements EntityNewsTypeInterface {

  /**
   * The News type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The News type label.
   *
   * @var string
   */
  protected $label;

}
