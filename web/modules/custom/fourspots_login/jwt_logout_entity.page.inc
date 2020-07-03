<?php

/**
 * @file
 * Contains jwt_logout_entity.page.inc.
 *
 * Page callback for Jwtlogout entity entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Jwtlogout entity templates.
 *
 * Default template: jwt_logout_entity.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_jwt_logout_entity(array &$variables) {
  // Fetch JWTLogoutEntity Entity Object.
  $jwt_logout_entity = $variables['elements']['#jwt_logout_entity'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
