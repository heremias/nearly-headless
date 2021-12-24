<?php

namespace Drupal\osu_brand\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'OSU Footer' Block.
 *
 * @Block(
 *   id = "osu_brand_footer",
 *   admin_label = @Translation("OSU Footer"),
 *   category = @Translation("Brand"),
 * )
 */
class FooterBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return array(
      '#theme' => 'osu_brand_footer',
    );
  }

}