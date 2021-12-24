# Content

The OSU content module set defines many types of types of content
at both granular (fields) and higher (content type) levels.

# Organization
The project is organized primarily by entity type (ie. node) with 
base fields (teaser) and bundles (listicle) in separate folder
hierarchies. 

Within node bundles (listicle), there is a
folder with supplemental modules for displays (teaser, form, full page)
and sample content (how I plan to migrate insights data).

Vocabularies are currently a little different in that we've been
exporting base fields, to reference that vocabulary in node bundles, 
from within the vocabulary (but we should probably stop for 
consistency).

```
content
  node
    fields
      osu_node_body
      osu_node_teaser
      ...
    types
      osu_listicle
        modules
          osu_listicle_content
          osu_listicle_displays
      osu_timeline
      ...
  paragraph
    fields
      osu_pg_body
      osu_pg_content
      ...
    types
      osu_pg_list_item
      osu_pg_simple
      ...
  taxonomy
    vocabularies
      osu_category
      osu_format
      ...

```
## Why separate base field modules?
It's for performance, consistency, and interoperability.
* Every field creates a database table.
* If two content types use the same base field they can be treated identically in views.
* Having a core reusable schema will make content syndication easier.

## Why separate display modules for node content types?
These content types may be extended in site specific fashions.
For example, these content types don't include insight specific
vocabularies because they won't all make sense on other sites.

# Other Conventions

* Prefix module names with "osu"
* Prefix field modules with "osu" and an entity prefix (osu_[pg|node]_teaser)
* Do not prefix bundle or field names within a module.
  * The content type should be "news" not "osu_news"
  * The teaser field should be "teaser" not "field_teaser" (which is drupal's default)
* Use singular content type machine names (event not events).

# How do I...
  
## Use a content type as is

1. Enable the content type module to install schema.
2. Enable the content type's "display" module to load the standard form, page, and teaser displays.
3. Enable the content type's "content" module to see sample content.

Note: If you are relying on our "displays", you may want to include
the [osu_themelab](https://code.osu.edu/ucom/osu_themelab.git) base 
theme.

## Customize a content type by adding fields or changing displays

1. Enable the content type module to install it's schema (do not install companion display module).
2. Add fields to meet your needs.
3. Customize your displays.
4. Export your new content type using features.

## Create/export a content type

1. Build your content type in Drupal using existing fields (make sure they are installed) when possible.
2. Export it using features.
3. Give the fields their own module.
  1. Create a module for each base field.
  2. Copy the "storage" for the field to config/install.
  3. Add the new base field module as a dependency for your content type.
  4. Move any field dependencies from the content type to the field.
4. Give the displays their own module.
  1. Create one module for all displays.
  2. Copy the all "display" config to config/install.
  3. Add your content type as a dependency of the display module.
  4. Move any display related depencies from the content type and add them to the display module.

## Export a content type with features?

1. Install [features](https://www.drupal.org/project/features) and the features_ui modules
2. Select the components to export under Config > Features

Once created, you can normally update features at the 
command line using drush.

```bash
drush [@site-alias] features-export my_listicle
```

## Export a particular piece of configuration
When you disassemble a content type, features doesn't always detect everything correctly.

To work around this, you may sometimes need to 
[export raw configuration](https://drushcommands.com/drush-8x/config/config-get/)
with drush.

```bash
$ drush @hub.local cget field.storage.node.teaser
uuid: 7eed3821-0f2a-4b4d-bd17-704f3b72a008
langcode: en
status: true
dependencies:
  module:
    - node
    - text
_core:
  default_config_hash: JapDCTJ3k5K1y9mhmobDTfEXBPiUYMkSINKKVCNp-Ig
id: node.teaser
field_name: teaser
entity_type: node
type: text_long
settings: {  }
module: text
locked: false
cardinality: 1
translatable: true
indexes: {  }
persist_with_no_fields: false
custom_storage: false
```

You can simply pipe this output to the appropriate configuration file.

```bash
drush [@site-alias] config-get field.storage.node.teaser > config/install/field.storage.node.teaser.yml
```

WARNING: If there is anything wonky with your setup, you may get warnings at the beginning of your 
drush output (timezone not set, etc). If present, these will mess up your config file.

