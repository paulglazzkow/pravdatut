<?php

/**
 * @file
 * Contains entity_enumeration.page.inc.
 *
 * Page callback for Entity enumeration entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Entity enumeration templates.
 *
 * Default template: entity_enumeration.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_entity_enumeration(array &$variables) {
  // Fetch EntityEnumeration Entity Object.
  $entity_enumeration = $variables['elements']['#entity_enumeration'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
