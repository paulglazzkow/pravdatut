<?php

namespace Drupal\site_grabber\Plugin\Derivative;

use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MigrateEntityUpdate implements ContainerDeriverInterface {

  /**
   * List of derivative definitions.
   *
   * @var array
   */
  protected $derivatives = [];

  /**
   * The entity definitions
   *
   * @var \Drupal\Core\Entity\EntityTypeInterface[]
   */
  protected $entityDefinitions;

  /**
   * Constructs a MigrateEntity object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface[] $entity_definitions
   *   A list of entity definition objects.
   */
  public function __construct(array $entity_definitions) {
    $this->entityDefinitions = $entity_definitions;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $container->get('entity.manager')->getDefinitions()
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinition($derivative_id, $base_plugin_definition) {
    if (!empty($this->derivatives) && !empty($this->derivatives[$derivative_id])) {
      return $this->derivatives[$derivative_id];
    }
    $this->getDerivativeDefinitions($base_plugin_definition);
    return $this->derivatives[$derivative_id];
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    foreach ($this->entityDefinitions as $entity_type => $entity_info) {
      $class = 'Drupal\site_grabber\Plugin\migrate\destination\EntityUpdate';

      $this->derivatives[$entity_type] = [
        'id' => "entity_update:$entity_type",
        'class' => $class,
        'requirements_met' => 1,
        'provider' => $entity_info->getProvider(),
      ];
    }
    return $this->derivatives;
  }

}
