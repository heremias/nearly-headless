<?php

namespace Drupal\osu_news_and_events\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\menu_link_content\Entity\MenuLinkContent;

/**
 * An example controller.
 */
class BaseController extends ControllerBase {

  public function index($type='all') {

    // Parse the type parameter and default to all.
    if ($type == 'external') {
      $this->type = 0;
    }
    elseif ($type == 'site') {
      $this->type = 1;
    }
    else {
      $this->type = null;
    }
    \Drupal::service('page_cache_kill_switch')->trigger();
    $build = array(
      '#theme' => 'osu_brand_tab_filter_listing',
      '#tabs' => $this->tabs(),
      '#filters' => $this->filters(),
      '#listing' => $this->view(),
    );
    return $build;
  }

  public function tabs() {
    $route = \Drupal::routeMatch()->getRouteName();
    return [
      [
        'title' => 'Events',
        'href' => '/events',
        'active' => $route == 'osu_news_and_events.events',
      ],
      [
        'title' => 'News',
        'href' => '/news',
        'active' => $route == 'osu_news_and_events.news',
      ],
      [
        'title' => 'Archive',
        'href' => '/event/archive',
        'active' => $route == 'osu_news_and_events.archive',
      ]
    ];
  }

  public function filter_for($path) {
    return [
      [
        'title' => 'All',
        'href' => $path,
        'active' => FALSE,
      ],
      [
        'title' => \Drupal::config('system.site')->get('name'),
        'href' => $path . '/site',
        'active' => FALSE,
      ],
      [
        'title' => 'Other',
        'href' => $path . '/external',
        'active' => FALSE,
      ],
    ];
  }

  public function view() {
    return 'override_me';
  }

  public function filters() {
    return 'override_me';
  }
}