<?php

namespace Drupal\standard_core\Service;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Config\ConfigFactoryOverrideInterface;
use Drupal\Core\Config\StorageInterface;
use Drupal\Component\Serialization\Json;

/**
 * Overrides standard_core settings.
 */
class Overrider implements ConfigFactoryOverrideInterface {

  /**
   * {@inheritdoc}
   */
  public function loadOverrides($names) {
    $overrides = [];

    // We allow sites to pick their own vocabularies as targets for certain fields.
    $types = ['content_article', 'content_page', 'external_article', 'person', 'practice'];
    foreach ($types as $type) {
      if (in_array('field.field.node.' . $type . '.site_tags', $names)) {
        self::processSiteTags(
          "{$type}_site_tag_vocabs",
          "field.field.node.{$type}.site_tags",
          $overrides);
      }
    }

    // Paragraph site tags are filters and should always have all configured site tags available.
    $types = ['floor_content_listing', 'card_listing'];
    foreach ($types as $type) {
      if (in_array("field.field.paragraph.{$type}.site_tags", $names)) {
        self::processParagraphSiteTags("field.field.paragraph.{$type}.site_tags", $overrides);
      }
    }


    // Max file size limits can be overridden.
    if (in_array('field.field.media.document.file', $names)) {
      $config = \Drupal::config('standard_core.developer_settings');
      $limit = $config->get('media_document_max_filesize');
      if ($limit) {
        $overrides['field.field.media.document.file']['settings']['max_filesize'] = $limit;
      }
    }

    return $overrides;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheSuffix() {
    return 'StandardCoreOverrider';
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

  public static function processSiteTags($key, $field, &$overrides) {
    $config = \Drupal::config('standard_core.developer_settings');
    $vocabs = explode(',', $config->get($key));
    foreach ($vocabs as $vocab) {
      if (\Drupal::entityTypeManager()->getStorage('taxonomy_vocabulary')->load($vocab)) {
        $overrides[$field]['settings']['handler_settings']['target_bundles'][$vocab] = $vocab;
      }
    }
    if (count($vocabs) > 0) {
      $overrides[$field]['description'] =
        'Add terms from one of the following vocabularies: '
        . implode(", ", $vocabs) . '. <br>'
        . 'To add allowed terms or vocabularies, contact martech.';
    }
    else {
      $overrides[$field]['description'] = 'This field is unused on this site.';
    }
  }

  public static function processParagraphSiteTags($field, &$overrides) {
    // First iterate over all the content types to see which site tags they allow.
    $vocabs = [];
    $types = ['content_article', 'content_page', 'external_article', 'person', 'practice'];
    $config = \Drupal::config('standard_core.developer_settings');
    foreach ($types as $type) {
      $vocabularies = $config->get("{$type}_site_tag_vocabs");
      if (strlen($vocabularies) > 0) {
        $vocabs = array_merge($vocabs, explode(',', $vocabularies));
      }
    }

    // Add all the vocabularies to the field.
    $vocabs = array_unique($vocabs);
    foreach ($vocabs as $vocab) {
      if (\Drupal::entityTypeManager()->getStorage('taxonomy_vocabulary')->load($vocab)) {
        $overrides[$field]['settings']['handler_settings']['target_bundles'][$vocab] = $vocab;
      }
    }
    if (count($vocabs) > 0) {
      $overrides[$field]['description'] =
        'Add terms from one of the following vocabularies: '
        . implode(", ", $vocabs) . '. <br>'
        . 'To add allowed terms or vocabularies, contact martech.';
    }
    else {
      $overrides[$field]['description'] = 'This field is unused on this site.';
    }
  }

}
