<?php

namespace Drupal\pravdatut_party\Plugin\Menu;

use Drupal\Core\Menu\MenuLinkDefault;
use Drupal\Core\Url;

class PartyMenuLink extends MenuLinkDefault {

  /**
   * {@inheritdoc}
   */
  public function getOptions() {
    $options = parent::getOptions();
    // Append the current path as destination to the query string.
    $options['query']['destination'] = Url::fromRoute('<current>')->toString();
    return $options;
  }


}