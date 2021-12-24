<?php

namespace Drupal\osu_core\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class RouteSubscriber.
 *
 * @package Drupal\create_user_permission\Routing
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    if ($route = $collection->get('user.admin_create')) {
      $route->setRequirement('_permission', 'administer editorial users');
    }
    // Disable the moderated content route since we have no workflows.
    // Note that if you do not delete the default view, it will re-enable this. BOOOO!!!
    if ($route = $collection->get('content_moderation.admin_moderated_content')) {
      $route->setRequirement('_access', 'FALSE');
    }

    // We want site developers to be able manage
    // a limited subset of site configuration tasks.
    $ids = [
      'system.site_information_settings',
      'system.performance_settings',
      'system.run_cron',
      'system.site_maintenance_mode',
      'system.status',
      'system.php'
    ];
    foreach ($ids as $id) {
      if ($route = $collection->get($id)) {
        $route->setRequirement('_permission', 'administer osu core developer settings');
      }
    }

  }

}
