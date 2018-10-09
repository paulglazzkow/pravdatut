<?php

namespace Drupal\site_grabber\Plugin\migrate_plus\data_fetcher;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\MigrateException;
use Drupal\migrate_plus\DataFetcherPluginBase;
use Drupal\site_grabber\parse_settings\ParseSettings;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use function explode;

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
 *   id = "http_fetcher",
 *   title = @Translation("HTTP Fetcher")
 * )
 */
class HttpFetcher extends DataFetcherPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The HTTP client.
   *
   * @var \GuzzleHttp\Client
   */
  protected $httpClient;

  /**
   * The request headers.
   *
   * @var array
   */
  protected $headers = [];

  /**
   * The data retrieval client.
   *
   * @var \Drupal\migrate_plus\AuthenticationPluginInterface
   */
  protected $authenticationPlugin;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->httpClient = \Drupal::httpClient();

    // Ensure there is a 'headers' key in the configuration.
    $configuration += ['headers' => []];
    $this->setRequestHeaders($configuration['headers']);
  }

  /**
   * Returns the initialized authentication plugin.
   *
   * @return \Drupal\migrate_plus\AuthenticationPluginInterface
   *   The authentication plugin.
   */
  public function getAuthenticationPlugin() {
    if (!isset($this->authenticationPlugin)) {
      $this->authenticationPlugin = \Drupal::service('plugin.manager.migrate_plus.authentication')
        ->createInstance($this->configuration['authentication']['plugin'], $this->configuration['authentication']);
    }
    return $this->authenticationPlugin;
  }

  /**
   * {@inheritdoc}
   */
  public function setRequestHeaders(array $headers) {
    $this->headers = $headers;
  }

  /**
   * {@inheritdoc}
   */
  public function getRequestHeaders() {
    return !empty($this->headers) ? $this->headers : [];
  }

  private function getResponseCharset(Response $response) {
    $content_type = $response->getHeader('Content-Type');
    $parts = explode(';', $content_type[0]);
    if (count($parts) > 1) {
      list(, $charset) = explode('=', $parts[1]);
      return $charset;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getResponse($config) {
    /* @var $config ParseSettings */
    $url = $config->getSourceUrl();
    try {
      $options = ['headers' => $this->getRequestHeaders()];
      if (!empty($this->configuration['authentication'])) {
        $options = array_merge($options, $this->getAuthenticationPlugin()->getAuthenticationOptions());
      }

      /* @var $this ->httpClient GuzzleHttp/Client */


      switch ($config->getSourceFormat()) {
        case 'html':
        case 'xml':

          break;
        case 'json':
          $options['Content-Type'] = 'application/json';
          break;
      }

      $response = $this->httpClient->get($url, $options);
      if (empty($response)) {
        throw new MigrateException('No response at ' . $url . '.');
      }
    } catch (RequestException $e) {
      throw new MigrateException('Error message: ' . $e->getMessage() . ' at ' . $url . '.');
    }
    return $response;
  }

  private function convertToUtf8($data, $charset) {
    return mb_convert_encoding($data, "utf-8", $charset);
  }

  private function getSourceCharset(ParseSettings $config, $response) {
    if ($charset = $this->getResponseCharset($response)) {
      return $charset;
    }
    else {
      return $config->getSourceCharset();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getResponseContent($config) {
    /* @var $config ParseSettings */
    $response = $this->getResponse($config);

    if ($charset = $this->getSourceCharset($config, $response)) {
      return $this->convertToUtf8($response->getBody(), $charset);
    }
    else {
      return $response->getBody();
    }
  }

}
