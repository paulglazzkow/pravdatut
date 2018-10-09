<?php

/**
 * @file
 * Contains entity_problem.page.inc.
 *
 * Page callback for Entity problem entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Entity problem templates.
 *
 * Default template: entity_problem.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_entity_problem(array &$variables) {
  // Fetch EntityProblem Entity Object.
  $entity_problem = $variables['elements']['#entity_problem'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
