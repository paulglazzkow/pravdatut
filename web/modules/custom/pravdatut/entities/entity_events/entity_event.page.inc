<?php

/**
 * @file
 * Contains entity_event.page.inc.
 *
 * Page callback for Event entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Event templates.
 *
 * Default template: entity_event.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_entity_event(array &$variables) {
  // Fetch EntityEvent Entity Object.
  $entity_event = $variables['elements']['#entity_event'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
