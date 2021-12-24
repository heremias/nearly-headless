<?php

namespace Drupal\standard_core\Service;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Site\MaintenanceMode;

class CustomMaintenanceMode extends MaintenanceMode {

  /**
   * {@inheritdoc}
   */
  public function exempt(AccountInterface $account) {
    $current_path = \Drupal::service('path.current')->getPath();
    $isAllowed = $this->isAllowed($current_path);
    if ($isAllowed) {
      return TRUE;
    }
    else {
      return $account->hasPermission('access site in maintenance mode');
    }
  }

  function isAllowed ($route) {
    $allowedRoutes = [
      '#^/manager$#',
      '#^/user#',
      '#^/admin#'
    ];

    $matches = array_map(function($regex) use($route){ return preg_match($regex, $route); }, $allowedRoutes);
    return in_array(1, $matches);
  }
}
