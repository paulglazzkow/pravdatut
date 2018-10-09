<?php

namespace Drupal\site_grabber\parse_settings;

use function preg_split;

class JSONSettings extends ParseSettings {


  protected function prepareSelector($selector) {
    return preg_split('/\//', $selector);
  }

  /**
   * @return mixed
   */
  public function getItemSelector() {
    if (isset($this->config['source']['item_selector'])) {
      return $this->prepareSelector($this->config['source']['item_selector']);
    }
    return [];
  }
}