<?php

/**
 * @file
 * Definition of Drupal\d8views\Plugin\views\filter\NodeTitles.
 */

namespace Drupal\osu_node_occurrences\Plugin\views\filter;

use Drupal\views\Plugin\views\filter\FilterPluginBase;
use Drupal\views\Plugin\views\filter\InOperator;
use Drupal\views\Plugin\views\filter\ManyToOne;
use Drupal\views\ViewExecutable;
use Drupal\views\Views;
/**
 * Filters by upcoming and past events.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("osu_node_occurrences_filters")
 */
class OccurrencesFilter extends FilterPluginBase {
  // exposed filter options
  protected $alwaysMultiple = TRUE;

  /**
   * Provide simple equality operator
   */
  public function operatorOptions() {
    return [
      'upcoming' => $this->t('Upcoming'),
      'past' => $this->t('Past'),
    ];
  }


  public function query() {
    $now_in_utc = new \DateTime('now', new \DateTimezone('UTC'));
    $now_in_utc_in_iso = $now_in_utc->format('Y-m-d\TH:i:s');

    $configuration = [
      'table'      => 'node__occurrences',
      'left_table' => 'node_field_data',
      'left_field' => 'nid',
      'field'      => 'entity_id',
      'type'       => 'LEFT',
      'extra_operator'   => 'AND',
    ];
    $join = Views::pluginManager('join')->createInstance('standard', $configuration);
    $this->query->addRelationship('node__occurrences', $join, 'node_field_data');

    switch($this->operator) {
      case 'upcoming':
        $this->query->addWhere('AND', 'node__occurrences.occurrences_value', $now_in_utc_in_iso, '>=');
        break;
      case 'past':
        $this->query->addWhere('AND', 'node__occurrences.occurrences_value', $now_in_utc_in_iso, '<');
        break;
    }
  }
}