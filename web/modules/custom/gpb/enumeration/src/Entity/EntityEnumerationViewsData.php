<?php

namespace Drupal\enumeration\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Entity enumeration entities.
 */
class EntityEnumerationViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.

    return $data;
  }

}
