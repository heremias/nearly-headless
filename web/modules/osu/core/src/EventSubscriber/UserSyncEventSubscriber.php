<?php

namespace Drupal\osu_core\EventSubscriber;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\TypedData\TypedDataManagerInterface;
use Drupal\samlauth\Event\SamlauthEvents;
use Drupal\samlauth\Event\SamlauthUserSyncEvent;
use Egulias\EmailValidator\EmailValidator;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event subscriber that synchronizes user properties on a user_sync event.
 *
 * This is basic module functionality, partially driven by config options. It's
 * split out into an event subscriber so that the logic is easier to tweak for
 * individual sites. (Set message or not? Completely break off login if an
 * account with the same name is found, or continue with a non-renamed account?
 * etc.)
 */
class UserSyncEventSubscriber implements EventSubscriberInterface {

  /**
   * The EntityTypeManager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The typed data manager.
   *
   * @var \Drupal\Core\TypedData\TypedDataManagerInterface
   */
  protected $typedDataManager;

  /**
   * A logger instance.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Construct a new UserSyncSubscriber.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The email validator.
   * @param \Drupal\Core\TypedData\TypedDataManagerInterface $typed_data_manager
   *   The typed data manager.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, TypedDataManagerInterface $typed_data_manager, LoggerInterface $logger) {
    $this->entityTypeManager = $entity_type_manager;
    $this->logger = $logger;
    $this->typedDataManager = $typed_data_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[SamlauthEvents::USER_SYNC][] = ['onUserSync'];
    return $events;
  }

  /**
   * Performs actions to synchronize users with Factory data on login.
   *
   * @param \Drupal\samlauth\Event\SamlauthUserSyncEvent $event
   *   The event.
   */
  public function onUserSync(SamlauthUserSyncEvent $event) {

    // It is possible this account will not be saved yet.
    $account = $event->getAccount();

    // These drupal field names are mapped into saml id's and attributes below.
    $fields = [ 'display_name', 'last_name', 'first_name' ];

    foreach ($fields as $field) {
      $value = $this->getAttributeByFieldName($field, $event);
      if ($value && ($account->get($field)->value != $value)) {
        $account->get($field)->setValue($value);
        $event->markAccountChanged();
      }
    }

    // Sync affiliation.
    $affiliations = [
      'affiliate@osu.edu' => 'affiliate_sso',
      'alum@osu.edu' => 'alumni_sso',
      'employee@osu.edu' => 'employee_sso',
      'faculty@osu.edu' => 'faculty_sso',
      'member@osu.edu' => 'member_sso',
      'staff@osu.edu' => 'staff_sso',
      'student@osu.edu' => 'student_sso',
    ];
    $updates = $this->getAttributeByFieldName('affiliation', $event);
    $added = [];
    $removed = [];
    foreach ($affiliations as $affiliation => $role) {

      // If they don't have the role, and should, add it.
      if (!$account->hasRole($role) && in_array($affiliation, $updates)) {
        $account->addRole($role);
        $event->markAccountChanged();
        $added[] = $role;
      }

      // If they have the role, and shouldn't, remove it.
      if ($account->hasRole($role) && !in_array($affiliation, $updates)) {
        $account->removeRole($role);
        $event->markAccountChanged();
        $removed[] = $role;
      }
    }

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

  public function getAttributeByFieldName($field, SamlauthUserSyncEvent $event) {
    $result = NULL;
    $fields = [
      'display_name' => 'urn:oid:2.16.840.1.113730.3.1.241',
      'last_name' => 'urn:oid:2.5.4.4',
      'first_name' => 'urn:oid:2.5.4.42',
      'affiliation' => 'urn:oid:1.3.6.1.4.1.5923.1.1.1.9',
    ];

    if (array_key_exists($field, $fields)) {
      $samlId = $fields[$field];
      $attributes = $event->getAttributes();

      // Handle multi-value fields differently.
      if (in_array($samlId, ['urn:oid:1.3.6.1.4.1.5923.1.1.1.9'])) {
        $result = is_array($attributes[$samlId]) ? $attributes[$samlId] : [];
      }
      else if (!empty($attributes[$samlId][0])) {
        $result = $attributes[$samlId][0];
      }
    }
    return $result;
  }

}
