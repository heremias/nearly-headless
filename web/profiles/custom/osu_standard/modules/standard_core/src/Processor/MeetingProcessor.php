<?php

namespace Drupal\standard_core\Processor;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal\standard_core\Processor\ProcessorBase;

class MeetingProcessor extends ProcessorBase {

  public static function processUpcomingFloor(&$variables) {
    $p = $variables['elements']['#paragraph'];
    $variables['content']['upcoming_meetings'] = self::upcoming($p->get('upcoming_days')->value);
    $variables['content']['recent_meetings'] = self::recent($p->get('recent_days')->value);
    $variables['#cache']['tags'][] = 'node_list';
  }

  public static function upcoming($days=28) {
    $values = [];

    // Get the current date.
    $now = new DrupalDateTime();
    $now->setTimezone(new \DateTimezone(DateTimeItemInterface::STORAGE_TIMEZONE));
    $now = $now->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT);

    // Figure out the date to filter on.
    $soon = new DrupalDateTime();
    $soon->add(\DateInterval::createFromDateString("{$days} days"));
    $soon->setTimezone(new \DateTimezone(DateTimeItemInterface::STORAGE_TIMEZONE));
    $soon = $soon->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT);

    // Query published nodes where the occurrence is in the future.
    $query = \Drupal::entityQuery('node')
      ->condition('status', 1)
      ->condition('occurrence', $now, '>' )
      ->condition('occurrence', $soon, '<' )
      ->sort('occurrence', 'ASC');

    $nids = $query->execute();
    $nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($nids);

    if (count($nodes) > 0) {
      $values = self::format($nodes);
    }

    return $values;
  }

  public static function recent($days=14) {
    $values = [];

    // Get the current date.
    $now = new DrupalDateTime();
    $now->setTimezone(new \DateTimezone(DateTimeItemInterface::STORAGE_TIMEZONE));
    $now = $now->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT);

    // Figure out the date to filter on.
    $then = new DrupalDateTime();
    $then->add(\DateInterval::createFromDateString("-{$days} days"));
    $then->setTimezone(new \DateTimezone(DateTimeItemInterface::STORAGE_TIMEZONE));
    $then = $then->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT);

    // Query published nodes where the occurrence is in the past.
    $query = \Drupal::entityQuery('node')
      ->condition('status', 1)
      ->condition('occurrence', $now, '<' )
      ->condition('occurrence', $then, '>' )
      ->sort('occurrence', 'DESC');

    $nids = $query->execute();
    $nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($nids);

    if (count($nodes) > 0) {
      $values = self::format($nodes);
    }

    return $values;
  }

  public static function format($meetings) {
    $values = [];
    foreach ($meetings as $meeting) {
      if ($meeting->occurrence->start_date) {
        $start = $meeting->occurrence->start_date->getTimestamp();
        $date = \Drupal::service('date.formatter')->format($start, 'custom', 'M. j');

        $values[$date][] = [
          'name' => $meeting->label(),
          'url' => $meeting->toUrl()->toString(),
          'linkable' => !($meeting->rh_action && in_array($meeting->rh_action->value, ['access_denied', 'page_not_found']))
        ];
      }
    }
    return $values;
  }
}
