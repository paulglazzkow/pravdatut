<?php

namespace Drupal\site_grabber\parse_settings;

use Drupal\Component\Utility\NestedArray;

abstract class ParseSettings implements ParseSettingsInterface {

  var $data;

  var $fields;


  var $config = [];


  public function __construct($config) {

    $this->config = $config;
    $this->data = [];
  }

  protected function prepareSelector($selector) {
    return $selector;
  }

  function getFieldSelectors() {
    $fields = [];
    foreach ($this->config['source']['fields_selectors'] as $source_field_name => $selector) {
      $fields[$source_field_name] = $this->prepareSelector($selector);
    }
    return $fields;
  }


  /**
   * @return mixed
   */
  public function getConfigId() {
    return $this->config['entity']->id();
  }

  /**
   * @return mixed
   */
  public function getProcess() {
    return $this->config['process'];
  }

  /**
   * @return mixed
   */
  public function getSourceUrl() {
    return $this->config['source_url'];
  }


  /**
   * @return mixed
   */
  public function getDestination() {
    return NestedArray::getValue($this->config, ['source', 'destination']);
  }


  /**
   * @return mixed
   */
  public function getContainerSelector() {
    return $this->prepareSelector($this->config['source']['container_selector']);
  }


  /**
   * @return mixed
   */
  public function getItemSelector() {
    return $this->prepareSelector($this->config['source']['item_selector']);
  }

  /**
   * @param mixed $item_selector
   */
  public function setData($name, $value) {
    NestedArray::setValue($this->data, preg_split('/\./', $name), $value);
    return $this;
  }

  /**
   * @return mixed
   */
  public function getData($name) {
    return NestedArray::getValue($this->data, preg_split('/\./', $name));
  }

  public function getDataAll() {
    return $this->data + $this->config;
  }


  /**
   * @return mixed
   */
  public function getSourceCharset() {
    if (isset($this->config['source_charset'])) {
      return $this->config['source_charset'];
    }
  }


  /**
   * @return mixed
   */
  public function getSourceFormat() {
    return $this->config['source_format'];
  }


}