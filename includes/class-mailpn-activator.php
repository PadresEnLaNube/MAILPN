<?php
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 * @package    mailpn
 * @subpackage mailpn/includes
 * @author     Padres en la Nube <info@padresenlanube.com>
 */
class MAILPN_Activator {
	/**
   * Plugin activation functions
   *
   * Functions to be loaded on plugin activation. This actions creates roles, options and post information attached to the plugin.
	 *
	 * @since    1.0.0
	 */
	public static function mailpn_activate() {
    require_once MAILPN_DIR . 'includes/class-mailpn-functions-post.php';
    require_once MAILPN_DIR . 'includes/class-mailpn-functions-attachment.php';

    $post_functions = new MAILPN_Functions_Post();
    $attachment_functions = new MAILPN_Functions_Attachment();

    add_role('mailpn_role_manager', esc_html(__('Mailing Manager - PN', 'mailpn')));

    $mailpn_role_admin = get_role('administrator');
    $mailpn_role_manager = get_role('mailpn_role_manager');

    $mailpn_role_manager->add_cap('upload_files'); 
    $mailpn_role_manager->add_cap('read'); 

    foreach (MAILPN_CPTS as $cpt_key => $cpt_name) { 
      $mailpn_role_admin->add_cap('manage_' . $cpt_key . '_options');
      $mailpn_role_manager->add_cap('manage_' . $cpt_key . '_options');
    }

    if (empty(get_posts(['fields' => 'ids', 'numberposts' => -1, 'post_type' => 'mailpn_mail', 'post_status' => 'any', ]))) {
      $mailpn_title = __('Test email', 'mailpn');
      $mailpn_post_content = __('Test email content', 'mailpn');
      $mailpn_id = $post_functions->mailpn_insert_post(esc_html($mailpn_title), $mailpn_post_content, '', sanitize_title(esc_html($mailpn_title)), 'mailpn_mail', 'publish', 1);

      update_post_meta($mailpn_id, 'mailpn_type', 'email_one_time');
      update_post_meta($mailpn_id, 'mailpn_distribution', 'private_user');
      update_post_meta($mailpn_id, 'mailpn_distribution_user', [get_current_user_id()]);
    }

    update_option('mailpn_options_changed', true);
  }
}