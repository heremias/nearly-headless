<?php

namespace Drupal\osu_core\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class AliasedReportForm extends FormBase
{
  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'aliased_report_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $store = \Drupal::service('user.private_tempstore')->get('osu_core');
    $paths = $store->get('aliased_report_paths');

    $form['paths'] = array(
      '#type' => 'textarea',
      '#title' => t('Paths to check, one per line'),
      '#default_value' => $paths,
      '#required' => TRUE,
      '#description' => 'Do not include domain.<br>/my/awesome/path.html<br>/even/more/awesome/path'
    );
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Query'),
      '#button_type' => 'primary',
    );

    $unmatched = $this->unmatched($paths);
    $form['unmatched'] = [
      '#theme' => 'item_list',
      '#list_type' => 'ul',
      '#title' => count ($unmatched) . ' Missing Paths',
      '#items' => $unmatched,
      '#weight' => 100
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $paths = $form_state->getValue('paths');
    \Drupal::messenger()->addMessage(count($this->unmatched($paths)) . ' unmatched paths');

    $store = \Drupal::service('user.private_tempstore')->get('osu_core');
    $store->set('aliased_report_paths', $paths);
  }

  public function unmatched($paths) {
    /** @var \Drupal\Core\Database\Connection $connection */
    $connection = \Drupal::database();

    // Only check english.
    $aliases = $connection->query("SELECT a.alias, a.source FROM {url_alias} a ")->fetchAllKeyed();
    $redirects = $connection->query("SELECT CONCAT('/', redirect_source__path), rid FROM {redirect} ")->fetchAllKeyed();

    $paths = preg_split("/\s+/", $paths);

    $unmatched = [];
    foreach ($paths as $path) {
      // If NOT (alias or redirect)
      if (!(isset($aliases[$path]) || isset($redirects[$path]))) {
        $unmatched[] = $path;
      }
    }
    return $unmatched;
  }
}
