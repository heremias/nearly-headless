<?php
namespace Drupal\osu_news_and_events\Breadcrumb;

use Drupal\Core\Breadcrumb\Breadcrumb;
use Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Link;

class Builder implements BreadcrumbBuilderInterface {

  /**
  * {@inheritdoc}
  */
  public function applies(RouteMatchInterface $attributes) {
    $parameters = $attributes->getParameters()->all();
    if (!empty($parameters['node']) && ($parameters['node']  instanceof NodeInterface)) {
      return in_array($parameters['node']->getType(), ['content_article', 'external_article', 'event', 'external_event']);
    }
  }

  /**
  * {@inheritdoc}
  */
  public function build(RouteMatchInterface $route_match) {

    // Get the node, this should only run on event and news types controlled in this listing.
    $node = \Drupal::request()->get('node');

    // Create a breadcrumb and add the home route.
    $breadcrumb = new Breadcrumb();
    $breadcrumb->addLink(Link::createFromRoute('Home', '<front>'));

    // If it's news add the news link.
    if ($node && in_array($node->bundle(), ['content_article', 'external_article'])) {
      $breadcrumb->addLink(Link::createFromRoute('News', 'osu_news_and_events.news'));
    }

    // If it's events add the events link.
    if ($node && in_array($node->bundle(), ['event', 'external_event'])) {
      // We point at the upcoming events page but the archive could be a legit (or additional) target.
      $breadcrumb->addLink(Link::createFromRoute('Events', 'osu_news_and_events.events'));
    }

    // Add the title for consistency.
    $breadcrumb->addLink(Link::createFromRoute($node->getTitle(), '<nolink>'));

    // If
    $breadcrumb->addCacheContexts(['route']);

    return $breadcrumb;
  }

}
