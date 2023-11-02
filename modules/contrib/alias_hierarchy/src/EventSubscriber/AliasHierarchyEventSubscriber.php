<?php

namespace Drupal\alias_hierarchy\EventSubscriber;

use Drupal\migrate_plus\Event\MigrateEvents;
use Drupal\node\Plugin\migrate\source\d7\Node;
use Drupal\migrate_plus\Event\MigratePrepareRowEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Alias Hierarchy event subscriber.
 */
class AliasHierarchyEventSubscriber implements EventSubscriberInterface {

  /**
   * React to a row being prepared for migration.
   */
  public function prepareRow(MigratePrepareRowEvent $event): void {
    // Do not bother if the module does not exist on the source site.
    if (!$event->getSource()->getDatabase()->schema()->tableExists('alias_hierarchy')) {
      return;
    }

    // Applies only to node migrations.
    if (!$event->getMigration()->getSourcePlugin() instanceof Node) {
      return;
    }

    // Fetch custom alias from the source site.
    $source_ids = $event->getRow()->getSourceIdValues();
    $alias_hierarchy_custom_alias = $event
      ->getSource()
      ->getDatabase()
      ->select('alias_hierarchy', 'ah')
      ->fields('ah', ['alias'])
      ->condition('nid', $source_ids['nid'])
      ->execute()
      ->fetchField(0);

    // Populate property.
    $event->getRow()->setSourceProperty('alias_hierarchy_custom_alias', $alias_hierarchy_custom_alias);
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = [];
    if (class_exists('\Drupal\migrate_plus\Event\MigrateEvents')) {
      $events[MigrateEvents::PREPARE_ROW] = ['prepareRow'];
    }
    return $events;
  }

}
