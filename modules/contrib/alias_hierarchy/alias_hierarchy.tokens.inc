<?php

/**
 * @file
 * Integration of alias_hierarchy with tokens.
 *
 * @codeCoverageIgnore Covered by functional tests.
 */

use Drupal\node\Entity\Node;
use Drupal\Core\Render\BubbleableMetadata;

/**
 * Implements hook_token_info().
 *
 * Register our tokens.
 */
function alias_hierarchy_token_info(): array {
  $tokens['alias-hierarchy-alias'] = [
    'name' => t('Alias Hierarchy: alias'),
    'description' => t('In order of precedence: custom alias, menu link title, node title.'),
    'type' => 'string',
  ];
  $tokens['alias-hierarchy-parents'] = [
    'name' => t('Alias Hierarchy: parents'),
    'description' => t('Array of parent aliases (as defined by the menu hierarchy)'),
    'type' => 'array',
  ];
  return [
    'tokens' => ['node' => $tokens],
  ];
}

/**
 * Implements hook_tokens().
 *
 * Implement our tokens.
 */
function alias_hierarchy_tokens($type, $tokens, array $data, array $options, BubbleableMetadata $bubbleable_metadata): array {
  $replacements = [];

  if ($type == 'node' && !empty($data['node'])) {
    /** @var Drupal\node\Entity\Node */
    $node = $data['node'];
    foreach ($tokens as $name => $original) {
      switch ($name) {
        case 'alias-hierarchy-alias':
          $replacements[$original] = alias_hierarchy_get_node_alias($node);
          break;

        case 'alias-hierarchy-parents:join-path':
          $menu_link = menu_ui_get_menu_link_defaults($node);
          if (!empty($menu_link['id'])) {
            $parents = alias_hierarchy_get_parents($menu_link['id']);
            // Ideally we should leverage the token 'join-path' provided by
            // pathauto, by somehow it does not get picked up so we just piggy
            // back the pathauto logic.
            // @see pathauto.tokens.inc
            $values = [];
            foreach ($parents as $parent) {
              $values[] = \Drupal::service('pathauto.alias_cleaner')->cleanString($parent, $options);
            }
            $replacements[$original] = implode('/', $values);
            // The aliases are updated on cron so adding the whole menu tree as
            // a cache dependency to $bubbleable_metadata does not seem to be
            // necessary.
          }
          else {
            // No parent item in menu hierarchy.
            $replacements[$original] = '';
          }
          break;
      }
    }
  }
  return $replacements;
}

/**
 * Get alias of a node.
 *
 * Returns, in order of precedence:
 * 1) node's custom alias (field provided by this module) ;
 * 2) node's menu link title ;
 * 3) node title.
 *
 * @param Drupal\node\Entity\Node $node
 *   Node.
 *
 * @return string
 *   Alias.
 */
function alias_hierarchy_get_node_alias(Node $node): string {
  // If a custom alias was provided, then use it.
  $custom_alias = $node->get('alias_hierarchy_custom_alias')->getString();
  if (!empty($custom_alias)) {
    return $custom_alias;
  }

  // Otherwise if a menu link was provided, then use it.
  $menu_link = menu_ui_get_menu_link_defaults($node);
  if (!empty($menu_link['title'])) {
    return $menu_link['title'];
  }

  // Last resort: use the node title (which is a mandatory field and as a result
  // is guaranteed to not be empty).
  return $node->getTitle();
}

/**
 * Get alias of a menu link.
 *
 * @param string $menu_link_id
 *   Menu link plugin ID.
 *
 * @return string
 *   Alias.
 */
function alias_hierarchy_get_menu_link_alias($menu_link_id): string {
  /** @var Drupal\Core\Menu\MenuTreeStorage */
  $menu_tree_storage = \Drupal::service('menu.tree_storage');
  $link = $menu_tree_storage->load($menu_link_id);

  // If the menu link points to a node, then we can re-use the rules from
  // alias_hierarchy_get_node_alias().
  if ($link['route_name'] == 'entity.node.canonical') {
    $nid = $link['route_parameters']['node'];
    $node = Node::load($nid);
    return empty($node) ? '' : alias_hierarchy_get_node_alias($node);
  }

  // Otherwise, just return the menu link title.
  // If a custom alias was provided, then use it.
  return $link['title'];
}

/**
 * Get alias of parents (as defined by the menu hierarchy).
 *
 * @param string $plugin_id
 *   Menu link plugin ID.
 *
 * @return array
 *   Alias of parents.
 */
function alias_hierarchy_get_parents($plugin_id): array {
  /** @var Drupal\Core\Menu\MenuLinkManager */
  $menu_link_manager = \Drupal::service('plugin.manager.menu.link');
  $parent_ids = array_reverse($menu_link_manager->getParentIds($plugin_id));

  // Remove last parent, which is the node itself.
  array_pop($parent_ids);

  $parent_aliases = [];
  foreach ($parent_ids as $parent_id) {
    // Do not load menu items via $menu_tree_storage::loadMultiple() so we
    // maintain the order of $parent_ids.
    $parent_aliases[] = alias_hierarchy_get_menu_link_alias($parent_id);
  }
  return $parent_aliases;
}
