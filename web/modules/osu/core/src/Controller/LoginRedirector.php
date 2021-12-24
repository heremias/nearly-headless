<?php

namespace Drupal\osu_core\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * An example controller.
 */
class LoginRedirector extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public function samlLogin() {
    if (\Drupal::currentUser()->isAuthenticated()) {
      return $this->redirect('osu_core.redirector');
    }
    else {
      return $this->redirect('samlauth.saml_controller_login', [], ['destination' => '/osu_core/redirector']);
    }
  }

  /**
   * Redirects the user somewhere sensible based on their permissions.
   */
  public function redirector() {
    // Possible routes to redirect a person to...
    $routes = [
      'system.admin_content' => 'access administration pages',
    ];
    foreach ($routes as $route => $permission) {
      if (\Drupal::currentUser()->hasPermission($permission)) {
        return $this->redirect($route, [], ['destination' => '<front>']);
      }
    }

    // Those other things didn't work, how about the home page.
    return $this->redirect('<front>');
  }

}
