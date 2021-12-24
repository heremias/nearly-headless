<?php

namespace Drupal\osu_media\Service;

use Drupal\crop\Entity\Crop;
use Drupal\image\Entity\ImageStyle;

class ImageManager {


  /**
   * Gets the best image style for a particular image, crop, and size.
   */
  public static function getBestStyle($image, $crop, $size) {
    $style = NULL;
    if (Crop::cropExists($image->uri->value, $crop)) {
      // If a crop exists matching the name of the aspect ratio,
      // this will be a manual crop and we should use it.
      $style = ImageStyle::load("{$crop}_{$size}");
    }
    else if ($focal = ImageStyle::load("focal_{$crop}_{$size}")) {
      // Otherwise, if a focal crop style exists, use that.
      $style = $focal;
    }
    else {
      $style = ImageStyle::load("{$crop}_{$size}");
    }
    return $style;
  }

  /**
   * Gets a url with the best possible image style matching
   * the desired aspect ratio and size depending on
   * existing crops and styles.
   * Crops: social, square, standard, widescreen.
   * Sizes: small, medium, large (not all exist)
   */
  public static function getSrc($image, $crop, $size) {
    return self::getBestStyle($image, $crop, $size)->buildUrl($image->uri->value);
  }


  public static function getSrcset($image, $crop, $size) {
    $style = self::getBestStyle($image, $crop, $size);
    $url = $style->buildUrl($image->uri->value);
    $dimensions = ['height' => NULL, 'width' => null];
    $style->transformDimensions($dimensions, $image->uri->value);
    return "{$url} {$dimensions['width']}w";
  }

  /**
   * Returns the alternate text for an image, replacing '[none]' with ''.
   */
  public static function getAlt($image) {
    return $image->alt == '[none]' ? '' : $image->alt;
  }
}
