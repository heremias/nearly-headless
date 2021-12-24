<?php

namespace Drupal\osu_news_and_events\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * An example controller.
 */
class EventArchiveController extends BaseController {

  public function filters() {
    return $this->filter_for('/event/archive');
  }

  public function view() {
    return views_embed_view('events', 'embed_2', $this->type);
  }

}
