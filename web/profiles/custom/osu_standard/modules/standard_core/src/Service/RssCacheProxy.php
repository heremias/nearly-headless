<?php

namespace Drupal\standard_core\Service;

class RssCacheProxy {

  /**
   * Gets a set of items from an rss feed, hitting a cache first.
   * @param $url
   * @return array|mixed|null
   */
  public static function get($url) {
    $items = null;
    try {
      $items = self::uncache($url);
      if (!$items) {
        $items = self::warm($url);
      }
    }
    catch (Exception $e) {
      \Drupal::logger('standard_core')->error($e->getMessage());
    }
    return $items;
  }

  /**
   * Warms the cache, returning results. Suitable for cron.
   * @param $url
   * @return array
   */
  public static function warm($url) {
    $cid = self::cid($url);
    $body = self::fetch($url);
    $items = self::parse($body);
    if ($items) {
      // Valid until an hour from now.
      self::cache($cid, $items, time() + 3600);
    }
    return $items;
  }

  /**
   * Gets the cache id.
   * @param $url
   * @return string
   */
  public static function cid($url) {
    return 'Drupal\standard_core\Service\HttpCacheProxy:' . $url;
  }

  public static function cache($url, $items) {
    $cid = self::cid($url);
    \Drupal::cache()->set($cid, json_encode($items));
  }

  public static function uncache($url) {
    $cid = self::cid($url);
    $items = NULL;
    if ($cache = \Drupal::cache()->get($cid)) {
      $items = json_decode($cache->data);
    }
    return $items;
  }

  /**
   * Fetch a url from site cache then a remote url.
   * @param $url
   * @return |null
   */
  public static function fetch($url) {
    $body = null;
    $client = \Drupal::httpClient();
    $response = $client->get($url);
    $body = $response->getBody();
    return $body;
  }

  public static function parse($body) {
    $stories = [];
    $rss = simplexml_load_string($body);

    if ($rss) {
      $namespaces = $rss->getNamespaces(true);
      $pressPages = isset($namespaces['pp']) && $namespaces['pp'] == 'http://www.presspage.com/rss/';

      // Limit too just three items.
      $parsed = 0;
      $max = 3;

      // foreach (items as item : limit 3)
      if ($rss->channel->item) {
        foreach ($rss->channel->item as $item) {
          $story = [
            'title' => (string) $item->title,
            'url' => (string) $item->link,
          ];

          if ($pressPages) {
            $story = array_merge($story, self::pressPages($item));
          }

          $stories[] = $story;
          $parsed++;
          if ($parsed == $max) {
            break;
          }
        }
      }
    }
    return $stories;
  }

  public static function pressPages($item) {
    return [
      'image' => (string) $item->children('http://www.presspage.com/rss/')->image,
    ];
  }

}
