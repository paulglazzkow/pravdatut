<?php

namespace Drupal\social_auth_ext\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class SocialAuthListUserController.
 */
class SocialAuthListUserController extends ControllerBase {

  /**
   * List.
   *
   * @return string
   *   Return Hello string.
   */
  public function list() {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: list')
    ];
  }

}
