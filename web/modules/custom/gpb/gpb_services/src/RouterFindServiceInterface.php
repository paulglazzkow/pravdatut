<?php

namespace Drupal\gpb_services;

/**
 * Interface RouterFindServiceInterface.
 */
interface RouterFindServiceInterface {

  public function findRouterByPath($pattern, $limit = 10);
}
