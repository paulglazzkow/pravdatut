<?php


namespace Drupal\pravdatut_party\Plugin\Block;


class ConfigParty {

  public static function pages() {
    return [
      'entity.pravdatut_party.canonical' => [
        'entity.pravdatut_party.canonical' => '',
        'view.party_programm.page_1',
      ],
      'view.party_programm.page_1' => [
        'entity.pravdatut_party.canonical',
        'view.party_programm.page_1',

      ],
    ];
  }



  public static function getPages($name) {
    $config = self::pages();
  }
}