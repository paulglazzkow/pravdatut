<?php

namespace Drupal\entity_events\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Event type entity.
 *
 * @ConfigEntityType(
 *   id = "entity_event_type",
 *   label = @Translation("Event type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\entity_events\EntityEventTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\entity_events\Form\EntityEventTypeForm",
 *       "edit" = "Drupal\entity_events\Form\EntityEventTypeForm",
 *       "delete" = "Drupal\entity_events\Form\EntityEventTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\entity_events\EntityEventTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "entity_event_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "entity_event",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/entity_event_type/{entity_event_type}",
 *     "add-form" = "/admin/structure/entity_event_type/add",
 *     "edit-form" = "/admin/structure/entity_event_type/{entity_event_type}/edit",
 *     "delete-form" = "/admin/structure/entity_event_type/{entity_event_type}/delete",
 *     "collection" = "/admin/structure/entity_event_type"
 *   }
 * )
 */
class EntityEventType extends ConfigEntityBundleBase implements EntityEventTypeInterface {

  /**
   * The Event type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Event type label.
   *
   * @var string
   */
  protected $label;

}
