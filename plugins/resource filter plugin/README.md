Resource Filter Plugin

  Description
  --------------

The  Resource Filter  plugin allows you to create a custom post type and custom taxonomies for Resources with AJAX filtering capabilities. Users can filter resources by keyword or taxonomy terms (e.g., Resource Type or Resource Topic) from the frontend.

  Features
  ------------

- Custom post type: `resource`
- Custom taxonomies: `resource_type` and `resource_topic`
- AJAX filtering by keyword and taxonomy terms
- Automatic creation and deletion of a resource filter page upon plugin activation and deactivation

  Installation
  --------------

1. Upload the plugin files to the `/wp-content/plugins/resource-filter` directory or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Ensure the `page-resource-filter.php` template file is present in your active theme directory.

  Usage
  ---------

1. Upon activation, the plugin will create a page titled  Resource Filter  that uses the `page-resource-filter.php` template and contains the `[resource_filter]` shortcode.
2. Navigate to the  Resource Filter  page to see the filter form and list of resources.
3. Use the form to filter resources by keyword or taxonomy terms.

  Shortcode
  ----------------

The plugin  Alternatively provides a shortcode `[resource_filter]` that displays the filter form and resource list.

  Template File

Ensure you have a template file named `page-resource-filter.php` in your active theme directory. The content of this file should be:

Demo
=>
/***
<?php
/*
Template Name: Resource Filter
*/

get_header(); ?>

<?php echo do_shortcode('[resource_filter]'); ?>

<?php get_footer(); ?>
***/
