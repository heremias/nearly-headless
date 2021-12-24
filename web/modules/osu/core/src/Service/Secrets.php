<?php

namespace Drupal\osu_core\Service;

class Secrets {
  public static function get($key) {
    $val = NULL;
    $path = \Drupal::service('file_system')->realpath('private://secrets.json');
    if (file_exists($path) && $secrets = json_decode(file_get_contents($path))) {
      $val = $secrets->$key;
    }
    return $val;
  }
}