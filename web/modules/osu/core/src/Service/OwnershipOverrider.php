<?php

namespace Drupal\osu_core\Service;

use Drupal\Core\Config\ConfigFactoryOverrideInterface;

/**
* Overrides site verification module metatags.
*/
class OwnershipOverrider extends BaseOverrider implements ConfigFactoryOverrideInterface {

  /**
  * {@inheritdoc}
  */
  public function loadOverrides($names) {
    $overrides = array();
    if (in_array('metatag.metatag_defaults.global', $names)) {
      $vals = [];
      foreach (['baidu', 'bing', 'google', 'norton_safe_web', 'pinterest', 'yandex'] as $key) {
        $property = \Drupal::config('osu_verify.settings')->get($key);
        $vals['tags'][$key] = $property;
      }
      $overrides['metatag.metatag_defaults.global'] = $vals;
    }
    return $overrides;
  }
}
