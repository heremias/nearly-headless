<?php
namespace Drupal\standard_patterns;

class ComponentHelper {

  //Using paragraph type and ID to create a unique value to use as dom id
  public static function getID( $paragraph ) {
    $paragraphType =  $paragraph->type->first()->target_id;
    $paragraphId = $paragraph->id->getValue()[0]['value'];
    $domID = $paragraphType . $paragraphId;
    return $domID;
  }

  public static function getEvents() {

    $eventIDs = \Drupal::entityQuery('node', 'OR')
      ->condition('type','event')
      ->condition('type','external_event')
      ->execute();
    $eventNodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($eventIDs);
    $events = [];
    foreach ($eventNodes as $event) {
      $occurrences = $event->get('occurrences')->getValue();
      foreach($occurrences as $occurrence) {
        $start = $occurrence['value'] . "Z";
        $end = $occurrence['end_value'] . "Z";
        $type = $event->get('type')->getString();
        if ($type === 'external_event') {
          $link = $event->get('canonical_link')->getString();
        } else {
          $alias = \Drupal::service('path_alias.manager')->getAliasByPath("/node/{$event->get('nid')->getString()}");
          $url = \Drupal\Core\Url::fromUri('base:'.$alias, [], ['absolute' => TRUE])->toString();
          $host = \Drupal::request()->getSchemeAndHttpHost();
          $link = $host . $url;
        }
        $events[] = [
          'title' => $event->get('title')->getString(),
          'date' => $start,
          'end' => $end,
          'url' => $link,
          'description' => $event->get('summary')->getString(),
        ];
      }
    }
    return $events;
  }

  public static function getActivePersonTagIDs() {
  $database = \Drupal::service('database');
  $result = $database->query(
    "SELECT n.nid as nid_list
    FROM {node} AS n
    JOIN {node_field_revision} AS nfr ON n.vid = nfr.vid
    WHERE status = 1 AND type = 'person'
    ")
    ->fetchCol(0);

$publishedUserIDs = $result;

  $result = $database->query(
    "SELECT GROUP_CONCAT(DISTINCT(site_tags_target_id)) AS vocab_ids FROM node__site_tags
      WHERE entity_id IN (:ids[])
      AND bundle = 'person'
      GROUP BY bundle
    ", [':ids[]' => $publishedUserIDs])->fetchAll();
  $vocabIDs = $result[0]->vocab_ids;
  return explode(",", $vocabIDs);
  }
}
