<?php

/**
 * Shamelessly ripped off/modified from migrate_source_directory module.
 * https://www.drupal.org/project/migrate_source_directory
 */

namespace Drupal\osu_media\Plugin\migrate\source;

use Drupal\migrate\MigrateException;
use Drupal\migrate\Plugin\migrate\source\SourcePluginBase;
use Drupal\migrate\Plugin\MigrationInterface;


class MediaToMigrate extends SourcePluginBase {

  /**
   * Recurse level of directory search.
   *
   * Uses http://php.net/manual/en/recursiveiteratoriterator.setmaxdepth.php.
   *
   * @var int
   */
  protected $recurseLevel = 20;

  /**
   * A list of files from the provided directory, and possible children.
   *
   * @var array
   */
  protected $filesList = [];

  /**
   * An list of file extensions to limit by.
   *
   * @var array
   */
  protected $fileExtensions = [];

  /**
   * An list of directories to search.
   *
   * @var array
   */
  protected $pathToScan = '';

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration);
    $this->pathToScan = \Drupal::service('file_system')->realpath(\Drupal::config('system.file')->get('default_scheme')  . '://to-migrate');
  }

  /**
   * Return a string representing the source file path.
   *
   * @return string
   *   The file path.
   */
  public function __toString() {
    return $this->pathToScan;
  }

  /**
   * {@inheritdoc}
   */
  public function initializeIterator() {
    // Set source an destination directories here to make migration files easier.
    $source = \Drupal::service('file_system')->realpath(\Drupal::config('system.file')->get('default_scheme')  . '://to-migrate');

    $recursive_iter = new \RecursiveDirectoryIterator($this->pathToScan, \FilesystemIterator::SKIP_DOTS);

    // Pass the RecursiveIterator to the constructor of RecursiveIteratorIterator.
    $recursive_iter_iter = new \RecursiveIteratorIterator(
      $recursive_iter, \RecursiveIteratorIterator::SELF_FIRST
    );
    $recursive_iter_iter->setMaxDepth($this->recurseLevel);

    foreach ($recursive_iter_iter as $path => $info) {
      if (!is_dir($path)) {
        $file = pathinfo($path);
        if (!empty($this->fileExtensions) && isset($file['extension'])) {
          $ext = $file['extension'];
          if (in_array($ext, $this->fileExtensions)) {
            array_push($this->filesList, [
              'alias' => str_replace($source, '', $path),
              'destination' => str_replace($source, 'public://migrated', $path),
              'label' => '{migrated}' . str_replace($source, '', $path),
              'path' => $file['dirname'],
              'source' => $path,
            ]);
          }
        }
      }
    }
    drush_print_r($this->filesList);

    return new \ArrayIterator($this->filesList);
  }

  /**
   * We use the full path to the file as the ID.
   */
  public function getIds() {
    $ids = ['source' => ['type' => 'string']];
    return $ids;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'alias' => '/where/i/was/on/web.pdf',
      'path' => '/home/mysite/web/sites/default/files/to-migrate/where/i/was/on/',
      'label' => '{migrated}/where/i/was/on/web.pdf',
      'source' => '/home/mysite/web/sites/default/files/to-migrate/where/i/was/on/web.pdf',
      'destination' => 'public://files/migrated/where/i/was/on/web.pdf'
    ];
    return $fields;
  }

}
