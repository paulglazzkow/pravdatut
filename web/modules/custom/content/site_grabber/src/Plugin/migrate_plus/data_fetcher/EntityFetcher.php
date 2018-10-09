<?php

namespace Drupal\site_grabber\Plugin\migrate_plus\data_fetcher;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate_plus\DataFetcherPluginBase;
use Drupal\site_grabber\parse_settings\ParseSettings;

//Drupal\migrate_plus\Plugin\migrate_plus\data_fetcher\HttpFetcher
//Drupal\site_grabber\Plugin\migrate_plus\data_fetcher\HttpFetcher" does not exist.

/**
 * Retrieve data over an HTTP connection for migration.
 *
 * Example:
 *
 * @code
 * source:
 *   plugin: url
 *   data_fetcher_plugin: http
 *   headers:
 *     Accept: application/json
 *     User-Agent: Internet Explorer 6
 *     Authorization-Key: secret
 *     Arbitrary-Header: foobarbaz
 * @endcode
 *
 * @DataFetcher(
 *   id = "entity_fetcher",
 *   title = @Translation("Entity local Fetcher")
 * )
 */
class EntityFetcher extends DataFetcherPluginBase implements ContainerFactoryPluginInterface {


  private $views;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->views = $configuration['views_config'];

  }

  public function getName() {
    return $this->views->name;
  }

  public function getDisplay() {
    return $this->views->display;
  }

  public function getFilters() {
    return isset($this->views->filters) ? $this->views->filters : [];
  }


  /**
   * {@inheritdoc}
   */
  public function getResponse($config) {
    $entity=$config->getData()
    return [$config];
  }


  /**
   * {@inheritdoc}
   */
  public function getResponseContent($config) {
    /* @var $config ParseSettings */

    return $this->getResponse($config);

  }

  /**
   * Set the client headers.
   *
   * @param array $headers
   *   An array of the headers to set on the HTTP request.
   */
  public function setRequestHeaders(array $headers) {
    return [];
  }

  /**
   * Get the currently set request headers.
   */
  public function getRequestHeaders() {
    return [];
  }
}
