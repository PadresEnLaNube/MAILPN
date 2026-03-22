<?php
/**
 * The-global functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to enqueue the-global stylesheet and JavaScript.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 * @package    MAILPN
 * @subpackage MAILPN/includes
 * @author     Padres en la Nube <info@padresenlanube.com>
 */
class MAILPN_Common
{

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
	public function __construct($plugin_name, $version)
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets.
	 *
	 * @since    1.0.0
	 */
	public function mailpn_enqueue_styles()
	{
		if (!wp_style_is('wph-material-icons-outlined', 'enqueued')) {
			wp_enqueue_style('wph-material-icons-outlined', MAILPN_URL . 'assets/css/material-icons-outlined.min.css', [], $this->version, 'all');
		}

		if (!wp_style_is($this->plugin_name . '-selector', 'enqueued')) {
			wp_enqueue_style($this->plugin_name . '-selector', MAILPN_URL . 'assets/css/mailpn-selector.css', [], $this->version, 'all');
		}

		if (!wp_style_is('wph-trumbowyg', 'enqueued')) {
			wp_enqueue_style('wph-trumbowyg', MAILPN_URL . 'assets/css/trumbowyg.min.css', [], $this->version, 'all');
		}

		if (!wp_style_is($this->plugin_name . '-popups', 'enqueued')) {
			wp_enqueue_style($this->plugin_name . '-popups', MAILPN_URL . 'assets/css/mailpn-popups.css', [], $this->version, 'all');
		}


		if (!wp_style_is('wph-tooltipster', 'enqueued')) {
			wp_enqueue_style('wph-tooltipster', MAILPN_URL . 'assets/css/tooltipster.min.css', [], $this->version, 'all');
		}

		if (!wp_style_is('wph-owl', 'enqueued')) {
			wp_enqueue_style('wph-owl', MAILPN_URL . 'assets/css/owl.min.css', [], $this->version, 'all');
		}

		wp_enqueue_style($this->plugin_name, MAILPN_URL . 'assets/css/mailpn.css', [], $this->version, 'all');
	}

