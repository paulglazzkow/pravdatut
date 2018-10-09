<?php


namespace Drupal\site_grabber\Plugin\migrate\source;


use Drupal\site_grabber\exceptions\ImportDataException;
use Drupal\site_grabber\parse_settings\ParseSettingsFactory;
use Drupal\views\Views;

trait TraitConfigEntity {

  public function createSettings($entity, $url_config) {
    $settings = [];
    foreach (self::getUrls($entity, $url_config) as $source_link) {
      $config = ParseSettingsFactory::create($this->getType(), $entity, $source_link, $this->configuration);
      $settings[] = $config;
    }
    return $settings;

  }

  protected function getViewResult($viewName, $viewDisplay) {
    // Remove $name and $display_id from the arguments.
    $args = [];
    $view = Views::getView($viewName);
    if (is_object($view)) {
      if (is_array($args)) {
        $view->setArguments($args);
      }

      $view->setDisplay($viewDisplay);


      $view->preExecute();
      $view->execute();
      return $view->result;
    }
    else {
      return [];
    }
  }

  public function getImportConfigs($viewName, $viewDisplay, $filters) {

    $entities = [];
    foreach (self::getViewResult($viewName, $viewDisplay, $filters) as $row) {
      /* @var $row \Drupal\views\ResultRow */
      $entities[] = $row->_entity;
    }
    return $entities;
  }

  /**
   * @throws \Drupal\site_grabber\exceptions\ImportDataException
   */
  protected function initializeParserData($viewName, $viewDisplay, $filters) {

    $viewsResult = $this->getImportConfigs($viewName, $viewDisplay, $filters);
    $import_data = $this->createImportData($viewsResult);

    if (empty($import_data)) {
      throw new ImportDataException('Import data is empty');
    }
    $this->messageDisplay('Sources count: @count', ['@count' => count($import_data)]);

    $this->addImportData($import_data);
  }
}