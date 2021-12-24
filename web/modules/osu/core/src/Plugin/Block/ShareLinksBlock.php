<?php

namespace Drupal\osu_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Access\AccessResult;
use Drupal\node\NodeInterface;

/**
 * Provides a 'Share Links' Block.
 *
 * @Block(
 *   id = "share_links",
 *   admin_label = @Translation("Share Links"),
 *   category = @Translation("Core"),
 * )
 */
class ShareLinksBlock extends BlockBase {

  public function build() {
    $request = \Drupal::request();
    $path = \Drupal::service('path.current')->getPath();
    $alias = \Drupal::service('path_alias.manager')->getAliasByPath($path);
    $url = $request->getSchemeAndHttpHost() . $alias;
    $url = urlencode($url);
    return [
      '#theme' => 'osu_core_sharelinks',
      '#facebook' => 'https://www.facebook.com/sharer/sharer.php?u=' . $url,
      '#linkedin' => 'https://www.linkedin.com/shareArticle?mini=true&url=' . $url,
      '#twitter' => 'https://twitter.com/intent/tweet?text=' . $url,
      '#email' => 'mailto:?body=' . $url,
    ];
  }

  public function getCacheTags() {
    if ($node = \Drupal::routeMatch()->getParameter('node')) {
      return Cache::mergeTags(parent::getCacheTags(), array('node:' . $node->id()));
    }
    else {
      return parent::getCacheTags();
    }
  }

  public function getCacheContexts() {
    return Cache::mergeContexts(parent::getCacheContexts(), array('route', 'url.path'));
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

    $present = FALSE;
    if ($node instanceof NodeInterface) {
      // Share links should always be on these content types.
      // We might want to delegate the decision about which types to the distribution.
      $automatic = in_array($node->bundle(), [
        'audio_story', 'content_article', 'event', 'meeting', 'session']);
      $selected = $node->hasField('include_share_links')
        && $node->get('include_share_links')->first()
        && ($node->get('include_share_links')->first()->value == '1');
      $present = $automatic || $selected;
    }
    return $present;
  }
}
