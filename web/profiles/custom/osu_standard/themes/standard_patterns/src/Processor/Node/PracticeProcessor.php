<?php

namespace Drupal\standard_patterns\Processor\Node;

class PracticeProcessor extends NodeProcessor {

  public static function process(&$variables) {
    $node = $variables['node'];
    $variables['revision'] = parent::revision($node);
  }
}
