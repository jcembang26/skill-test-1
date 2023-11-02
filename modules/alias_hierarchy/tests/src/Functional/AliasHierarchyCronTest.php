<?php

namespace Drupal\Tests\alias_hierarchy\Functional;

use Drupal\Tests\BrowserTestBase;
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\Tests\pathauto\Functional\PathautoTestHelperTrait;

/**
 * Test that menu-based aliases are updated by cron when menu hierarchy changed.
 *
 * @group alias_hierarchy
 */
class AliasHierarchyCronTest extends BrowserTestBase {

  use PathautoTestHelperTrait;

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stable';

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'alias_hierarchy',
    'menu_ui',
  ];

  /**
   * User.
   *
   * @var \Drupal\user\Entity\User
   */
  protected $user;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Create content type.
    $this->drupalCreateContentType([
      'type' => 'page',
      'name' => 'Basic page',
      'display_submitted' => FALSE,
    ]);

    // Assign content type a pathauto pattern based on the menu hierarchy.
    $this->createPattern('node', '[node:menu-link:parents:join-path]/[node:menu-link]');

    // Create user and sign in.
    $this->user = $this->drupalCreateUser([
      'access content',
      'administer menu',
      'administer site configuration',
      'create page content',
      'edit any page content',
    ]);
    $this->drupalLogin($this->user);
  }

  /**
   * Create a new node along with a menu item.
   *
   * @param string $title
   *   Node title.
   * @param string $menu_parent
   *   Parent menu item.
   */
  protected function createNodeWithMenuItem($title, $menu_parent): void {
    $edit = [
      'title[0][value]' => $title,
      'menu[enabled]' => 1,
      'menu[title]' => $title,
      'menu[menu_parent]' => $menu_parent,
    ];
    $this->drupalGet('node/add/page');
    $this->submitForm($edit, 'Save');
  }

  /**
   * Helper: get UUID of menu item associated with a node.
   *
   * @param string $title
   *   Node title.
   *
   * @return string
   *   UUID of menu item associated with the node.
   */
  protected function getMenuLinkUuid($title): string {
    $node = $this->getNodeByTitle($title);
    $mlids = \Drupal::entityQuery('menu_link_content')
      ->accessCheck(FALSE)
      ->condition('link.uri', 'entity:node/' . $node->id())
      ->condition('menu_name', 'main')
      ->execute();
    return MenuLinkContent::load(reset($mlids))->uuid();
  }

  /**
   * Helper: run cron.
   */
  protected function runCron(): void {
    $this->drupalGet('admin/config/system/cron');
    $this->submitForm([], 'Run cron');
  }

  /**
   * Updating a menu item from node/%/edit should update its children.
   */
  public function testUpdateParentNodeEdit(): void {
    // Create 3 nodes and put them in a menu hierarchy.
    $this->createNodeWithMenuItem('Parent', 'main:');
    $this->createNodeWithMenuItem('Child', 'main:menu_link_content:' . $this->getMenuLinkUuid('Parent'));
    $this->createNodeWithMenuItem('Grandchild', 'main:menu_link_content:' . $this->getMenuLinkUuid('Child'));

    // Verify the paths follow the menu hierarchy.
    $this->drupalGet('admin/structure/menu/manage/main');
    $this->assertSession()->linkByHrefExists('parent');
    $this->assertSession()->linkByHrefExists('parent/child');
    $this->assertSession()->linkByHrefExists('parent/child/grandchild');

    // Edit child node and move its menu item to the top level of the hierarchy.
    $child = $this->getNodeByTitle('Child');
    $this->drupalGet('node/' . $child->id() . '/edit');
    $edit = [
      'menu[menu_parent]' => 'main:',
    ];
    $this->submitForm($edit, 'Save');

    // Verify new paths:
    // - the parent has not changed
    // - the child is now a top-level item
    // - the grandchild should have been updated, but it has not
    // The point of this module is to fix the grandchild.
    $this->drupalGet('admin/structure/menu/manage/main');
    $this->assertSession()->linkByHrefExists('parent');
    $this->assertSession()->linkByHrefExists('child');
    $this->assertSession()->linkByHrefExists('parent/child/grandchild');

    // Run cron to update the paths.
    $this->runCron();

    // Verify new paths:
    // - the parent has not changed
    // - the child is still a top-level item
    // - the grandchild has been updated.
    $this->drupalGet('admin/structure/menu/manage/main');
    $this->assertSession()->linkByHrefExists('parent');
    $this->assertSession()->linkByHrefExists('child');
    $this->assertSession()->linkByHrefExists('child/grandchild');
  }

  /**
   * Updating a menu item from admin/structure/menu should update its children.
   */
  public function testUpdateParentMenuAdmin(): void {
    // Create 3 nodes and put them in a menu hierarchy.
    $this->createNodeWithMenuItem('Parent', 'main:');
    $this->createNodeWithMenuItem('Child', 'main:menu_link_content:' . $this->getMenuLinkUuid('Parent'));
    $this->createNodeWithMenuItem('Grandchild', 'main:menu_link_content:' . $this->getMenuLinkUuid('Child'));

    // Verify the paths follow the menu hierarchy.
    $this->drupalGet('admin/structure/menu/manage/main');
    $this->assertSession()->linkByHrefExists('parent');
    $this->assertSession()->linkByHrefExists('parent/child');
    $this->assertSession()->linkByHrefExists('parent/child/grandchild');

    // Go to admin/structure/menu and move child menu item to the top level.
    $this->drupalGet('admin/structure/menu/manage/main');
    $this->clickLink('Edit', 1);
    $edit = [
      'menu_parent' => 'main:',
    ];
    $this->submitForm($edit, 'Save');

    // Verify new paths:
    // - the parent has not changed
    // - the child is now a top-level item
    // - the grandchild should have been updated, but it has not
    // The point of this module is to fix the grandchild.
    $this->drupalGet('admin/structure/menu/manage/main');
    $this->assertSession()->linkByHrefExists('parent');
    $this->assertSession()->linkByHrefExists('child');
    $this->assertSession()->linkByHrefExists('parent/child/grandchild');

    // Run cron to update the paths.
    $this->runCron();

    // Verify new paths:
    // - the parent has not changed
    // - the child is still a top-level item
    // - the grandchild has been updated.
    $this->drupalGet('admin/structure/menu/manage/main');
    $this->assertSession()->linkByHrefExists('parent');
    $this->assertSession()->linkByHrefExists('child');
    $this->assertSession()->linkByHrefExists('child/grandchild');
  }

}
