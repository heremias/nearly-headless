<?php

namespace Drupal\osu_core\Service;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Config\ConfigFactoryOverrideInterface;
use Drupal\Core\Config\StorageInterface;
use Drupal\Component\Serialization\Json;

/**
* Overrides redirect settings.
*/
class RedirectOverrider extends BaseOverrider implements ConfigFactoryOverrideInterface {

  /**
  * {@inheritdoc}
  */
  public function loadOverrides($names) {
    $overrides = array();
    if (in_array('redirect.settings', $names)) {
      $vals = [];
      foreach (['auto_redirect'] as $key) {
        $property = \Drupal::config('osu_core.developer_settings')->get($key);
        $vals[$key] = $property;
      }
      $overrides['redirect.settings'] = $vals;
    }
    return $overrides;
  }
}