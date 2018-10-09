<?php

namespace Drupal\pmtasks\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class PMTaskTypeForm.
 */
class PMTaskTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $pmtask_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $pmtask_type->label(),
      '#description' => $this->t("Label for the Project task type."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $pmtask_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\pmtasks\Entity\PMTaskType::load',
      ],
      '#disabled' => !$pmtask_type->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $pmtask_type = $this->entity;
    $status = $pmtask_type->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Project task type.', [
          '%label' => $pmtask_type->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Project task type.', [
          '%label' => $pmtask_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($pmtask_type->toUrl('collection'));
  }

}
