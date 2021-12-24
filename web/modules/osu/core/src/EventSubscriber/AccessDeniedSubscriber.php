<?php

namespace Drupal\osu_core\EventSubscriber;

use Drupal\Component\Utility\UrlHelper;
use Drupal\node\NodeInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\EventSubscriber\HttpExceptionSubscriberBase;
use Drupal\Core\Path\PathMatcherInterface;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Component\Utility\Xss;

/**
 * Redirect 403 to User Login if appropriate event subscriber.
 */
class AccessDeniedSubscriber extends HttpExceptionSubscriberBase {

  /**
   * The configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The request stack service.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * The path matcher.
   *
   * @var \Drupal\Core\Path\PathMatcherInterface
   */
  protected $pathMatcher;

  /**
   * An event dispatcher instance to use for map events.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $eventDispatcher;

  /**
   * Constructs a new R4032LoginSubscriber.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The configuration factory.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The request stack service.
   * @param \Drupal\Core\Path\PathMatcherInterface $path_matcher
   *   The path matcher.
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $event_dispatcher
   *   The event dispatcher.
   */
  public function __construct(ConfigFactoryInterface $config_factory, AccountInterface $current_user, RequestStack $request_stack, PathMatcherInterface $path_matcher, EventDispatcherInterface $event_dispatcher) {
    $this->configFactory = $config_factory;
    $this->currentUser = $current_user;
    $this->requestStack = $request_stack;
    $this->pathMatcher = $path_matcher;
    $this->eventDispatcher = $event_dispatcher;
  }

  /**
   * {@inheritdoc}
   */
  protected function getHandledFormats() {
    return ['html'];
  }

  /**
   * Redirects on 403 Access Denied kernel exceptions.
   *
   * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
   *   The Event to process.
   */
  public function on403(GetResponseEvent $event)
  {

    /** @var \Drupal\node\NodeInterface $node */
    $node = $this->requestStack->getCurrentRequest()->get('node');

    /** @var \Drupal\user\UserInterface $account */
    $account = $this->requestStack->getCurrentRequest()->getUser();

    // Only operate on nodes that have the allowed roles field populated for users that can access content overview.
    if ($node
      && ($node->hasField('allowed_roles') && !$node->get('allowed_roles')->isEmpty())
      && (!$this->currentUser || !$this->currentUser->hasPermission('access content overview')))
    {

      // We only want to redirect anonymous users (no loops).
      if (!$this->currentUser || $this->currentUser->isAnonymous()) {
        // @TODO: This should use drupal url building methods.
        $url = \Drupal::request()->getSchemeAndHttpHost()
          . '/saml/login?destination='
          . urlencode(\Drupal::service('path.current')->getPath());

        $response = new TrustedRedirectResponse($url);
        $event->setResponse($response);
      }
    }

  }

}
