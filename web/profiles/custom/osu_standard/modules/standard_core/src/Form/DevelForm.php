<?php

namespace Drupal\standard_core\Form;

use Drupal\Component\Utility\Unicode;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Config\FileStorage;

/**
 * Configure Google_Analytics settings for this site.
 */
class DevelForm extends ConfigFormBase {

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
    return ['standard_core.developer_settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('standard_core.developer_settings');

    $form['intro'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => $this->t('Information here can change the way the site works. Be very careful.'),
    ];

    $form['content_article'] = [
      '#type' => 'details',
      '#title' => "Content Articles",
    ];
    $form['content_article']['content_article_site_tag_vocabs'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Content Article Site Tag Vocabularies'),
      '#description' => $this->t("Enter a comma delimited list of vocabulary machine names which should be targetable for the site_tag field  <br>Example: \"machine_name1,machine_name2\"" ),
      '#default_value' => $config->get('content_article_site_tag_vocabs'),
      '#required' => FALSE,
      '#size' => 30,
    ];

    $form['external_article'] = [
      '#type' => 'details',
      '#title' => "External Articles",
    ];
    $form['external_article']['external_article_site_tag_vocabs'] = [
      '#type' => 'textfield',
      '#title' => $this->t('External Article Site Tag Vocabularies'),
      '#description' => $this->t("Enter a comma delimited list of vocabulary machine names which should be targetable for the site_tag field  <br>Example: \"machine_name1,machine_name2\"" ),
      '#default_value' => $config->get('external_article_site_tag_vocabs'),
      '#required' => FALSE,
      '#size' => 30,
    ];

    $form['content_page'] = [
      '#type' => 'details',
      '#title' => "Content Pages",
    ];
    $form['content_page']['content_page_site_tag_vocabs'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Content Page Site Tag Vocabularies'),
      '#description' => $this->t("Enter a comma delimited list of vocabulary machine names which should be targetable for the site_tag field  <br>Example: \"machine_name1,machine_name2\"" ),
      '#default_value' => $config->get('content_page_site_tag_vocabs'),
      '#required' => FALSE,
      '#size' => 30,
    ];

    $form['practice'] = [
      '#type' => 'details',
      '#title' => "Practices",
    ];
    $form['practice']['practice_site_tag_vocabs'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Practice Site Tag Vocabularies'),
      '#description' => $this->t("Enter a comma delimited list of vocabulary machine names which should be targetable for the site_tag field  <br>Example: \"machine_name1,machine_name2\"" ),
      '#default_value' => $config->get('practice_site_tag_vocabs'),
      '#required' => FALSE,
      '#size' => 30,
    ];

    $form['people'] = [
      '#type' => 'details',
      '#title' => "People",
    ];
    $form['people']['person_site_tag_vocabs'] = [
      '#type' => 'textfield',
      '#title' => $this->t('People Site Tag Vocabularies'),
      '#description' => $this->t("Enter a comma delimited list of vocabulary machine names which should be targetable for the site_tag field  <br>Example: \"machine_name1,machine_name2\"" ),
      '#default_value' => $config->get('person_site_tag_vocabs'),
      '#required' => FALSE,
      '#size' => 30,
    ];
    $form['people']['person_teaser_vocabs'] = [
      '#type' => 'textfield',
      '#title' => $this->t('People Teaser Vocabularies'),
      '#description' => $this->t("Enter a comma delimited list of vocabulary machine names which should appear in teasers  <br>Example: \"machine_name1,machine_name2\"" ),
      '#default_value' => $config->get('person_teaser_vocabs'),
      '#required' => FALSE,
      '#size' => 30,
    ];
    $form['people']['person_full_vocabs'] = [
      '#type' => 'textfield',
      '#title' => $this->t('People Full Vocabularies'),
      '#description' => $this->t("Enter a comma delimited list of vocabulary machine names which should appear in full views  <br>Example: \"machine_name1,machine_name2\"" ),
      '#default_value' => $config->get('person_full_vocabs'),
      '#required' => FALSE,
      '#size' => 30,
    ];

    $form['media'] = [
      '#type' => 'details',
      '#title' => "Media",
    ];
    $form['media']['media_document_max_filesize'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Document File Size Limit'),
      '#description' => $this->t("Enter a number followed by a unit without spaces.<br>The default value is '20M' and Pantheon can't support much more than '90M' due to a post_max_size limit of 100M." ),
      '#default_value' => $config->get('media_document_max_filesize'),
      '#required' => FALSE,
      '#size' => 5,
    ];

    $form['metadata'] = [
      '#type' => 'details',
      '#title' => "Metadata",
    ];
    $form['metadata']['pipe_text_behavior'] = [
      '#type' => 'radios',
      '#title' => $this->t('When should <em>site pipe text</em> be automatically applied?'),
      '#default_value' => $config->get('pipe_text_behavior') ?  $config->get('pipe_text_behavior') : 'all',
      '#options' => [
        'page' => 'To page titles but not meta titles.',
        'all' => 'To both page titles and meta titles (always).',
        'none' => 'To neither page titles nor meta titles (never).'
      ],
      '#description' => $this->t("Typically, the title used in metadata falls back, from meta title (may be entered w\pipe text), to the page's title (never entered w\pipe text)." ),
      '#required' => TRUE,
    ];
    $form['metadata']['pipe_text_add_if_found'] = [
      '#type' => 'radios',
      '#title' => $this->t('If the selected title already includes pipe text, should the <em>site pipe text</em> still be applied?'),
      '#default_value' => $config->get('pipe_text_add_if_found') ?  $config->get('pipe_text_add_if_found') : 'no',
      '#options' => [
        'yes' => 'Yes, add pipe text even if it already exists.',
        'no' => 'No, that\'s just silly.',
      ],
      '#description' => $this->t("Multiple levels of pipe text may be useful in establishing hierarchies but it should be avoided since only 50-55 characters will show." ),
      '#required' => TRUE,
    ];
    $form['metadata']['pipe_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Site Pipe Text / Branded Term'),
      '#description' => $this->t("Subject to the settings above, text you enter here will appear in meta titles following a pipe character '|'. <br />Including pipe text, titles shouldn't exceed 55 characters." ),
      '#default_value' => $config->get('pipe_text') ? $config->get('pipe_text') : 'The Ohio State University',
      '#required' => FALSE,
      '#size' => 25,
    ];

