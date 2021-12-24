<?php

namespace Drupal\osu_core\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Url;
use Drupal\externalauth\ExternalAuth;
use Drupal\samlauth\Event\SamlauthEvents;
use Drupal\samlauth\Event\SamlauthUserLinkEvent;
use Drupal\samlauth\Event\SamlauthUserSyncEvent;
use Drupal\user\PrivateTempStoreFactory;
use Drupal\user\UserInterface;
use Exception;
use OneLogin_Saml2_Auth;
use OneLogin_Saml2_Error;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Drupal\key\KeyRepositoryInterface ;
/**
 * Governs communication between the SAML toolkit and the IDP / login behavior.
 */
class SamlService extends \Drupal\samlauth\SamlService {


  /**
   * Override samlauth's config to tweak some things.
   * Note, we're not really changing anything yet but this may be how we inject
   * settings if we do IdP metadata refreshing.
   */
  protected static function reformatConfig(ImmutableConfig $config,  $base_url = '', $purpose = '', KeyRepositoryInterface $key_repository = null) {

    $library_config = parent::reformatConfig($config, $base_url, $purpose, $key_repository);
    return $library_config;
  }

}
