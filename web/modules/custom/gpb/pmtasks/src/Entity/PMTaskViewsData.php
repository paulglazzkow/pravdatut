<?php

namespace Drupal\pmtasks\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Project task entities.
 */
class PMTaskViewsData extends EntityViewsData {

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
