<?php

namespace Drupal\standard_core\Processor;

use Drupal\standard_core\Processor\ProcessorBase;

class PracticeProcessor extends ProcessorBase {

  public static function process(&$variables) {
    $variables['content']['site_tags'] = self::extract($variables['elements']['#node'], $variables['view_mode']);
  }

  public static function extract($node, $mode) {
    $site_tags = [];

    // Only preprocess on full and teaser.
    if (in_array($mode, ['full']) && $node->hasField('site_tags')) {

      // Load our vocabularies.
      $vocabs = self::vocabularies($mode);

      // Iterate through all our configured vocabularies. Order is important.
      if ($vocabs) {
        foreach ($vocabs as $vocab) {

          // Initialize our vocabulary structure.
          $vocabulary = [
            'id' => $vocab->id(),
            'name' => $vocab->label(),
            'terms' => []
          ];

          // Add any terms matching this vocabulary.
          foreach ($node->site_tags as $t) {
            if ($t->entity->bundle() == $vocab->id()) {
              $term = [
                'id' => $t->entity->id(),
                'name' => $t->entity->label()
              ];
              $vocabulary['terms'][] = $term;
            }
          }

          // Add our vocabulary to site_tags.
          $site_tags[] = $vocabulary;
        }
      }
    }
    return $site_tags;
  }

  /**
   * @param $mode - "full" or "teaser"
   * @return \Drupal\Core\Entity\EntityInterface[]
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function vocabularies($mode) {
    $config = \Drupal::config('standard_core.developer_settings');
    $vocab_ids = explode(',', $config->get("practice_site_tag_vocabs"));
    $vocabularies = \Drupal::entityTypeManager()->getStorage('taxonomy_vocabulary')->loadMultiple($vocab_ids);
    return $vocabularies;
  }
}
