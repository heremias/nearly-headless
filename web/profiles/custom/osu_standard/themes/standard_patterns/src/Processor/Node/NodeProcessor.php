<?php

namespace Drupal\standard_patterns\Processor\Node;

use Drupal\standard_patterns\Processor\EntityProcessor;
use Drupal\Core\Datetime\DrupalDateTime;

class NodeProcessor extends EntityProcessor {

  public static function revision($node) {
    $author = $node->getRevisionUser();
    // \\Drupal\\Core\\Entity\\RevisionLogInterface::getRevisionUser()
    $created = $node->getRevisionCreationTime();
    $formatter = \Drupal::service('date.formatter');
    $isRecent = DrupalDateTime::createFromFormat('U', $created)
                ->diff( new DrupalDateTime())
                ->days < 30;
    $modified = $formatter->format($created, 'custom', 'M. j, Y');
    $months = [
      'good' => [
        'March',
        'April',
        'May',
        'June',
        'July',
        'Sept.',
      ],
      'bad' => [
        'Mar.',
        'Apr.',
        'May.',
        'Jun.',
        'Jul.',
        'Sep.',
      ]
    ];
    $baseDate = $formatter->format($created, 'custom', 'M. j, Y');
    $modified = str_replace($months['bad'], $months['good'], $baseDate);
    return [
      'message' => $node->getRevisionLogMessage(),
      'user' => $author->getDisplayName(),
      'date' => [
        'isRecent' => $isRecent,
        'ago' =>  $formatter->formatDiff($created, \Drupal::time()->getRequestTime(), [
          'granularity' => 1,
          'return_as_object' => TRUE,
        ]),
        'modified' => $modified
      ]
    ];
  }
}
