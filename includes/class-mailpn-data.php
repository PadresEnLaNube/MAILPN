<?php
/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin so that it is ready for translation.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 * @package    MAILPN
 * @subpackage MAILPN/includes
 * @author     Padres en la Nube <info@padresenlanube.com>
 */
class MAILPN_Data {
	/**
	 * The main data array.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      MAILPN_Data    $data    Empty array.
	 */
	protected $data = [];

	/**
	 * Load the plugin most usefull data.
	 *
	 * @since    1.0.0
	 */
	public function mailpn_load_plugin_data() {
		$this->data['user_id'] = get_current_user_id();

		if (is_admin()) {
			$this->data['post_id'] = !empty($GLOBALS['_REQUEST']['post']) ? $GLOBALS['_REQUEST']['post'] : 0;
		}else{
			$this->data['post_id'] = get_the_ID();
		}

		$GLOBALS['mailpn_data'] = $this->data;
	}

	/**
	 * Flush wp rewrite rules.
	 *
	 * @since    1.0.0
	 */
	public function mailpn_flush_rewrite_rules() {
    if (get_option('mailpn_options_changed')) {
      flush_rewrite_rules();
      update_option('mailpn_options_changed', false);
    }
  }

  /**
	 * Gets the mini loader.
	 *
	 * @since    1.0.0
	 */
	public static function mailpn_loader($display = false) {
		?>
			<div class="mailpn-waiting <?php echo ($display) ? 'mailpn-display-block' : 'mailpn-display-none'; ?>">
				<div class="mailpn-loader-circle-waiting"><div></div><div></div><div></div><div></div></div>
			</div>
		<?php
  }

  public static function mailpn_mail_types() {
		// MAILPN_Data::mail_types();
		return apply_filters('mailpn_mail_types', ['email_one_time' => __('One time email', 'mailpn'), 'email_periodic' => __('Periodic time email', 'mailpn'), 'email_published_content' => __('Published content email', 'mailpn'), 'email_welcome' => __('Welcome email', 'mailpn'), 'newsletter_welcome' => __('Newsletter welcome email', 'mailpn'), 'email_verify_code' => __('Account verification code', 'mailpn'), 'email_coded' => __('Email sent from code', 'mailpn'), 'email_password_reset' => __('Password reset email', 'mailpn'), ]);
	}
}