<?php

namespace Drupal\standard_core\Commands;

use Drush\Commands\DrushCommands;
use Drush\Exceptions\UserAbortException;

/**
 * Standard drush commands
 */
class StandardDrushCommands extends DrushCommands {

  /**
   * @command standard:rebuild-field-map
   * @param string $entity_type
   *   The type of entity to rebuild mappings for.
   * @option array
   *   An option that takes multiple values
   * @option string
   *   Whether to force a rebuild
   * @aliases standard-rfm
   * @usage standard:rebuild-field-map node --force
   *
   */
  public function rebuildFieldMap($entity_type, $options = ['force' => FALSE]) {
    $bundle_key_value = \Drupal::service('keyvalue')->get('entity.definitions.bundle_field_map');

    $new_mapping = $this->getBundleMapByEntityType($entity_type);
    $existing_map = $bundle_key_value->get($entity_type);

    $to_be_added = array_diff_key($new_mapping, $existing_map);
    $to_be_removed = array_diff_key($existing_map, $new_mapping);
    $has_changes = !empty($to_be_added) || !empty($to_be_removed);

    $this->output()->writeln("Rebuilding stored field mapping values for $entity_type.");

    if ($has_changes) {
      $rows = [
        ['Field', NULL, 'Change']
      ];

      $rows = array_merge(
        $rows,
        $this->listTableToRow($to_be_added, dt('Add')),
        $this->listTableToRow($to_be_removed, dt('Remove'))
      );

      // replaces drush_print_table($rows, TRUE)
      // TODO: implement with RowsOfFields https://github.com/consolidation/output-formatters
      foreach ($feedback as $key => $value) {
        foreach ($value as $k => $v) {
          $this->output()->writeln("$k: $v");
        }
      }

      if (!empty($to_be_added)) {
        $count = count($to_be_added);
        $this->output()->writeln("$count items will be added and removed");
      }

      if (!empty($to_be_removed)) {
        $count = count($to_be_removed);
        $this->output()->writeln("$count items will be added and removed");
      }
    } else {
      $this->logger()->info('There are no changes to be made. The stored field mapping matches the generated one.');
    }

    if ($has_changes || $options['force'] == TRUE) {
      $bundle_key_value->set($entity_type, $new_mapping);
      drupal_flush_all_caches();
      $this->logger()->info('The field mapping has been rebuilt');
    } else {
      throw new UserAbortException();
    }
  }

  /**
   * Gets the field mappings for the given entity type.
   *
   * @param string $entity_type
   *   The type of the entity to generate field mappings for.
   *
   * @return array
   *   An array of field mappings ready to store in the key value store.
   */
  private function getBundleMapByEntityType() {
    $bundle_field_map = [];

    $entity_manager = \Drupal::service('entity_field.manager');
    $bundle_info = \Drupal::service('entity_type.bundle.info')->getBundleInfo($entity_type);

    foreach ($bundle_info as $bundle => $info) {
      $definitions = $entity_manager->getFieldDefinitions($entity_type, $bundle);

      if (empty($definitions)) {
        continue; // do nothing, move on
      }

      // filter out the definitions that are not of the target type
      $definitions = array_filter($definitions, function($definition) {
        return $definition instanceof \Drupal\Field\Entity\FieldConfig;
      });

      if (!empty($definitions)) {
        foreach ($definitions as $field_definition) {
          $field_bundle = $field_definition->getTargetBundle();
          $field_name = $field_definition->getName();

          if (!isset($bundle_field_map[$field_name])) {
            // field doesn't exist yet, so initialize it w/ type && empty bundle list
            $bundle_field_map[$field_name] = [
              'type' => $field_definition->getType(),
              'bundle' => []
            ];
          }
          $bundle_field_map[$field_name]['bundles'][$field_bundle] = $field_bundle;
        }
      }
    }

    return $bundle_field_map;
  }

  /**
   * Convert a field mapping into a drush table value.
   *
   * @param array $map
   *   The field mapping array keyed by the field name.
   * @param string $action
   *   The action being taken on this set of mappings (e.g. Add, Remove)
   *
   * @return array
   *   A multivalued array that combined that action with the mapping
   */
  private function listTableToRow(array $map, string $action): array {
    return $this->keyValueToArrayTable(
      array_combine(
        array_keys($map),
        array_pad([], count($map), $action)
      )
    );
  }

  /**
   * Implements drush_key_value_to_array_table
   *   http://api.drush.org/api/drush/includes%21output.inc/function/drush_key_value_to_array_table/8.0.x
   */
  private function keyValueToArrayTable($keyvalue_table, $metadata = []) {
    if (!is_array($metadata)) {
      $metadata = array('key-value-item' => $metadata);
    }

    $item_key = array_key_exists('key-value-item', $metadata) ? $metadata['key-value-item'] : NULL;
    $metadata += array(
      'format' => 'list',
      'separator' => ' ',
    );
    $table = array();

    foreach ($keyvalue_table as $key => $value) {
      if (isset($value)) {
        if (is_array($value) && isset($item_key)) {
          $value = $value[$item_key];
        }
        // if (is_array($value)) {
        //   $value = drush_format($value, $metadata, 'list');
        // }
      }
      if (isset($metadata['include-field-labels']) && !$metadata['include-field-labels']) {
        $table[] = array(isset($value) ? $value : '');
      }
      elseif (isset($value)) {
        $table[] = array($key, ' :', $value);
      }
      else {
        $table[] = array($key . ':', '', '');
      }
    }
    return $table;
  }
}
