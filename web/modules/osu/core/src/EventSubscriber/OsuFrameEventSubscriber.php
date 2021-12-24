<?php

namespace Drupal\osu_core\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;


class OsuFrameEventSubscriber implements EventSubscriberInterface {

  /**
   * Set header 'Content-Security-Policy' to response to allow embedding in iFrame.
   */
  public function setHeaderContentSecurityPolicy(FilterResponseEvent $event) {
    $response = $event->getResponse();
    $response->headers->remove('X-Frame-Options');
    $response->headers->set('Content-Security-Policy', "frame-ancestors 'self' proofhq.com *.proofhq.com osu.attask-ondemand.com webcapture.int.proofhq.com *.attask-ondemand.com workfront.com *.workfront.com", FALSE);
  }

  /**
   * {@inheritdoc}
   */
  static function getSubscribedEvents() {
    // Response: set header content security policy
    $events[KernelEvents::RESPONSE][] = ['setHeaderContentSecurityPolicy', -10];
    return $events;
  }

}