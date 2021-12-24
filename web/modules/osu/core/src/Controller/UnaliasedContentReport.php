<?php

namespace Drupal\osu_core\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * An example controller.
 */
class UnaliasedContentReport extends ControllerBase {

  /**
   * Returns a render-able array for a test page.
   */
  public function content() {

    $sql = "
      SELECT n.type, n.nid, n.title 
      FROM {node_field_data} n 
      LEFT JOIN {url_alias} a ON a.source = CONCAT('/node/', n.nid) 
      WHERE a.alias IS NULL AND n.status = 1 
      ORDER BY n.type, n.title";

    $connection = \Drupal::database();
    $query = $connection->query($sql);
    $nodes = $query->fetchAll();

    $header = ['#', 'Type', 'Edit', 'View'];
    $rows = [];
    if ($nodes) {
      foreach ($nodes as $node) {
        $rows[] = [
          $node->nid,
          $node->type,
          Link::fromTextAndUrl('Edit', Url::fromRoute('entity.node.edit_form', ['node' => $node->nid])),
          Link::fromTextAndUrl($node->title, Url::fromRoute('entity.node.canonical', ['node' => $node->nid]))
        ];
      }
    }
    $build = [
      '#theme' => 'table',
      //'#cache' => ['disabled' => TRUE],
      '#caption' => count($nodes) . ' published nodes lack aliases.',
      '#header' => $header,
      '#rows' => $rows,
    ];
    return $build;
  }

}
