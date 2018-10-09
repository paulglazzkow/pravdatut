<?php


namespace Drupal\pravdatut_party\Plugin\Block;


class LinksParty {

  public static function routes() {
    return [
      'entity.pravdatut_party.canonical' => [
        'text' => t('Main'),
        'parameters' => ['group' => 'group'],
      ],
      'view.party_programm.page_1' => [
        'text' => t('Programms'),
        'parameters' => ['arg_0' => 'group'],
      ],
    ];
  }

  public static function getRoutes($name) {
    $routes = self::routes();
    return $routes[$name];
  }

}