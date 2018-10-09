<?php

/**
 * @file
 * Contains import_info_entity.page.inc.
 *
 * Page callback for Import info entity entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Import info entity templates.
 *
 * Default template: import_info_entity.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_import_info_entity(array &$variables) {
  // Fetch ImportInfoEntity Entity Object.
  $import_info_entity = $variables['elements']['#import_info_entity'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
