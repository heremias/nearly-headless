<?php
/*

https://git.drupalcode.org/project/samlauth/blob/8.x-3.x/src/EventSubscriber/UserSyncEventSubscriber.php
*/
namespace Drupal\osu_core\EventSubscriber;

use Drupal\user\UserInterface;
use Drupal\externalauth\Event\ExternalAuthEvents;
use Drupal\externalauth\Event\ExternalAuthLoginEvent;
use Drupal\http_client_manager\HttpClientManagerFactoryInterface;
use \GuzzleHttp\ClientInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Psr\Log\LoggerInterface;

/**
 * Event subscriber subscribing to ExternalAuthEvents.
 */
class ExternalAuthSubscriber implements EventSubscriberInterface {

  /**
   * The HTTP client to fetch the feed data with.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * A logger instance.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * {@inheritdoc}
   *
   * @param \GuzzleHttp\ClientInterface $http_client
   *   A Guzzle client object.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   */
  public function __construct(ClientInterface $http_client, LoggerInterface $logger) {
  	$this->httpClient = $http_client;
    $this->logger = $logger;
  }

  /**
   * React on an ExternalAuth login event.
   *
   * @param \Drupal\externalauth\Event\ExternalAuthLoginEvent $event
   *   The subscribed event.
   */
  public function externalAuthLogin(ExternalAuthLoginEvent $event) {
    $account = $event->getAccount();

    try {
      $isDeveloper = self::isDeveloper($account->getAccountName());

      // Track changes for logging.
      $added = [];
      $removed = [];

      // Remove roles a current/transitioning developers shouldn't have.
      if ($account->hasRole('site_developer') || $isDeveloper) {
        foreach ($account->getRoles() as $role) {
          // The only role we keep are site_developer, authenticated, and anything ending in _sso...
          // and we lose developer if they are transitioning.
          $protected = in_array($role, ['authenticated', 'site_developer']) || preg_match('/\_sso$/', $role);
          $transitioning = !$isDeveloper && ($role == 'site_developer');
          if (!$protected || $transitioning) {
            $account->removeRole($role);
            $removed[] = $role;
          }
        }
      }

      // Add a site developer role if appropriate.
      if ($isDeveloper && !$account->hasRole('site_developer')) {
        $account->addRole('site_developer');
        $added[] = 'site_developer';
      }

      // Persist the changes.
      $account->save();

      // Log any additions or removals.
      if (count($added) > 0) {
        $this->logger->info('Added roles (%added) for user (%user)', [
          '%added' => implode(', ', $added),
          '%user' => $account->getAccountName(),
        ]);
      }
      if (count($removed) > 0) {
        $this->logger->info('Removed roles (%removed) for user (%user)', [
          '%removed' => implode(', ', $removed),
          '%user' => $account->getAccountName(),
        ]);
      }

    }
    catch (\Exception $e) {
      // If an excpetion occurs, like because the service is down,
      // we just log it and continue.
      $this->logger->error('Failed checking central role db (%message)', [
        '%message' => $e->getMessage()
      ]);
    }
  }

  public function isDeveloper($username) {
    // Initialize to false.
    $isDeveloper = FALSE;

    // Build parameters
    $params = [
      'user' => $username,
      'grants' => implode(',', self::keyedRoles())
    ];
    $url = 'https://newmedia.osu.edu/api/authorizer/?' . http_build_query($params);

    // Send request and parse response.
    $request = $this->httpClient->get($url);
    $grants = json_decode($request->getBody());

    // If the service is failing to generate good json
    // we throw an exception.
    if (is_null($grants)) {
      throw new \Exception('Authorizer service returned invalid json.');
    }

    // What we get back should be a subset of the grants
    // we sent in our query so we check against what we're expecting.
    foreach (self::keyedRoles() as $grant) {
      $isDeveloper = $isDeveloper || in_array($grant, $grants);
    }
    return $isDeveloper;
  }

  public static function roles() {
    return ['site_developer'];
  }

  public static function keys() {
    return [
      'drupal',
      \Drupal::installProfile()
    ];
  }

  public static function keyedRoles() {
    $keyedRoles = [];
    foreach (self::keys() as $key) {
      foreach (self::roles() as $role) {
        $keyedRoles[] = "{$key}.{$role}";
      }
    }
    return $keyedRoles;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[ExternalAuthEvents::LOGIN][] = ['externalAuthLogin'];
    return $events;
  }

}
