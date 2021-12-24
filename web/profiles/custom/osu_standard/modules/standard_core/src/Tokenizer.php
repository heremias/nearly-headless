<?php

namespace Drupal\standard_core;
use Drupal\image\Entity\ImageStyle;
use Drupal\core\Entity\ContentEntityInterface;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;

/**
 * Class Tokenizer
 * Creates tokens for use in metatag module, often using several fallback fields.
 * @package Drupal\standard_core
 */
class Tokenizer {

  /**
   * Defines tokens supplied by this module.
   * @return array
   */
  public static function info() {
    $standard['standard_title'] = [
      'name' => t("Standard Title"),
      'description' => t('The "meta title" field, title field if absent.'),
    ];
    $standard['standard_seo_title'] = [
      'name' => t("Standard SEO Title"),
      'description' => t('The "meta title" field, title field if absent, optionally appended with pipe text'),
    ];
    $standard['standard_description'] = [
      'name' => t("Standard Description"),
      'description' => t('The meta description, summary, and then introduction field if absent'),
    ];
    $standard['standard_image_path'] = [
      'name' => t("Standard Image Path"),
      'description' => t('Page or site image to be used for sharing.'),
    ];
    $standard['standard_image_alt'] = [
      'name' => t("Standard Image Alt"),
      'description' => t('Alt of page or site image to be used for sharing.'),
    ];

    return [
      'types' => [
        'node' => [
          'name' => t('Nodes'),
          'description' => t('Tokens related to individual content items, or "nodes".'),
          'needs-data' => 'node',
        ],
        'term' => [
          'name' => t('Taxonomy terms'),
          'description' => t('Tokens related to individual taxonomy terms.'),
          'needs-data' => 'term',
        ],
        'user' => [
          'name' => t('Users'),
          'description' => t('Tokens related to individual users.'),
          'needs-data' => 'user',
        ],
      ],
      'tokens' => [
        'node' => $standard,
        'term' => $standard,
        'user' => $standard,
        'current-page' => [
          'standard_seo_title' => $standard['standard_seo_title']
        ]
      ]
    ];
  }

  /**
   * Implements hook_tokens().
   */
  public static function tokens($type, $tokens, $data, $options, $bubbleable_metadata) {

    $replacements = [];

    $entity = NULL;
    if ($type == 'node' && !empty($data['node'])) {
      $entity = $data['node'];
    }
    else if ($type == 'term' && !empty($data['term'])) {
      $entity = $data['term'];
    }
    else if ($type == 'user' && !empty($data['user'])) {
      $entity = $data['user'];
    }

    foreach ($tokens as $name => $original) {
      if ($entity) {
        switch ($name) {
          case 'standard_title':
            $replacements[$original] = self::title($entity);
            break;
          case 'standard_seo_title':
            $replacements[$original] = self::seo_title($entity);
            break;
          case 'standard_description':
            $replacements[$original] = self::description($entity);
            break;
          case 'standard_image_alt':
            $replacements[$original] = self::image_alt($entity);
            break;
          case 'standard_image_path':
            $replacements[$original] = self::image_path($entity);
            break;
        }
      }
      else if ($type == 'current-page') {
        switch($name) {
          case 'standard_seo_title':
            $replacements[$original] = self::current_page_seo_title();
            break;
        }
      }
    }
    return $replacements;
  }

  public static function current_page_seo_title() {
    // Get site settings for pipe text.
    $config = \Drupal::config('standard_core.developer_settings');
    $pipe = $config->get('pipe_text') ? $config->get('pipe_text') : 'The Ohio State University';
    $addfound = $config->get('pipe_text_add_if_found') ?  $config->get('pipe_text_add_if_found') : 'no';
    $behavior = $config->get('pipe_text_behavior') ?  $config->get('pipe_text_behavior') : 'all';

    $request = \Drupal::request();
    $route = $request->attributes->get(RouteObjectInterface::ROUTE_OBJECT);
    $title = $pipe;
    if ($route) {
      $title = \Drupal::service('title_resolver')->getTitle($request, $route);

      // If we're in a mode that adds pipe text to page titles.
      if (in_array($behavior, ['page', 'all'])) {
        // If we add to existing pipe text or if no existing pipe text is found.
        if (($addfound == 'yes') || !strstr($title, '|')) {
          $title .= " | {$pipe}";
        }
      }
    }
    return $title;
  }

