<?php

namespace Drupal\site_grabber\parser;

use Drupal\site_grabber\parse_settings\ParseSettingsInterface;
use function array_shift;
use function is_array;
use function is_object;

class JSONParser extends BaseParser {

  public function parseContainer(ParseSettingsInterface $config, $data) {
    $json = \GuzzleHttp\json_decode($data);
    return self::jsonGet($json, $config->getContainerSelector());
  }

  private static function jsonGet($json, $path) {
    if (empty($path)) {
      return $json;
    }

    $segment = array_shift($path);

    if (is_array($json)) {
      $value = $json[$segment];
    }

    if (is_object($json)) {
      $value = $json->{$segment};
    }

    return self::jsonGet($value, $path);
  }

  public function parseItems(ParseSettingsInterface $config, $container) {
    return $container;
  }

  public function getErrorContainer($config, $data, $result) {
    // TODO: Implement getErrorContainer() method.
  }

  public function getErrorItems($config, $container, $result) {
    // TODO: Implement getErrorItems() method.
  }

  public function parseRowValue(ParseSettingsInterface $config, $row_data, $selector) {
    return [self::jsonGet($row_data, $selector)];
  }
}