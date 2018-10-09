<?php

namespace Drupal\page_title\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\token\TreeBuilderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Route;
use function preg_match;
use function preg_split;

/**
 * Class TitleTokenConfigForm.
 */
class TitleTokenConfigForm extends EntityForm {

  /**
   * @var \Drupal\token\TreeBuilderInterface
   */
  protected $treeBuilder;

  public function __construct(TreeBuilderInterface $tree_builder) {
    $this->treeBuilder = $tree_builder;
  }

  private function getConfig($config_id) {
    /* @var $routes \Drupal\Core\Routing\RouteProvider */
    try {
      $result = \Drupal::entityTypeManager()
        ->getStorage('page_title.title_token_config')
        ->load($config_id);
    } catch (\Exception $e) {
      $result = NULL;
    }
    return $result;
  }

  private static function getRouteByName($route_name) {
    /* @var $routes \Drupal\Core\Routing\RouteProvider */
    $routes = \Drupal::service('router.route_provider');
    return $routes->getRouteByName($route_name);
  }

  private static function getTokenTypes(Route $route) {
    $params = $route->getOptions()['parameters'];
    $types = [];

    foreach ($params as $param) {

      $parts = preg_split('/\:/', $param['type']);

      if ($parts[0] === 'entity') {
        $types[] = $parts[1];
      }
    }
    return $types;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('token.tree_builder')
    );
  }


  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    /* @var $routes \Symfony\Cmf\Component\Routing\PagedRouteCollection */

    /* @var $config \Drupal\page_title\Entity\TitleTokenConfigInterface */
    $config = $this->entity;
    $token_types = [];

    if ($config->isNew()) {
      $path = '';
    }
    else {
      $route_name = $config->id();
      $route = $this->getRouteByName($route_name);
      $path = $route->getPath() . " ({$route_name})";
      $token_types = $this->getTokenTypes($route);
    }

    $form['path'] = [
      '#type' => 'textfield',
      '#title' => t('Path'),
      '#description' => t('Enter a comma-separated list of words to describe your view.'),
      '#default_value' => $path,
      '#autocomplete_route_name' => 'page_title.routes_autocomplete',
      '#autocomplete_route_parameters' => ['limit' => 10],
      '#ajax' => [
        'callback' => '\Drupal\page_title\Form\TitleTokenConfigForm::ajaxCallback',
        'event' => 'change',
        'wrapper' => 'edit-result',
        'progress' => [
          'type' => 'throbber',
          'message' => t('Verifying entry...'),
        ],
      ],
    ];

    $form['template'] = [
      '#type' => 'textfield',
      '#title' => t('Template'),
      '#description' => t('Enter a page title template.'),
      '#default_value' => $config->get('template'),
      '#required' => TRUE,
    ];

    $form['tokens'] = [
      '#type' => 'fieldset',
      '#title' => t('Tokens'),
      '#open' => TRUE,
      '#prefix' => '<div id="title-token-token-tree">',
      '#suffix' => '</div>',
    ];

    $form['tokens']['tree'] = $this->treeBuilder->buildRenderable($token_types);

    $form['result'] = [
      '#type' => 'item',
      '#title' => 'Result',
      '#plain_text' => print_r($token_types, TRUE),
      '#id' => 'edit-result',
      '#prefix' => '<div id="title-token-result">',
      '#suffix' => '</div>',
      '#attributes' => [
        'id' => ['edit-result'],
      ],
    ];;

    return $form;
  }

  public static function ajaxCallback(array &$form, FormStateInterface $form_state) {
    $renderer = \Drupal::service('renderer');
    $response = new AjaxResponse();

    $result = $form['result'];
    $tokens = $form['tokens'];

    $treeBuilder = \Drupal::service('token.tree_builder');

    $route_data = self::getAutocompleteValues($form_state->getValue('path'));
    $route = self::getRouteByName($route_data['name']);
    $token_types = self::getTokenTypes($route);

    $tokens['tree'] = $treeBuilder->buildRenderable($token_types);

    $token_tree['#token_types'] = $token_types;
    $response->addCommand(new ReplaceCommand('#title-token-result', $renderer->render($result)));
    $response->addCommand(new ReplaceCommand('#title-token-token-tree', $renderer->render($tokens)));
    return $response;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
    $route_data = $this->getAutocompleteValues($form_state->getValue('path'));
    if (!$route_data) {
      $form_state->setErrorByName('path', t('Path value is incorrect'));
      return;
    }

    try {
      $route = $this->getRouteByName($route_data['name']);
    } catch (\Exception $e) {
      $form_state->setErrorByName('path', t('Route for path %name not found', ['%name' => $route_data['name']]));
    }

    if ($this->entity->isNew() && $this->getConfig($route_data['name'])) {
      $form_state->setErrorByName('path', t('Config for route %path already exist', ['%path' => $route_data['path']]));
    }

    //    if (!$form_state->getErrors()) {
    $form_state->setValue('id', $route_data['name']);
    $form_state->setValue('label', $form_state->getValue('path'));

    //    }
  }

  private static function getAutocompleteValues($path) {
    $path_pattern = '^[\/a-z\.\{\}]+';
    $name_pattern = '[a-z\_\.]+';
    $pattern = "/({$path_pattern}).*\(({$name_pattern})/";
    preg_match($pattern, $path, $match);
    if (count($match) < 3) {
      return NULL;
    }
    $result = [];
    list(, $result['path'], $result['name']) = $match;
    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    /* @var $config \Drupal\page_title\Entity\TitleTokenConfigInterface */
    $config = $this->entity;

    $status = $config->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Title token config.', [
          '%label' => $config->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Title token config.', [
          '%label' => $config->label(),
        ]));
    }
    //    $form_state->setRedirectUrl($config->toUrl('collection'));
  }

}
