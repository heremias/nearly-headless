<?php

namespace Drupal\osu_siteinfo\Form;

use Drupal\Component\Utility\Unicode;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Configure Google_Analytics settings for this site.
 */
class InfoForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'osu_siteinfo_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['osu_siteinfo.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('osu_siteinfo.settings');

    $form['intro'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => $this->t('The information below may be used in the footer, header, or other functional areas of your site'),
    ];
    $form['organization'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Organization Name'),
      '#description' => $this->t("Name of your organization. Example: \"Department of Mathematics\"" ),
      '#default_value' => $config->get('organization'),
      '#required' => FALSE,
      '#size' => 30,
    ];
    $form['address1'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Address Line 1 / Building and Room'),
      '#description' => $this->t("Often a building and/or room number" ),
      '#default_value' => $config->get('address1'),
      '#required' => FALSE,
      '#size' => 30,
    ];
    $form['address2'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Address Line 2 / Street Address'),
      '#description' => $this->t("Usually a full street address" ),
      '#default_value' => $config->get('address2'),
      '#required' => FALSE,
      '#size' => 30,
    ];
    $form['city'] = [
      '#type' => 'textfield',
      '#title' => $this->t('City'),
      '#description' => $this->t("Name of the city" ),
      '#default_value' => $config->get('city'),
      '#required' => FALSE,
      '#size' => 15,
    ];
    $form['state'] = [
      '#type' => 'textfield',
      '#title' => $this->t('State'),
      '#description' => $this->t("Two letter code for state or province" ),
      '#default_value' => $config->get('state'),
      '#required' => FALSE,
      '#size' => 2,
    ];
    $form['postal'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Postal/Zip Code'),
      '#description' => $this->t("5 character zip code" ),
      '#default_value' => $config->get('postal'),
      '#required' => FALSE,
      '#size' => 10,
    ];
    $form['phone'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Phone'),
      '#description' => "Full phone number <br>Example: 614-555-5555",
      '#default_value' => $config->get('phone'),
      '#required' => FALSE,
      '#size' => 15,
    ];
    $form['email'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Email Address (Public)'),
      '#description' => "A public email address that can be displayed prominently on the site",
      '#default_value' => $config->get('email'),
      '#required' => FALSE,
      '#size' => 30,
    ];

    $form['twitter_handle'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Twitter Handle'),
      '#description' => "Twitter username without @",
      '#default_value' => $config->get('twitter_handle'),
      '#required' => FALSE,
      '#size' => 30,
    ];

    if ($this->currentUser()->hasPermission('administer osu siteinfo publisher')) {
      $form['publisher'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Publisher'),
        '#description' => "Code identifying the organization publishing this content. UMAR for marketing",
        '#default_value' => $config->get('publisher'),
        '#required' => FALSE,
        '#size' => 30,
      ];
    }

    $form['social_image_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Social Image Id'),
      '#description' => "An integer representing the id of an image to use as the default on shares.",
      '#default_value' => $config->get('social_image_id'),
      '#required' => FALSE,
      '#size' => 10,
    ];

    $form['facebook_link'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Facebook link'),
      '#description' => "Link to your facebook page. Defaults to <a href='https://www.facebook.com/osu'>www.facebook.com/osu</a>.",
      '#default_value' => $config->get('facebook_link'),
      '#required' => FALSE,
      '#size' => 30,
    ];

    $form['twitter_link'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Twitter link'),
      '#description' => "Link to your Twitter page. Defaults to <a href='https://www.twitter.com/ohiostate' >www.twitter.com/ohiostate</a>.",
      '#default_value' => $config->get('twitter_link'),
      '#required' => FALSE,
      '#size' => 30,
    ];

    $form['instagram_link'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Instagram link'),
      '#description' => "Link to your Instagram page. Defaults to <a href='https://instagram.com/theohiostateuniversity'>instagram.com/theohiostateuniversity</a>.",
      '#default_value' => $config->get('instagram_link'),
      '#required' => FALSE,
      '#size' => 30,
    ];

    $form['linkedin_link'] = [
      '#type' => 'textfield',
      '#title' => $this->t('LinkedIn link'),
      '#description' => "Link to your LinkedIn page. Defaults to <a href='https://www.linkedin.com/school/the-ohio-state-university/'>www.linkedin.com/school/the-ohio-state-university</a>.",
      '#default_value' => $config->get('linkedin_link'),
      '#required' => FALSE,
      '#size' => 30,
    ];

    $form['youtube_link'] = [
      '#type' => 'textfield',
      '#title' => $this->t('YouTube link'),
      '#description' => "Link to your YouTube page. Defaults to <a href='https://www.youtube.com/user/OhioStateUniversity'>www.youtube.com/user/OhioStateUniversity</a>.",
      '#default_value' => $config->get('youtube_link'),
      '#required' => FALSE,
      '#size' => 30,
    ];
    
    $form['ada_unit'] = [
      '#type' => 'textfield',
      '#title' => $this->t('ADA Unit Name'),
      '#description' => "Name of your unit for ADA purposes. Defaults to 'the ADA Coordinator's Office'.",
      '#default_value' => $config->get('ada_unit'),
      '#required' => FALSE,
      '#size' => 40,
    ];

    $form['ada_email'] = [
      '#type' => 'email',
      '#title' => $this->t('ADA Unit Email Address'),
      '#description' => "Contact email of your unit for ADA purposes. Defaults to 'ada-osu@osu.edu'.",
      '#default_value' => $config->get('ada_email'),
      '#required' => FALSE,
      '#size' => 20,
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('osu_siteinfo.settings');
    foreach ($form_state->getValues() as $key => $value) {
      $config->set($key, $value);
    }
    $config->save();

    if ($this->currentUser()->hasPermission('administer osu siteinfo publisher')) {
      $config
        ->set('publisher', $form_state->getValue('publisher'))
        ->save();
    }
    parent::submitForm($form, $form_state);
    drupal_flush_all_caches();
  }

}
