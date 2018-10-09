<?php

namespace Drupal\site_grabber\parser;

use Drupal\migrate_plus\Plugin\migrate_plus\data_parser\XmlTrait;
use Drupal\site_grabber\parse_settings\ParseSettingsInterface;
use function array_map;
use function is_array;
use function is_scalar;
use function strtolower;

class HTMLParser extends BaseParser {

  use XmlTrait;

  public function onContentLoad($content) {
    return $this->tidyResponse($content);
  }

  private function tidyResponse($content) {
    $options = [
      'indent' => FALSE,
      'output-xml' => TRUE,
      'clean' => TRUE,
      'drop-proprietary-attributes' => TRUE,
      'drop-font-tags' => TRUE,
      'drop-empty-paras' => TRUE,
      'hide-comments' => TRUE,
      'join-classes' => FALSE,
      'join-styles' => FALSE,
      'show-body-only' => TRUE,
      //
      'quote-nbsp' => FALSE,

    ];

    $handler = new \tidy();
    $handler->parseString($content, $options);
    $handler->cleanRepair();

    return (string) $handler;
  }

  public function parseContainer(ParseSettingsInterface $config, $data) {
    libxml_clear_errors();
    if ($xml = simplexml_load_string($data)) {
      $xpath = $config->getContainerSelector();
      $result = $xml->xpath($xpath);
      return $result;
    }
  }

  public function parseItems(ParseSettingsInterface $config, $data) {
    libxml_clear_errors();
    $items = [];
    $selector = $config->getItemSelector();
    foreach ($data as $container) {
      /* @var $container  \SimpleXMLElement */
      if ($result = $container->xpath($selector)) {
        $items += $result;
      }
    }
    return $items;
  }


  protected function checkErrors() {
    return array_map(function ($error) {
      return self::parseLibXmlError($error);
    }, libxml_get_errors());
  }

  public function getErrorContainer($config, $data, $result) {
    return $this->checkErrors();
  }

  public function getErrorItems($config, $container, $result) {
    return $this->checkErrors();
  }

  public function parseRowValue(ParseSettingsInterface $config, $row_data, $selector) {
    /* @var $row_data \SimpleXMLElement */

    list($xpath, $type) = preg_split('/\>\>/', $selector . '>>');

    $type = strtolower(trim($type));

    if (empty($type)) {
      $type = 'text';
    }


    $data = $row_data->xpath($xpath);
    $values = [];
    if (is_array($data)) {
      foreach ($data as $value) {
        switch ($type) {
          case 'text':
            $ret = (string) $value;

            break;
          case 'html':
            $ret = $value->asXML();
            break;
        }
        if (is_scalar($ret) && !empty($ret)) {
          $values[] = $ret;
        }
        else {
          $n = 0;
        }
      }
    }
    return $values;
  }
}