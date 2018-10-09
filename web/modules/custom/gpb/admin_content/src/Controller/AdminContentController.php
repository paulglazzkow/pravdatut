<?php

namespace Drupal\admin_content\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class AdminContentController.
 */
class AdminContentController extends ControllerBase {

  /**
   * Content-types.
   *
   * @return array
   *   Return Hello string.
   */
  public function TypesPage() {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: content-types')
    ];
  }
//TypesContentPage
    /**
     * Content-types.
     *
     * @return array
     *   Return Hello string.
     */
    public function TypesContentPage() {
        return [
            '#type' => 'markup',
            '#markup' => $this->t('Implement method: content-types')
        ];
    }
}
