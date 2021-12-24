<?php

namespace Drupal\standard_core\Processor;

use Drupal\standard_core\Processor\ProcessorBase;
use Drupal\standard_core\Service\RssCacheProxy;

class RssFeedProcessor extends ProcessorBase {
  public static function process(&$variables) {
    $p = $variables['paragraph'];
    $variables['items'] = RssCacheProxy::get($p->get('feed_url')->uri);
    $variables['source'] = [
      'text' => $p->get('feed_website')->title,
      'url' => $p->get('feed_website')->uri,
      'heading_level' => 2
    ];
    $variables['#cache']['max-age'] = 3600;
  }
}
