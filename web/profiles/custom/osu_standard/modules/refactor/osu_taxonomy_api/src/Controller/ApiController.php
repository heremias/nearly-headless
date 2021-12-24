<?php

namespace Drupal\osu_taxonomy_api\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * An example controller.
 */
class ApiController extends ControllerBase {

  public function json($machine_name) {
    $terms = $this->terms($machine_name);
    $vals = [];
    foreach ($terms as $term) {

      $item = [];

      // If something has a human friendly analytics code, use that.
      if ($term->hasField('analytics_code')) {
        $item['code'] = $term->analytics_code->value;
      }
      // Otherwise use uuid.
      else {
        $item['uuid'] = $term->uuid();
      }

      // Add a friendly name and description
      $item['name'] = $term->label();
      $item['description'] = $term->getDescription();

      $vals[] = $item;

    }
    return new \Symfony\Component\HttpFoundation\JsonResponse($vals);
  }

  public function terms($machine_name) {
    $query = \Drupal::entityQuery('taxonomy_term');
    $query->condition('vid', $machine_name);
    $query->sort('name');
    $tids = $query->execute();

    $storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
    $terms = $storage->loadMultiple($tids);
    return $terms;
  }
}
