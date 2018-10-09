<?php

namespace Drupal\gpb_services;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Database\Driver\mysql\Connection;

/**
 * Class RouterFindService.
 */
class RouterFindService implements RouterFindServiceInterface {

  /**
   * Drupal\Core\Database\Driver\mysql\Connection definition.
   *
   * @var \Drupal\Core\Database\Driver\mysql\Connection
   */
  protected $database;

  /**
   * The cache backend.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cache;

  /**
   * The name of the SQL table from which to read the routes.
   *
   * @var string
   */
  protected $tableName;

  /**
   * Constructs a new RouterFindService object.
   */
  public function __construct(Connection $database, CacheBackendInterface $cache_backend, $table = 'router') {
    $this->database = $database;
    $this->cache = $cache_backend;
    $this->tableName = $table;
  }

  public function findRouterByPath($pattern, $limit = 10) {
    try {
      $query = $this->database->select($this->tableName, 'r')
        ->fields('r', ['path', 'name']);
      $query->condition('path', $this->database->escapeLike($pattern) . '%', 'LIKE');
      $query->range(0, $limit);
      $routes = $query->execute()->fetchAll(\PDO::FETCH_OBJ);
    } catch (\Exception $e) {
      $routes = [];
    }
    return $routes ? $routes : [];
  }

}
