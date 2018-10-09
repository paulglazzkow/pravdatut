<?php

/**
 * @file
 * Contains entity_tech.page.inc.
 *
 * Page callback for Entity tech entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Entity tech templates.
 *
 * Default template: entity_tech.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_entity_tech(array &$variables) {
  // Fetch EntityTech Entity Object.
  $entity_tech = $variables['elements']['#entity_tech'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
