<?php

namespace Drupal\pravdatut_party\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Link;

/**
 * Provides a 'ContextualActionsBlock' block.
 *
 * @Block(
 *  id = "contextual_actions_block",
 *  admin_label = @Translation("Contextual actions block"),
 * )
 */
class ContextualActionsBlock extends BlockBase {

  var $config;

  private function getConfig() {
    return [
      'entity.pravdatut_party.canonical' => [
        'parameters' => ['group'],
        'items' => [
          [
            'route' => 'entity.pravdatut_party.canonical',
            'text' => t('Main'),
            'parameters' => ['group' => 'group'],
          ],
          [
            'route' => 'view.party_programm.page_1',
            'text' => t('Programms'),
            'parameters' => ['group' => 'arg_0'],
          ],
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
      $this->config = $this->getConfig();
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
      dpm($parameters);
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


    $build['contextual_menu_block'] = [
      '#theme' => 'item_list',
      '#list_type' => 'ul',
      '#title' => 'My List',
      '#items' => $this->createLinks($route_name),
      '#attributes' => ['class' => 'mylist'],
      '#wrapper_attributes' => ['class' => 'container'],
    ];

    return $build;
  }

}
