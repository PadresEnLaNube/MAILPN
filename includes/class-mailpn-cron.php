<?php
/**
 * The Cron functionalities of the plugin.
 *
 * Defines the behaviour of the plugin on Cron functions.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 * @package    MAILPN
 * @subpackage MAILPN/includes
 * @author     Padres en la Nube <info@padresenlanube.com>
 */
class MAILPN_Cron {
	/**
	 * Set the plugin schedule for Cron execution
	 *
	 * @since       1.0.0
	 */
	public function cron_schedule() {
    if (!wp_next_scheduled('mailpn_cron_daily')){
      wp_schedule_event(time(), 'daily', 'mailpn_cron_daily');
    }

    if (!wp_next_scheduled('mailpn_cron_ten_minutes')){
      wp_schedule_event(time(), 'mailpn_ten_minutes', 'mailpn_cron_ten_minutes');
    }
	}

	public function cron_ten_minutes_schedule($schedules) {
    $schedules['mailpn_ten_minutes'] = array(
      'interval' => 600,
      'display' => __('Every 10 minutes', 'mailpn'),
    );

    return $schedules;
  }

	/**
	 * Set the plugin cron daily functions to be executed
	 *
	 * @since       1.0.0
	 */
	public function cron_daily() {
		$posts_user_removed_atts = [
      'fields' => 'ids',
      'numberposts' => -1,
      'post_type' => 'mailpn_rec',
      'post_status' => 'publish', 
    ];

    $posts_user_removed = get_posts($posts_user_removed_atts);

    if (!empty($posts_user_removed)) {
      foreach ($posts_user_removed as $post_id) {
        $user_id = get_post_meta($post_id, 'mailpn_rec_to', true);

        if (get_userdata($user_id) === false) {
          wp_trash_post($post_id);
        }
      }
    }
  }

  /**
	 * Set the plugin cron every ten minutes functions to be executed
	 *
	 * @since       1.0.0
	 */
	public function cron_ten_minutes() {
    $mailing_plugin = new MAILPN_Mailing();
    $mailing_plugin->mailpn_queue_process();
  }
}
