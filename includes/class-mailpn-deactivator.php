<?php

/**
 * Fired during plugin deactivation
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 *
 * @package    MAILPN
 * @subpackage MAILPN/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    MAILPN
 * @subpackage MAILPN/includes
 * @author     Padres en la Nube <info@padresenlanube.com>
 */
class MAILPN_Deactivator {

	/**
	 * Plugin deactivation functions
	 *
	 * Functions to be loaded on plugin deactivation. This actions remove roles, options and post information attached to the plugin.
	 *
	 * @since    1.0.0
	 */
	public static function mailpn_deactivate() {
		$plugin_post = new MAILPN_Post_Type_Mail();
		
		if (get_option('mailpn_options_remove') == 'on') {
      remove_role('mailpn_role_manager');

      $mailpn_mail = get_posts(['fields' => 'ids', 'numberposts' => -1, 'post_type' => 'mailpn_mail', 'post_status' => 'any', ]);

      if (!empty($mailpn_mail)) {
        foreach ($mailpn_mail as $post_id) {
          wp_delete_post($post_id, true);
        }
      }

      foreach ($plugin_post->get_fields() as $mailpn_option) {
        delete_option($mailpn_option['id']);
      }
    }

    update_option('mailpn_options_changed', true);
	}
}