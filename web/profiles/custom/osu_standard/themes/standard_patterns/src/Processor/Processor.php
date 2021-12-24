<?php

namespace Drupal\standard_patterns\Processor;

class Processor {

  public static function markup($markup) {
    return [
      '#markup' => $markup
    ];
  }
}