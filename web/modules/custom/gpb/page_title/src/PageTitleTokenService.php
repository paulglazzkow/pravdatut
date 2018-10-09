<?php

namespace Drupal\page_title;

use Drupal\Core\Controller\ControllerResolverInterface;
use Drupal\Core\Controller\TitleResolver;
use Drupal\Core\StringTranslation\TranslationInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

/**
 * Class PageTitleTokenService.
 */
class PageTitleTokenService extends TitleResolver {

  /**
   * Constructs a TitleResolver instance.
   *
   * @param \Drupal\Core\Controller\ControllerResolverInterface $controller_resolver
   *   The controller resolver.
   * @param \Drupal\Core\StringTranslation\TranslationInterface $string_translation
   *   The translation manager.
   */
  public function __construct(ControllerResolverInterface $controller_resolver, TranslationInterface $string_translation) {
    parent::__construct($controller_resolver, $string_translation);

  }
  //page_title.title_token_config.entity.title_token_config.edit_form
  //page_title.title_token_config.entity.title_token_config.edit_form
  private function getConfigByRouteName($route_id) {
    /* @var $routes \Drupal\Core\Routing\RouteProvider */
    $config_id = "page_title.title_token_config.{$route_id}";
    try {
      $result = \Drupal::entityTypeManager()
        ->getStorage('title_token_config')
        ->load($route_id);
    } catch (\Exception $e) {
      dpm($e->getMessage());
      $result = NULL;
    }
    return $result;
  }

  private function getRouteNameByPath($path) {
    /* @var $routes \Drupal\Core\Routing\RouteProvider */
    $routes = \Drupal::service('router.route_provider');
    return key($routes->getRoutesByPattern($path)->all());
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle(Request $request, Route $route) {

    $route_name = $this->getRouteNameByPath($route->getPath());
    $title = parent::getTitle($request, $route);
    if ($config = $this->getConfigByRouteName($route_name)) {
      $title = \Drupal::token()->replace($config->get('template'));
    }

    return $title;
    //    return \Drupal::token()->replace($title);
  }

}
