<?php

namespace Drupal\site_grabber;

use function array_merge_recursive;
use function is_array;

trait LogTrait {

  private $_log;

  private function resetLog() {
    $this->_log = [];
  }

  private function addLog($log) {

    if (empty($log)) {
      return;
    }

    if (FALSE === is_array($log)) {
      $log = [$log];
    }
    $this->_log = array_merge_recursive($this->_log, $log);
  }

  private function setLog($msg, $op = 'common', $status = 'debug') {
    if (!isset($this->$_log[$op])) {
      $this->_log[$op] = [];
    }
    $this->_log[$op][] = ['message' => $msg, 'status' => $status];
  }

  public function getLog() {
    return $this->_log;
  }

}