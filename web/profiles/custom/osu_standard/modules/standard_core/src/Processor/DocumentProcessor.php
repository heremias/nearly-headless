<?php

namespace Drupal\standard_core\Processor;
use Drupal\standard_core\Processor\ProcessorBase;

class DocumentProcessor extends ProcessorBase {

  public static function processDocument($document) {
    if ($document->file && $document->file->entity) {
      $guesser = \Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesser::getInstance();
      $doc = [
        'extension' => $guesser->guess($document->file->entity->getMimeType()),
        'mime' => $document->file->entity->getMimeType(),
        'name' => $document->label(),
        'size' => self::formatSize($document->file->entity->getSize()),
        'url' => $document->toUrl(),
      ];
    } else {
      $doc = [
        'extension' => null,
        'mime' => null,
        'name' => $document->label(),
        'size' => null,
        'url' => $document->toUrl(),
      ];
    }

    return $doc;
  }

  public static function formatSize($bytes) {
    // Drupal's format_size does heavy lifting but unfortunately doesn't support our rounding and throws in a space.
    $formatted_size = format_size($bytes);

    // Lets carefully (it's translatable) pull apart the number and string.
    $parts = explode(' ', $formatted_size);
    if ((count($parts) == 2) && is_numeric($parts[0])) {
      $size = floatval($parts[0]);
      if ($parts[1] == 'KB') {
        $size = round($size, 0);
      }
      else {
        $size = round($size, 1);
      }
      $formatted_size = "{$size}{$parts[1]}";
    }
    return $formatted_size;
  }
}
