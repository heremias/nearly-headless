<?php

namespace Drupal\standard_patterns\Processor\Paragraph;

class QaListProcessor extends ParagraphProcessor {

  public static function variant($value, $default='accordion') {
    return in_array($value, self::variants()) ? $value : $default;
  }

  public static function variants() {
    return ['accordion', 'listicle', 'qalist'];
  }

  public static function process(&$variables) {
    $p = $variables['paragraph'];
    $variables['id'] = $p->id();
    $variables['items'] = [];
    foreach ($p->get('items') as $item) {
      $variables['items'][] = [
        'heading' => $item->entity->get('question')->value,
        'body' => self::body($item->entity->get('answer'))
      ];
    }
    $variables['variant'] = self::variant($p->get('qa_list_variant')->value);
  }

}