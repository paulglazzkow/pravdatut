<?php

namespace Drupal\route_subscribe\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class RouteSubscriber.
 *
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    $this->setTitle($collection);
  }

  protected function setTitle(RouteCollection $collection) {
    foreach ($this->getHandlersTitle() as $routeId => $handler) {
      if ($route = $collection->get($routeId)) {
        $route->setDefault('_title_callback', $handler);
      }
    }
  }

  protected function getHandlersTitle() {
    return [
      'entity.entity_order.add_form' => '\Drupal\entity_order\custom\TitleController::formAdd',
    ];
  }
}
