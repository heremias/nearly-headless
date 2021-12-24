<?php

namespace Drupal\standard_patterns\Processor\ViewMode;

use Drupal\osu_media\Service\ImageManager;
use Drupal\standard_patterns\Processor\EntityProcessor;

class CardProcessor extends EntityProcessor {

  public static function processNode(&$variables) {
    $card = $variables['node'];

    // Add a heading.
    $variables['heading'] = $card->label();

    // Add a url.
    $variables['url'] = \Drupal\Core\Url::fromRoute(
      'entity.node.canonical',
      ['node' => $card->id()],
      ['absolute' => TRUE]);

    // Format the creation / publish date.
    $variables['date'] =  $card->hasField('publish_date')
      ? $card->publish_date->date->format('M d, Y')
      : Drupal::service('date.formatter')->format($card->getCreatedTime(), 'custom', 'M d, Y');

    // Get a body.
    $variables['body'] = $card->hasField('summary') ? $card->get('summary')->value : '';

    // Add an image.
    $field = $card->hasField('teaser_image') ? 'teaser_image' : 'image';
    $image = self::getImageMedia($card, $field);
    if ($image) {
      $desktop = 'widescreen';
      $mobile = 'widescreen';

      // Create easy to render information for the image.
      $variables['image'] = [
        'alt' => ImageManager::getAlt($image->image),
        'src' => ImageManager::getSrc($image->image->entity, $mobile, 'medium'),
        'srcset' => [
          'mobile' => ImageManager::getSrc($image->image->entity, $mobile, 'medium'),
          'desktop' => ImageManager::getSrc($image->image->entity, $desktop, 'medium')
        ]
      ];
    }

    // Add tags.
    $variables['tags'] = [];
    foreach ($card->get('site_tags')->referencedEntities() as $term) {
      $variables['tags'][] = $term->label();
    }
  }
}
