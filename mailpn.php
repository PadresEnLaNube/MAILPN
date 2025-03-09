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
 * Requires at least: 3.0.1
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

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-mailpn-activator.php
 */
function mailpn_activate() {
	require_once plugin_dir_path(__FILE__) . 'includes/class-mailpn-activator.php';
	MAILPN_Activator::activate();
}
register_activation_hook(__FILE__, 'mailpn_activate');

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-mailpn-deactivator.php
 */
function mailpn_deactivate() {
	require_once plugin_dir_path(__FILE__) . 'includes/class-mailpn-deactivator.php';
	MAILPN_Deactivator::deactivate();
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
	$plugin->run();

	require_once plugin_dir_path(__FILE__) . 'includes/class-mailpn-activator.php';
	MAILPN_Activator::activate();
}

mailpn_run();