      $form['features'] = [
          '#type' => 'details',
          '#title' => "Features",
      ];

      $form['features']['news'] = [
          '#type' => 'details',
          '#title' => "Articles / News",
          '#open' => TRUE,
      ];
      $form['features']['news']['content_article_editor'] = [
          '#type' => 'checkbox',
          '#title' => $this->t('Content Articles'),
          '#default_value' => $this->roleExists('content_article_editor'),
      ];
      $form['features']['news']['external_article_editor'] = [
          '#type' => 'checkbox',
          '#title' => $this->t('External Articles'),
          '#default_value' => $this->roleExists('external_article_editor'),
      ];

      $form['features']['events'] = [
          '#type' => 'details',
          '#title' => "Events & Meetings",
          '#open' => TRUE,
      ];
      $form['features']['events']['event_editor'] = [
          '#type' => 'checkbox',
          '#title' => $this->t('Events'),
          '#default_value' => $this->roleExists('event_editor'),
      ];
      $form['features']['events']['external_event_editor'] = [
          '#type' => 'checkbox',
          '#title' => $this->t('External Events'),
          '#default_value' => $this->roleExists('external_event_editor'),
      ];
      $form['features']['events']['meeting_editor'] = [
          '#type' => 'checkbox',
          '#title' => $this->t('Meetings'),
          '#default_value' => $this->roleExists('meeting_editor'),
      ];


      $form['features']['webforms'] = [
          '#type' => 'details',
          '#title' => "Webforms",
          '#open' => TRUE,
      ];
      $form['features']['webforms']['webform_basic'] = [
          '#type' => 'checkbox',
          '#title' => $this->t('Basic'),
          '#description' => $this->t("Allows creation of webform content, viewing submissions, but not modifying forms." ),
          '#default_value' => $this->roleExists('webform_basic')
      ];
      $form['features']['webforms']['webform_advanced'] = [
          '#type' => 'checkbox',
          '#title' => $this->t('Advanced'),
          '#description' => $this->t("Allows creation and modification of forms." ),
          '#default_value' => $this->roleExists('webform_advanced'),
      ];

      $form['features']['other'] = [
          '#type' => 'details',
          '#title' => "Other",
          '#open' => TRUE,
      ];
      $form['features']['other']['audio_story_editor'] = [
          '#type' => 'checkbox',
          '#title' => $this->t('Audio Stories'),
          '#default_value' => $this->roleExists('audio_story_editor'),
      ];
      $form['features']['other']['practice_editor'] = [
          '#type' => 'checkbox',
          '#title' => $this->t('Practices'),
          '#default_value' => $this->roleExists('practice_editor')
      ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $roles = [
      'content_article_editor',
      'external_article_editor',
      'event_editor',
      'external_event_editor',
      'meeting_editor',
      'webform_basic',
      'webform_advanced',
      'audio_story_editor',
      'practice_editor'
    ];
    $config = $this->config('standard_core.developer_settings');
    foreach ($form_state->getValues() as $key => $value) {

      if (in_array($key, $roles)) {
        // If it's a role, we enable or disable the role per the setting.
        if ($value) {
          $this->createRole($key);
        }
        else {
          $this->deleteRole($key);
        }
      }
      else {
        // If it's a setting, we store it.
        $config->set($key, $value);
      }
    }
    $config->save();

    parent::submitForm($form, $form_state);
    drupal_flush_all_caches();
  }

  /**
   * Returns true role if it exists.
   * @param $role
   * @return \Drupal\Core\Config\Config|\Drupal\Core\Config\ImmutableConfig
   */
  public function roleExists($role) {
    return is_array($this->config('user.role.' . $role)->get('permissions'));
  }

  /**
   * Creates a role from the config/roles directory.
   * @param $role
   */
  public function createRole($role) {
    $config_path = drupal_get_path('module', 'standard_core') . '/config/roles';
    $source = new FileStorage($config_path);
    if (!$this->roleExists($role)) {
      \Drupal::entityTypeManager()->getStorage('user_role')
        ->create($source->read('user.role.' . $role))
        ->save();
    }
  }

  /**
   * Deletes a role.
   * @param $role
   */
  public function deleteRole($role) {
    \Drupal::configFactory()->getEditable('user.role.' . $role)->delete();
  }

}
