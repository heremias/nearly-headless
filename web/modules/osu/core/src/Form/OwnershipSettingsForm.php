<?php

namespace Drupal\osu_core\Form;

use Drupal\Component\Utility\Unicode;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Configure Google_Analytics settings for this site.
 */
class OwnershipSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'osu_core_search_ownership_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['osu_core.search_ownership'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('osu_core.search_ownership');

    $form['intro'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => $this->t('These meta tags are used to confirm site ownership for search engines and other services.'),
    ];

    $form['google'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Google'),
      '#description' => $this->t("A string provided by <a href=':google'>Google</a>, full details are available from the <a href=':verify_url'>Google online help</a>.", [":google" => "https://www.google.com/", ":verify_url" => "https://support.google.com/webmasters/answer/35179?hl=en"] ),
      '#default_value' => $config->get('google'),
      '#required' => FALSE,
    ];
    $form['bing'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Bing'),
      '#description' => $this->t("A string provided by <a href=':bing'>Bing</a>, full details are available from the <a href=':verify_url'>Bing online help</a>.", [":bing" => "http://www.bing.com/", ":verify_url" => "http://www.bing.com/webmaster/help/how-to-verify-ownership-of-your-site-afcfefc6" ]),
      '#default_value' => $config->get('bing'),
      '#required' => FALSE,
    ];
    $form['baidu'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Baidu'),
      '#description' => $this->t("A string provided by <a href=':baidu'>Baidu</a>.", [":baidu"  => "http://www.baidu.com/"]),
      '#default_value' => $config->get('container_id'),
      '#required' => FALSE,
    ];
    $form['norton_safe_web'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Norton Safe Web'),
      '#description' => $this->t("A string provided by <a href=':norton'>Norton Safe Web</a>, full details are available from the <a href=':verify_url'>Norton Safe Web online help</a>.", [":norton" => "https://safeweb.norton.com/", ":verify_url" => "https://safeweb.norton.com/help/site_owners" ]),
      '#default_value' => $config->get('norton_safe_web'),
      '#required' => FALSE,
    ];
    $form['pinterest'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Pinterest'),
      '#description' => $this->t("A string provided by <a href=':pinterest'>Pinterest</a>, full details are available from the <a href=':verify_url'>Pinterest online help</a>.", [":pinterest" => "https://www.pinterest.com/", ":verify_url" => "https://help.pinterest.com/en/articles/verify-your-website" ]),
      '#default_value' => $config->get('pinterest'),
      '#required' => FALSE,
    ];
    $form['yandex'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Yandex'),
      '#description' => $this->t("A string provided by <a href=':yandex'>Yandex</a>, full details are available from the <a href=':verify_url'>Yandex online help</a>.", [ ":yandex" => "http://www.yandex.com/", ":verify_url" => "http://api.yandex.com/webmaster/doc/dg/reference/hosts-type.xml"]),
      '#default_value' => $config->get('yandex'),
      '#required' => FALSE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('osu_core.search_ownership');
    $config
      ->set('baidu', $form_state->getValue('baidu'))
      ->set('bing', $form_state->getValue('bing'))
      ->set('google', $form_state->getValue('google'))
      ->set('norton_safe_web', $form_state->getValue('norton_safe_web'))
      ->set('pinterest', $form_state->getValue('pinterest'))
      ->set('yandex', $form_state->getValue('yandex'))
      ->save();

    parent::submitForm($form, $form_state);

  }

}
