<?php

namespace Drupal\enumeration\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class EntityEnumerationTypeForm.
 */
class EntityEnumerationTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $entity_enumeration_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $entity_enumeration_type->label(),
      '#description' => $this->t("Label for the Entity enumeration type."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $entity_enumeration_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\enumeration\Entity\EntityEnumerationType::load',
      ],
      '#disabled' => !$entity_enumeration_type->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity_enumeration_type = $this->entity;
    $status = $entity_enumeration_type->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Entity enumeration type.', [
          '%label' => $entity_enumeration_type->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Entity enumeration type.', [
          '%label' => $entity_enumeration_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($entity_enumeration_type->toUrl('collection'));
  }

}
