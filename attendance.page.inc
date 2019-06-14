<?php

/**
 * @file
 * Contains attendance.page.inc.
 *
 * Page callback for Attendance entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Attendance templates.
 *
 * Default template: attendance.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_attendance(array &$variables) {
  // Fetch Attendance Entity Object.
  $attendance = $variables['elements']['#attendance'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
