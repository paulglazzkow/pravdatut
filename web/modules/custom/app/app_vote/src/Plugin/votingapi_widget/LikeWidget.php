<?php

namespace Drupal\app_vote\Plugin\votingapi_widget;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\votingapi_widgets\Plugin\VotingApiWidgetBase;

/**
 * Assigns ownership of a node to a user.
 *
 * @VotingApiWidget(
 *   id = "like",
 *   label = @Translation("like"),
 *   values = {
 *    1 = @Translation("Like"),
 *    0 = @Translation("Cancel"),
 *    -1 = @Translation("Dislike"),
 *   },
 * )
 */
class LikeWidget extends VotingApiWidgetBase {

  use StringTranslationTrait;

  private static function hasValue($element) {
    $value = isset($element['#default_value'][0]) ? $element['#default_value'][0] : NULL;
    return $value != 0 && isset($element['#options'][$value]);
  }

  public static function prepareElement($element, $title, $settings, $submit) {

    $ajax = $submit['#ajax'];

    $ajax['event'] = 'change';
    $ajax['progress']['type'] = 'trobber';
    //  $icon = '<i class="fa fa-' . $settings['style'] . '"></i>';
    if (FALSE === self::hasValue($element)) {
      unset($element['#options'][0]);

    }
    else {
      $excluded = $element['#default_value'] * -1;
      unset($element['#options'][$excluded]);
    }

    return [
      '#type' => 'radios',
      '#title' => $title,
      '#attributes' => $element['#attributes'],
      '#default_value' => $element['#default_value'],
      '#options' => $element['#options'],
      '#ajax' => $ajax,
      //    '#prefix' => $icon,
      '#attached' => [
        'library' => ['app_vote/flag_widget'],
      ],
    ];
  }

  /**
   * Vote form.
   */
  public function buildForm($entity_type, $entity_bundle, $entity_id, $vote_type, $field_name, $settings) {
    $form = $this->getForm($entity_type, $entity_bundle, $entity_id, $vote_type, $field_name, $settings);


    $build = [
      'rating' => [
        '#theme' => 'container',
        '#attributes' => [
          'class' => [
            'votingapi-widgets',
            'like',
            ($settings['readonly'] === 1) ? 'read_only' : '',
          ],
        ],
        '#children' => [
          'form' => $form,
        ],
      ],
      '#attached' => [
        //        'library' => ['votingapi_widgets/useful'],
      ],
    ];
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function getInitialVotingElement(array &$form) {
    $form['value']['#prefix'] = '<div class="votingapi-widgets like-widget">';
    $form['value']['#attached'] = [
      'library' => ['app_vote/like-widget'],
    ];
    $form['value']['#suffix'] = '</div>';
  }

  /**
   * {@inheritdoc}
   */
  public function getStyles() {
    return [
      'like' => $this->t('Like'),
    ];
  }

}
