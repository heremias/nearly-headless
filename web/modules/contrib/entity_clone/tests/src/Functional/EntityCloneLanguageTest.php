<?php

namespace Drupal\Tests\entity_clone\Functional;

use Drupal\language\Entity\ConfigurableLanguage;
use Drupal\Tests\BrowserTestBase;

/**
 * Create an language and test a clone.
 *
 * @group entity_clone
 */
class EntityCloneLanguageTest extends BrowserTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['entity_clone', 'language'];

  /**
   * Theme to enable by default
   * @var string
   */
  protected $defaultTheme = 'classy';

  /**
   * Permissions to grant admin user.
   *
   * @var array
   */
  protected $permissions = [
    'administer languages',
    'clone configurable_language entity',
  ];

  /**
   * An administrative user with permission to configure languages settings.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $adminUser;

  /**
   * Sets the test up.
   */
  protected function setUp(): void {
    parent::setUp();

    $this->adminUser = $this->drupalCreateUser($this->permissions);
    $this->drupalLogin($this->adminUser);
  }

  /**
   * Test language entity clone.
   */
  public function testLanguageEntityClone() {
    $edit = [
      'predefined_langcode' => 'fr',
    ];
    $this->drupalPostForm("/admin/config/regional/language/add", $edit, t('Add language'));

    $languages = \Drupal::entityTypeManager()
      ->getStorage('configurable_language')
      ->loadByProperties([
        'id' => 'fr',
      ]);
    $language = reset($languages);

    $edit = [
      'id' => 'test_language_cloned',
      'label' => 'French language cloned',
    ];
    $this->drupalPostForm('entity_clone/configurable_language/' . $language->id(), $edit, t('Clone'));

    $languages = \Drupal::entityTypeManager()
      ->getStorage('configurable_language')
      ->loadByProperties([
        'id' => $edit['id'],
      ]);
    $language = reset($languages);
    $this->assertInstanceOf(ConfigurableLanguage::class, $language, 'Test language cloned found in database.');
  }

}
