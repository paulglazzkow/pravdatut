<?php

namespace Drupal\site_grabber\parse_settings;

interface ParseSettingsInterface {

  public function getDestination();

  /**
   * @return mixed
   */
  public function getConfigId();

  /**
   * @return mixed
   */
  public function getSourceUrl();

  /**
   * @return mixed
   */
  public function getContainerSelector();


  /**
   * @return mixed
   */
  public function getItemSelector();


  /**
   * @param mixed $item_selector
   */
  public function setData($name, $value);

  /**
   * @return mixed
   */
  public function getData($name);

  /**
   * @return mixed
   */
  public function getSourceFormat();
}