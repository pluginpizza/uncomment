<?php
/**
 * Author URI:        https://plugin.pizza/
 * Author:            Plugin Pizza
 * Description:       Your one-stop shop for removing WordPress comment functionality.
 * Domain Path:       /languages
 * License:           GPLv3+
 * Plugin Name:       Uncomment
 * Plugin URI:        https://github.com/pluginpizza/uncomment/
 * Text Domain:       uncomment
 * Version:           1.2.1
 * Requires PHP:      5.3.0
 * Requires at least: 4.6.0
 * GitHub Plugin URI: pluginpizza/uncomment
 *
 * @package PluginPizza\Uncomment
 *
 * Based on a refactor of the "Remove Comments Absolutely" plugin by Frank Bueltge.
 * https://github.com/bueltge/remove-comments-absolutely/pull/27
 *
 * phpcs:disable WordPressVIPMinimum.Files.IncludingFile.UsingCustomConstant
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'UNCOMMENT_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'UNCOMMENT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'UNCOMMENT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once UNCOMMENT_PLUGIN_DIR . 'includes/helpers.php';
require_once UNCOMMENT_PLUGIN_DIR . 'includes/core.php';
require_once UNCOMMENT_PLUGIN_DIR . 'includes/feeds.php';
require_once UNCOMMENT_PLUGIN_DIR . 'includes/rewrites.php';
require_once UNCOMMENT_PLUGIN_DIR . 'includes/xmlrpc.php';

if ( is_admin() ) {
	require_once UNCOMMENT_PLUGIN_DIR . 'includes/admin.php';
}

/*
 * Removing comment rewrite rules:
 *
 * If this plugin is installed as a must-use plugin, please visit the
 * WordPress admin rewrite rules settings page to automatically flush
 * the rewrite rules.
 *
 * Alternatively, you might want to use the Rewrite Rules Inspector
 * plugin or run the `wp rewrite flush` WP CLI command.
 *
 * https://wordpress.org/plugins/rewrite-rules-inspector/
 * https://developer.wordpress.org/cli/commands/rewrite/flush/
 */
register_activation_hook( __FILE__, 'flush_rewrite_rules' );