  /**
   * Returns the best title for an entity for sharing.
   * 1) meta_title, 2) title
   * @param $entity
   * @return string
   */
  public static function title(ContentEntityInterface $entity) {
    if ($entity->meta_title && strlen($entity->meta_title->value) > 0) {
      $title = $entity->meta_title->value;
    }
    else {
      $title = $entity->label();
    }
    return $title;
  }

  /**
   * Returns the best title for an entity for sharing.
   * 1) meta_title, 2) title, followed by pipe text.
   * @param $entity
   * @return string
   */
  public static function seo_title(ContentEntityInterface $entity) {

    // Get site settings for pipe text.
    $config = \Drupal::config('standard_core.developer_settings');
    $pipe = $config->get('pipe_text') ? $config->get('pipe_text') : 'The Ohio State University';
    $addfound = $config->get('pipe_text_add_if_found') ?  $config->get('pipe_text_add_if_found') : 'no';
    $behavior = $config->get('pipe_text_behavior') ?  $config->get('pipe_text_behavior') : 'all';

    if ($entity->meta_title && strlen($entity->meta_title->value) > 0) {
      $title = $entity->meta_title->value;

      // If we're in a mode that adds pipe text to meta titles.
      if ($behavior == 'all') {
        // If we add to existing pipe text or if no existing pipe text is found.
        if (($addfound == 'yes') || !strstr($title, '|')) {
          $title .= " | {$pipe}";
        }
      }
    }
    else {
      $title = $entity->label();

      // If we're in a mode that adds pipe text to page titles.
      if (in_array($behavior, ['page', 'all'])) {
        // If we add to existing pipe text or if no existing pipe text is found.
        if (($addfound == 'yes') || !strstr($title, '|')) {
          $title .= " | {$pipe}";
        }
      }
    }
    return $title;
  }

  /**
   * Returns the best description for an entity.
   * 1) summary, 2) introduction
   * @param $entity
   * @return string
   */
  public static function description(ContentEntityInterface $entity) {

    $description = '';

    $candidates = [
      'summary' => 'plain',
      'intro' => 'plain',
      'introduction' => 'rich',
      'body' => 'rich'
    ];

    foreach ($candidates as $field => $type) {
      if ($description == '') {
        if ($entity->hasField($field)) {
          if ($type == 'rich') {
            $value = check_markup($entity->get($field)->value, $entity->get($field)->format);

            // We want a plain text only variant, not sure if there are other options.
            $value = \Drupal\Core\Mail\MailFormatHelper::htmlToText($value);

            // Break on a word around the 200th character.
            $pos = strpos($value, ' ', strlen($value) > 200 ? 200 : strlen($value));
            $value = substr($value,0, $pos ? $pos : strlen($value));
          }
          else {
            $value = $entity->get($field)->value;
          }
          $value = trim($value);
          $description = $value ? $value : '';
        }
      }
    }
    return $description;
  }

  /**
   * Returns the best image for an entity.
   * 1) teaser image, 2) site image
   * @param $entity
   */
  public static function image_path(ContentEntityInterface $entity) {
    // This should probably be initialized to something generic.
    $image = self::image($entity);
    if ($image) {
      $uri = $image->image->entity->getFileUri();
      $image_path = file_url_transform_relative(ImageStyle::load('social_large')->buildUrl($uri));
    }
    else {
      $image_path = '';
    }
    return $image_path;
  }

  public static function image_alt(ContentEntityInterface $entity) {
    $image = self::image($entity);
    if ($image) {
      $uri = $image->image->entity->getFileUri();
      $image_path = file_url_transform_relative(ImageStyle::load('social_large')->buildUrl($uri));
    }
  }

  public static function image(ContentEntityInterface $entity) {
    $image = NULL;
    if ($entity->teaser_image
      && $entity->teaser_image->entity
      && $entity->teaser_image->entity->image
      && $entity->teaser_image->entity->image->entity) {

      $image = $entity->teaser_image->entity;
    }
    else {
      $config = \Drupal::config('osu_siteinfo.settings');
      $id = $config->get('social_image_id');
      if ($id) {
        $media = \Drupal::entityTypeManager()->getStorage('media')->load($id);
        if ($media && $media->image && $media->image->entity) {
          $image = $media;
        }
      }
    }
    return $image;
  }

}
