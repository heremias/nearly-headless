<?php

namespace Drupal\standard_core\Processor;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal\standard_core\Processor\ProcessorBase;
use Drupal\views\ViewExecutable;
use Drupal\views\Views;


class ContentListingProcessor extends ProcessorBase
{
  public static function process(&$variables)
  {
    $p = $variables['elements']['#paragraph'];
    $ids = implode('+', array_column($p->site_tags->getValue(), 'target_id'));
    $types = implode('+', array_column($p->content_types->getValue(), 'value'));

    $variables['content']['listing'] = views_embed_view(
      'content_card_listing',
      $p->bundle() == 'floor_content_listing' ? 'embed_1' : 'embed_2',
      strlen($types) > 0 ? $types : NULL,
      strlen($ids) > 0 ? $ids : NULL
    );

    // Set which fields should be excluded.
    $variables['excluded'] = array_column($p->card_components->getValue(), 'value');
  }
}
