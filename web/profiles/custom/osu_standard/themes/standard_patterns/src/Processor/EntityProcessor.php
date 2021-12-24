<?php

namespace Drupal\standard_patterns\Processor;

use Drupal\osu_media\Service\ImageManager;

class EntityProcessor extends Processor {

  /**
   * Returns a render array with an appropriately rendered body.
   */
  public static function body($field) {
    return self::markup(check_markup($field->value, $field->format));
  }

  /**
   * Returns an image array with src and alt attributes.
   */
  public static function image($entity, $field='image', $aspect, $size) {
    $image = NULL;
    $imageEntity = self::getImageEntity($entity, $field);
    if ($imageEntity) {
      $image = [
      	'alt' => $entity->$field->entity->image->alt,
      	'src' => ImageManager::getSrc($imageEntity, $aspect, $size)
      ];
    }
    return $image;
  }

  /**
   * Returns dereferenced image entity from a node/paragraph field.
   */
  public static function getImageEntity($entity, $field='image') {
  	$image = NULL;
  	if ($entity->$field
  		&& $entity->$field->entity
  		&& $entity->$field->entity->image
  		&& $entity->$field->entity->image->entity) {
	    $image = $entity->$field->entity->image->entity;
    }
    return $image;
  }

  public static function hasImage($entity, $field='image') {
    return $entity->hasField($field)             // if it has a field
      && $entity->$field                         // that is not empty
      && $entity->$field->entity                 // and references a (media) entity
      && $entity->$field->entity->image          // with an image field
      && $entity->$field->entity->image->entity; // that resolves to a file entity
  }

  public static function getImageMedia($entity, $field='image') {
    return self::hasImage($entity, $field) ? $entity->$field->entity : null;
  }
}
