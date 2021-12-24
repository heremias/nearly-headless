<?php

namespace Drupal\osu_core\Service;

use Drupal\Core\Config\ConfigFactoryOverrideInterface;

/**
* Overrides settings from a terminus secrets file.
*/
class SecretsOverrider extends BaseOverrider implements ConfigFactoryOverrideInterface {

  /**
  * {@inheritdoc}
  */
  public function loadOverrides($names) {
    $overrides = array();
    if (in_array('samlauth.authentication', $names)) {
     $overrides['samlauth.authentication'] = [
       'sp_entity_id' => Secrets::get('samlauth.authentication.sp_entity_id'),
       'sp_x509_certificate' => Secrets::get('samlauth.authentication.sp_x509_certificate'),
       'sp_private_key' => Secrets::get('samlauth.authentication.sp_private_key'),
     ];
    }

    // Must set password from secrets, can set username.
    if (in_array('swiftmailer.transport', $names)) {
      $username = Secrets::get('swiftmailer.transport.smtp_credentials.swiftmailer.username');
      $password = Secrets::get('swiftmailer.transport.smtp_credentials.swiftmailer.password');
      $overrides['swiftmailer.transport']['smtp_credentials']['swiftmailer']['password'] = $password;
      if ($username) {
        $overrides['swiftmailer.transport']['smtp_credentials']['swiftmailer']['username'] = $username;
      }
    }

    return $overrides;
  }

}
