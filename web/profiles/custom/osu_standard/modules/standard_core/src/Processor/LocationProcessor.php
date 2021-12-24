<?php

namespace Drupal\standard_core\Processor;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal\standard_core\Processor\ProcessorBase;

class LocationProcessor extends ProcessorBase
{

  public static function processLocation($location)
  {
    $values = [];

    $values['name'] = $location->label();
    
    if ($location->building->value) {
      $service = \Drupal::service('osu_buildings.building_service');
      $map = $service->map();
      $values['building'] = [
        'code' => $location->building->value,
        'name' => isset($map[$location->building->value]) ? $map[$location->building->value] : '',
        'url' => 'https://www.osu.edu/map/building.php?building=' . urlencode($location->building->value)
      ];
    }

    // Addresses can be very complicated objects with complex localization rules.
    // Let the address_plain formatter do the heavy work of parsing a the
    // address into a set of template variables and then expose those directly.
    if ($location->address && $location->address[0]) {
      $render = $location->address[0]->view([
        'type' => 'address_plain',
        'label' => 'hidden',
        'settings' => array(),
      ]);
      $fields = [
        'given_name', 'additional_name', 'family_name', 'organization',
        'address_line1', 'address_line2', 'postal_code', 'sorting_code',
        'administrative_area', 'locality', 'dependent_locality', 'country'
      ];
      foreach ($fields as $field) {
        $values['address'][$field] = $render['#' . $field];
      }

      // Add a google map url.
      $mapLinkManager = \Drupal::service('plugin.manager.map_link');
      $mapLinkType = $mapLinkManager->createInstance('google_maps_directions');
      $values['address']['url'] = $mapLinkType->getAddressUrl($location->address[0])->toString();
    }

    return $values;
  }
}