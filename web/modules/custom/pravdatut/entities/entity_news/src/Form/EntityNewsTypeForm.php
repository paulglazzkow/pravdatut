<?php

namespace Drupal\entity_news\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class EntityNewsTypeForm.
 */
class EntityNewsTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $entity_news_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $entity_news_type->label(),
      '#description' => $this->t("Label for the News type."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $entity_news_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\entity_news\Entity\EntityNewsType::load',
      ],
      '#disabled' => !$entity_news_type->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity_news_type = $this->entity;
    $status = $entity_news_type->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label News type.', [
          '%label' => $entity_news_type->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label News type.', [
          '%label' => $entity_news_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($entity_news_type->toUrl('collection'));
  }

}
