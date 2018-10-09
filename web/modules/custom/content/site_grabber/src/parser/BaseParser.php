<?php

namespace Drupal\site_grabber\parser;

use Drupal\site_grabber\parse_settings\ParseSettingsInterface;
use function join;


class ParserItemsException extends \Exception {

}

abstract class BaseParser {


  abstract public function parseContainer(ParseSettingsInterface $config, $data);

  abstract public function parseItems(ParseSettingsInterface $config, $container);

  abstract public function parseRowValue(ParseSettingsInterface $config, $row_data, $selector);

  abstract public function getErrorContainer($config, $data, $result);

  abstract public function getErrorItems($config, $container, $result);

  public function onContentLoad($content) {
    return $content;
  }

  public function parse(ParseSettingsInterface $config, $data) {
    $container = $this->parseContainer($config, $data);
    if ($errors = $this->getErrorContainer($config, $data, $container)) {
      throw new  ParserContainerException(join("\n", $errors));
    }

    $result = $this->parseItems($config, $container);

    if ($errors = $this->getErrorItems($config, $container, $result)) {
      throw new  ParserContainerException(join("\n", $errors));
    }

    return $result;
  }


  private function _parseContainer(ParseSettingsInterface $config, $data) {
    $container = $this->parseContainer($config, $data);
    if ($errors = $this->getErrorContainer($config, $data, $container)) {
      throw new  ParserContainerException(join("\n", $errors));
    }

    return $container;
  }

  private function _parseItems(ParseSettingsInterface $config, $container) {

    $result = $this->parseItems($config, $container);

    if ($errors = $this->getErrorItems($config, $container, $result)) {
      throw new  ParserItemsException(join("\n", $errors));
    }

    return $result;
  }

  public function parseLink(ParseSettingsInterface $config, $data) {
    try {
      return $this->_parseItems($config, $this->_parseContainer($config, $data));
    } catch (ParserContainerException$e) {
    } catch (ParserItemsException$e) {
    }
  }

  public function parseContent(ParseSettingsInterface $config, $data) {
    try {
      return $this->_parseContainer($config, $data);
    } catch (ParserContainerException $e) {
    }
  }

}