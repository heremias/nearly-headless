<?php

namespace Drupal\standard_core\Service;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Config\ConfigFactoryOverrideInterface;
use Drupal\Core\Config\StorageInterface;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Config\FileStorage;

/**
 * Makes it easier to perform schema updates.
 */
class Updater {

  protected $source;

  public function __construct() {
    $config_path = dirname(dirname(dirname(__FILE__))) . '/config/install';
    $this->source = new FileStorage($config_path);
  }

  public function addMissingFieldStorage($entity, $field) {
    if (!\Drupal\field\Entity\FieldStorageConfig::loadByName($entity, $field)) {
      $this->addFieldStorage($entity, $field);
    }
  }

  /**
   * Adds field storage without checking if it exists.
   * @param $entity
   * @param $field
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function addFieldStorage($entity, $field) {
    \Drupal::entityTypeManager()->getStorage('field_storage_config')
      ->create($this->source->read("field.storage.{$entity}.{$field}"))
      ->save();
  }

  /**
   * Adds a bare field instance if it doesn't already exist (requires storage to exist).
   * @param $entity
   * @param $bundle
   * @param $field
   * @param string $label
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function addMissingFieldInstance($entity, $bundle, $field) {
    if (!\Drupal\field\Entity\FieldConfig::loadByName($entity, $bundle, $field)) {
      $this->addFieldInstance($entity, $bundle, $field);
    }
  }

  /**
   * Adds a field instance without checking if it (or the storage) exists.
   * @param $entity
   * @param $bundle
   * @param $field
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function addFieldInstance($entity, $bundle, $field) {
    \Drupal::entityTypeManager()->getStorage('field_config')
      ->create($this->source->read("field.field.{$entity}.{$bundle}.{$field}"))
      ->save();
  }

  public function updateValues(&$sandbox, string $entity, array $bundles, string $field, $default)
  {
    $this->addMissingFieldStorage($entity, $field);
    foreach ($bundles as $bundle) {
      $this->addMissingFieldInstance($entity, $bundle, $field);
    }

    // First time through the update.
    if (!isset($sandbox['total'])) {
      // Collect a total of the number of paragraphs to update.
      $ids = \Drupal::entityQuery($entity)
        ->condition('type', $bundles, 'IN')
        ->execute();
      $sandbox['total'] = count($ids);
      $sandbox['current'] = 0;
    }

    $per_batch = 25;

    // Handle one pass through.
    $ids = \Drupal::entityQuery($entity)
      ->condition('type', $bundles, 'IN')
      ->range($sandbox['current'], $sandbox['current'] + $per_batch)
      ->execute();

    foreach ($ids as $id) {
      $e = \Drupal\paragraphs\Entity\Paragraph::load($id);
      $e->$field->value = $default;
      $e->save();
      $sandbox['current']++;
    }
    $messenger = \Drupal::messenger();
    $messenger->addMessage($sandbox['current'] . ' ' . $entity . ' processed.', $messenger::TYPE_STATUS);

    if ($sandbox['total'] > 0) {
      $sandbox['#finished'] = ($sandbox['current'] / $sandbox['total']);
    } else {
      $sandbox['#finished'] = 1;
    }
  }
}
