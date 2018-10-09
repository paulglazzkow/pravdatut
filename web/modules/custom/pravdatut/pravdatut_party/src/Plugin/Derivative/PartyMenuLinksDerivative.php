<?php

namespace Drupal\pravdatut_party\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PartyMenuLinksDerivative extends DeriverBase implements ContainerDeriverInterface {

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static();
  }

  private function pages() {
    return [
      'party' => ['text' => '', 'route' => ''],
'programm'=> ['text' => '', 'route' => ''],
    ];
  }

  private function config() {
    return [
      'party' => [
        'route' => '',
        'children' => [
          'programm' => ['route' => '', 'text' => t('Programm')],
        ],
      ],
    ];
  }


  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $links = [];
    // Get all nodes of type page.
    $nodeQuery = \Drupal::entityQuery('node');
    $nodeQuery->condition('type', 'page');
    $nodeQuery->condition('status', TRUE);
    $ids = $nodeQuery->execute();
    $ids = array_values($ids);
    $nodes = Node::loadMultiple($ids);
    foreach ($nodes as $node) {
      $links['mymodule_menulink_' . $node->id()] = [
          'title' => $node->get('title')->getString(),
          'menu_name' => 'main',
          'route_name' => 'entity.node.canonical',
          'route_parameters' => [
            'node' => $node->id(),
          ],
        ] + $base_plugin_definition;
    }
    return $links;
  }
}