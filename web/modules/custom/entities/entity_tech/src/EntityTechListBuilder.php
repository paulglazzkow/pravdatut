<?php

namespace Drupal\entity_tech;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Entity tech entities.
 *
 * @ingroup entity_tech
 */
class EntityTechListBuilder extends EntityListBuilder {


  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Entity tech ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\entity_tech\Entity\EntityTech */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.entity_tech.edit_form',
      ['entity_tech' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
