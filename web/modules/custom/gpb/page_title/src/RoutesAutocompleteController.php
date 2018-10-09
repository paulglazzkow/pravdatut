<?php


namespace Drupal\page_title;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use function ltrim;


class RoutesAutocompleteController extends ControllerBase {

  private function findRoutesByPath(Request $request, $limit) {
    /* @var $route_find \Drupal\gpb_services\RouterFindService */
    $route_find = \Drupal::service('gpb_service.router_find');

    if ($input = $request->query->get('q')) {

      $routes = $route_find->findRouterByPath($this->prepareAutocompleteInput($input), $limit);
      return $this->prepareAutocompleteResult($routes);
    }
    return [];

  }

  private function prepareAutocompleteInput($input) {
    return '/' . ltrim($input, "\/");
  }

  private function prepareAutocompleteResult(array $result) {
    return array_map(function ($item) {
      return [
        'value' => "{$item->path} ({$item->name})",
        'label' => $item->path,
      ];
    }, $result);
  }

  /**
   * Handler for autocomplete request.
   */
  public function handleAutocomplete(Request $request, $limit) {
    return new JsonResponse($this->findRoutesByPath($request, $limit));
  }

}