<?php

namespace Drupal\entity_tree\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class EntityTreeTypeForm.
 */
class EntityTreeTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $entity_tree_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $entity_tree_type->label(),
      '#description' => $this->t("Label for the Entity tree type."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $entity_tree_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\entity_tree\Entity\EntityTreeType::load',
      ],
      '#disabled' => !$entity_tree_type->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity_tree_type = $this->entity;
    $status = $entity_tree_type->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Entity tree type.', [
          '%label' => $entity_tree_type->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Entity tree type.', [
          '%label' => $entity_tree_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($entity_tree_type->toUrl('collection'));
  }

}
