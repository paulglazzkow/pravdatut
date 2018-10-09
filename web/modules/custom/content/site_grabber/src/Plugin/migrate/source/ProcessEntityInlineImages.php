<?php

namespace Drupal\site_grabber\Plugin\migrate\source;


/**
 * Source plugin for retrieving data via Views.
 *
 * @MigrateSource(
 *   id = "process_image_inline_news"
 * )
 */
class ProcessEntityInlineImages extends ProcessEntity {

  var $type = 'file';


  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'file_source' => [
        'type' => 'string',
        'alias' => 'fu',
      ],
    ];
  }

  public function destinationFields() {
    $dataFields = [
      'file_destination' => $this->t('Import config ID'),
    ];
    return $dataFields;
  }

  public function getStatusNextConfig() {
    return [
      'state' => 'image_inline',
      'status' => 'start',
    ];
  }

  public function getStatusPrevConfig() {
    return [
      'state' => 'link',
      'status' => 'start',
    ];
  }

  /**
   * Allows class to decide how it will react when it is treated like a string.
   */
  public function __toString() {
    // TODO: Implement __toString() method.
    return '';
  }

  protected function getViewsName() {
    return 'import_process_news';
  }

  protected function getViewsDisplay() {
    return 'default';
  }


  public function getType() {
    return 'process_import_image';
  }
}
