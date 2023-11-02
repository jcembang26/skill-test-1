<?php

namespace Drupal\Tests\alias_hierarchy\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Test permissions for alias_hierarchy.
 *
 * @group alias_hierarchy
 */
class AliasHierarchyPermsTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stable';

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['alias_hierarchy'];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $user = $this->drupalCreateUser(['administer pathauto']);
    $this->drupalLogin($user);
  }

  /**
   * Test users are disallowed to delete our pathauto pattern.
   */
  public function testDeleting(): void {
    // Test our pattern shows up on the pathauto config page.
    $this->drupalGet('admin/config/search/path/patterns');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('Alias Hierarchy');

    // Test 'Delete' button does not show up when editing the pattern.
    $this->drupalGet('admin/config/search/path/patterns/alias_hierarchy');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextNotContains('Delete');

    // Test going to the delete page returns a 403.
    $this->drupalGet('admin/config/search/path/patterns/alias_hierarchy/delete');
    $this->assertSession()->statusCodeEquals(403);
  }

}
