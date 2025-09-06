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

    if (!wp_next_scheduled('mailpn_cron_weekly')){
      wp_schedule_event(time(), 'weekly', 'mailpn_cron_weekly');
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
	public function mailpn_cron_daily() {
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
    
    // Clean up stuck pending registrations (older than 30 days)
    $settings_plugin->mailpn_cleanup_stuck_pending_registrations();
    
    // Clean up problematic pending registrations
    $settings_plugin->mailpn_cleanup_problematic_pending_registrations();
  }

  /**
	 * Set the plugin cron every ten minutes functions to be executed
	 *
	 * @since       1.0.0
	 */
	public function mailpn_cron_ten_minutes() {
    $mailing_plugin = new MAILPN_Mailing();
    $mailing_plugin->mailpn_queue_process();
    
    // Process scheduled welcome emails
    $this->mailpn_process_scheduled_welcome_emails();
    
    // Process pending welcome registrations
    $settings_plugin = new MAILPN_Settings();
    $settings_plugin->mailpn_process_pending_welcome_registrations();
    
    // Process WooCommerce automated emails
    if (class_exists('WooCommerce')) {
      $this->mailpn_process_woocommerce_automated_emails();
    }
  }

	/**
	 * Set the plugin cron weekly functions to be executed
	 *
	 * @since       1.0.0
	 */
	public function mailpn_cron_weekly() {
		// Clean up processed pending registrations weekly
		$settings_plugin = new MAILPN_Settings();
		$settings_plugin->mailpn_cleanup_processed_pending_registrations();
		
		// Clean up scheduled emails with unknown users weekly
		$this->mailpn_cleanup_scheduled_unknown_users();
	}
  
  /**
   * Process scheduled welcome emails
   *
   * @since       1.0.0
   */
  public function mailpn_process_scheduled_welcome_emails() {
    $current_time = time();
    $mailing_plugin = new MAILPN_Mailing();
    $settings_plugin = new MAILPN_Settings();
    
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
          // Check if user's email is in the exception lists
          if (!$settings_plugin->mailpn_is_email_excepted($scheduled_email['user_id'])) {
            // Add to queue for immediate sending
            $mailing_plugin->mailpn_queue_add($scheduled_email['email_id'], $scheduled_email['user_id']);
            
            // Log the scheduled email as sent
            $this->mailpn_log_scheduled_welcome_email($scheduled_email);
          } else {
            // Log the scheduled email as skipped due to exception
            $this->mailpn_log_scheduled_welcome_email($scheduled_email, 'skipped_exception');
          }
          
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
   * @param string $status The status of the email (sent, skipped_exception)
   */
  public function mailpn_log_scheduled_welcome_email($scheduled_email, $status = 'sent') {
    // Create a log entry for the scheduled email
    $log_data = [
      'email_id' => $scheduled_email['email_id'],
      'user_id' => $scheduled_email['user_id'],
      'scheduled_time' => $scheduled_email['scheduled_time'],
      'created_time' => $scheduled_email['created_time'],
      'sent_time' => time(),
      'type' => 'scheduled_welcome_email',
      'status' => $status
    ];
    
    $scheduled_logs = get_option('mailpn_scheduled_welcome_logs', []);
    
    // Ensure $scheduled_logs is always an array
    if (!is_array($scheduled_logs)) {
      $scheduled_logs = [];
    }
    
    $scheduled_logs[] = $log_data;
    update_option('mailpn_scheduled_welcome_logs', $scheduled_logs);
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
   * Clean up scheduled emails with unknown users
   *
   * @since       1.0.0
   * @return array Array with removed_count and removed_user_ids
   */
  public function mailpn_cleanup_scheduled_unknown_users() {
    $scheduled_emails = get_option('mailpn_scheduled_welcome_emails', []);
    
    if (!is_array($scheduled_emails)) {
      $scheduled_emails = [];
    }
    
    $cleaned_emails = [];
    $removed_count = 0;
    $removed_user_ids = [];
    
    foreach ($scheduled_emails as $scheduled_email) {
      $user_id = $scheduled_email['user_id'];
      $user = get_userdata($user_id);
      
      // Remove scheduled emails for non-existent users (Desconocido)
      if (!$user) {
        $removed_count++;
        if (!in_array($user_id, $removed_user_ids)) {
          $removed_user_ids[] = $user_id;
        }
        continue;
      }
      
      // Keep this scheduled email
      $cleaned_emails[] = $scheduled_email;
    }
    
    update_option('mailpn_scheduled_welcome_emails', $cleaned_emails);
    
    return [
      'removed_count' => $removed_count,
      'removed_user_ids' => $removed_user_ids
    ];
  }
  
  /**
   * Process WooCommerce automated emails
   *
   * @since       1.0.0
   */
  public function mailpn_process_woocommerce_automated_emails() {
    $current_time = time();
    $mailing_plugin = new MAILPN_Mailing();
    
    // Get all WooCommerce email templates
    $woocommerce_email_types = ['email_woocommerce_purchase', 'email_woocommerce_abandoned_cart'];
    
    foreach ($woocommerce_email_types as $email_type) {
      $this->mailpn_process_woocommerce_email_type($email_type, $current_time, $mailing_plugin);
    }
  }
  
  /**
   * Process specific WooCommerce email type
   *
   * @param string $email_type Email type
   * @param int $current_time Current timestamp
   * @param MAILPN_Mailing $mailing_plugin Mailing plugin instance
   * @since       1.0.0
   */
  private function mailpn_process_woocommerce_email_type($email_type, $current_time, $mailing_plugin) {
    // Get all email templates of this type
    $email_templates = get_posts([
      'post_type' => 'mailpn_mail',
      'post_status' => 'publish',
      'numberposts' => -1,
      'meta_query' => [
        [
          'key' => 'mailpn_type',
          'value' => $email_type,
          'compare' => '='
        ]
      ]
    ]);
    
    foreach ($email_templates as $email_template) {
      // Remove 'email_' prefix from email_type for meta key construction
      $meta_suffix = str_replace('email_', '', $email_type);
      $delay_value = get_post_meta($email_template->ID, 'mailpn_' . $meta_suffix . '_delay_value', true);
      $delay_unit = get_post_meta($email_template->ID, 'mailpn_' . $meta_suffix . '_delay_unit', true);
      
      if (empty($delay_value) || empty($delay_unit)) {
        continue;
      }
      
      // Convert delay to seconds
      $delay_seconds = $this->mailpn_convert_delay_to_seconds($delay_value, $delay_unit);
      
      if ($delay_seconds === false) {
        continue;
      }
      
      // Process based on email type
      switch ($email_type) {
        case 'email_woocommerce_purchase':
          $this->mailpn_process_purchase_emails($email_template->ID, $delay_seconds, $current_time, $mailing_plugin);
          break;
          
        case 'email_woocommerce_abandoned_cart':
          $this->mailpn_process_abandoned_cart_emails($email_template->ID, $delay_seconds, $current_time, $mailing_plugin);
          break;
      }
    }
  }
  
  /**
   * Process purchase emails
   *
   * @param int $email_id Email template ID
   * @param int $delay_seconds Delay in seconds
   * @param int $current_time Current timestamp
   * @param MAILPN_Mailing $mailing_plugin Mailing plugin instance
   * @since       1.0.0
   */
  private function mailpn_process_purchase_emails($email_id, $delay_seconds, $current_time, $mailing_plugin) {
    // Get all users with purchase timestamps
    $users = get_users([
      'meta_key' => 'mailpn_woocommerce_purchase_timestamp',
      'fields' => 'ids'
    ]);
    
    foreach ($users as $user_id) {
      // Check if user still exists
      if (!get_userdata($user_id)) {
        MAILPN_WooCommerce::remove_purchase_meta($user_id);
        continue;
      }
      
      // Check if order still exists
      if (!MAILPN_WooCommerce::user_order_still_exists($user_id)) {
        MAILPN_WooCommerce::remove_purchase_meta($user_id);
        continue;
      }
      
      $purchase_timestamp = MAILPN_WooCommerce::get_purchase_timestamp($user_id);
      
      if (!$purchase_timestamp) {
        continue;
      }
      
      // Check if enough time has passed
      if (($purchase_timestamp + $delay_seconds) <= $current_time) {
        // Check if user has already received this email
        if (!$this->user_has_received_email($email_id, $user_id)) {
          // Add to queue
          $mailing_plugin->mailpn_queue_add($email_id, $user_id);
          
          // Mark user as having received this email
          $this->mark_user_received_email($email_id, $user_id);
        }
        
        // Remove meta to prevent duplicate sending
        MAILPN_WooCommerce::remove_purchase_meta($user_id);
      }
    }
  }
  
  /**
   * Process abandoned cart emails
   *
   * @param int $email_id Email template ID
   * @param int $delay_seconds Delay in seconds
   * @param int $current_time Current timestamp
   * @param MAILPN_Mailing $mailing_plugin Mailing plugin instance
   * @since       1.0.0
   */
  private function mailpn_process_abandoned_cart_emails($email_id, $delay_seconds, $current_time, $mailing_plugin) {
    // Get all users with cart timestamps
    $users = get_users([
      'meta_key' => 'mailpn_woocommerce_cart_timestamp',
      'fields' => 'ids'
    ]);
    
    foreach ($users as $user_id) {
      // Check if user still exists
      if (!get_userdata($user_id)) {
        MAILPN_WooCommerce::remove_cart_abandonment_meta($user_id);
        continue;
      }
      
      // Check if user still has cart items (use saved cart data)
      $saved_cart_items = get_user_meta($user_id, 'mailpn_woocommerce_cart_items', true);
      if (empty($saved_cart_items) || !is_array($saved_cart_items)) {
        MAILPN_WooCommerce::remove_cart_abandonment_meta($user_id);
        continue;
      }
      
      $cart_timestamp = MAILPN_WooCommerce::get_cart_abandonment_timestamp($user_id);
      
      if (!$cart_timestamp) {
        continue;
      }
      
      // Check if enough time has passed
      if (($cart_timestamp + $delay_seconds) <= $current_time) {
        // Check if user has already received this email
        if (!$this->user_has_received_email($email_id, $user_id)) {
          // Add to queue
          $mailing_plugin->mailpn_queue_add($email_id, $user_id);
          
          // Mark user as having received this email
          $this->mark_user_received_email($email_id, $user_id);
        }
        
        // Remove meta to prevent duplicate sending
        MAILPN_WooCommerce::remove_cart_abandonment_meta($user_id);
      }
    }
  }
  
  /**
   * Check if user has already received a specific email
   *
   * @param int $email_id Email template ID
   * @param int $user_id User ID
   * @return bool
   * @since       1.0.0
   */
  private function user_has_received_email($email_id, $user_id) {
    $sent_to_users = get_post_meta($email_id, 'mailpn_sent_to_users', true);
    
    if (empty($sent_to_users) || !is_array($sent_to_users)) {
      return false;
    }
    
    return in_array($user_id, $sent_to_users);
  }
  
  /**
   * Mark user as having received a specific email
   *
   * @param int $email_id Email template ID
   * @param int $user_id User ID
   * @since       1.0.0
   */
  private function mark_user_received_email($email_id, $user_id) {
    $sent_to_users = get_post_meta($email_id, 'mailpn_sent_to_users', true);
    
    if (empty($sent_to_users) || !is_array($sent_to_users)) {
      $sent_to_users = [];
    }
    
    if (!in_array($user_id, $sent_to_users)) {
      $sent_to_users[] = $user_id;
      update_post_meta($email_id, 'mailpn_sent_to_users', $sent_to_users);
    }
  }
  
  /**
   * Convert delay value and unit to seconds
   *
   * @param int $value Delay value
   * @param string $unit Delay unit (minutes, hours, days)
   * @return int|false Delay in seconds or false if invalid
   * @since       1.0.0
   */
  private function mailpn_convert_delay_to_seconds($value, $unit) {
    if (!is_numeric($value) || $value <= 0) {
      return false;
    }
    
    switch ($unit) {
      case 'minutes':
        return $value * 60;
      case 'hours':
        return $value * 3600;
      case 'days':
        return $value * 86400;
      default:
        return false;
    }
  }
}
