<?php

namespace Drupal\osu_news_and_events\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * An example controller.
 */
class NewsController extends BaseController {

  public function filters() {
    return $this->filter_for('/news');
  }

  public function view() {
    return views_embed_view('news', 'embed_1', $this->type);
  }
}
