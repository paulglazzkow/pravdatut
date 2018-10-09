<?php

/**
 * @file
 * Contains entity_party_programm.page.inc.
 *
 * Page callback for Entity party programm entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Entity party programm templates.
 *
 * Default template: entity_party_programm.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_entity_party_programm(array &$variables) {
  // Fetch EntityPartyProgramm Entity Object.
  $entity_party_programm = $variables['elements']['#entity_party_programm'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
