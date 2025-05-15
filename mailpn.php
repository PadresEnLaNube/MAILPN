<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin admin area. This file also includes all of the dependencies used by the plugin, registers the activation and deactivation functions, and defines a function that starts the plugin.
 *
 * @link              padresenlanube.com/
 * @since             1.0.0
 * @package           MAILPN
 *
 * @wordpress-plugin
 * Plugin Name:       Mailing Manager - PN
 * Plugin URI:        https://padresenlanube.com/plugins/mailpn/
 * Description:       Effortlessly manage your email campaigns with our WordPress Email Management Plugin. Schedule, send, and track emails directly from your dashboard to engage your audience like never before.
 * Version:           1.0.0
 * Requires at least: 3.0
 * Requires PHP:      7.2
 * Author:            Padres en la Nube
 * Author URI:        https://padresenlanube.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       mailpn
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('MAILPN_VERSION', '1.0.0');
define('MAILPN_DIR', plugin_dir_path(__FILE__));
define('MAILPN_URL', plugin_dir_url(__FILE__));

define('MAILPN_ROLE_CAPABILITIES', [
	'edit_post' => 'edit_mailpn_mail',
	'edit_posts' => 'edit_mailpn',
	'edit_private_posts' => 'edit_private_mailpn',
	'edit_published_posts' => 'edit_published_mailpn',
	'edit_others_posts' => 'edit_other_mailpn',
	'publish_posts' => 'publish_mailpn',
	'read_post' => 'read_mailpn_mail',
	'read_private_posts' => 'read_private_mailpn',
	'delete_post' => 'delete_mailpn_mail',
	'delete_posts' => 'delete_mailpn',
	'delete_private_posts' => 'delete_private_mailpn',
	'delete_published_posts' => 'delete_published_mailpn',
	'delete_others_posts' => 'delete_others_mailpn',
	'upload_files' => 'upload_files',
	'manage_terms' => 'manage_mailpn_category',
	'edit_terms' => 'edit_mailpn_category',
	'delete_terms' => 'delete_mailpn_category',
	'assign_terms' => 'assign_mailpn_category',
	'manage_options' => 'manage_mailpn_options',
]);

define('MAILPN_KSES', [
	'div' => [
		'id' => [],
		'class' => [],
		'data-mailpn-section-id' => [],
	],
	'span' => [
		'id' => [],
		'class' => [],
	],
	'p' => [
		'id' => [],
		'class' => [],
	],
	'ul' => [
		'id' => [],
		'class' => [],
	],
	'ol' => [
		'id' => [],
		'class' => [],
	],
	'li' => [
		'id' => [],
		'class' => [],
	],
	'small' => [
		'id' => [],
		'class' => [],
	],
	'a' => [
		'id' => [],
		'class' => [],
		'href' => [],
		'title' => [],
		'target' => [],
		'data-mailpn-post-id' => [],
	],
	'form' => [
		'id' => [],
		'class' => [],
		'action' => [],
		'method' => [],
	],
	'input' => [
		'name' => [],
		'id' => [],
		'class' => [],
		'type' => [],
		'checked' => [],
		'multiple' => [],
		'disabled' => [],
		'value' => [],
		'placeholder' => [],
		'data-mailpn-parent' => [],
		'data-mailpn-parent-option' => [],
		'data-mailpn-type' => [],
		'data-mailpn-subtype' => [],
		'data-mailpn-user-id' => [],
		'data-mailpn-post-id' => [],
	],
	'select' => [
		'name' => [],
		'id' => [],
		'class' => [],
		'type' => [],
		'checked' => [],
		'multiple' => [],
		'disabled' => [],
		'value' => [],
		'placeholder' => [],
		'data-placeholder' => [],
		'data-mailpn-parent' => [],
		'data-mailpn-parent-option' => [],
	],
	'option' => [
		'name' => [],
		'id' => [],
		'class' => [],
		'disabled' => [],
		'selected' => [],
		'value' => [],
		'placeholder' => [],
	],
	'textarea' => [
		'name' => [],
		'id' => [],
		'class' => [],
		'type' => [],
		'multiple' => [],
		'disabled' => [],
		'value' => [],
		'placeholder' => [],
		'data-mailpn-parent' => [],
		'data-mailpn-parent-option' => [],
	],
	'label' => [
		'id' => [],
		'class' => [],
		'for' => [],
	],
	'i' => [
		'id' => [],
		'class' => [],
		'title' => [],
	],
	'br' => [],
	'em' => [],
	'strong' => [],
	'h1' => [
		'id' => [],
		'class' => [],
	],
	'h2' => [
		'id' => [],
		'class' => [],
	],
	'h3' => [
		'id' => [],
		'class' => [],
	],
	'h4' => [
		'id' => [],
		'class' => [],
	],
	'h5' => [
		'id' => [],
		'class' => [],
	],
	'h6' => [
		'id' => [],
		'class' => [],
	],
	'img' => [
		'id' => [],
		'class' => [],
		'src' => [],
		'alt' => [],
		'title' => [],
	],
]);

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-mailpn-activator.php
 */
function mailpn_activate() {
	require_once plugin_dir_path(__FILE__) . 'includes/class-mailpn-activator.php';
	MAILPN_Activator::mailpn_activate();
}
register_activation_hook(__FILE__, 'mailpn_activate');

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-mailpn-deactivator.php
 */
function mailpn_deactivate() {
	require_once plugin_dir_path(__FILE__) . 'includes/class-mailpn-deactivator.php';
	MAILPN_Deactivator::mailpn_deactivate();
}
register_deactivation_hook(__FILE__, 'mailpn_deactivate');

/**
 * The core plugin class that is used to define internationalization, admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-mailpn.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks, then kicking off the plugin from this point in the file does not affect the page life cycle.
 *
 * @since    1.0.0
 */
function mailpn_run() {
	$plugin = new MAILPN();
	$plugin->mailpn_run();

	require_once plugin_dir_path(__FILE__) . 'includes/class-mailpn-activator.php';
	MAILPN_Activator::mailpn_activate();
}

// Initialize the plugin on plugins_loaded hook
add_action('plugins_loaded', 'mailpn_run');