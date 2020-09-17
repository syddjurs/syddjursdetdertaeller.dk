<?php

namespace Drupal\Tests\matomo\Functional;

use Drupal\Core\File\FileSystemInterface;
use Drupal\Tests\BrowserTestBase;

/**
 * Test uninstall functionality of Matomo module.
 *
 * @group Matomo
 */
class MatomoUninstallTest extends BrowserTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['matomo'];

  /**
   * Default theme.
   *
   * @var string
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $permissions = [
      'access administration pages',
      'administer matomo',
      'administer modules',
    ];

    // User to set up matomo.
    $this->admin_user = $this->drupalCreateUser($permissions);
    $this->drupalLogin($this->admin_user);
  }

  /**
   * Tests if the module cleans up the disk on uninstall.
   */
  public function testMatomoUninstall() {
    $cache_path = 'public://matomo';
    $site_id = '1';
    $this->config('matomo.settings')->set('site_id', $site_id)->save();
    $this->config('matomo.settings')->set('url_http', 'http://www.example.com/matomo/')->save();
    $this->config('matomo.settings')->set('url_https', 'https://www.example.com/matomo/')->save();

    // Enable local caching of matomo.js.
    $this->config('matomo.settings')->set('cache', 1)->save();

    // Load front page to get the matomo.js downloaded into local cache. But
    // loading the matomo.js is not possible as "url_http" is a test dummy only.
    // Create a dummy file to complete the rest of the tests.
    \Drupal::service('file_system')->prepareDirectory($cache_path, FileSystemInterface::CREATE_DIRECTORY);
    $data = $this->randomMachineName(128);
    $file_destination = $cache_path . '/matomo.js';
    \Drupal::service('file_system')->saveData($data, $file_destination);
    \Drupal::service('file_system')->saveData(gzencode($data, 9, FORCE_GZIP), $file_destination . '.gz', FileSystemInterface::EXISTS_REPLACE);

    // Test if the directory and matomo.js exists.
    $this->assertTrue(\Drupal::service('file_system')->prepareDirectory($cache_path), 'Cache directory "public://matomo" has been found.');
    $this->assertTrue(file_exists($cache_path . '/matomo.js'), 'Cached matomo.js tracking file has been found.');
    $this->assertTrue(file_exists($cache_path . '/matomo.js.gz'), 'Cached matomo.js.gz tracking file has been found.');

    // Uninstall the module.
    $edit = [];
    $edit['uninstall[matomo]'] = TRUE;
    $this->drupalPostForm('admin/modules/uninstall', $edit, 'Uninstall');
    $this->assertNoText(\Drupal::translation()->translate('Configuration deletions'), 'No configuration deletions listed on the module install confirmation page.');
    $this->drupalPostForm(NULL, NULL, 'Uninstall');
    $this->assertText('The selected modules have been uninstalled.', 'Modules status has been updated.');

    // Test if the directory and all files have been removed.
    $this->assertFalse(\Drupal::service('file_system')->prepareDirectory($cache_path), 'Cache directory "public://matomo" has been removed.');
  }

}
