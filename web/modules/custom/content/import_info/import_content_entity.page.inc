<?php

/**
 * @file
 * Contains import_content_entity.page.inc.
 *
 * Page callback for Import Content entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Import Content templates.
 *
 * Default template: import_content_entity.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_import_content_entity(array &$variables) {
  // Fetch ImportContentEntity Entity Object.
  $import_content_entity = $variables['elements']['#import_content_entity'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
