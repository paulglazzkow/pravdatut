<?php

/**
 * @file
 * Contains pmtask.page.inc.
 *
 * Page callback for Project task entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Project task templates.
 *
 * Default template: pmtask.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_pmtask(array &$variables) {
  // Fetch PMTask Entity Object.
  $pmtask = $variables['elements']['#pmtask'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
