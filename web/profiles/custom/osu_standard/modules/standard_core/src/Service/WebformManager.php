<?php

namespace Drupal\standard_core\Service;

class WebformManager {

  /**
   * Defines the list of allowed lists. Whoa. See also purgeLists below.
   */
  public static function allowedLists() {
    return [
      'webform.webform_options.months',
      'webform.webform_options.phone_types',
      'webform.webform_options.state_codes',
      'webform.webform_options.state_names',
      'webform.webform_options.state_province_codes',
      'webform.webform_options.state_province_names',
      'webform.webform_options.yes_no',
    ];
  }


  /**
   * Removes unwanted option lists that webform ships with.
   */
  public static function purgeLists() {
    $allowed = self::allowedLists();
    $config_factory = \Drupal::configFactory();
    foreach ($config_factory->listAll('webform.webform_options.') as $config) {
      if (!in_array($config, $allowed)) {
        \Drupal::configFactory()->getEditable($config)->delete();
      }
    }
  }

  /**
   * Webform ships with some unwanted things. This removes them.
   */
  public static function purgeUnwanted() {
    // We don't want 80% of the lists.
    self::purgeLists();

    // Delete the webform content type which we don't want.
    $type = \Drupal::entityTypeManager()->getStorage('node_type')->load('webform');
    if ($type) {
      $type->delete();
    }
  }
}
