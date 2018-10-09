<?php

namespace Drupal\site_grabber\Plugin\migrate\source;


use Drupal\site_grabber\parse_settings\ParseSettingsFactory;

abstract class ImportLinks extends ImportFromSite {

  var $type = 'link';


  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'source_link' => [
        'type' => 'string',
        'alias' => 'su',
      ],
    ];
  }

  public function destinationFields() {
    $dataFields = [
      'name' => $this->t('Entity Title'),
      'config_id' => $this->t('Import config ID'),
      'source_link' => $this->t('Source link URL'),
      'source_title' => $this->t('Source link title'),
    ];
    return $dataFields;
  }

  public function getStatusConfig() {
    return [
      'state' => 'link',
      'status' => 'start',
    ];
  }

  public function getImportConfigs() {

    $queryViewsName = 'query_party_news_source_links';
    $queryViewsDisplay = 'default';

    $entities = [];
    foreach (views_get_view_result($queryViewsName, $queryViewsDisplay) as $row) {
      /* @var $row \Drupal\views\ResultRow */
      $entities[] = $row->_entity;
    }
    return $entities;
  }
}
