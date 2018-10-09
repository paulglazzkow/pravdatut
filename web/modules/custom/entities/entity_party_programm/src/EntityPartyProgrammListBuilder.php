<?php

namespace Drupal\entity_party_programm;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Entity party programm entities.
 *
 * @ingroup entity_party_programm
 */
class EntityPartyProgrammListBuilder extends EntityListBuilder {


  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Entity party programm ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\entity_party_programm\Entity\EntityPartyProgramm */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.entity_party_programm.edit_form',
      ['entity_party_programm' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
