<?php
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current version of the plugin.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 * @package    MAILPN
 * @subpackage MAILPN/includes
 * @author     Padres en la Nube <info@padresenlanube.com>
 */

class MAILPN {
	/**
	 * The loader that's responsible for maintaining and registering all hooks that power the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      MAILPN_Loader    $mailpn_loader    Maintains and registers all hooks for the plugin.
	 */
	protected $mailpn_loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $mailpn_plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $mailpn_plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $mailpn_version    The current version of the plugin.
	 */
	protected $mailpn_version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin. Load the dependencies, define the locale, and set the hooks for the admin area and the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if (defined('MAILPN_VERSION')) {
			$this->mailpn_version = MAILPN_VERSION;
		} else {
			$this->mailpn_version = '1.0.0';
		}

		$this->mailpn_plugin_name = 'mailpn';

		self::mailpn_load_dependencies();
		self::mailpn_load_i18n();
		self::mailpn_define_common_hooks();
		self::mailpn_define_admin_hooks();
		self::mailpn_define_public_hooks();
		self::mailpn_define_post_types();
		self::mailpn_define_taxonomies();
		self::mailpn_load_ajax();
		self::mailpn_load_ajax_nopriv();
		self::mailpn_load_data();
		self::mailpn_load_templates();
		self::mailpn_load_settings();
		self::mailpn_load_shortcodes();
		self::mailpn_load_cron();
		self::mailpn_load_notifications();
	}
			
	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 * - MAILPN_Loader. Orchestrates the hooks of the plugin.
	 * - MAILPN_i18n. Defines internationalization functionality.
	 * - MAILPN_Common. Defines hooks used accross both, admin and public side.
	 * - MAILPN_Admin. Defines all hooks for the admin area.
	 * - MAILPN_Public. Defines all hooks for the public side of the site.
	 * - MAILPN_Post_Type_Mail. Defines Mail custom post type.
	 * - MAILPN_Taxonomies_Mail. Defines Mail taxonomies.
	 * - MAILPN_Templates. Load plugin templates.
	 * - MAILPN_Data. Load main usefull data.
	 * - MAILPN_Functions_Post. Posts management functions.
	 * - MAILPN_Functions_User. Users management functions.
	 * - MAILPN_Functions_Attachment. Attachments management functions.
	 * - MAILPN_Functions_Settings. Define settings.
	 * - MAILPN_Functions_Forms. Forms management functions.
	 * - MAILPN_Functions_Ajax. Ajax functions.
	 * - MAILPN_Functions_Ajax_Nopriv. Ajax No Private functions.
	 * - MAILPN_Functions_Shortcodes. Define all shortcodes for the platform.
	 * - MAILPN_Cron. Define all cron jobs for the platform.
	 * - MAILPN_Mailing. Define all mailing functions for the platform.
	 * - MAILPN_Notifications. Define all notifications for the platform.
	 *
	 * Create an instance of the loader which will be used to register the hooks with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function mailpn_load_dependencies() {
		/**
		 * The class responsible for orchestrating the actions and filters of the core plugin.
		 */
		require_once MAILPN_DIR . 'includes/class-mailpn-loader.php';

		/**
		 * The class responsible for defining internationalization functionality of the plugin.
		 */
		require_once MAILPN_DIR . 'includes/class-mailpn-i18n.php';

		/**
		 * The class responsible for defining all actions that occur both in the admin area and in the public-facing side of the site.
		 */
		require_once MAILPN_DIR . 'includes/class-mailpn-common.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once MAILPN_DIR . 'includes/admin/class-mailpn-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing side of the site.
		 */
		require_once MAILPN_DIR . 'includes/public/class-mailpn-public.php';

		/**
		 * The class responsible for create the Mail custom post type.
		 */
		require_once MAILPN_DIR . 'includes/class-mailpn-post-type-mail.php';

		/**
		 * The class responsible for create the Mail Record custom post type.
		 */
		require_once MAILPN_DIR . 'includes/class-mailpn-post-type-rec.php';

		/**
		 * The class responsible for create the Mail custom taxonomies.
		 */
		require_once MAILPN_DIR . 'includes/class-mailpn-taxonomies-mail.php';

		/**
		 * The class responsible for create the Mail Record custom taxonomies.
		 */
		require_once MAILPN_DIR . 'includes/class-mailpn-taxonomies-rec.php';

		/**
		 * The class responsible for plugin templates.
		 */
		require_once MAILPN_DIR . 'includes/class-mailpn-templates.php';

		/**
		 * The class getting key data of the platform.
		 */
		require_once MAILPN_DIR . 'includes/class-mailpn-data.php';

		/**
		 * The class defining posts management functions.
		 */
		require_once MAILPN_DIR . 'includes/class-mailpn-functions-post.php';

		/**
		 * The class defining users management functions.
		 */
		require_once MAILPN_DIR . 'includes/class-mailpn-functions-user.php';

		/**
		 * The class defining attahcments management functions.
		 */
		require_once MAILPN_DIR . 'includes/class-mailpn-functions-attachment.php';

		/**
		 * The class defining settings.
		 */
		require_once MAILPN_DIR . 'includes/class-mailpn-settings.php';

		/**
		 * The class defining form management.
		 */
		require_once MAILPN_DIR . 'includes/class-mailpn-forms.php';

		/**
		 * The class defining ajax functions.
		 */
		require_once MAILPN_DIR . 'includes/class-mailpn-ajax.php';

		/**
		 * The class defining no private ajax functions.
		 */
		require_once MAILPN_DIR . 'includes/class-mailpn-ajax-nopriv.php';

		/**
		 * The class defining shortcodes.
		 */
		require_once MAILPN_DIR . 'includes/class-mailpn-shortcodes.php';

		/**
		 * The class defining cron.
		 */
		require_once MAILPN_DIR . 'includes/class-mailpn-cron.php';

		/**
		 * The class defining mailing functions.
		 */
		require_once MAILPN_DIR . 'includes/class-mailpn-mailing.php';

		/**
		 * The class defining notifications.
		 */
		require_once MAILPN_DIR . 'includes/class-mailpn-notifications.php';

		/**
		 * The class responsible for popups functionality.
		 */
		require_once MAILPN_DIR . 'includes/class-mailpn-popups.php';

		$this->mailpn_loader = new MAILPN_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the MAILPN_i18n class in order to set the domain and to register the hook with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function mailpn_load_i18n() {
		$plugin_i18n = new MAILPN_i18n();
		$this->mailpn_loader->mailpn_add_action('init', $plugin_i18n, 'mailpn_load_plugin_textdomain', 20);

		if (class_exists('Polylang')) {
			$this->mailpn_loader->mailpn_add_filter('pll_get_post_types', $plugin_i18n, 'mailpn_pll_get_post_types', 10, 2);
		}
	}

	/**
	 * Register all of the hooks related to the main functionalities of the plugin, common to public and admin faces.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function mailpn_define_common_hooks() {
		$plugin_common = new MAILPN_Common(self::mailpn_get_plugin_name(), self::mailpn_get_version());
		$this->mailpn_loader->mailpn_add_action('wp_enqueue_scripts', $plugin_common, 'mailpn_enqueue_styles');
		$this->mailpn_loader->mailpn_add_action('wp_enqueue_scripts', $plugin_common, 'mailpn_enqueue_scripts');
		$this->mailpn_loader->mailpn_add_action('admin_enqueue_scripts', $plugin_common, 'mailpn_enqueue_styles');
		$this->mailpn_loader->mailpn_add_action('admin_enqueue_scripts', $plugin_common, 'mailpn_enqueue_scripts');
		$this->mailpn_loader->mailpn_add_filter('body_class', $plugin_common, 'mailpn_body_classes');
		$this->mailpn_loader->mailpn_add_filter('body_class', $plugin_common, 'mailpn_body_classes');

		$plugin_post_type_mail = new MAILPN_Post_Type_Mail();
		$this->mailpn_loader->mailpn_add_action('mailpn_form_save', $plugin_post_type_mail, 'mailpn_form_save', 4, 999);

		$plugin_post_type_rec = new MAILPN_Post_Type_Rec();
		$this->mailpn_loader->mailpn_add_action('mailpn_form_save', $plugin_post_type_rec, 'mailpn_form_save', 4, 999);
	}

	/**
	 * Register all of the hooks related to the admin area functionality of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function mailpn_define_admin_hooks() {
		$plugin_admin = new MAILPN_Admin(self::mailpn_get_plugin_name(), self::mailpn_get_version());
		$this->mailpn_loader->mailpn_add_action('admin_enqueue_scripts', $plugin_admin, 'mailpn_enqueue_styles');
		$this->mailpn_loader->mailpn_add_action('admin_enqueue_scripts', $plugin_admin, 'mailpn_enqueue_scripts');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function mailpn_define_public_hooks() {
		$plugin_public = new MAILPN_Public(self::mailpn_get_plugin_name(), self::mailpn_get_version());
		$this->mailpn_loader->mailpn_add_action('wp_enqueue_scripts', $plugin_public, 'mailpn_enqueue_styles');
		$this->mailpn_loader->mailpn_add_action('wp_enqueue_scripts', $plugin_public, 'mailpn_enqueue_scripts');

		$plugin_user = new MAILPN_Functions_User();
		$this->mailpn_loader->mailpn_add_action('wp_login', $plugin_user, 'mailpn_wp_login');
	}

	/**
	 * Register all Post Types with meta boxes and templates.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function mailpn_define_post_types() {
		$plugin_post_type_mail = new MAILPN_Post_Type_Mail();
		$this->mailpn_loader->mailpn_add_action('init', $plugin_post_type_mail, 'mailpn_register_post_type');
		$this->mailpn_loader->mailpn_add_action('admin_init', $plugin_post_type_mail, 'mailpn_add_meta_box');
		$this->mailpn_loader->mailpn_add_action('save_post_mailpn_mail', $plugin_post_type_mail, 'mailpn_save_post', 10, 3);
		// Add new column hooks
		$this->mailpn_loader->mailpn_add_filter('manage_mailpn_mail_posts_columns', $plugin_post_type_mail, 'mailpn_mail_posts_columns');
		$this->mailpn_loader->mailpn_add_action('manage_mailpn_mail_posts_custom_column', $plugin_post_type_mail, 'mailpn_mail_posts_custom_column', 10, 2);

		$plugin_post_type_rec = new MAILPN_Post_Type_Rec();
		$this->mailpn_loader->mailpn_add_action('init', $plugin_post_type_rec, 'mailpn_register_post_type');
		$this->mailpn_loader->mailpn_add_action('admin_init', $plugin_post_type_rec, 'mailpn_add_meta_box');
		$this->mailpn_loader->mailpn_add_action('save_post_mailpn_rec', $plugin_post_type_rec, 'mailpn_save_post', 10, 3);
		$this->mailpn_loader->mailpn_add_filter('manage_mailpn_rec_posts_columns', $plugin_post_type_rec, 'mailpn_rec_posts_columns', 10);
		$this->mailpn_loader->mailpn_add_filter('manage_mailpn_rec_posts_custom_column', $plugin_post_type_rec, 'mailpn_rec_posts_custom_column', 10, 2);
		$this->mailpn_loader->mailpn_add_filter('manage_edit-mailpn_rec_sortable_columns', $plugin_post_type_rec, 'mailpn_rec_posts_columns', 10, 3);
		
		// Add new hooks for recipient filter
		$this->mailpn_loader->mailpn_add_action('restrict_manage_posts', $plugin_post_type_rec, 'mailpn_rec_filter_dropdown');
		$this->mailpn_loader->mailpn_add_action('pre_get_posts', $plugin_post_type_rec, 'mailpn_rec_filter_query');
	}

	/**
	 * Register all of the hooks related to Taxonomies.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function mailpn_define_taxonomies() {
		$plugin_taxonomies_mail = new MAILPN_Taxonomies_Mail();
		$this->mailpn_loader->mailpn_add_action('init', $plugin_taxonomies_mail, 'mailpn_register_taxonomies');

		$plugin_taxonomies_rec = new MAILPN_Taxonomies_Rec();
		$this->mailpn_loader->mailpn_add_action('init', $plugin_taxonomies_rec, 'mailpn_register_taxonomies');
	}

	/**
	 * Load most common data used on the platform.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function mailpn_load_data() {
		$plugin_data = new MAILPN_Data();

		if (is_admin()) {
			$this->mailpn_loader->mailpn_add_action('init', $plugin_data, 'mailpn_load_plugin_data');
		}else{
			$this->mailpn_loader->mailpn_add_action('wp_footer', $plugin_data, 'mailpn_load_plugin_data');
		}

		$this->mailpn_loader->mailpn_add_action('wp_footer', $plugin_data, 'mailpn_flush_rewrite_rules');
		$this->mailpn_loader->mailpn_add_action('admin_footer', $plugin_data, 'mailpn_flush_rewrite_rules');
	}

	/**
	 * Register templates.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function mailpn_load_templates() {
		if (!defined('DOING_AJAX')) {
			$plugin_templates = new MAILPN_Templates();
			$this->mailpn_loader->mailpn_add_action('wp_footer', $plugin_templates, 'load_plugin_templates');
			$this->mailpn_loader->mailpn_add_action('admin_footer', $plugin_templates, 'load_plugin_templates');
		}
	}

	/**
	 * Cron hooks and functionalities.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function mailpn_load_cron() {
		$plugin_cron = new MAILPN_Cron();

		$this->mailpn_loader->mailpn_add_action('wp', $plugin_cron, 'cron_schedule');
		$this->mailpn_loader->mailpn_add_action('mailpn_cron_daily', $plugin_cron, 'cron_daily');
		$this->mailpn_loader->mailpn_add_action('mailpn_cron_ten_minutes', $plugin_cron, 'cron_ten_minutes');
		$this->mailpn_loader->mailpn_add_filter('cron_schedules', $plugin_cron, 'cron_ten_minutes_schedule');
	}

	/**
	 * Load notifications.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function mailpn_load_notifications() {
		$plugin_notifications = new MAILPN_Notifications();
		$this->mailpn_loader->mailpn_add_action('wp_body_open', $plugin_notifications, 'mailpn_wp_body_open');
	}
	
	/**
	 * Register settings.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function mailpn_load_settings() {
		$plugin_settings = new MAILPN_Settings();
		$this->mailpn_loader->mailpn_add_action('admin_menu', $plugin_settings, 'mailpn_admin_menu');
		$this->mailpn_loader->mailpn_add_action('activated_plugin', $plugin_settings, 'activated_plugin');
		$this->mailpn_loader->mailpn_add_action('user_register', $plugin_settings, 'mailpn_user_register', 11, 1);		
		$this->mailpn_loader->mailpn_add_action('init', $plugin_settings, 'mailpn_init_hook');		
		$this->mailpn_loader->mailpn_add_action('pre_get_posts', $plugin_settings, 'mailpn_pre_get_posts');
		$this->mailpn_loader->mailpn_add_filter('wp_mail_from', $plugin_settings, 'mailpn_wp_mail_from', 999);
		$this->mailpn_loader->mailpn_add_filter('wp_mail_from_name', $plugin_settings, 'mailpn_wp_mail_from_name', 999);
	}

	/**
	 * Load ajax functions.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function mailpn_load_ajax() {
		$plugin_ajax = new MAILPN_Ajax();
		$this->mailpn_loader->mailpn_add_action('wp_ajax_mailpn_ajax', $plugin_ajax, 'mailpn_ajax_server');
	}

	/**
	 * Load no private ajax functions.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function mailpn_load_ajax_nopriv() {
		$plugin_ajax_nopriv = new MAILPN_Ajax_Nopriv();
		$this->mailpn_loader->mailpn_add_action('wp_ajax_mailpn_ajax_nopriv', $plugin_ajax_nopriv, 'mailpn_ajax_nopriv_server');
		$this->mailpn_loader->mailpn_add_action('wp_ajax_nopriv_mailpn_ajax_nopriv', $plugin_ajax_nopriv, 'mailpn_ajax_nopriv_server');
	}

	/**
	 * Register shortcodes of the platform.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function mailpn_load_shortcodes() {
		$plugin_shortcodes = new MAILPN_Shortcodes();
		$this->mailpn_loader->mailpn_add_shortcode('mailpn-mail', $plugin_shortcodes, 'mailpn_mail');
		$this->mailpn_loader->mailpn_add_shortcode('mailpn-call-to-action', $plugin_shortcodes, 'mailpn_call_to_action');
		
		$plugin_mailing = new MAILPN_Mailing();
		if (get_option('mailpn_password_new') == 'on') {
			$this->mailpn_loader->mailpn_add_filter('wp_new_user_notification_email', $plugin_mailing, 'mailpn_wp_new_user_notification_email', 10, 3);
		}
		
		if (get_option('mailpn_password_retrieve') == 'on') {
			$this->mailpn_loader->mailpn_add_filter('retrieve_password_message', $plugin_mailing, 'mailpn_retrieve_password_message', 10, 4);
		}

		$this->mailpn_loader->mailpn_add_shortcode('mailpn-sender', $plugin_mailing, 'mailpn_sender');
		$this->mailpn_loader->mailpn_add_shortcode('mailpn-text', $plugin_mailing, 'mailpn_text');
		$this->mailpn_loader->mailpn_add_shortcode('mailpn-contents', $plugin_mailing, 'mailpn_contents');
		$this->mailpn_loader->mailpn_add_shortcode('user-name', $plugin_mailing, 'mailpn_user_name');
		$this->mailpn_loader->mailpn_add_shortcode('post-name', $plugin_mailing, 'mailpn_post_name');
		$this->mailpn_loader->mailpn_add_shortcode('new-contents', $plugin_mailing, 'mailpn_new_contents');
		$this->mailpn_loader->mailpn_add_shortcode('mailpn-tools', $plugin_mailing, 'mailpn_tools');
		$this->mailpn_loader->mailpn_add_shortcode('mailpn-test-email-button', $plugin_mailing, 'mailpn_test_email_btn');

		// Register the tracking endpoint
		$this->mailpn_loader->mailpn_add_action('rest_api_init', $plugin_mailing, 'register_tracking_endpoint');
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress. Then it flushes the rewrite rules if needed.
	 *
	 * @since    1.0.0
	 */
	public function mailpn_run() {
		$this->mailpn_loader->mailpn_run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function mailpn_get_plugin_name() {
		return $this->mailpn_plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    MAILPN_Loader    Orchestrates the hooks of the plugin.
	 */
	public function mailpn_get_loader() {
		return $this->mailpn_loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function mailpn_get_version() {
		return $this->mailpn_version;
	}
}