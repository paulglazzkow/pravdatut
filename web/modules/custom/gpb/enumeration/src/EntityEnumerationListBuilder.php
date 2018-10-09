<?php

namespace Drupal\enumeration;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Entity enumeration entities.
 *
 * @ingroup enumeration
 */
class EntityEnumerationListBuilder extends EntityListBuilder {


  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Entity enumeration ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\enumeration\Entity\EntityEnumeration */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.entity_enumeration.edit_form',
      ['entity_enumeration' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
