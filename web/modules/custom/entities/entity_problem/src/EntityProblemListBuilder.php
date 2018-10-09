<?php

namespace Drupal\entity_problem;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Entity problem entities.
 *
 * @ingroup entity_problem
 */
class EntityProblemListBuilder extends EntityListBuilder {


  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Entity problem ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\entity_problem\Entity\EntityProblem */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.entity_problem.edit_form',
      ['entity_problem' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
