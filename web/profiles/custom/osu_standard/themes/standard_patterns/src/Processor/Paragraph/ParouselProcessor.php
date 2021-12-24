<?php

namespace Drupal\standard_patterns\Processor\Paragraph;

class ParouselProcessor extends ParagraphProcessor {

  public static function process(&$variables) {
  	$p = $variables['paragraph'];
    $items = [];
    foreach ($p->items->referencedEntities() as $item) {
      $image = self::getImageEntity($item);
      if ($image) {
        $items[] = [
          'description' => $item->description->value,
          'image' => self::image($item, 'image', 'highlight', 'large')
        ];
      }
    }
    $variables['content']['items_raw'] = $items;
  }
}