<?php

namespace Drupal\osu_audio_stories\Form;

use Drupal\Component\Utility\Unicode;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Configure Google_Analytics settings for this site.
 */
class ListingConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'osu_audio_stories_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['osu_audio_stories.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('osu_audio_stories.settings');

    $form['title'] = [
      '#default_value' => $config->get('title'),
      '#placeholder' => "",
      '#description' => htmlentities("The title for the listing page."),
      '#required' => FALSE,
      '#size' => 30,
      '#title' => $this->t('Title'),
      '#type' => 'textfield',
    ];

    $form['introduction'] = [
      '#default_value' => $config->get('introduction'),
      '#placeholder' => "",
      '#description' => 'An introductory paragraph for the lisitng page.<br />' . htmlentities("Allowed tags: <p>, <a>"),
      '#required' => FALSE,
      '#size' => 15,
      '#title' => $this->t('Introduction'),
      '#type' => 'textarea',
    ];

    $form['back'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => 'Back to the <a href="/stories/audio">audio listing</a>',
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('osu_audio_stories.settings');
    $config
      ->set('title', $form_state->getValue('title'))
      ->set('introduction', $form_state->getValue('introduction'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
