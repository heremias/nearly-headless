<?php

namespace Drupal\osu_core\Service;

use Drupal\Core\Config\ConfigFactoryOverrideInterface;

/**
* Overrides allowed menus in nodes.
*/
class MenuOverrider extends BaseOverrider implements ConfigFactoryOverrideInterface {

  /**
  * {@inheritdoc}
  */
  public function loadOverrides($names) {
    $overrides = array();
    $types = [
      'content_page',
      'listing',
    ];
    /*
    $types = \Drupal::entityTypeManager()
      ->getStorage('menu')
      ->loadMultiple();
    */
    foreach ($types as $type) {
      $configKey = 'node.type.' . $type;
      if (in_array($configKey, $names )) {
        $config = \Drupal::service('config.factory')->getEditable($configKey);
        // If we're allowing menus, then add all the menus a site manager can manage.
        if ($config->get('third_party_settings.menu_ui.available_menus')
          && (count($config->get('third_party_settings.menu_ui.available_menus')) > 0)) {
          $overrides[$configKey]['third_party_settings']['menu_ui']['available_menus'] = self::filteredMenus();
        }
      }
    }
    return $overrides;
  }

  /**
   * Allow users to put things in non-system menus.
   * @return array
   */
  public static function filteredMenus() {
    $filtered = [];
    $all = menu_ui_get_menus();
    $system = ['admin','footer', 'tools', 'account'];
    foreach ($all as $name => $title) {
      if (!in_array($name, $system)) {
        $filtered[] = $name;
      }
    }
    return $filtered;
  }
}
