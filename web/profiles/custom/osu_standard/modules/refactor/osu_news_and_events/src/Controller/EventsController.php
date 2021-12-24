<?php

namespace Drupal\osu_news_and_events\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * An example controller.
 */
class EventsController extends BaseController {

  public function filters() {
    return $this->filter_for('/events');
  }

  public function view() {
    return views_embed_view('events', 'embed_1', $this->type);
  }
}
