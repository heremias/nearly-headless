<?php

namespace Drupal\standard_core;

use \Drupal\node\NodeInterface;

/**
 * Implements the logic to determine what part of the site is the H1.
 * Class PrimaryHeaderDeterminer
 */
class PrimaryHeaderDeterminer {

  public static function determine() {
    $header = 'page.title';
    $node = \Drupal::routeMatch()->getParameter('node');
    if ($node instanceof NodeInterface) {
      if ($node->hasField('hero') && !$node->get('hero')->isEmpty()) {
        $header = 'site.title';
        $hero = $node->get('hero')->entity;
        if ($hero->hasField('h1_field') && $hero->get('h1_field')->value == 'component.heading') {
          $header = 'component.heading';
        }
        else if ($hero->hasField('include_page_title') && $hero->get('include_page_title')->value == '1') {
          if (($hero->bundle() == 'hero_banner') && $hero->get('heading')->value) {
            $header = 'component.heading';
          }
          else {
            $header = 'page.title';
          }
        }
      }
    }
    return $header;
  }

}
