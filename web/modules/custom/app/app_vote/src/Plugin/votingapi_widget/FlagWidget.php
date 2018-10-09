<?php

namespace Drupal\app_vote\Plugin\votingapi_widget;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\votingapi_widgets\Plugin\VotingApiWidgetBase;

/**
 * Assigns ownership of a node to a user.
 *
 * @VotingApiWidget(
 *   id = "flag",
 *   label = @Translation("Flag"),
 *   values = {
 *    1 = @Translation("Check"),
 *    0 = @Translation("Uncheck"),
 *   },
 * )
 */
class FlagWidget extends VotingApiWidgetBase {

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
    if ($settings['style'] === 'ban' && self::hasValue($element)) {
      $element['#attributes']['disabled'] = 'disabled';
    }

    return [
      '#type' => 'checkbox',
      '#title' => $title,
      '#attributes' => $element['#attributes'],
      '#default_value' => $element['#default_value'],
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
            'flag',
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
    $form['value']['#prefix'] = '<div class="votingapi-widgets flag-widget">';
    $form['value']['#attached'] = [
      'library' => ['app_vote/flag-widget'],
    ];
    $form['value']['#suffix'] = '</div>';
  }

  /**
   * {@inheritdoc}
   */
  public function getStyles() {
    return [
      'ban' => $this->t('Abuse'),
      'thumbs-up' => $this->t('Like'),
      'thumbs-down' => $this->t('Dislike'),
    ];
  }

}
