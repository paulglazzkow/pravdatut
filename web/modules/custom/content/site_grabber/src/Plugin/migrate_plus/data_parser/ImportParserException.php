<?php


namespace Drupal\site_grabber\Plugin\migrate_plus\data_parser;


class ImportParserException extends \Error {

  public function __construct(string $message = "", $code = 0, \Throwable $previous = NULL) {
    parent::__construct($message, $code, $previous);
  }
}