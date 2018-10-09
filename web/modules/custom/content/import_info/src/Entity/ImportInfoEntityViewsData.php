<?php

namespace Drupal\import_info\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Import info entity entities.
 */
class ImportInfoEntityViewsData extends EntityViewsData {

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
