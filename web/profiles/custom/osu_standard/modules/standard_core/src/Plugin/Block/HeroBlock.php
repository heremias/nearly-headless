<?php

namespace Drupal\standard_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Access\AccessResult;
use Drupal\node\NodeInterface;

/**
 * Provides a 'Standard Hero' Block.
 *
 * @Block(
 *   id = "standard_hero",
 *   admin_label = @Translation("Standard Hero"),
 *   category = @Translation("Standard"),
 * )
 */
class HeroBlock extends BlockBase {

  public function build() {

    if ($this->present()) {
      $node = \Drupal::routeMatch()->getParameter('node');
      $build = \Drupal::entityTypeManager()->getViewBuilder('paragraph')->view($node->get('hero')->entity);
    }
    else {
      $build = [
        '#markup' => '',
      ];
    }

    return $build;
  }

  public function getCacheTags() {
    if ($node = \Drupal::routeMatch()->getParameter('node')) {
      return Cache::mergeTags(parent::getCacheTags(), array('node:' . $node->id()));
    } else {
      return parent::getCacheTags();
    }
  }

  public function getCacheContexts() {
    return Cache::mergeContexts(parent::getCacheContexts(), array('route'));
  }

  protected function blockAccess(\Drupal\Core\Session\AccountInterface $account) {
    return $this->present() ? AccessResult::allowed() : AccessResult::forbidden();
  }

  /**
   * Returns true if there is a hero on the current node.
   * @return bool
   */
  public function present() {
    $node = \Drupal::routeMatch()->getParameter('node');
    return ($node instanceof NodeInterface) && $node->hasField('hero') && (!$node->get('hero')->isEmpty());
  }
}