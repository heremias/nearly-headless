<?php

namespace Drupal\osu_brand\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'OSU Navbar' Block.
 *
 * @Block(
 *   id = "osu_brand_navbar",
 *   admin_label = @Translation("OSU Navbar"),
 *   category = @Translation("Brand"),
 * )
 */
class NavbarBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return array(
      '#theme' => 'osu_brand_navbar',
    );
  }

}