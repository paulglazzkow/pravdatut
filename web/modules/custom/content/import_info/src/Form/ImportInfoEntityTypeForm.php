<?php

namespace Drupal\import_info\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ImportInfoEntityTypeForm.
 */
class ImportInfoEntityTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $import_info_entity_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $import_info_entity_type->label(),
      '#description' => $this->t("Label for the Import info entity type."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $import_info_entity_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\import_info\Entity\ImportInfoEntityType::load',
      ],
      '#disabled' => !$import_info_entity_type->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $import_info_entity_type = $this->entity;
    $status = $import_info_entity_type->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Import info entity type.', [
          '%label' => $import_info_entity_type->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Import info entity type.', [
          '%label' => $import_info_entity_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($import_info_entity_type->toUrl('collection'));
  }

}
