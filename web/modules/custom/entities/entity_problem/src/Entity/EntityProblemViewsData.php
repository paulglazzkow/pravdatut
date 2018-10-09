<?php

namespace Drupal\entity_problem\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Entity problem entities.
 */
class EntityProblemViewsData extends EntityViewsData {

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
