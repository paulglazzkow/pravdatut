<?php


namespace Drupal\site_grabber\parser;


class ParserContainerException extends \Exception {


  var $result;

  public function __construct($message, $result = NULL) {
    parent::__construct($message);
    $this->result = $result;
  }


  /**
   * @return mixed
   */
  public function getResult() {
    return $this->result;
  }
}