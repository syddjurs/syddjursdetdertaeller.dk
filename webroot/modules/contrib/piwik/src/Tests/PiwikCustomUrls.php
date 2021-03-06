<?php

namespace Drupal\piwik\Tests;

use Drupal\simpletest\WebTestBase;
use Drupal\Component\Serialization\Json;

/**
 * Test custom url functionality of Piwik module.
 *
 * @group Piwik
 */
class PiwikCustomUrls extends WebTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['piwik'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $permissions = [
      'access administration pages',
      'administer piwik',
      'administer modules',
      'administer site configuration',
    ];

    // User to set up piwik.
    $this->admin_user = $this->drupalCreateUser($permissions);
  }

  /**
   * Tests if user password page urls are overridden.
   */
  public function testPiwikUserPasswordPage() {
    $base_path = base_path();
    $site_id = '1';
    $this->config('piwik.settings')->set('site_id', $site_id)->save();
    $this->config('piwik.settings')->set('url_http', 'http://www.example.com/piwik/')->save();
    $this->config('piwik.settings')->set('url_https', 'https://www.example.com/piwik/')->save();

    $this->drupalGet('user/password', ['query' => ['name' => 'foo']]);
    $this->assertRaw('_paq.push(["setCustomUrl", ' . Json::encode($base_path . 'user/password') . ']);');

    $this->drupalGet('user/password', ['query' => ['name' => 'foo@example.com']]);
    $this->assertRaw('_paq.push(["setCustomUrl", ' . Json::encode($base_path . 'user/password') . ']);');

    $this->drupalGet('user/password');
    $this->assertNoRaw('_paq.push(["setCustomUrl", "', '[testPiwikCustomUrls]: Custom url not set.');
  }

}
