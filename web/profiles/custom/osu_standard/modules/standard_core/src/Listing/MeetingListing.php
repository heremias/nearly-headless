<?php

namespace Drupal\standard_core\Listing;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal\standard_core\Processor\LocationProcessor;
use Drupal\standard_core\Listing\ListingBase;

class MeetingListing {

  public function __construct() {
    $this->year = isset($_GET['year']) ? (int) $_GET['year'] : (int) date('Y');
    $this->month = isset($_GET['month']) ? (int) $_GET['month'] : (int) date('n');

    // We only use every other month in our nav.
    if ($this->month % 2 == 0) {
      $this->month--;
    }
  }

  public function process(&$variables) {
    $variables['content']['meetings'] = $this->data();
    $variables['content']['navigation'] = $this->navigation();
    $this->setCacheTags($variables['#cache']);
  }

  public function setCacheTags(&$cache) {
    // Listing should expire after 1 hour.
    $cache['max-age'] = 3600;
    // Invalidate any time a node is changed.
    $cache['tags'] = ['node_list'];
    // Cache separate by query string.
    $cache['contexts'][] = 'url.query_args';
  }

  public function navigation() {

    $path = \Drupal::service('path.current')->getPath();
    $alias = \Drupal::service('path_alias.manager')->getAliasByPath($path);

    return [
      'current_year' => strip_tags($this->year),
      'last_year' => [
        'exists' => $this->meetingsInPreviousYears(),
        'text' => strip_tags($this->year - 1),
        'url' =>  $alias . '?year=' . urlencode($this->year - 1) . '&month=' . urlencode($this->month)
        ],
      'next_year' => [
        'exists' => $this->meetingsInFutureYears(),
        'text' => strip_tags($this->year + 1),
        'url' => $alias . '?year=' . urlencode($this->year + 1) . '&month=' . urlencode($this->month)
      ],
      'months' => [
        [
          'active' => $this->month == 1,
          'text' => 'Jan-Feb',
          'url' => $alias . '?year=' . urlencode($this->year) . '&month=1'
        ],
        [
          'active' => $this->month == 3,
          'text' => 'Mar-Apr',
          'url' => $alias . '?year=' . urlencode($this->year) . '&month=3'
        ],
        [
          'active' => $this->month == 5,
          'text' => 'May-June',
          'url' => $alias . '?year=' . urlencode($this->year) . '&month=5'
        ],
        [
          'active' => $this->month == 7,
          'text' => 'July-Aug',
          'url' => $alias . '?year=' . urlencode($this->year) . '&month=7'
        ],
        [
          'active' => $this->month == 9,
          'text' => 'Sept-Oct',
          'url' => $alias . '?year=' . urlencode($this->year) . '&month=9'
        ],
        [
          'active' => $this->month == 11,
          'text' => 'Nov-Dec',
          'url' => $alias . '?year=' . urlencode($this->year) . '&month=11'
        ]
      ],
    ];
  }

  /*
   * We don't have a view for this listing as the filters seemed like they could become
   * prohibitively difficult given uncertainty around them.
   */
  public function data() {

    $values = [];

    // If we want nov + dec 2018, we will do
    // Jan 1 2019 > our stuff > Nov 1, 2018

    // Determine min date.
    $min = DrupalDateTime::createFromArray(['year' => $this->year, 'month' => $this->month]);
    $min->setTimezone(new \DateTimezone(DateTimeItemInterface::STORAGE_TIMEZONE));
    $min = $min->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT);

    // Determine max date with rollover.
    $max = DrupalDateTime::createFromArray([
      'year' => $this->month == 11 ? $this->year + 1 : $this->year,
      'month' => $this->month == 11 ? 1 : $this->month + 2
    ]);
    $max->setTimezone(new \DateTimezone(DateTimeItemInterface::STORAGE_TIMEZONE));
    $max = $max->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT);


    // Get published nodes
    $query = \Drupal::entityQuery('node')
      ->condition('status', 1)
      ->condition('occurrence', $min, ">=" )
      ->condition('occurrence', $max, "<" )
      ->sort('occurrence', 'ASC');

    $nids = $query->execute();
    $nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($nids);
    if (count($nodes) > 0) {
      foreach ($nodes as $node) {
        if ($node->occurrence->start_date) {
          $start = $node->occurrence->start_date->getTimestamp();
          $end = $node->occurrence->end_date->getTimestamp();
          $date = \Drupal::service('date.formatter')->format($start, 'custom', 'M. j');
          $start_time = \Drupal::service('date.formatter')->format($start, 'custom', 'g:i A');
          $end_time = \Drupal::service('date.formatter')->format($end, 'custom', 'g:i A');

          $meeting = [
            'name' => $node->label(),
            'url' => $node->toUrl()->toString(),
            'linkable' => !($node->rh_action && in_array($node->rh_action->value, ['access_denied', 'page_not_found']))
          ];
          if ($node->location && $node->location->entity) {
            $meeting['location'] = LocationProcessor::processLocation($node->location->entity);
          }
          $values[$date]["{$start_time} - {$end_time}"][] = $meeting;
        }
      }
    }
    return $values;
  }

  public function meetingsInPreviousYears() {
    $connection = \Drupal::database();
    $query = $connection->query('
      SELECT 1 
      FROM {node__occurrence} 
      WHERE deleted = 0 AND YEAR(occurrence_value) < :year',
      [':year' => $this->year]
    );
    return count($query->fetchAll()) > 0;
  }

  public function meetingsInFutureYears() {
    $connection = \Drupal::database();
    $query = $connection->query('
      SELECT 1 
      FROM {node__occurrence} 
      WHERE deleted = 0 AND YEAR(occurrence_value) > :year',
      [':year' => $this->year]
    );
    return count($query->fetchAll()) > 0;
  }
}
