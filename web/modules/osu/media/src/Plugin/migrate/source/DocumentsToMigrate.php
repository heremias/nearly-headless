<?php

/**
 * Shamelessly ripped off/modified from migrate_source_directory module.
 * https://www.drupal.org/project/migrate_source_directory
 */

namespace Drupal\osu_media\Plugin\migrate\source;

use Drupal\migrate\MigrateException;
use Drupal\migrate\Plugin\migrate\source\SourcePluginBase;
use Drupal\migrate\Plugin\MigrationInterface;

/**
 * Source for a given directory path.
 *
 * @MigrateSource(
 *   id = "documents_to_migrate",
 *   source_module = "osu_media",
 * )
 */
class DocumentsToMigrate extends MediaToMigrate {
  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration);
    $this->fileExtensions = ['csv', 'doc', 'docx', 'pdf', 'ppt', 'pptx', 'txt', 'xls', 'xlsx'];
  }

}
