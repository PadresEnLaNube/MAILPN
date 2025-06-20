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
    
    // Clean up old scheduled welcome email logs (older than 30 days)
    $this->mailpn_cleanup_old_scheduled_logs();
    
    // Clean up old pending welcome registrations (older than 7 days)
    $settings_plugin = new MAILPN_Settings();
    $settings_plugin->mailpn_cleanup_old_pending_registrations();
  }
  
  /**
   * Clean up old scheduled welcome email logs
   *
   * @since       1.0.0
   */
  public function mailpn_cleanup_old_scheduled_logs() {
    $scheduled_logs = get_option('mailpn_scheduled_welcome_logs', []);
    
    // Ensure $scheduled_logs is always an array
    if (!is_array($scheduled_logs)) {
      $scheduled_logs = [];
    }
    
    $thirty_days_ago = time() - (30 * DAY_IN_SECONDS);
    $cleaned_logs = [];
    
    foreach ($scheduled_logs as $log) {
      if ($log['sent_time'] > $thirty_days_ago) {
        $cleaned_logs[] = $log;
      }
    }
    
    update_option('mailpn_scheduled_welcome_logs', $cleaned_logs);
  }

  /**
	 * Set the plugin cron every ten minutes functions to be executed
	 *
	 * @since       1.0.0
	 */
	public function cron_ten_minutes() {
    $mailing_plugin = new MAILPN_Mailing();
    $mailing_plugin->mailpn_queue_process();
    
    // Process scheduled welcome emails
    $this->mailpn_process_scheduled_welcome_emails();
    
    // Process pending welcome registrations
    $settings_plugin = new MAILPN_Settings();
    $settings_plugin->mailpn_process_pending_welcome_registrations();
  }
  
  /**
   * Process scheduled welcome emails
   *
   * @since       1.0.0
   */
  public function mailpn_process_scheduled_welcome_emails() {
    $current_time = time();
    $mailing_plugin = new MAILPN_Mailing();
    
    // Process emails in a loop until no more are ready to be sent
    while (true) {
      $scheduled_emails = get_option('mailpn_scheduled_welcome_emails', []);
      
      // Ensure $scheduled_emails is always an array
      if (!is_array($scheduled_emails)) {
        $scheduled_emails = [];
      }
      
      if (empty($scheduled_emails)) {
        break;
      }
      
      $updated_scheduled_emails = [];
      $emails_processed = false;
      
      foreach ($scheduled_emails as $scheduled_email) {
        // Check if it's time to send this email
        if ($scheduled_email['scheduled_time'] <= $current_time) {
          // Add to queue for immediate sending
          $mailing_plugin->mailpn_queue_add($scheduled_email['email_id'], $scheduled_email['user_id']);
          
          // Log the scheduled email as sent
          $this->mailpn_log_scheduled_welcome_email($scheduled_email);
          
          $emails_processed = true;
        } else {
          // Keep in scheduled list for later processing
          $updated_scheduled_emails[] = $scheduled_email;
        }
      }

      // Update the scheduled emails list
      update_option('mailpn_scheduled_welcome_emails', $updated_scheduled_emails);
      
      // If no emails were processed in this iteration, break the loop
      if (!$emails_processed) {
        break;
      }
    }
  }
  
  /**
   * Log a scheduled welcome email as sent
   *
   * @param array $scheduled_email The scheduled email data
   */
  public function mailpn_log_scheduled_welcome_email($scheduled_email) {
    // Create a log entry for the scheduled email
    $log_data = [
      'email_id' => $scheduled_email['email_id'],
      'user_id' => $scheduled_email['user_id'],
      'scheduled_time' => $scheduled_email['scheduled_time'],
      'created_time' => $scheduled_email['created_time'],
      'sent_time' => time(),
      'type' => 'scheduled_welcome_email'
    ];
    
    $scheduled_logs = get_option('mailpn_scheduled_welcome_logs', []);
    
    // Ensure $scheduled_logs is always an array
    if (!is_array($scheduled_logs)) {
      $scheduled_logs = [];
    }
    
    $scheduled_logs[] = $log_data;
    update_option('mailpn_scheduled_welcome_logs', $scheduled_logs);
  }
}
