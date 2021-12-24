<?php

namespace Drupal\osu_media\Service;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Config\ConfigFactoryOverrideInterface;
use Drupal\Core\Config\StorageInterface;
use Drupal\Component\Serialization\Json;

/**
 * Overrides media settings.
 */
class Overrides implements ConfigFactoryOverrideInterface {

  /**
   * {@inheritdoc}
   */
  public function loadOverrides($names) {
    $overrides = [];

    // Override document embeds.
    if (in_array('embed.button.document', $names)) {
      $displays =  \Drupal::moduleHandler()->invokeAll('osu_document_embed_displays');
      for ($i=0; $i<count($displays); $i++) {
        $displays[$i] = 'view_mode:media.' . $displays[$i];
      }
      $overrides['embed.button.document'] = [
        'type_settings' => [
          'display_plugins' => $displays
        ]
      ];
    }

    // Override styles in standard image form.
    if (in_array('embed.button.image', $names) || in_array('filter.format.rich', $names)) {

      // Get the list of displays.
      $displays =  \Drupal::moduleHandler()->invokeAll('osu_image_embed_displays');

      // Add these displays to the image embed button options.
      if (in_array('embed.button.image', $names)) {
        for ($i=0; $i<count($displays); $i++) {
          $displays[$i] = 'view_mode:media.' . $displays[$i];
        }
        $overrides['embed.button.image'] = [
          'type_settings' => [
            'display_plugins' => $displays
          ]
        ];
      }
      else {
        // If there is only 1 display and it is large, remove alignment options.
        // Presupposes that video are also only large.
        if ((count($displays) == 1) && $displays[0] = 'large') {
          $overrides['filter.format.rich'] = [
            'filters' => [
              'filter_align' => [
                'status' => false
              ]
            ]
          ];
        }
      }
    }

    // Override styles in standard image form.
    if (in_array('core.entity_form_display.media.image.default', $names)) {
      $crops =  \Drupal::moduleHandler()->invokeAll('osu_media_image_crops');
      $overrides['core.entity_form_display.media.image.default'] = [
        'content' => [
          'image' => [
            'settings' => [
              'crop_list' => $crops
            ]
          ]
        ]
      ];
    }

    return $overrides;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheSuffix() {
    return 'OsuDocumentEmbedOverrider';
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
