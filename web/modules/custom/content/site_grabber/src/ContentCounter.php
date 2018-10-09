<?php


namespace Drupal\site_grabber;


class ContentCounter {

  public static function count($entity_type, $bundle, $condition = []) {
    $query = \Drupal::entityQuery($entity_type)
      ->condition('type', $bundle);
    return $query->count()->execute();
  }
}