	/**
	 * Register the JavaScript.
	 *
	 * @since    1.0.0
	 */
	public function mailpn_enqueue_scripts()
	{
		if (!wp_script_is('jquery-ui-sortable', 'enqueued')) {
			wp_enqueue_script('jquery-ui-sortable');
		}

		if(!wp_script_is($this->plugin_name . '-selector', 'enqueued')) {
			wp_enqueue_script($this->plugin_name . '-selector', MAILPN_URL . 'assets/js/mailpn-selector.js', ['jquery'], $this->version, false, ['in_footer' => true, 'strategy' => 'defer']);
		}

		if (!wp_script_is('wph-trumbowyg', 'enqueued')) {
			wp_enqueue_script('wph-trumbowyg', MAILPN_URL . 'assets/js/trumbowyg.min.js', ['jquery'], $this->version, false, ['in_footer' => true, 'strategy' => 'defer']);
		}

		if (!wp_script_is($this->plugin_name . '-popups', 'enqueued')) {
			wp_enqueue_script($this->plugin_name . '-popups', MAILPN_URL . 'assets/js/mailpn-popups.js', ['jquery'], $this->version, false, ['in_footer' => true, 'strategy' => 'defer']);
		}

		if (!wp_script_is('wph-tooltipster', 'enqueued')) {
			wp_enqueue_script('wph-tooltipster', MAILPN_URL . 'assets/js/tooltipster.min.js', ['jquery'], $this->version, false, ['in_footer' => true, 'strategy' => 'defer']);
		}

		if (!wp_script_is('wph-owl', 'enqueued')) {
			wp_enqueue_script('wph-owl', MAILPN_URL . 'assets/js/owl.min.js', ['jquery'], $this->version, false, ['in_footer' => true, 'strategy' => 'defer']);
		}

		wp_enqueue_script($this->plugin_name, MAILPN_URL . 'assets/js/mailpn.js', ['jquery'], $this->version, false, ['in_footer' => true, 'strategy' => 'defer']);
		wp_enqueue_script($this->plugin_name . '-ajax', MAILPN_URL . 'assets/js/mailpn-ajax.js', ['jquery'], $this->version, false, ['in_footer' => true, 'strategy' => 'defer']);
		wp_enqueue_script($this->plugin_name . '-aux', MAILPN_URL . 'assets/js/mailpn-aux.js', ['jquery'], $this->version, false, ['in_footer' => true, 'strategy' => 'defer']);
		wp_enqueue_script($this->plugin_name . '-forms', MAILPN_URL . 'assets/js/mailpn-forms.js', ['jquery', 'jquery-ui-sortable'], $this->version, false, ['in_footer' => true, 'strategy' => 'defer']);

		if (!wp_script_is($this->plugin_name . '-notifications', 'enqueued')) {
			wp_enqueue_script($this->plugin_name . '-notifications', MAILPN_URL . 'assets/js/mailpn-notifications.js', ['jquery'], $this->version, false, ['in_footer' => true, 'strategy' => 'defer']);
			wp_localize_script($this->plugin_name . '-notifications', 'mailpn_notifications_ajax', [
				'ajax_url' => admin_url('admin-ajax.php'),
				'nonce' => wp_create_nonce('mailpn_notification_nonce'),
				'mark_read_text' => __('Mark as read', 'mailpn'),
				'mark_unread_text' => __('Mark as unread', 'mailpn'),
				'processing_text' => __('Processing...', 'mailpn'),
			]);
		}

		wp_localize_script($this->plugin_name, 'mailpn_ajax', [
			'ajax_url' => admin_url('admin-ajax.php'),
			'mailpn_ajax_nonce' => wp_create_nonce('mailpn-nonce'),
		]);

		wp_localize_script($this->plugin_name, 'mailpn_path', [
			'main' => MAILPN_URL,
			'assets' => MAILPN_URL . 'assets/',
			'css' => MAILPN_URL . 'assets/css/',
			'js' => MAILPN_URL . 'assets/js/',
			'media' => MAILPN_URL . 'assets/media/',
		]);

		$mailpn_action = !empty($_GET['mailpn_action']) ? MAILPN_Forms::mailpn_sanitizer(wp_unslash($_GET['mailpn_action'])) : '';
		$mailpn_btn_id = !empty($_GET['mailpn_btn_id']) ? MAILPN_Forms::mailpn_sanitizer(wp_unslash($_GET['mailpn_btn_id'])) : '';
		$mailpn_popup = !empty($_GET['mailpn_popup']) ? MAILPN_Forms::mailpn_sanitizer(wp_unslash($_GET['mailpn_popup'])) : '';
		$mailpn_tab = !empty($_GET['mailpn_tab']) ? MAILPN_Forms::mailpn_sanitizer(wp_unslash($_GET['mailpn_tab'])) : '';

		wp_localize_script($this->plugin_name, 'mailpn_action', [
			'action' => $mailpn_action,
			'btn_id' => $mailpn_btn_id,
			'popup' => $mailpn_popup,
			'tab' => $mailpn_tab,
		]);

		$mailpn_notice = !empty($_GET['mailpn_notice']) ? MAILPN_Forms::mailpn_sanitizer(wp_unslash($_GET['mailpn_notice'])) : '';

		wp_localize_script($this->plugin_name, 'mailpn_notice', [
			'notice' => $mailpn_notice,
		]);

		wp_localize_script($this->plugin_name, 'mailpn_trumbowyg', [
			'path' => MAILPN_URL . 'assets/media/trumbowyg-icons.svg',
		]);

		wp_localize_script($this->plugin_name, 'mailpn_i18n', [
			'an_error_has_occurred' => esc_html(__('An error has occurred. Please try again in a few minutes.', 'mailpn')),
			'user_unlogged' => esc_html(__('Please create a new user or login to save the information.', 'mailpn')),
			'saved_successfully' => esc_html(__('Saved successfully', 'mailpn')),
			'edit_image' => esc_html(__('Edit image', 'mailpn')),
			'edit_images' => esc_html(__('Edit images', 'mailpn')),
			'select_image' => esc_html(__('Select image', 'mailpn')),
			'select_images' => esc_html(__('Select images', 'mailpn')),
			'edit_video' => esc_html(__('Edit video', 'mailpn')),
			'edit_videos' => esc_html(__('Edit videos', 'mailpn')),
			'select_video' => esc_html(__('Select video', 'mailpn')),
			'select_videos' => esc_html(__('Select videos', 'mailpn')),
			'edit_audio' => esc_html(__('Edit audio', 'mailpn')),
			'edit_audios' => esc_html(__('Edit audios', 'mailpn')),
			'select_audio' => esc_html(__('Select audio', 'mailpn')),
			'select_audios' => esc_html(__('Select audios', 'mailpn')),
			'edit_file' => esc_html(__('Edit file', 'mailpn')),
			'edit_files' => esc_html(__('Edit files', 'mailpn')),
			'select_file' => esc_html(__('Select file', 'mailpn')),
			'select_files' => esc_html(__('Select files', 'mailpn')),
			'ordered_element' => esc_html(__('Ordered element', 'mailpn')),
		]);

		// Initialize popups
		MAILPN_Popups::instance();

		// Initialize selectors
		MAILPN_Selector::instance();
	}

	public function mailpn_body_classes($classes)
	{
		$classes[] = 'mailpn-body';

		if (!is_user_logged_in()) {
			$classes[] = 'mailpn-body-unlogged';
		} else {
			$classes[] = 'mailpn-body-logged-in';

			$user = new WP_User(get_current_user_id());
			foreach ($user->roles as $role) {
				$classes[] = 'mailpn-body-' . $role;
			}
		}

		return $classes;
	}
}
