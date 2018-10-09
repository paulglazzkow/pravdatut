<?php

namespace Drupal\entity_party_programm\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class EntityPartyProgrammTypeForm.
 */
class EntityPartyProgrammTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $entity_party_programm_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $entity_party_programm_type->label(),
      '#description' => $this->t("Label for the Entity party programm type."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $entity_party_programm_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\entity_party_programm\Entity\EntityPartyProgrammType::load',
      ],
      '#disabled' => !$entity_party_programm_type->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity_party_programm_type = $this->entity;
    $status = $entity_party_programm_type->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Entity party programm type.', [
          '%label' => $entity_party_programm_type->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Entity party programm type.', [
          '%label' => $entity_party_programm_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($entity_party_programm_type->toUrl('collection'));
  }

}
