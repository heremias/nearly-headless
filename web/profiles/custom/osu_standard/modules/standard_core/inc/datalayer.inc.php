<?php

/**
 * Customize datalayer output
 * @param $data
 */
function standard_core_datalayer_alter(&$data) {

  // Lets not expose the user id.
  unset($data['userUid']);

  // Categorize the visitor
  $user = \Drupal::currentUser();
  if ($user->isAnonymous()) {
    $data['user_type'] = 'anonymous';
  }
  else {
    if (count(array_intersect($user->getRoles(), ['site_manager', 'content_editor', 'administrator'])) > 0) {
      $data['user_type'] = 'administrator';
    }
    else {
      $data['user_type'] = 'other';
    }
  }

  // Add node fields that want to be added.
  $entity = _datalayer_menu_get_any_object();
  if ($entity && ($entity->getEntityTypeId() == 'node')) {
    $handler = Drupal::moduleHandler();
    $node_data_fields = standard_core_datalayer_node_fields($entity);
    $data = array_merge($data, $node_data_fields);
  }
}

/**
 * Adds audiences when the primary entity is a node.
 * @param $entity
 *
 * @return array
 */
function standard_core_datalayer_node_fields($node) {
  $data = [];
  if ($node) {

    // Audiences
    if ($node->audiences) {
      foreach($node->audiences as $audience) {
        $data['audiences'][$audience->entity->slug->value] = $audience->entity->label();
      }
      $data['audience_codes'] = isset($data['audiences']) ? implode(',', array_keys($data['audiences'])) : '';
    }

    // Colleges
    if ($node->colleges) {
      foreach($node->colleges as $college) {
        $data['colleges'][$college->entity->slug->value] = $college->entity->label();
      }
      $data['college_codes'] = isset($data['colleges']) ? implode(',', array_keys($data['colleges'])) : '';
    }

    // Places
    if ($node->places) {
      foreach($node->places as $place) {
        $data['places'][$place->entity->slug->value] = $place->entity->label();
      }
      $data['place_codes'] = isset($data['places']) ? implode(',', array_keys($data['places'])) : '';
    }

    // Slugs
    if ($node->slug) {
      $data['slug'] = $node->slug->value;
    }

    if (($node->source) && ($node->source->entity)) {
      $data['source'] = $node->source->entity->analytics_code->value;
      $data['source_label'] = $node->source->entity->label();
    }

    if ($node->publish_date) {
      $data['publish_date'] = $node->publish_date->value;
    }

    if ($node->subtitle) {
      $data['subtitle'] = $node->subtitle->value;
    }

    if ($node->summary) {
      $data['summary'] = $node->summary->value;
    }

    if ($node->title_prefix) {
      $data['title_prefix'] = $node->title_prefix->value;
    }

    if ($node->topics) {
      foreach($node->topics as $topic) {
        $data['topics'][$topic->entity->slug->value] = $topic->entity->label();
      }
      $data['topic_codes'] = isset($data['topics']) ? implode(',', array_keys($data['topics'])) : '';
    }

    if ($node->types) {
      foreach($node->types as $type) {
        $data['types'][$type->entity->slug->value] = $type->entity->label();
      }
      $data['type_codes'] = isset($data['types']) ? implode(',', array_keys($data['types'])) : '';
    }
  }
  return $data;
}
