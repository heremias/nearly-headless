<?php

/**
 * Implements hook_preprocess_node to add some control variables to theme layer.
 *
 */
function standard_core_preprocess_node(&$variables) {

  $node = $variables['node'];

  if ($node instanceof \Drupal\node\NodeInterface) {
    if ($node->registration_window) {
      $now = new \Drupal\Core\Datetime\DrupalDateTime();
      $started = $now > $node->registration_window->start_date;
      $finished = $now > $node->registration_window->end_date;

      // Add some values to control display
      $variables['control']['registration_window_is_open'] = $started && !$finished;
      $variables['control']['registration_window_is_future'] = !$started;
      $variables['control']['registration_window_is_past'] = $finished;

      // Show registration windows if populated.
      if ($node->registration_window->start_date && $node->registration_window->end_date) {
        $variables['content']['registration_window_closes_string'] = [
          '#type' => 'markup',
          '#markup' => $node->registration_window->end_date->format('M j, Y')
        ];
        $variables['content']['registration_window_opens_string'] = [
          '#type' => 'markup',
          '#markup' => $node->registration_window->start_date->format('M j, Y')
        ];
      }
    }

    if ($node->hasField('allowed_roles') && !$node->get('allowed_roles')->isEmpty()) {
      \Drupal::service('page_cache_kill_switch')->trigger();
    }
  }
}
