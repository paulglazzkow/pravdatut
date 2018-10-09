<?php

namespace Drupal\site_grabber\Plugin\migrate\source;


use Drupal\site_grabber\parse_settings\ParseSettingsFactory;

abstract class ImportContent extends ImportFromSite {

  var $type = 'content';

  //  /**
  //   * {@inheritdoc}
  //   */
  //  public function fields() {
  //    $fields = [
  //      'source_url' => $this->t('Source link URL'),
  //      'name' => $this->t('Title'),
  //      'content' => $this->t('Content'),
  //    ];
  //
  //    return $fields;
  //  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'entity_id' => [
        'type' => 'integer',
        'alias' => 'eid',
      ],
    ];
  }

  public function destinationFields() {
    $dataFields = [
      'name' => $this->t('Entity Title'),
      'field_body' => $this->t('Content processed '),
      'field_body_raw' => $this->t('Content raw'),
    ];
    return $dataFields;
  }

  public function createSettings($entity, $test_config = NULL) {
    $settings = [];
    $config = ParseSettingsFactory::create($this->getType(), $entity, NULL, $this->configuration);

    $settings[] = $config;

    return $settings;

  }

  public function getImportConfigs() {

    $queryViewsName = 'query_party_news_content_links';
    $queryViewsDisplay = 'default';

    $entities = [];
    foreach (views_get_view_result($queryViewsName, $queryViewsDisplay) as $row) {
      /* @var $row \Drupal\views\ResultRow */
      $entities[] = $row->_relationship_entities['reverse__import_content_entity__field_import_config'];
    }
    return $entities;
  }

}
