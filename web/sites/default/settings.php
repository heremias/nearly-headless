<?php

/**
 * Load services definition file.
 */
$settings['container_yamls'][] = __DIR__ . '/services.yml';

/**
 * Include the Pantheon-specific settings file.
 *
 * n.b. The settings.pantheon.php file makes some changes
 *      that affect all envrionments that this site
 *      exists in.  Always include this file, even in
 *      a local development environment, to ensure that
 *      the site settings remain consistent.
 */
include __DIR__ . "/settings.pantheon.php";

/**
 * Place the config directory outside of the Drupal root.
 */
$config_directories = array(
  'CONFIG_SYNC_DIRECTORY' => dirname(DRUPAL_ROOT) . '/config',
);

/**
 * If there is a local settings file, then include it
 */
$local_settings = __DIR__ . "/settings.local.php";
if (file_exists($local_settings)) {
  include $local_settings;
}

if (isset($_SERVER['PANTHEON_ENVIRONMENT']) && php_sapi_name() != 'cli') {

  // Create a stub to simplify conditionals.
  $secrets = [];

  // Load secrets from secrets file.
  $secrets_file = $_SERVER['HOME'] . '/files/private/secrets.json';
  if (file_exists($secrets_file)) {
    $secrets = json_decode(file_get_contents($secrets_file), 1);
  }

  // Initialize the primary_domain to the current domain.
  $primary_domain = $_SERVER['HTTP_HOST'];

  // If the environment is live and a primary domain exists in the secret, set it.
  if (($_ENV['PANTHEON_ENVIRONMENT'] === 'live') && isset($secrets['primary_domain'])) {

    $primary_domain = $secrets['primary_domain'];
  }

  // If the domain doesn't match the primary and https is not on, redirect.
  if ($_SERVER['HTTP_HOST'] != $primary_domain
    || !isset($_SERVER['HTTP_X_SSL'])
    || $_SERVER['HTTP_X_SSL'] != 'ON' ) {

    # Name transaction "redirect" in New Relic for improved reporting (optional)
    if (extension_loaded('newrelic')) {
      newrelic_name_transaction("redirect");
    }

    header('HTTP/1.0 301 Moved Permanently');
    header('Location: https://'. $primary_domain . $_SERVER['REQUEST_URI']);
    exit();
  }

  // Drupal 8 Trusted Host Settings
  if (is_array($settings)) {

    // Always include the primary domain.
    $trusted = ['^'. preg_quote($primary_domain) .'$'];

    // This shouldn't be neccessary in most cases, but we can load trusted domains from a secret.
    if (isset($secrets['trusted'])) {
      foreach (preg_split('/,', $secrets['trusted']) as $domain) {
        $trusted[] = '^'. preg_quote($domain) . '$';
      }
    }
    $settings['trusted_host_patterns'] = array_unique($trusted);
  }

  // This is necessary for onelogin php-saml.
  if (isset($_SERVER['HTTP_X_SSL']) && $_SERVER['HTTP_X_SSL'] === 'ON') {
    $_SERVER['SERVER_PORT'] = 443;
  }
  else {
    $_SERVER['SERVER_PORT'] = 80;
  }
}

// See https://pantheon.io/docs/environment-indicator/#drupal%3A-environment-indicator
if (!defined('PANTHEON_ENVIRONMENT')) {
   $config['environment_indicator.indicator']['name'] = 'Local';
   $config['environment_indicator.indicator']['bg_color'] = '#2E0854';
   $config['environment_indicator.indicator']['fg_color'] = '#ffffff';
}
// Pantheon Env Specific Config
if (isset($_ENV['PANTHEON_ENVIRONMENT'])) {
   switch ($_ENV['PANTHEON_ENVIRONMENT']) {
     case 'dev':
       $config['environment_indicator.indicator']['name'] = 'Dev';
       $config['environment_indicator.indicator']['bg_color'] = '#d25e0f';
       $config['environment_indicator.indicator']['fg_color'] = '#ffffff';
       break;
     case 'test':
       $config['environment_indicator.indicator']['name'] = 'Test';
       $config['environment_indicator.indicator']['bg_color'] = '#c50707';
       $config['environment_indicator.indicator']['fg_color'] = '#ffffff';
       break;
     case 'live':
       $config['environment_indicator.indicator']['name'] = '';
       $config['environment_indicator.indicator']['bg_color'] = '#000000';
       $config['environment_indicator.indicator']['fg_color'] = '#ffffff';
       break;
   }
}
