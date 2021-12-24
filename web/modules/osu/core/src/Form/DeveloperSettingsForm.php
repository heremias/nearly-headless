<?php

namespace Drupal\osu_core\Form;

use Drupal\Component\Utility\Unicode;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Configure Google_Analytics settings for this site.
 */
class DeveloperSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'osu_core_developer_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['osu_core.developer_settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('osu_core.developer_settings');

    $form['intro'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => $this->t('We recommend turning auto_redirect on after a site has launched.'),
    ];

    $form['auto_redirect'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Automatically create redirects when URL aliases are changed.'),
      '#default_value' => $config->get('auto_redirect'),
      '#disabled' => !\Drupal::moduleHandler()->moduleExists('path'),
    );

    $form['optimize_container'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Google Optimize Container'),
      '#description' => $this->t('Allows injecting a google optimize container into a datalayer variable used by the global container.'),
      '#default_value' => $config->get('optimize_container'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('osu_core.developer_settings');
    $config
      ->set('auto_redirect', $form_state->getValue('auto_redirect'))
      ->set('optimize_container', $form_state->getValue('optimize_container'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
