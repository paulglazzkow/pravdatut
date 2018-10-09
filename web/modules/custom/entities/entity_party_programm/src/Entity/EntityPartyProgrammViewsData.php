<?php

namespace Drupal\entity_party_programm\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Entity party programm entities.
 */
class EntityPartyProgrammViewsData extends EntityViewsData {

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
