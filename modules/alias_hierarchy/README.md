## Description

This module provides a [pathauto](https://www.drupal.org/project/pathauto) pattern that mimics the hierarchy of nodes in the menu system.

## Installation

* Enable the module
* List pathauto patterns at `/admin/config/search/path/patterns`
  * There should be a pattern labeled `Alias Hierarchy`
  * Note the contents of the pattern: `[node:alias-hierarchy-parents:join-path]/[node:alias-hierarchy-alias]`
  * This pattern is necessary for the module to work properly so you won't be able to delete it unless you uninstall the module
* Edit the pattern at `/admin/config/search/path/patterns/alias_hierarchy`
  * For instance apply it to basic pages
  * Note that by default the pattern applies to all content types and all languages
* Create a new page at `/node/add/page`
  * Instead of the core fieldset `URL Alias` you should see two fields [like those](https://www.drupal.org/files/project-images/node_ui.png)
  * `Path prefix` is always read-only and is derived from the node's parents in the menu system
  * `Custom alias` allows the end-user to customize the last part of the URL alias ; if no custom alias is provided then one is automatically generated from the menu link title (if one is provided) or from the node title

## Example

Say you have three nodes organized into the following hierarchy in the menu system:

```
<main-menu>
-- Our organization (node/1)
---- Our department (node/2)
------ Our team (node/3)
```

Then this module will automatically generate the following aliases:
* /node/1: /our-organization
* /node/2: /our-organization/our-department
* /node/3: /our-organization/our-department/our-team

## Migration from Drupal 7

An upgrade path from Drupal 7 is provided for migrations using
[Migrate Plus](https://www.drupal.org/project/migrate_plus). A source field
`alias_hierarchy_custom_alias` is provided for all Node migrations and should
be mapped to the destination field like so:

```yml
...
process:
  ...
  alias_hierarchy_custom_alias: alias_hierarchy_custom_alias
...
```

Because of [https://www.drupal.org/project/pathauto/issues/3016532](https://www.drupal.org/project/pathauto/issues/3016532),
you may want to run cron after migrating both custom aliases and menu links.

## Limitations

Because of [#3016532](https://www.drupal.org/project/pathauto/issues/3016532), URL aliases will not automatically update when making changes via the menu interface (`/admin/structure/menu`). As a workaround they will update on cron. Note that the cron tasks sets a batch operation and as a result will not be processed if cron is triggered by a webhook.

Saving the pathauto pattern at `/admin/config/search/path/patterns/alias_hierarchy` will clear cached field definitions so the right fields show up for each bundle when creating / editing nodes. If the bundle selection criteria are changed in another way (e.g. programatically) instead of saving this form, then Alias Hierarchy may not kick in until the field definition caches are cleared.
