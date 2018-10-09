<?php

namespace Drupal\site_grabber\Plugin\migrate\source;

/**
 * Source plugin for retrieving data via URLs.
 *
 * @MigrateSource(
 *   id = "import_content_news"
 * )
 */
class ImportContentNews extends ImportContent {

  public function getStatusConfig() {
    return [
      'state' => 'link',
      'status' => 'start',
    ];
  }


}
