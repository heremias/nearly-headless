<?php

namespace Drupal\osu_core\Service;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Config\ConfigFactoryOverrideInterface;
use Drupal\Core\Config\StorageInterface;

/**
 * Example configuration override.
 */
abstract class BaseOverrider implements ConfigFactoryOverrideInterface {

  /**
   * {@inheritdoc}
   */
  abstract function loadOverrides($names);

  /**
   * {@inheritdoc}
   */
  public function getCacheSuffix() {
    return 'OsuCore' . get_called_class();
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheableMetadata($name) {
    return new CacheableMetadata();
  }

  /**
   * {@inheritdoc}
   */
  public function createConfigObject($name, $collection = StorageInterface::DEFAULT_COLLECTION) {
    return NULL;
  }

}