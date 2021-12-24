<?php

namespace Drupal\osu_core\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Serves assets for Ckeditor.
 */
class CkeditorController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public function image($name) {
    $uri = drupal_get_path('module', 'osu_core') . '/images/' . $name;
    return new BinaryFileResponse($uri, 200);
  }

  public function template() {
    $uri = drupal_get_path('module', 'osu_core') . '/js/templates.js';
    return new BinaryFileResponse($uri, 200);
  }

}
