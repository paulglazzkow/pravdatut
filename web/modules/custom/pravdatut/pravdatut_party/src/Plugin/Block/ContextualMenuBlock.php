<?php

namespace Drupal\pravdatut_party\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Link;

/**
 * Provides a 'ContextualMenuBlock' block.
 *
 * @Block(
 *  id = "contextual_menu_block",
 *  admin_label = @Translation("Contextual menu block"),
 * )
 */
class ContextualMenuBlock extends BlockBase {

  var $config;

  private function getRoutes() {
    return [
      'entity.pravdatut_party.canonical' => [
        'parameters' => ['group'],
        'items' => [

        ],
      ],
      'view.party_programm.page_1' => [
        'parameters' => ['arg_0'],
        'items' => [
          [
            'route' => 'entity.pravdatut_party.canonical',
            'text' => t('Main'),
            'parameters' => ['arg_0' => 'group'],
          ],
          [
            'route' => 'view.party_programm.page_1',
            'text' => t('Programms'),
            'parameters' => ['arg_0' => 'arg_0'],
          ],
        ],
      ],
    ];
  }

  private function getRouteConfig($route_name) {
    if (!$this->config) {
      $this->config = $this->getRoutes();
    }
    return isset($this->config[$route_name]) ? $this->config[$route_name] : NULL;
  }

  private function createLinks($route_name) {
    $links = [];


    if ($config = $this->getRouteConfig($route_name)) {
      $route = \Drupal::routeMatch();

      $parameters = [];
      foreach ($config['parameters'] as $parameter) {
        if ($obj = $route->getParameter($parameter)) {
          $parameters[$parameter] = $obj->id();
        }
      }
//      dpm($parameters);
      foreach ($config['items'] as $item) {
        $params = [];
        foreach ($item['parameters'] as $source => $target) {
          $params[$target] = $parameters[$source];
        }
        $links[] = Link::createFromRoute($item['text'], $item['route'], $params);
      }
    }

    return $links;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['contextual_menu_block']['#markup'] = '';
    $route_name = \Drupal::routeMatch()->getRouteName();

    /* @var $tree \Drupal\Core\Menu\MenuTreeStorage */
    $tree = \Drupal::service('menu.tree_storage');

    $links = $tree->loadByProperties([]);
//    dpm($links);
    $build['contextual_menu_block'] = [
      '#theme' => 'item_list',
      '#list_type' => 'ul',
      '#title' => 'My List',
      //      '#items' => $this->createLinks($route_name),
      '#attributes' => ['class' => 'mylist'],
      '#wrapper_attributes' => ['class' => 'container'],
    ];

    return $build;
  }

}
