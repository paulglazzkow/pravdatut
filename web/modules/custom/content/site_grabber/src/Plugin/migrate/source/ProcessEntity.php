<?php

namespace Drupal\site_grabber\Plugin\migrate\source;


use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\migrate\MigrateMessageInterface;
use Drupal\migrate_plus\Plugin\migrate\source\SourcePluginExtension;
use Drupal\site_grabber\exceptions\ImportDataException;
use Drupal\site_grabber\parse_settings\ParseSettings;
use Drupal\site_grabber\parse_settings\ParseSettingsFactory;
use Drupal\views\Views;

abstract class ProcessEntity extends SourcePluginExtension {

  use TraitConfigEntity;

  var $type = 'file';

  /** @var MigrateMessageInterface $message */
  protected $message;

  protected $viewsName;

  protected $viewsDisplay;

  abstract protected function getViewsName();

  abstract protected function getViewsDisplay();

  /**
   * {@inheritdoc}
   */

  public abstract function destinationFields();

  public abstract function getStatusNextConfig();

  public abstract function getStatusPrevConfig();

  public static function getUrlConfig() {
    return ['field' => 'field_source_url', 'column' => 'uri'];
  }

  protected static function getUrls(RevisionableContentEntityBase $entity, $url_config) {


    $field = $entity->get($url_config['field']);

    $values = [];
    foreach ($field as $item) {
      $value = $item->getValue();
      if (empty($value[$url_config['column']])) {
        continue;
      }
      $values[] = $value[$url_config['column']];
    }
    return $values;
  }

  public function createSettings($entity) {
    $settings = [];
    foreach (self::getUrls($entity, self::getUrlConfig()) as $source_link) {
      $config = ParseSettingsFactory::create($this->getType(), $entity, $source_link, $this->configuration);
      $settings[] = $config;
    }
    return $settings;

  }

  abstract public function getType() ;

  public function addImportData($import_data) {
    if (!isset($this->configuration['urls'])) {
      $this->configuration['urls'] = [];
    }
    foreach ($import_data as $data) {
      $this->configuration['urls'][] = $data;
    }
  }

  protected function initializeIterator() {
    try {
      $this->initializeParserData($this->getViewsName(), $this->getViewsDisplay(), $this->getStatusPrevConfig());
    } catch (ImportDataException $e) {
    }

    return $this->getDataParserPlugin();
  }

  protected function getViewResult($viewName, $viewDisplay, $filters) {
    // Remove $name and $display_id from the arguments.
    $args = [];
    $view = Views::getView($viewName);
    if (is_object($view)) {
      if (is_array($args)) {
        $view->setArguments($args);
      }

      $view->setDisplay($viewDisplay);

      $views_filters = $view->display_handler->getOption('filters');
      foreach ($filters as $name => $value) {
        $filter_name = "field_{$name}_value";
        $views_filters[$filter_name]['value'] = [$value => $value];
      }
      $view->display_handler->setOption('filters', $views_filters);
      $view->preExecute();
      $view->execute();
      return $view->result;
    }
    else {
      return [];
    }
  }

  public function setMessage($message) {
    $this->message = $message;
  }

  protected function messageDisplay($message, $context = [], $type = 'status') {
    $this->message->display($this->t($message, $context), $type);
  }

  /**
   * Returns the initialized data parser plugin.
   *
   * @return \Drupal\migrate_plus\DataParserPluginInterface
   *   The data parser plugin.
   */
  public function getDataParserPlugin() {
    if (!isset($this->dataParserPlugin)) {
      $this->dataParserPlugin = \Drupal::service('plugin.manager.migrate_plus.data_parser')
        ->createInstance($this->configuration['data_parser_plugin'], $this->configuration);
    }
    return $this->dataParserPlugin;
  }


  public function createImportData($settings) {
    $import_data = [];

    foreach ($settings as $entity) {
      foreach ($this->createSettings($entity, self::getUrlConfig()) as $entity_data) {
        /** @var ParseSettings $entity_data */
        foreach ($entity_data->getProcess() as $property => $process) {
          $this->migration->setProcessOfProperty($property, $process);
        }

        foreach ($this->getStatusProcess() as $property => $process) {
          $this->migration->setProcessOfProperty($property, $process);
        }

        $import_data[] = $entity_data;
      }
    }
    return $import_data;
  }

  protected function getStatusProcess() {
    $process = [];
    $config = $this->getStatusNextConfig();
    foreach ($this->statusFields() as $name => $info) {
      $field_name = $info['field'];
      $process[$field_name] = [
        'plugin' => 'default_value',
        'default_value' => $config[$name],
      ];
    }
    return $process;
  }

  public function statusFields() {
    return [
      'state' => ['field' => 'field_state', 'title' => t('State field')],
      'status' => ['field' => 'field_status', 'title' => t('State status field')],
    ];
  }

}
