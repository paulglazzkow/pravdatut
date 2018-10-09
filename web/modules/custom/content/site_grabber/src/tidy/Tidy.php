<?php

namespace Drupal\site_grabber\tidy;

class Tidy {

  var $options;

  function setOption($name, $value) {
    $this->options[$name] = $value;
  }

}