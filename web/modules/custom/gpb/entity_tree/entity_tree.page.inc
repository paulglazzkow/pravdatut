<?php

/**
 * @file
 * Contains entity_tree.page.inc.
 *
 * Page callback for Entity tree entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Entity tree templates.
 *
 * Default template: entity_tree.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_entity_tree(array &$variables) {
  // Fetch EntityTree Entity Object.
  $entity_tree = $variables['elements']['#entity_tree'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
