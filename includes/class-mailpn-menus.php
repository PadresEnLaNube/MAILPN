<?php
/**
 * Plugin menus manager.
 *
 * This class defines plugin menus, both in dashboard or in front-end.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 * @package    MAILPN
 * @subpackage MAILPN/includes
 * @author     Padres en la Nube <info@padresenlanube.com>
 */
class MAILPN_Menus {
  public function get_options() {
    $mailpn_options = [];
    $mailpn_options['mailpn'] = [
      'id' => 'mailpn',
      'class' => 'mailpn-input mailpn-width-100-percent',
      'input' => 'input',
      'type' => 'text',
      'label' => __('Mail slug', 'mailpn'),
      'placeholder' => __('Mail slug', 'mailpn'),
      'description' => __('This option sets the slug of the main Mail archive page, and the Mail pages. By default they will be:', 'mailpn') . '<br><a href="' . esc_url(home_url('/mail')) . '" target="_blank">' . esc_url(home_url('/mail')) . '</a><br>' . esc_url(home_url('/mail/mail-name')),
    ];
    $mailpn_options['mailpn_options_remove'] = [
      'id' => 'mailpn_options_remove',
      'class' => 'mailpn-input mailpn-width-100-percent',
      'input' => 'input',
      'type' => 'checkbox',
      'label' => __('Remove plugin options on deactivation', 'mailpn'),
      'description' => __('If you activate this option the plugin will remove all options on deactivation. Please, be careful. This process cannot be undone.', 'mailpn'),
    ];
    $mailpn_options['mailpn_nonce'] = [
      'id' => 'mailpn_nonce',
      'input' => 'input',
      'type' => 'hidden',
    ];
    $mailpn_options['mailpn_submit'] = [
      'id' => 'mailpn_submit',
      'input' => 'input',
      'type' => 'submit',
      'value' => __('Save options', 'mailpn'),
    ];

    return $mailpn_options;
  }

	/**
	 * Administrator menu.
	 *
	 * @since    1.0.0
	 */
	public function mailpn_admin_menu() {
    // add_menu_page(__('Users manager', 'userspn'), __('Users manager', 'userspn'), 'administrator', 'userspn_options', [$this, 'userspn_options'], esc_url(USERSPN_URL . 'assets/media/userspn-menu-icon.svg'));
		add_submenu_page('edit.php?post_type=mailpn_mail', esc_html(__('Settings', 'mailpn')), esc_html(__('Settings', 'mailpn')), 'manage_mailpn_options', 'mailpn-options', [$this, 'mailpn_options'], );
	}

	public function mailpn_options() {
	  ?>
	    <div class="mailpn-options mailpn-max-width-1000 mailpn-margin-auto mailpn-mt-50 mailpn-mb-50">
        <h1 class="mailpn-mb-30"><?php esc_html_e('Base - PN Options', 'mailpn'); ?></h1>
        <div class="mailpn-options-fields mailpn-mb-30">
          <form action="" method="post" id="mailpn_form" class="mailpn-form">
            <?php foreach ($this->get_options() as $mailpn_option): ?>
              <?php MAILPN_Forms::input_wrapper_builder($mailpn_option, 'option', 0, 0, 'half'); ?>
            <?php endforeach ?>
          </form> 
        </div>
      </div>
	  <?php
	}		
}