<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to enqueue the admin-specific stylesheet and JavaScript.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 * @package    MAILPN
 * @subpackage MAILPN/admin
 * @author     Padres en la Nube <info@padresenlanube.com>
 */
class MAILPN_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function mailpn_enqueue_styles() {
		wp_enqueue_style($this->plugin_name . '-admin', MAILPN_URL . 'assets/css/admin/mailpn-admin.css', [], $this->version, 'all');
		
		// Load dashboard styles if on dashboard page
		if (isset($_GET['page']) && $_GET['page'] === 'mailpn_dashboard') {
			wp_enqueue_style($this->plugin_name . '-dashboard', MAILPN_URL . 'assets/css/admin/mailpn-dashboard.css', [], $this->version, 'all');
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function mailpn_enqueue_scripts() {
		wp_enqueue_media();
		wp_enqueue_script($this->plugin_name . '-admin', MAILPN_URL . 'assets/js/admin/mailpn-admin.js', ['jquery'], $this->version, false);
		
		// Load dashboard scripts if on dashboard page
		if (isset($_GET['page']) && $_GET['page'] === 'mailpn_dashboard') {
			// Load popups script first (dependency)
			wp_enqueue_script($this->plugin_name . '-popups', MAILPN_URL . 'assets/js/mailpn-popups.js', ['jquery'], $this->version, false);
			
			// Load dashboard script
			wp_enqueue_script($this->plugin_name . '-dashboard', MAILPN_URL . 'assets/js/admin/mailpn-dashboard.js', ['jquery', $this->plugin_name . '-popups'], $this->version, false);
			
			// Localize script for translations and data
			wp_localize_script($this->plugin_name . '-dashboard', 'mailpn_dashboard', array(
				'search_placeholder' => __('Search emails...', 'mailpn'),
				'ajax_url' => admin_url('admin-ajax.php'),
				'nonce' => wp_create_nonce('mailpn_dashboard_nonce'),
			));
			
			// Localize MAILPN_Data for loader access
			wp_localize_script($this->plugin_name . '-dashboard', 'MAILPN_Data', array(
				'mailpn_loader' => MAILPN_Data::mailpn_loader(true),
			));
		}
	}
}
