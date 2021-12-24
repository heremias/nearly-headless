<?php

namespace Drupal\osu_core\Service;

use Drupal\Core\Access\AccessResult;
use Drupal\node\NodeInterface;
use Drupal\Core\Session\AccountInterface;

class NodeProtector {

  /**
   * Protects view access to a node based.
   * If allowed_roles is populated, only listed roles + editorial users can view.
   * Perhaps obviously, Bypass node access bypasses this!
   */
  public static function access(NodeInterface $node, $op, AccountInterface $account) {

    if (($op == 'view')
      && $node->hasField('allowed_roles')
      && !$node->get('allowed_roles')->isEmpty()
      && !$account->hasPermission('access content overview')
    ) {
      $roles = array_column($node->get('allowed_roles')->getValue(), 'value');
      foreach ($roles as $role) {
        // If the user has a matching role id (manual), or an SSO assigned variant thereof.
        if (in_array($role, $account->getRoles()) || in_array($role . '_sso', $account->getRoles())) {
          return AccessResult::allowed();
        }
      }
      return  AccessResult::forbidden();
    }
    return AccessResult::neutral();
  }
}
