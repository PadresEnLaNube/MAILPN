<?php
/**
 * The Mailing functionalities of the plugin.
 *
 * Defines the behaviour of the plugin on Mailing functions.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 * @package    MAILPN
 * @subpackage MAILPN/includes
 * @author     Padres en la Nube <info@padresenlanube.com>
 */
class MAILPN_Mailing {
  private $wrapped_email_data = null;

  public function mailpn_text($atts) {
    /* echo do_shortcode('[mailpn-text query="addressee_name"]'); */
    $atts = shortcode_atts([
      'user_id' => 'addressee_user_id',
      'query' => 'addressee_name',
    ], $atts);
    
    $user_id = $atts['user_id'];
    $query = $atts['query'];

    $user_info = is_numeric($user_id) ? get_userdata(intval($user_id)) : false;

    if (empty($user_info) || !is_object($user_info)) {
      return '';
    }

    switch ($query) {
      case 'addressee_name':
        return $user_info->first_name . ' ' . $user_info->last_name;
      case 'addressee_first_name':
        return $user_info->first_name;
      case 'addressee_last_name':
        return $user_info->last_name;
      case 'addressee_email':
        return $user_info->user_email;
      case 'addressee_id':
        return $user_info->ID;
      case 'addressee_nickname':
        return $user_info->nickname;
    }
  }

  public function mailpn_contents($atts) {
    /* echo do_shortcode('[mailpn-test mailpn_user_id="1"]'); */
    $atts = shortcode_atts([
      'post_id' => 0,
    ], $atts);
    
    $post_id = $atts['post_id'];
    /* echo do_shortcode('[mailpn-contents]'); */

    $mail_type = get_post_meta($post_id, 'mailpn_type', true);

    if ($mail_type == 'email_published_content') {
      $mail_content = get_post_meta($post_id, 'mailpn_updated_content', true);
      $mail_cpt = get_post_meta($post_id, 'mailpn_updated_content_cpt', true);
      $mail_amount = get_post_meta($post_id, 'mailpn_updated_content_new_amount', true);

      ob_start();
      
      switch ($mail_content) {
        case 'email_published_content_new':
          $mail_posts = get_posts(['fields' => 'ids', 'numberposts' => $mail_amount, 'post_type' => $mail_cpt, 'post_status' => ['any'], 'orderby' => 'publish_date', 'order' => 'DESC', ]);

          if (!empty($mail_posts)) {
            foreach ($mail_posts as $mail_post_id) {
              ?>
                <div class="mailpn-post">
                  <h3><a href="<?php echo esc_url(get_permalink($mail_post_id)); ?>"><?php echo esc_html(get_the_title($mail_post_id)); ?></a></h3>
                </div>
              <?php
            }
          }
          break;
        case 'email_published_content_period':
          // code...
          break;
        case 'email_published_content_date':
          // code...
          break;
      }

      $mailpn_return_string = ob_get_contents(); 
      ob_end_clean(); 
      return $mailpn_return_string;
    }

    return false;
  }

  public function mailpn_sender($atts, $mailpn_content = null) {
    /* echo do_shortcode('[mailpn-sender mailpn_type="email_welcome" mailpn_user_to="1" mailpn_subject="Mail Subject"]<h2 class="mailpn_h2_styles">Title</h2><p class="mailpn_p_styles">Paragraph</p>[/mailpn-sender]'); */

    // Repair attributes potentially broken by square brackets in values
    // WordPress shortcode parser uses ] to close tags, so values like
    // mailpn_subject="New guest - [Sea Suite Spain]" break parsing
    $atts = self::repair_shortcode_atts( $atts, $mailpn_content );

    $atts = shortcode_atts([
      'mailpn_user_to' => 1,
      'mailpn_id' => 0,
      'post_id' => 0,
      'post_parent_id' => 0,
      'mailpn_once' => 0,
      'mailpn_type' => '',
      'mailpn_subject' => '',
    ], $atts);
    
    // Configure PHPMailer for SMTP if enabled
    if (get_option('mailpn_smtp_enabled') === 'on') {
      try {
        add_action('phpmailer_init', [$this, 'mailpn_configure_smtp']);
      } catch (Exception $e) {
        // Continue with default mail settings silently
      }
    }

    $mailpn_user_to = $atts['mailpn_user_to'];
    $mailpn_id = $atts['mailpn_id'];
    $post_id = $atts['post_id'];
    $post_parent_id = $atts['post_parent_id'];
    $mailpn_once = $atts['mailpn_once'];
    $mailpn_type = $atts['mailpn_type'];
    $mailpn_subject = $atts['mailpn_subject'];

    $mailpn_result = 0;
    $user_data = get_userdata($mailpn_user_to);
    $user_email = !empty($user_data) && is_object($user_data) ? $user_data->user_email : '';

    // If mailpn_user_to is a direct email address, use it for exception checks
    if (empty($user_email) && filter_var($mailpn_user_to, FILTER_VALIDATE_EMAIL)) {
      $user_email = $mailpn_user_to;
    }

    // Resolve type early so system emails can bypass restrictions
    $mailpn_type = !empty($mailpn_type) ? $mailpn_type : (!empty($mailpn_id) ? get_post_meta($mailpn_id, 'mailpn_type', true) : bin2hex(openssl_random_pseudo_bytes(6)));

    // System email types that must always be delivered regardless of restrictions
    $mailpn_system_types = ['email_password_reset', 'email_verify_code', 'email_welcome'];
    $is_system_email = in_array($mailpn_type, $mailpn_system_types, true);

    $mailpn_exception_emails = get_option('mailpn_exception_emails');
    $mailpn_exception_emails_domains = get_option('mailpn_exception_emails_domains');
    $mailpn_exception_emails_addresses = get_option('mailpn_exception_emails_addresses');

    // Exception domains and emails check (skip for system emails)
    if ($mailpn_exception_emails == 'on' && !$is_system_email) {
      if ($mailpn_exception_emails_domains == 'on') {
        $mailpn_exception_emails_domain = get_option('mailpn_exception_emails_domain');

        if (!empty($mailpn_exception_emails_domain)) {
          // Check if whitelist is enabled and email is whitelisted
          $mailpn_domain_whitelist_enabled = get_option('mailpn_exception_emails_domains_whitelist');
          $mailpn_domain_whitelist = $mailpn_domain_whitelist_enabled == 'on' ? get_option('mailpn_exception_emails_domains_whitelist_address') : [];
          $is_whitelisted = !empty($mailpn_domain_whitelist) && in_array($user_email, $mailpn_domain_whitelist);

          foreach ($mailpn_exception_emails_domain as $mailpn_exception_email_domain) {
            if (strpos($user_email, $mailpn_exception_email_domain) !== false && !$is_whitelisted) {
              return false;
            }
          }
        }
      }

      if ($mailpn_exception_emails_addresses == 'on') {
        $mailpn_exception_emails_address = get_option('mailpn_exception_emails_address');

        if (!empty($mailpn_exception_emails_address) && in_array($user_email, $mailpn_exception_emails_address)) {
          return false;
        }
      }
    }
    $mailpn_subject = !empty($mailpn_subject) ? $mailpn_subject : (!empty($mailpn_id) ? esc_html(get_the_title($mailpn_id)) : esc_html(__('Mail subject', 'mailpn')));

    $mailpn_content = !empty($mailpn_id) ? get_post($mailpn_id)->post_content : $mailpn_content;

    if (!empty($mailpn_content)) {
      $content_filters = apply_filters('mailpn_content_filters', [
        '[user-name]' => '[user-name user_id="' . $mailpn_user_to . '"]',
        '[post-name]' => '[post-name post_id="' . $post_id . '"]',
        '[new-contents]' => '[new-contents post_id="' . $post_id . '" mail_id="' . $mailpn_id . '"]',
      ], $post_id, $post_parent_id, $mailpn_id);

      foreach ($content_filters as $filter_base => $filter_final) {
        if (strpos($mailpn_content, $filter_base) !== false) {
          $mailpn_content = str_replace($filter_base, $filter_final, $mailpn_content);
        }
      }
    }

    $mailpn_content = do_shortcode($mailpn_content);
    
    // Replace links with tracking links if click tracking is enabled
    if (!empty($mailpn_id) && !empty($mailpn_user_to) && get_option('mailpn_click_tracking') === 'on') {
        $mailpn_content = MAILPN_Click_Tracking::replace_links($mailpn_content, $mailpn_id, $mailpn_user_to);
    }

    // Validation: Don't send email if content is empty or subject is default
    if (empty(trim($mailpn_content)) || $mailpn_subject === esc_html(__('Mail subject', 'mailpn'))) {
        return false;
    }

    $mailpn_attachments = [];
    $attachments = !empty($mailpn_id) ? get_post_meta($mailpn_id, 'mailpn_attachments', true) : [];

    if (!empty($attachments)) {
      foreach ($attachments as $attachment_id) {
        $mailpn_attachments[] = get_attached_file($attachment_id);
      }
    }

    $mailpn_socials = [
      // 'Facebook' => ['img_src' => esc_url(home_url()) . '/wp-content/uploads/2019/08/pn-facebook.png', 'url' => ''],
    ];

    $mailpn_legal_name = get_option('mailpn_legal_name');
    $mailpn_legal_address = get_option('mailpn_legal_address');

    $headers[] = 'Content-Type: text/html; charset=UTF-8';

    // List-Unsubscribe header (required by Gmail/Outlook to avoid spam)
    if (!filter_var($mailpn_user_to, FILTER_VALIDATE_EMAIL) && class_exists('USERSPN')) {
      $unsubscribe_url = add_query_arg([
        'mailpn_action' => 'subscription-unsubscribe',
        'user' => $mailpn_user_to,
      ], home_url());
      $unsubscribe_url = wp_nonce_url($unsubscribe_url, 'subscription-unsubscribe', 'subscription-unsubscribe-nonce');
      $headers[] = 'List-Unsubscribe: <' . esc_url($unsubscribe_url) . '>';
      $headers[] = 'List-Unsubscribe-Post: List-Unsubscribe=One-Click';
    }

    $mailpn_message = self::mailpn_template($mailpn_subject, $mailpn_content, $mailpn_socials, $mailpn_legal_name, $mailpn_legal_address, $mailpn_user_to, $mailpn_id);

    if (filter_var($mailpn_user_to, FILTER_VALIDATE_EMAIL)) {
      $mailpn_result = wp_mail($mailpn_user_to, $mailpn_subject, $mailpn_message, $headers, $mailpn_attachments);
    }elseif (class_exists('USERSPN') && (get_user_meta($mailpn_user_to, 'userspn_notifications', true) == 'on' || in_array($mailpn_type, ['email_verify_code'])) && !empty($user_email) && !(self::mailpn_once_mailed($mailpn_id, $mailpn_user_to, $mailpn_once, $mailpn_type))) {
      $mailpn_result = wp_mail($user_email, $mailpn_subject, $mailpn_message, $headers, $mailpn_attachments);
    }else{
      $wph_meta_value = [
        'mailpn_user_to' => $mailpn_user_to,
        'user_email' => $user_email,
        'mailpn_type' => $mailpn_type,
        'mailpn_subject' => $mailpn_subject,
        'notifications' => get_user_meta($mailpn_user_to, 'userspn_notifications', true),
        'once' => !(self::mailpn_once_mailed($mailpn_id, $mailpn_user_to, $mailpn_once, $mailpn_type)),
      ];

      $unique_id = strtotime('now') . '-' . $mailpn_user_to;

      if(!empty($unique_id)) {
        if(empty(get_option('mailpn_error'))) {
          update_option('mailpn_error', [$mailpn_id => [$unique_id => $wph_meta_value]]);
        }else{
          $wph_option_new = get_option('mailpn_error', true);
          $wph_option_new[$mailpn_id][$unique_id] = $wph_meta_value;
          update_option('mailpn_error', $wph_option_new);
        }
      }
        
      return false;
    }
    
    $post_functions = new MAILPN_Functions_Post();

    if ($mailpn_result) {
      $post_functions->mailpn_insert_post($mailpn_subject, $mailpn_message, '', esc_url($mailpn_subject), 'mailpn_rec', 'publish', 1, 0, [], [], [
        'mailpn_rec_content' => $mailpn_message,
        'mailpn_rec_type' => $mailpn_type,
        'mailpn_rec_to' => $mailpn_user_to,
        'mailpn_rec_to_email' => $user_email,
        'mailpn_rec_attachments' => $mailpn_attachments,
        'mailpn_rec_mail_id' => $mailpn_id,
        'mailpn_rec_mail_result' => $mailpn_result,
        'mailpn_rec_post_id' => $post_id,
        'mailpn_rec_headers' => implode("\n", $headers),
        'mailpn_rec_error' => '',
        'mailpn_rec_server_ip' => $_SERVER['SERVER_ADDR'] ?? '',
        'mailpn_rec_sent_datetime' => current_time('mysql'),
        'mailpn_rec_subject' => $mailpn_subject,
        'mailpn_rec_content_html' => $mailpn_message,
        'mailpn_rec_content_text' => wp_strip_all_tags($mailpn_message),
      ], false);

      return true;
    }else{
      // Enhanced error handling
      $error_message = '';
      $error_details = [];
      
      // Check PHPMailer error
      if (isset($GLOBALS['phpmailer']) && is_object($GLOBALS['phpmailer'])) {
        $error_message = $GLOBALS['phpmailer']->ErrorInfo;
        
        // Get additional error details
        $error_details[] = 'PHPMailer Error: ' . $error_message;
        
        // Get SMTP settings for error reporting
        $error_details[] = 'SMTP Settings:';
        $error_details[] = '- SMTP Enabled: ' . (get_option('mailpn_smtp_enabled') === 'on' ? 'Yes' : 'No');
        $error_details[] = '- SMTP Host: ' . get_option('mailpn_smtp_host');
        $error_details[] = '- SMTP Port: ' . get_option('mailpn_smtp_port');
        $error_details[] = '- SMTP Secure: ' . get_option('mailpn_smtp_secure');
        $error_details[] = '- SMTP Auth: ' . (get_option('mailpn_smtp_auth') === 'on' ? 'Enabled' : 'Disabled');
        
        // Check WordPress mail settings
        $error_details[] = 'WordPress Mail Settings:';
        $error_details[] = '- wp_mail() function exists: ' . (function_exists('wp_mail') ? 'Yes' : 'No');
        $error_details[] = '- Default mail server: ' . (ini_get('sendmail_path') ? ini_get('sendmail_path') : 'Not configured');
        
        // Add server information
        $error_details[] = 'Server Information:';
        $error_details[] = '- PHP Version: ' . PHP_VERSION;
        $error_details[] = '- WordPress Version: ' . get_bloginfo('version');
        $error_details[] = '- Server Software: ' . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown');
      } else {
        $error_message = 'PHPMailer object not available';
        $error_details[] = 'PHPMailer Error: ' . $error_message;
      }
      
      // Store detailed error information for AJAX responses
      $GLOBALS['mailpn_last_error'] = [
        'message' => $error_message,
        'details' => $error_details,
        'timestamp' => current_time('mysql'),
        'phpmailer_error' => isset($GLOBALS['phpmailer']) && is_object($GLOBALS['phpmailer']) ? $GLOBALS['phpmailer']->ErrorInfo : '',
        'smtp_enabled' => get_option('mailpn_smtp_enabled'),
        'smtp_host' => get_option('mailpn_smtp_host'),
        'smtp_port' => get_option('mailpn_smtp_port')
      ];

      // Error logging removed

      $post_functions->mailpn_insert_post($mailpn_subject, $mailpn_message, '', esc_url($mailpn_subject), 'mailpn_rec', 'publish', 1, 0, [], [], [
        'mailpn_rec_content' => $mailpn_message,
        'mailpn_rec_type' => $mailpn_type,
        'mailpn_rec_to' => $mailpn_user_to,
        'mailpn_rec_to_email' => $user_email,
        'mailpn_rec_attachments' => $mailpn_attachments,
        'mailpn_rec_mail_id' => $mailpn_id,
        'mailpn_rec_mail_result' => $mailpn_result,
        'mailpn_rec_post_id' => $post_id,
        'mailpn_rec_headers' => implode("\n", $headers),
        'mailpn_rec_error' => implode("\n", $error_details),
        'mailpn_rec_server_ip' => $_SERVER['SERVER_ADDR'] ?? '',
        'mailpn_rec_sent_datetime' => current_time('mysql'),
        'mailpn_rec_subject' => $mailpn_subject,
        'mailpn_rec_content_html' => $mailpn_message,
        'mailpn_rec_content_text' => wp_strip_all_tags($mailpn_message),
      ], false);

      if (get_option('mailpn_errors_to_admin') == 'on') {
        $admin_error_message = 'Error sending mail - ' . get_bloginfo('name') . "\n\n";
        $admin_error_message .= "Recipient: {$user_email}\n";
        $admin_error_message .= "Type: {$mailpn_type}\n";
        $admin_error_message .= "Subject: {$mailpn_subject}\n";
        $admin_error_message .= "Attachments: " . implode(', ', $mailpn_attachments) . "\n\n";
        $admin_error_message .= "Error Details:\n" . implode("\n", $error_details);
        
        $error_email = wp_mail(
          get_bloginfo('admin_email'), 
          'Error sending mail - ' . get_bloginfo('name'),
          $admin_error_message,
          ['Content-Type: text/plain; charset=UTF-8'],
          $mailpn_attachments
        );
      }

      return false;
    }
  }

  /**
   * Repair shortcode attributes broken by square brackets in values.
   *
   * WordPress shortcode parser uses ] to close tags. If attribute values
   * contain square brackets (e.g., site names like "[Sea Suite Spain]"),
   * the parser breaks: attributes become numeric-indexed orphans and
   * remaining attribute text spills into $content.
   *
   * This method detects breakage and recovers attributes from both the
   * orphaned array entries and the spilled content.
   *
   * @param array|string $raw_atts Raw attributes from shortcode parser.
   * @param string|null  $content  Shortcode content (passed by reference, cleaned of spillover).
   * @return array Repaired attributes array.
   */
  private static function repair_shortcode_atts( $raw_atts, &$content ) {
    if ( ! is_array( $raw_atts ) ) {
      return $raw_atts;
    }

    // Detect broken parsing: numeric-indexed values indicate shortcode_parse_atts
    // could not parse them as key="value" pairs
    $has_numeric_keys = false;
    foreach ( $raw_atts as $key => $value ) {
      if ( is_int( $key ) ) {
        $has_numeric_keys = true;
        break;
      }
    }

    if ( ! $has_numeric_keys ) {
      // No breakage detected — just strip stray brackets from existing values as safety net
      foreach ( $raw_atts as $key => $value ) {
        if ( is_string( $value ) ) {
          $raw_atts[ $key ] = str_replace( [ '[', ']' ], '', $value );
        }
      }
      return $raw_atts;
    }

    // Broken parsing detected — try to recover attributes from content
    $known_attrs = [
      'mailpn_type', 'mailpn_subject', 'mailpn_user_to',
      'mailpn_id', 'post_id', 'post_parent_id', 'mailpn_once',
    ];

    if ( ! empty( $content ) ) {
      foreach ( $known_attrs as $attr_name ) {
        if ( preg_match( '/' . preg_quote( $attr_name, '/' ) . '=["\']([^"\']*)["\']/', $content, $match ) ) {
          $raw_atts[ $attr_name ] = str_replace( [ '[', ']' ], '', $match[1] );
          $content = str_replace( $match[0], '', $content );
        }
      }

      // Clean spillover characters: leading ], ", [, whitespace
      $content = preg_replace( '/^[\s"\]\[]+/', '', $content );
      $content = preg_replace( '/[\]\[\s"]+$/', '', $content );
    }

    // Remove numeric-indexed orphan entries (broken fragments)
    foreach ( $raw_atts as $key => $value ) {
      if ( is_int( $key ) ) {
        unset( $raw_atts[ $key ] );
      }
    }

    // Strip brackets from all remaining attribute values
    foreach ( $raw_atts as $key => $value ) {
      if ( is_string( $value ) ) {
        $raw_atts[ $key ] = str_replace( [ '[', ']' ], '', $value );
      }
    }

    return $raw_atts;
  }

  /**
   * Configure PHPMailer to use SMTP
   *
   * @param PHPMailer $phpmailer The PHPMailer instance
   */
  public function mailpn_configure_smtp($phpmailer) {
    try {
      // Get SMTP settings
      $smtp_host = get_option('mailpn_smtp_host');
      $smtp_port = get_option('mailpn_smtp_port');
      $smtp_secure = get_option('mailpn_smtp_secure');
      $smtp_auth = get_option('mailpn_smtp_auth');
      $smtp_username = get_option('mailpn_smtp_username');
      $smtp_password = get_option('mailpn_smtp_password');
      $smtp_from_email = get_option('mailpn_smtp_from_email');
      $smtp_from_name = get_option('mailpn_smtp_from_name');

      // Validate required settings - if not configured, don't configure SMTP and let WordPress use default mail
      if (empty($smtp_host)) {
        // SMTP is enabled but not configured - remove SMTP configuration and use default mail
        return;
      }
      if (empty($smtp_port)) {
        // SMTP port is not configured - remove SMTP configuration and use default mail
        return;
      }

      // Enable SMTP only if configuration is valid
      $phpmailer->isSMTP();

      // Configure SMTP settings
      $phpmailer->Host = $smtp_host;
      $phpmailer->Port = $smtp_port;
      
      // Set security type
      if (!empty($smtp_secure) && $smtp_secure !== 'none') {
        $phpmailer->SMTPSecure = $smtp_secure;
      }

      // Set authentication if enabled
      if ($smtp_auth === 'on') {
        if (empty($smtp_username) || empty($smtp_password)) {
          // SMTP auth is enabled but credentials are missing - disable SMTP and use default mail
          return;
        }
        $phpmailer->SMTPAuth = true;
        $phpmailer->Username = $smtp_username;
        $phpmailer->Password = $smtp_password;
        
        // Additional Gmail-specific settings
        if (strpos($smtp_host, 'gmail.com') !== false) {
          // Gmail requires these specific settings
          $phpmailer->SMTPOptions = array(
            'ssl' => array(
              'verify_peer' => false,
              'verify_peer_name' => false,
              'allow_self_signed' => true
            )
          );
        }
      }

      // Set sender information if configured
      if (!empty($smtp_from_email)) {
        $phpmailer->From = $smtp_from_email;
      }
      if (!empty($smtp_from_name)) {
        $phpmailer->FromName = $smtp_from_name;
      }

      // Enable debug output if WP_DEBUG is enabled
      // SMTP verbose debug output removed

      // Set timeout
      $phpmailer->Timeout = 30; // 30 seconds timeout

    } catch (Exception $e) {
      throw $e; // Re-throw to be caught by the calling function
    }
  }

  public function mailpn_template($mailpn_subject, $mailpn_content, $mailpn_socials, $mailpn_legal_name, $mailpn_legal_address, $mailpn_user_to, $mailpn_id = 0) {
    $mailpn_template_css = file_get_contents(MAILPN_DIR . 'assets/css/mail-template.css');

    wp_register_style('mail-template-css', false);
    wp_enqueue_style('mail-template-css');
    wp_add_inline_style('mail-template-css', $mailpn_template_css);
    
    $mailpn_max_width = get_option('mailpn_max_width');
    $mailpn_max_width_val = (!empty($mailpn_max_width) && is_numeric($mailpn_max_width)) ? intval($mailpn_max_width) : 700;
    ob_start();
    ?>
      <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
      <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
          <meta name="viewport" content="width=device-width" />
          <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
          <title><?php echo esc_html($mailpn_subject); ?></title>
        </head>

        <body class="mailpn-content" style="margin:0;padding:0;">
          <table class="mailpn-table-main" width="<?php echo esc_attr($mailpn_max_width_val); ?>" cellpadding="0" cellspacing="0" border="0" align="center" style="width:100%;max-width:<?php echo esc_attr($mailpn_max_width_val); ?>px;margin:0 auto;">
            <tbody>
              <?php if (!empty(get_option('mailpn_image_header'))): ?>
                <tr style="text-align:center;">
                  <td class="text-align-center mailpn-mb-30" align="center">
                    <a target="_blank" href="<?php echo esc_url(home_url()); ?>" class="mailpn-header-image" style="color:#3d731a;text-decoration:none;"><img src="<?php echo esc_url(wp_get_attachment_image_src(get_option('mailpn_image_header'), 'full')[0]); ?>" border="0" alt="<?php echo esc_attr($mailpn_legal_name); ?>" style="max-height:150px;width:auto;margin-right:5px;margin-left:5px;margin-bottom:30px;"></a>
                  </td>
                </tr>
              <?php endif ?>

              <tr style="text-align:left;">
                <td>
                  <?php echo do_shortcode($mailpn_content); ?>
                </td>
              </tr>

              <?php if (!empty($mailpn_socials)): ?>
                <tr>
                  <td>
                    <p><?php esc_html_e('Follow us to keep in touch.', 'mailpn'); ?></p>
                  </td>
                </tr>

                <tr>
                  <table class="mailpn-table-social" align="center">
                    <tr>
                      <?php foreach ($mailpn_socials as $mailpn_social_name => $mailpn_social_data): ?>
                        <td class="mailpn-td-social">
                          <div align="center">
                            <a target="_blank" href="<?php echo esc_url($mailpn_social_data['url']); ?>" class="<?php echo esc_attr($mailpn_social_name); ?>"><img src="<?php echo esc_url($mailpn_social_data['img_src']); ?>" alt="<?php echo esc_attr($mailpn_social_name); ?>" class="mailpn-social-img"></a>
                          </div>
                        </td>
                      <?php endforeach ?>
                    </tr>
                  </table>
                </tr>
              <?php endif ?>

              <?php if (!empty(get_option('mailpn_image_footer'))): ?>
                <tr>
                  <td class="text-align-center" align="center">
                    <a target="_blank" href="<?php echo esc_url(home_url()); ?>" class="mailpn-header-image"><img src="<?php echo esc_url(wp_get_attachment_image_src(get_option('mailpn_image_footer'), 'full')[0]); ?>" border="0" alt="<?php echo esc_attr($mailpn_legal_name); ?>" style="height:50px;width:auto;margin-right:5px;margin-left:5px;"></a>
                  </td>
                </tr>
              <?php endif ?>

              <tr>
                <td width="100%" valign="top" align="center">
                  <div style="padding:20px;" class="mailpn-td-footer text-align-center">
                    <small class="text-align-center">
                      <p>© <?php echo esc_html($mailpn_legal_name); ?> <?php echo esc_html(gmdate('Y')); ?>.<br><?php esc_html_e('All rights reserved', 'mailpn'); ?>.</p>
                      <p><?php 
                        $footer_reason = get_option('mailpn_footer_reason');
                        echo !empty($footer_reason) 
                          ? esc_html($footer_reason) 
                          : esc_html__('You receive this email for your relationship with the project.', 'mailpn');
                      ?></p>

                      <?php if (class_exists('USERSPN') && !empty($mailpn_user_to)): ?>
                        <table align="center">
                          <tr>
                            <td align="center">
                              <a href="<?php echo esc_url(add_query_arg(['mailpn_action' => 'popup_open', 'mailpn_popup' => 'userspn-profile-popup', 'mailpn_tab' => 'notifications', 'user_id' => $mailpn_user_to, '_wpnonce' => wp_create_nonce('mailpn_action_' . $mailpn_user_to)], home_url())); ?>"><small><?php esc_html_e('Manage subscription', 'mailpn'); ?></small></a>
                            </td>
                            
                            <td></td>

                            <td align="center">
                              <?php if (!filter_var($mailpn_user_to, FILTER_VALIDATE_EMAIL)): ?>
                                <?php echo wp_kses_post(self::mailpn_subscription_unsubscribe_btn($mailpn_user_to)); ?>
                              <?php endif ?>
                            </td>
                          </tr>
                        </table>
                      <?php endif ?>

                      <p><?php echo esc_html($mailpn_legal_address); ?></p>
                    </small>
                  </div>
                </td>
                
                <td>
                  <img src="<?php echo esc_url(home_url('wp-json/mailpn/v1/track/' . $mailpn_user_to . '/' . $mailpn_id)); ?>" width="1" height="1" alt="" style="display:none" onload="document.getElementById('mailpn-confirm-read').style.display='none';" onerror="document.getElementById('mailpn-confirm-read').style.display='block';" />
                </td>
              </tr>
            </tbody>
          </table>
        </body>
      </html> 
    <?php
    $mailpn_return_string = ob_get_contents(); 
    ob_end_clean(); 
    return $mailpn_return_string;
  }

  /**
   * Register the tracking pixel endpoint
   */
  public function register_tracking_endpoint() {
    register_rest_route('mailpn/v1', '/track/(?P<user_id>\d+)/(?P<mail_id>\d+)', [
      'methods' => 'GET',
      'callback' => [$this, 'handle_tracking_pixel'],
      'permission_callback' => '__return_true'
    ]);
  }

  /**
   * Handle the tracking pixel request
   */
  public function handle_tracking_pixel($request) {
    $user_id = $request->get_param('user_id');
    $mail_id = $request->get_param('mail_id');
    
    // Build meta query based on whether we have a specific mail_id
    $meta_query = [
      [
        'key' => 'mailpn_rec_to',
        'value' => $user_id
      ]
    ];

    // If we have a specific mail_id, add it to the query
    if (!empty($mail_id)) {
      $meta_query[] = [
        'key' => 'mailpn_rec_mail_id',
        'value' => $mail_id
      ];
    }
    
    // Get the email record
    $args = [
      'post_type' => 'mailpn_rec',
      'posts_per_page' => 1,
      'meta_query' => $meta_query,
      'orderby' => 'date',
      'order' => 'DESC'
    ];
    
    $query = new WP_Query($args);
    
    if ($query->have_posts()) {
      $post = $query->posts[0];
      
      // Update the email open status
      update_post_meta($post->ID, 'mailpn_rec_opened', true);
      update_post_meta($post->ID, 'mailpn_rec_opened_at', current_time('mysql'));
    }
    
    // Return a 1x1 transparent pixel
    header('Content-Type: image/gif');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Use a predefined constant for the 1x1 transparent GIF
    echo "\x47\x49\x46\x38\x39\x61\x01\x00\x01\x00\x80\x00\x00\xff\xff\xff\x00\x00\x00\x21\xf9\x04\x01\x00\x00\x00\x00\x2c\x00\x00\x00\x00\x01\x00\x01\x00\x00\x02\x02\x44\x01\x00\x3b";
    exit;
  }

  public function mailpn_user_name($atts) {
    $a = extract(shortcode_atts([
      'user_id' => 0,
    ], $atts));

    if (!empty($user_id)) {
      $user_info = get_userdata($user_id);

      if (!empty($user_info->first_name)) {
        return $user_info->first_name;
      }else if (!empty($user_info->last_name)) {
        return $user_info->last_name;
      }else if (!empty($user_info->user_nicename)){
        return $user_info->user_nicename;
      }else{
        return $user_info->user_email;
      }
    }
  }

  public function mailpn_post_name($atts) {
    $a = extract(shortcode_atts([
      'post_id' => 0,
    ], $atts));

    if (!empty($post_id)) {
      ob_start();
      ?>
        <a href="<?php echo esc_url(get_permalink($post_id)); ?>"><strong><?php echo esc_html(get_the_title($post_id)); ?></strong></a>
      <?php
      $wph_return_string = ob_get_contents(); 
      ob_end_clean(); 
      return $wph_return_string;
    }
  }

  public function mailpn_new_contents($atts) {
    $atts = shortcode_atts([
      'post_id' => 0,
      'mail_id' => 0,
    ], $atts);

    $post_id = $atts['post_id'];
    $mail_id = intval($atts['mail_id']);

    // Read CPT and period from the mail template (mail_id) or fallback to post_id.
    $source_id = $mail_id ? $mail_id : $post_id;

    if (empty($source_id)) {
      return '';
    }

    $post_type = get_post_meta($source_id, 'mailpn_updated_content_cpt', true);
    if (empty($post_type)) {
      $post_type = 'post';
    }

    $posts_atts = [
      'fields' => 'ids',
      'numberposts' => 20,
      'post_type' => $post_type,
      'post_status' => 'publish',
      'orderby' => 'publish_date',
      'order' => 'DESC',
    ];

    // Apply date filter based on the mail template's period configuration.
    if ($mail_id) {
      $days = self::mailpn_get_period_days($mail_id);
      if ($days > 0) {
        $posts_atts['date_query'] = [['after' => $days . ' days ago']];
      }
    }

    if (class_exists('Polylang')) {
      $posts_atts['lang'] = pll_current_language('slug');
    }

    $posts = get_posts($posts_atts);

    if (empty($posts)) {
      return '';
    }

    ob_start();
    foreach ($posts as $mail_post_id) {
      ?>
        <div style="padding:8px 0;border-bottom:1px solid #eee;">
          <a href="<?php echo esc_url(get_permalink($mail_post_id)); ?>" style="color:#007cba;text-decoration:none;font-weight:bold;"><?php echo esc_html(get_the_title($mail_post_id)); ?></a>
        </div>
      <?php
    }
    return ob_get_clean();
  }

  /**
   * Calculate how many days back to query based on a mail template's period config.
   *
   * @param int $mail_id The mail template post ID.
   * @return int Number of days (0 means no filter).
   */
  public static function mailpn_get_period_days($mail_id) {
    $mail_type = get_post_meta($mail_id, 'mailpn_type', true);

    // Periodic emails: mailpn_periodic_period.
    if ($mail_type === 'email_periodic') {
      $period = get_post_meta($mail_id, 'mailpn_periodic_period', true);
      return self::mailpn_period_to_days($period);
    }

    // Published content emails: mailpn_updated_content_period.
    if ($mail_type === 'email_published_content') {
      $when = get_post_meta($mail_id, 'mailpn_updated_content', true);

      if ($when === 'email_published_content_period') {
        $period = get_post_meta($mail_id, 'mailpn_updated_content_period', true);
        return self::mailpn_period_to_days($period);
      }

      // "Each new content" → no date filter (just the latest).
      return 0;
    }

    // Welcome / delay-based: value + unit.
    $delay_prefixes = [
      'email_welcome'                    => 'mailpn_welcome_delay',
      'email_woocommerce_purchase'       => 'mailpn_woocommerce_purchase_delay',
      'email_woocommerce_abandoned_cart'  => 'mailpn_woocommerce_abandoned_cart_delay',
    ];

    if (isset($delay_prefixes[$mail_type])) {
      $prefix = $delay_prefixes[$mail_type];
      $value  = intval(get_post_meta($mail_id, $prefix . '_value', true));
      $unit   = get_post_meta($mail_id, $prefix . '_unit', true);
      if ($value > 0 && $unit) {
        return self::mailpn_delay_to_days($value, $unit);
      }
    }

    return 7; // Default: weekly.
  }

  /**
   * Convert a period keyword (hourly/daily/weekly/monthly/yearly) to days.
   */
  private static function mailpn_period_to_days($period) {
    switch ($period) {
      case 'hourly':  return 1;
      case 'daily':   return 1;
      case 'weekly':  return 7;
      case 'monthly': return 30;
      case 'yearly':  return 365;
      default:        return 7;
    }
  }

  /**
   * Convert a delay value + unit to days.
   */
  private static function mailpn_delay_to_days($value, $unit) {
    switch ($unit) {
      case 'minutes': return 1;
      case 'hours':   return max(1, intval($value / 24));
      case 'days':    return $value;
      case 'weeks':   return $value * 7;
      case 'months':  return $value * 30;
      case 'years':   return $value * 365;
      default:        return 7;
    }
  }

  public function mailpn_tools($atts) {
    /* echo do_shortcode('[mailpn-tools post_id="1"]'); */
    $a = extract(shortcode_atts([
      'post_id' => 0,
    ], $atts));
  
    if (empty($post_id)) {
      return false;
    }

    $mailpn_type = get_post_meta($post_id, 'mailpn_type', true);

    ob_start();
    ?>
      <?php if (in_array($mailpn_type, ['email_one_time', 'email_published_content', 'email_coded', 'email_periodic'])): ?>
        <?php $mailpn_status = get_post_meta($post_id, 'mailpn_status', true); ?>
        
        <?php if ($mailpn_status == 'sent'): ?>
          <?php
            $mailpn_timestamps = get_post_meta($post_id, 'mailpn_timestamp_sent', true);
            $last_sent = !empty($mailpn_timestamps) && is_array($mailpn_timestamps) ? end($mailpn_timestamps) : '';
            $emails_sent_count = count(get_posts(['fields' => 'ids', 'numberposts' => -1, 'post_type' => 'mailpn_rec', 'post_status' => ['any'], 'meta_key' => 'mailpn_rec_mail_id', 'meta_value' => $post_id, 'orderby' => 'ID', 'order' => 'ASC']));
            $mailpn_errors = get_option('mailpn_error');
            $has_errors = !empty($mailpn_errors[$post_id]);
          ?>
          <div class="mailpn-status-card mailpn-status-sent">
            <div class="mailpn-status-header">
              <i class="material-icons-outlined">check_circle</i>
              <span class="mailpn-status-label"><?php esc_html_e('Sent', 'mailpn'); ?></span>
            </div>
            <div class="mailpn-status-body">
              <div class="mailpn-status-stats">
                <?php if (!empty($last_sent)): ?>
                  <span class="mailpn-status-stat"><i class="material-icons-outlined">schedule</i> <?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $last_sent)); ?></span>
                <?php endif; ?>
                <span class="mailpn-status-stat"><i class="material-icons-outlined">mail</i> <?php echo esc_html($emails_sent_count); ?> <?php esc_html_e('emails sent', 'mailpn'); ?></span>
                <?php if ($mailpn_type === 'email_periodic' && !empty($last_sent)): ?>
                  <?php
                    $periodic_period = get_post_meta($post_id, 'mailpn_periodic_period', true);
                    $periodic_interval = MAILPN_Cron::mailpn_periodic_interval_seconds_static($periodic_period);
                    $next_send = $last_sent + $periodic_interval;
                    $period_labels = [
                      'hourly'  => __('Hourly', 'mailpn'),
                      'daily'   => __('Daily', 'mailpn'),
                      'weekly'  => __('Weekly', 'mailpn'),
                      'monthly' => __('Monthly', 'mailpn'),
                      'yearly'  => __('Yearly', 'mailpn'),
                    ];
                    $period_label = isset($period_labels[$periodic_period]) ? $period_labels[$periodic_period] : $period_labels['weekly'];
                  ?>
                  <span class="mailpn-status-stat"><i class="material-icons-outlined">update</i> <?php esc_html_e('Next send:', 'mailpn'); ?> <?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $next_send)); ?></span>
                  <span class="mailpn-status-stat"><i class="material-icons-outlined">repeat</i> <?php echo esc_html($period_label); ?></span>
                <?php endif; ?>
              </div>

              <?php if ($has_errors): ?>
                <div class="mailpn-status-errors">
                  <p class="mailpn-status-error-msg"><i class="material-icons-outlined">warning</i> <?php esc_html_e('Some errors occurred during sending.', 'mailpn'); ?></p>
                  <details class="mailpn-status-error-details">
                    <summary><?php esc_html_e('View affected users', 'mailpn'); ?></summary>
                    <ul>
                      <?php foreach ($mailpn_errors[$post_id] as $unique_id => $mailpn_error): ?>
                        <?php $user_info = get_userdata($mailpn_error['mailpn_user_to']); ?>
                        <?php if (!empty($user_info)): ?>
                          <li>
                            <a href="<?php echo esc_url(admin_url('user-edit.php?user_id=' . $mailpn_error['mailpn_user_to'])); ?>" target="_blank">#<?php echo esc_html($mailpn_error['mailpn_user_to']); ?> <?php echo esc_html($user_info->first_name) . ' ' . esc_html($user_info->last_name); ?></a>
                            (<a href="mailto:<?php echo esc_attr($user_info->user_email); ?>"><?php echo esc_html($user_info->user_email); ?></a>)
                          </li>
                        <?php endif; ?>
                      <?php endforeach; ?>
                    </ul>
                  </details>
                </div>
              <?php endif; ?>
            </div>
            <div class="mailpn-status-actions">
              <a href="<?php echo esc_url(admin_url('edit.php?post_type=mailpn_rec&mailpn_type_filter=' . $mailpn_type)); ?>" target="_blank" class="mailpn-btn mailpn-btn-mini"><?php esc_html_e('View submissions', 'mailpn'); ?></a>
              <a href="#" data-mailpn-post-id="<?php echo esc_attr($post_id); ?>" class="mailpn-btn mailpn-btn-mini mailpn-btn-resend-all"><?php esc_html_e('Resend to all', 'mailpn'); ?></a>
              <?php if ($mailpn_type === 'email_periodic'): ?>
                <a href="#" data-mailpn-post-id="<?php echo esc_attr($post_id); ?>" class="mailpn-btn mailpn-btn-mini mailpn-btn-force-send-periodic">
                  <i class="material-icons-outlined mailpn-vertical-align-middle mailpn-font-size-16">send</i>
                  <?php esc_html_e('Force send now', 'mailpn'); ?>
                </a>
              <?php endif; ?>
              <?php if ($has_errors): ?>
                <a href="#" data-mailpn-post-id="<?php echo esc_attr($post_id); ?>" class="mailpn-btn mailpn-btn-mini mailpn-btn-error-resend"><?php esc_html_e('Resend errors', 'mailpn'); ?></a>
              <?php endif; ?>
              <?php if (is_user_logged_in()): $current_user = wp_get_current_user(); ?>
                <a href="#" class="mailpn-btn mailpn-btn-mini mailpn-btn-test-email"
                  data-mailpn-post-id="<?php echo esc_attr($post_id); ?>"
                  data-mailpn-user-id="<?php echo esc_attr($current_user->ID); ?>">
                  <?php esc_html_e('Send test email', 'mailpn'); ?>
                </a>
                <i class="material-icons-outlined mailpn-vertical-align-middle mailpn-font-size-16 mailpn-color-main-0 mailpn-cursor-pointer mailpn-tooltip" title="<?php esc_attr_e('This will send a test email to your current email address using the same template and content as this mail campaign, bypassing all restrictions and queue system.', 'mailpn'); ?>">info</i>
              <?php endif; ?>
              <?php esc_html(MAILPN_Data::mailpn_loader()); ?>
            </div>
          </div>

        <?php elseif ($mailpn_status == 'queue'): ?>
          <?php
            $mailpn_queue_data = get_option('mailpn_queue');
            $emails_pending = !empty($mailpn_queue_data[$post_id]) ? count($mailpn_queue_data[$post_id]) : 0;
            $emails_sent = count(get_posts(['fields' => 'ids', 'numberposts' => -1, 'post_type' => 'mailpn_rec', 'post_status' => ['any'], 'meta_key' => 'mailpn_rec_mail_id', 'meta_value' => $post_id, 'orderby' => 'ID', 'order' => 'ASC']));
            $emails_total = $emails_pending + $emails_sent;
            $progress_pct = $emails_total > 0 ? round(($emails_sent * 100) / $emails_total, 1) : 0;
            $mails_sent_every_ten_minutes = (!empty(get_option('mailpn_sent_every_ten_minutes'))) ? get_option('mailpn_sent_every_ten_minutes') : 5;
          ?>
          <div class="mailpn-status-card mailpn-status-queue">
            <div class="mailpn-status-header">
              <i class="material-icons-outlined">send</i>
              <span class="mailpn-status-label"><?php esc_html_e('Sending...', 'mailpn'); ?></span>
            </div>
            <div class="mailpn-status-body">
              <div class="mailpn-queue-progress-track">
                <div class="mailpn-queue-progress-fill" style="width:<?php echo esc_attr($progress_pct); ?>%"></div>
              </div>
              <div class="mailpn-status-stats">
                <span class="mailpn-status-stat"><strong><?php echo esc_html($emails_sent); ?></strong> <?php esc_html_e('of', 'mailpn'); ?> <strong><?php echo esc_html($emails_total); ?></strong> <?php esc_html_e('emails sent', 'mailpn'); ?> (<?php echo esc_html($progress_pct); ?>%)</span>
                <span class="mailpn-status-stat mailpn-status-rate"><i class="material-icons-outlined">speed</i> <?php echo esc_html($mails_sent_every_ten_minutes); ?> <?php esc_html_e('emails every ten minutes', 'mailpn'); ?></span>
                <?php if ($mailpn_type === 'email_periodic'): ?>
                  <?php
                    $periodic_period_q = get_post_meta($post_id, 'mailpn_periodic_period', true);
                    $periodic_interval_q = MAILPN_Cron::mailpn_periodic_interval_seconds_static($periodic_period_q);
                    $next_send_q = time() + $periodic_interval_q;
                    $period_labels_q = [
                      'hourly'  => __('Hourly', 'mailpn'),
                      'daily'   => __('Daily', 'mailpn'),
                      'weekly'  => __('Weekly', 'mailpn'),
                      'monthly' => __('Monthly', 'mailpn'),
                      'yearly'  => __('Yearly', 'mailpn'),
                    ];
                    $period_label_q = isset($period_labels_q[$periodic_period_q]) ? $period_labels_q[$periodic_period_q] : $period_labels_q['weekly'];
                  ?>
                  <span class="mailpn-status-stat"><i class="material-icons-outlined">repeat</i> <?php echo esc_html($period_label_q); ?></span>
                  <span class="mailpn-status-stat"><i class="material-icons-outlined">update</i> <?php esc_html_e('Next send:', 'mailpn'); ?> <?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $next_send_q)); ?></span>
                <?php endif; ?>
              </div>
            </div>
            <?php if (!empty(get_option('mailpn_queue_paused'))): ?>
              <div class="mailpn-status-paused">
                <i class="material-icons-outlined">pause_circle</i> <?php esc_html_e('The mail queue is paused. Please check the mail queue settings.', 'mailpn'); ?>
              </div>
            <?php endif; ?>
            <div class="mailpn-status-actions">
              <a href="<?php echo esc_url(admin_url('edit.php?post_type=mailpn_rec&mailpn_type_filter=' . $mailpn_type)); ?>" target="_blank" class="mailpn-btn mailpn-btn-mini"><?php esc_html_e('View submissions', 'mailpn'); ?></a>
              <?php if ($mailpn_type === 'email_periodic'): ?>
                <a href="#" data-mailpn-post-id="<?php echo esc_attr($post_id); ?>" class="mailpn-btn mailpn-btn-mini mailpn-btn-force-send-periodic">
                  <i class="material-icons-outlined mailpn-vertical-align-middle mailpn-font-size-16">send</i>
                  <?php esc_html_e('Force send now', 'mailpn'); ?>
                </a>
              <?php endif; ?>
              <?php if (is_user_logged_in()): $current_user = wp_get_current_user(); ?>
                <a href="#" class="mailpn-btn mailpn-btn-mini mailpn-btn-test-email"
                  data-mailpn-post-id="<?php echo esc_attr($post_id); ?>"
                  data-mailpn-user-id="<?php echo esc_attr($current_user->ID); ?>">
                  <?php esc_html_e('Send test email', 'mailpn'); ?>
                </a>
                <i class="material-icons-outlined mailpn-vertical-align-middle mailpn-font-size-16 mailpn-color-main-0 mailpn-cursor-pointer mailpn-tooltip" title="<?php esc_attr_e('This will send a test email to your current email address using the same template and content as this mail campaign, bypassing all restrictions and queue system.', 'mailpn'); ?>">info</i>
              <?php endif; ?>
              <?php esc_html(MAILPN_Data::mailpn_loader()); ?>
            </div>
          </div>

        <?php else: ?>
          <div class="mailpn-status-card mailpn-status-draft">
            <div class="mailpn-status-header">
              <i class="material-icons-outlined">drafts</i>
              <?php if ($mailpn_type === 'email_periodic' && get_post_status($post_id) === 'publish'): ?>
                <span class="mailpn-status-label"><?php esc_html_e('Scheduled — will be sent on the next cron cycle.', 'mailpn'); ?></span>
              <?php else: ?>
                <span class="mailpn-status-label"><?php esc_html_e('Publish or update to begin sending.', 'mailpn'); ?></span>
              <?php endif; ?>
            </div>
            <?php if ($mailpn_type === 'email_periodic' && get_post_status($post_id) === 'publish'): ?>
              <?php
                $periodic_period = get_post_meta($post_id, 'mailpn_periodic_period', true);
                $period_labels = [
                  'hourly'  => __('Hourly', 'mailpn'),
                  'daily'   => __('Daily', 'mailpn'),
                  'weekly'  => __('Weekly', 'mailpn'),
                  'monthly' => __('Monthly', 'mailpn'),
                  'yearly'  => __('Yearly', 'mailpn'),
                ];
                $period_label = isset($period_labels[$periodic_period]) ? $period_labels[$periodic_period] : $period_labels['weekly'];
              ?>
              <div class="mailpn-status-body">
                <div class="mailpn-status-stats">
                  <span class="mailpn-status-stat"><i class="material-icons-outlined">repeat</i> <?php echo esc_html($period_label); ?></span>
                </div>
              </div>
            <?php endif; ?>
            <?php if (is_user_logged_in()): $current_user = wp_get_current_user(); ?>
              <div class="mailpn-status-actions" style="margin-top:8px;">
                <?php if ($mailpn_type === 'email_periodic' && get_post_status($post_id) === 'publish'): ?>
                  <a href="#" data-mailpn-post-id="<?php echo esc_attr($post_id); ?>" class="mailpn-btn mailpn-btn-mini mailpn-btn-force-send-periodic">
                    <i class="material-icons-outlined mailpn-vertical-align-middle mailpn-font-size-16">send</i>
                    <?php esc_html_e('Force send now', 'mailpn'); ?>
                  </a>
                <?php endif; ?>
                <a href="#" class="mailpn-btn mailpn-btn-mini mailpn-btn-test-email"
                  data-mailpn-post-id="<?php echo esc_attr($post_id); ?>"
                  data-mailpn-user-id="<?php echo esc_attr($current_user->ID); ?>">
                  <i class="material-icons-outlined mailpn-vertical-align-middle mailpn-font-size-16">send</i>
                  <?php esc_html_e('Send test email', 'mailpn'); ?>
                </a>
                <i class="material-icons-outlined mailpn-vertical-align-middle mailpn-font-size-16 mailpn-color-main-0 mailpn-cursor-pointer mailpn-tooltip" title="<?php esc_attr_e('This will send a test email to your current email address using the same template and content as this mail campaign, bypassing all restrictions and queue system.', 'mailpn'); ?>">info</i>
                <?php esc_html(MAILPN_Data::mailpn_loader()); ?>
              </div>
            <?php endif; ?>
          </div>
        <?php endif; ?>

        <?php if (in_array($mailpn_type, ['email_published_content'])): ?>
          <?php
            $post_type = !empty(get_post_meta($post_id, 'mailpn_updated_content_cpt', true)) ? get_post_meta($post_id, 'mailpn_updated_content_cpt', true) : 'post';

            $posts_atts = [
              'fields' => 'ids',
              'numberposts' => -1,
              'post_type' => $post_type,
              'post_status' => 'future',
              'orderby' => 'publish_date',
              'order' => 'ASC',
            ];
            
            if (class_exists('Polylang')) {
              $posts_atts['lang'] = pll_current_language('slug');
            }
            
            $posts = get_posts($posts_atts);
          ?>
            <h3><?php esc_html_e('Any content you publish in the post type selected will be sent.', 'mailpn'); ?></h3>
            
            <?php if (!empty($posts)): ?>
              <h3><?php esc_html_e('Also there are new contents to be published in the future and sent by email.', 'mailpn'); ?></h3>

              <ul class="mailpn-ml-20">
                <?php foreach ($posts as $post_id): ?>
                  <li>
                    <span><i class="material-icons-outlined mailpn-font-size-20 mailpn-vertical-align-middle">calendar_today</i> <?php echo esc_html(gmdate(get_option('date_format') . ' ' . get_option('time_format'), strtotime(get_post($post_id)->post_date))); ?></span>
                    <a href="<?php echo esc_url(get_permalink($post_id)); ?>" target="_blank"><?php echo esc_html(get_the_title($post_id)); ?></a>  
                  </li>
                <?php endforeach ?>
              </ul>
              
            <?php endif ?>
        <?php endif ?>
      <?php endif ?>

      <?php if (!in_array($mailpn_type, ['email_one_time', 'email_published_content', 'email_coded', 'email_periodic'])): ?>
        <?php if (is_user_logged_in()): $current_user = wp_get_current_user(); ?>
          <div class="mailpn-status-card" style="margin-top:8px;">
            <div class="mailpn-status-actions">
              <a href="#" class="mailpn-btn mailpn-btn-mini mailpn-btn-test-email"
                data-mailpn-post-id="<?php echo esc_attr($post_id); ?>"
                data-mailpn-user-id="<?php echo esc_attr($current_user->ID); ?>">
                <i class="material-icons-outlined mailpn-vertical-align-middle mailpn-font-size-16">send</i>
                <?php esc_html_e('Send test email', 'mailpn'); ?>
              </a>
              <i class="material-icons-outlined mailpn-vertical-align-middle mailpn-font-size-16 mailpn-color-main-0 mailpn-cursor-pointer mailpn-tooltip" title="<?php esc_attr_e('This will send a test email to your current email address using the same template and content as this mail campaign, bypassing all restrictions and queue system.', 'mailpn'); ?>">info</i>
              <?php esc_html(MAILPN_Data::mailpn_loader()); ?>
            </div>
          </div>
        <?php endif; ?>
      <?php endif; ?>
    <?php
    $mailpn_return_string = ob_get_contents();
    ob_end_clean();
    return $mailpn_return_string;
  }

  public function mailpn_resend_errors($post_id) {
    $mailpn_error = get_option('mailpn_error');
    $mailpn_error_data = $mailpn_error[$post_id];

    if (!empty($mailpn_error_data)) {
      foreach ($mailpn_error_data as $unique_id => $mailpn_error_data) {
        self::mailpn_queue_add($post_id, $mailpn_error_data['mailpn_user_to']);
      }
    } 

    $mailpn_error[$post_id] = [];
    update_option('mailpn_error', $mailpn_error);
  }

  public function mailpn_resend_all($post_id) {
    $users_to = self::mailpn_get_users_to($post_id);

    if (!empty($users_to)) {
      // Delete existing mailpn_rec records for this mail so mailpn_once_mailed does not block
      $existing_recs = get_posts([
        'fields'      => 'ids',
        'numberposts' => -1,
        'post_type'   => 'mailpn_rec',
        'post_status' => ['any'],
        'meta_key'    => 'mailpn_rec_mail_id',
        'meta_value'  => $post_id,
        'orderby'     => 'ID',
        'order'       => 'ASC',
      ]);

      if (!empty($existing_recs)) {
        foreach ($existing_recs as $rec_id) {
          wp_delete_post($rec_id, true);
        }
      }

      // Re-enqueue each user
      foreach ($users_to as $user_id) {
        self::mailpn_queue_add($post_id, $user_id);
      }
    }
  }

  public function mailpn_subscription_unsubscribe_btn($user_id){
    ob_start();

    $url = add_query_arg([
        'mailpn_action' => 'subscription-unsubscribe',
        'user' => $user_id,
      ],
      esc_url(home_url())
    );

    $url_nonced = wp_nonce_url(html_entity_decode($url), 'subscription-unsubscribe', 'subscription-unsubscribe-nonce');
    ?>
      <a href="<?php echo esc_attr($url_nonced); ?>"><small><?php esc_html_e('Unsubscribe', 'mailpn'); ?></small></a>
    <?php
    $mailpn_return_string = ob_get_contents(); 
    ob_end_clean(); 
    return $mailpn_return_string;
  }

  public function mailpn_queue_process() {
    $mailpn_queue = get_option('mailpn_queue');
    $mailpn_queue_paused = get_option('mailpn_queue_paused');
    $mailing_counter = 0;
    $mailpn_mails_sent_today = (!empty(get_option('mailpn_mails_sent_today'))) ? get_option('mailpn_mails_sent_today') : 0;
    $mails_sent_every_ten_minutes = (!empty(get_option('mailpn_sent_every_ten_minutes'))) ? get_option('mailpn_sent_every_ten_minutes') : 5;
    $mails_sent_every_day = (!empty(get_option('mailpn_sent_every_day'))) ? get_option('mailpn_sent_every_day') : 500;

    if (!empty($mailpn_queue)) {
      if (empty($mailpn_queue_paused)) {
        foreach ($mailpn_queue as $mail_id => $mail_users) {
          if (!empty($mail_users)) {
            foreach ($mail_users as $index => $user_id) {
              $mail_result = do_shortcode('[mailpn-sender mailpn_user_to="' . $user_id . '" mailpn_id="' . $mail_id . '"]');

              unset($mailpn_queue[$mail_id][array_search($user_id, $mailpn_queue[$mail_id])]);
              update_option('mailpn_queue', $mailpn_queue);

              if ($mail_result) {
                $mailing_counter++;
              }

              if ($index == (count($mail_users) - 1)) {
                update_post_meta($mail_id, 'mailpn_status', 'sent');

                $mailpn_meta_value = current_time('timestamp');
                if(empty(get_post_meta($mail_id, 'mailpn_timestamp_sent', true))) {
                  update_post_meta($mail_id, 'mailpn_timestamp_sent', [$mailpn_meta_value]);
                }else{
                  $wph_post_meta_new = get_post_meta($mail_id, 'mailpn_timestamp_sent', true);
                  $wph_post_meta_new[] = $mailpn_meta_value;
                  update_post_meta($mail_id, 'mailpn_timestamp_sent', $wph_post_meta_new);
                }
              }

              if ($mailing_counter >= $mails_sent_every_ten_minutes) {
                $mailpn_mails_sent_today = $mailpn_mails_sent_today + $mailing_counter;
                update_option('mailpn_mails_sent_today', $mailpn_mails_sent_today);

                if ($mailpn_mails_sent_today >= ($mails_sent_every_day - $mails_sent_every_ten_minutes)) {
                  update_option('mailpn_queue_paused', strtotime('now'));
                }

                return true;
              }
            }
          }else{
            update_post_meta($mail_id, 'mailpn_status', 'sent');
          }
        }
      }else{
        if (($mailpn_queue_paused + DAY_IN_SECONDS) < time()) {
          delete_option('mailpn_queue_paused');
          delete_option('mailpn_mails_sent_today');
        }
      }
    }else{
      return false;
    }
  }

  public function mailpn_queue_add($mail_id, $user_id) {
    $mailpn_queue = get_option('mailpn_queue');

    if (!empty($mail_id) && !empty($user_id)) {
      // Check if user is blocked before adding to queue
      if (is_numeric($user_id)) {
        // Check notification status
        if (class_exists('USERSPN') && get_user_meta($user_id, 'userspn_notifications', true) != 'on') {
          $mailpn_type = get_post_meta($mail_id, 'mailpn_type', true);
          $mailpn_system_types = ['email_password_reset', 'email_verify_code', 'email_welcome'];
          if (!in_array($mailpn_type, $mailpn_system_types, true)) {
            return false;
          }
        }

        // Check exception email lists
        $settings_plugin = new MAILPN_Settings();
        $mailpn_type = !empty($mailpn_type) ? $mailpn_type : get_post_meta($mail_id, 'mailpn_type', true);
        $mailpn_system_types = !empty($mailpn_system_types) ? $mailpn_system_types : ['email_password_reset', 'email_verify_code', 'email_welcome'];
        if (!in_array($mailpn_type, $mailpn_system_types, true) && $settings_plugin->mailpn_is_email_excepted($user_id)) {
          return false;
        }
      }
      if (empty($mailpn_queue)) {
        update_option('mailpn_queue', [$mail_id => [$user_id]]);
      }else{
        if (!array_key_exists($mail_id, $mailpn_queue) || (array_key_exists($mail_id, $mailpn_queue) && !in_array($user_id, $mailpn_queue[$mail_id]))) {
          if (!array_key_exists($mail_id, $mailpn_queue)) {
            $mailpn_queue[$mail_id] = [];
            update_option('mailpn_queue', $mailpn_queue);
          }

          $mailpn_queue[$mail_id][] = $user_id;
          update_option('mailpn_queue', $mailpn_queue);
        }
      }

      return true;
    }

    return false;
  }

  public function mailpn_once_mailed($mail_id, $user_id, $mailpn_once, $mailpn_type) {
    if (!empty($user_id)) {
      if ($mailpn_type != 'email_verify_code') {
        if ($mailpn_once) {
          if (!empty($mailpn_type) && !empty(get_posts(['fields' => 'ids', 'numberposts' => -1, 'post_type' => 'mailpn_rec', 'post_status' => 'publish', 'meta_query' => ['relation'  => 'AND', ['key' => 'mailpn_rec_type', 'value' => $mailpn_type, ], ['key' => 'mailpn_rec_to', 'value' => $user_id, ], ], 'orderby' => 'ID', 'order' => 'ASC', ]))) {
            return true;
          }
        }else{
          if (((!empty($mail_id) && in_array(get_post_meta($mail_id, 'mailpn_type', true), ['email_one_time', 'email_welcome', 'newsletter_welcome'])) || (in_array(get_post_meta($mail_id, 'mailpn_type', true), ['email_coded']) && get_post_meta($mail_id, 'mailpn_email_coded_once', true) == 'on')) && get_posts(['fields' => 'ids', 'numberposts' => -1, 'post_type' => 'mailpn_rec', 'post_status' => 'publish', 'meta_query' => ['relation'  => 'AND', ['key' => 'mailpn_rec_mail_id', 'value' => $mail_id, ], ['key' => 'mailpn_rec_to', 'value' => $user_id, ], ], 'orderby' => 'ID', 'order' => 'ASC', ])) {
            return true;
          }
        }
      }else{
        return false;
      }
    }

    return false;
  }

  public static function mailpn_get_users_to($mail_id) {
    if (get_post_type($mail_id) != 'mailpn_mail') {
      return false;
    }

    $mail_distribution = get_post_meta($mail_id, 'mailpn_distribution', true);

    if ($mail_distribution == 'private_role') {
      $user_ids = [];
      $mailpn_distribution_role = get_post_meta($mail_id, 'mailpn_distribution_role', true);

      if (!empty($mailpn_distribution_role)) {
        foreach ($mailpn_distribution_role as $role) {
          $users_role = get_users(['fields' => 'ids', 'number' => -1, 'role' => $role, 'orderby' => 'ID', 'order' => 'ASC']);

          if (!empty($users_role)) {
            foreach ($users_role as $user_id) {
              $user_ids[] = $user_id;
            }
          }
        }
      }

      // Sort all user IDs by ID to ensure consistent order
      sort($user_ids, SORT_NUMERIC);
      return self::mailpn_filter_blocked_users($user_ids, $mail_id);
    }elseif ($mail_distribution == 'private_user') {
      $user_ids = get_post_meta($mail_id, 'mailpn_distribution_user', true);

      // Sort user IDs if it's an array
      if (is_array($user_ids)) {
        sort($user_ids, SORT_NUMERIC);
      }

      return self::mailpn_filter_blocked_users($user_ids, $mail_id);
    }else{
      $user_ids = get_users(['fields' => 'ids', 'number' => -1, 'orderby' => 'ID', 'order' => 'ASC']);
      return self::mailpn_filter_blocked_users($user_ids, $mail_id);
    }
  }

  /**
   * Filter out users that are blocked from receiving emails
   * Checks notification status and exception email lists
   */
  private static function mailpn_filter_blocked_users($user_ids, $mail_id) {
    if (empty($user_ids) || !is_array($user_ids)) {
      return $user_ids;
    }

    $mailpn_type = get_post_meta($mail_id, 'mailpn_type', true);
    $mailpn_system_types = ['email_password_reset', 'email_verify_code', 'email_welcome'];
    $is_system_email = in_array($mailpn_type, $mailpn_system_types, true);

    // System emails bypass all blocking
    if ($is_system_email) {
      return $user_ids;
    }

    $settings_plugin = new MAILPN_Settings();
    $filtered_ids = [];

    foreach ($user_ids as $user_id) {
      // Check notification status
      if (class_exists('USERSPN') && get_user_meta($user_id, 'userspn_notifications', true) != 'on') {
        continue;
      }

      // Check exception email lists
      if ($settings_plugin->mailpn_is_email_excepted($user_id)) {
        continue;
      }

      $filtered_ids[] = $user_id;
    }

    return $filtered_ids;
  }

  /**
   * Check if a user matches the distribution settings of a mail template
   *
   * @param int $email_id The mail template post ID
   * @param int $user_id The user ID
   * @return bool True if the user should receive this email
   */
  public static function mailpn_user_matches_distribution($email_id, $user_id) {
    $distribution = get_post_meta($email_id, 'mailpn_distribution', true);
    $user = get_userdata($user_id);

    if (empty($user)) {
      return false;
    }

    switch ($distribution) {
      case 'public':
        return true;
      case 'private_role':
        $template_roles = get_post_meta($email_id, 'mailpn_distribution_role', true);
        if (!empty($template_roles) && is_array($template_roles)) {
          foreach ($template_roles as $role) {
            if (in_array($role, $user->roles)) {
              return true;
            }
          }
        }
        return false;
      case 'private_user':
        $user_list = get_post_meta($email_id, 'mailpn_distribution_user', true);
        return !empty($user_list) && is_array($user_list) && in_array($user_id, $user_list);
      default:
        return false;
    }
  }

  public function mailpn_transition_post_status($new_status, $old_status, $post) {
    if (!($new_status !== 'publish' || $old_status === 'publish')) {
      $post_id = $post->ID;

      $emails_new_content_atts = ['fields' => 'ids',
        'numberposts' => -1,
        'post_type' => 'mailpn_mail',
        'post_status' => 'any', 
        'meta_key' => 'mailpn_type', 
        'meta_value' => 'email_published_content',
      ];

      if (class_exists('Polylang')) {
        $emails_new_content_atts['lang'] = pll_current_language('slug');
      }

      $emails_new_content = get_posts($emails_new_content_atts);

      if (!empty($emails_new_content)) {
        foreach ($emails_new_content as $mail_id) {
          update_post_meta($mail_id, 'mailpn_status', '');

          $mailpn_updated_content_cpt = get_post_meta($mail_id, 'mailpn_updated_content_cpt', true);
          
          if (!empty($mailpn_updated_content_cpt) && $mailpn_updated_content_cpt == $post->post_type) {
            $users_to = self::mailpn_get_users_to($mail_id);

            if (!empty($users_to)) {
              foreach ($users_to as $index => $user_id) {
                self::mailpn_queue_add($mail_id, $user_id);
                
                if ($index == (count($users_to) - 1)) {
                  update_post_meta($mail_id, 'mailpn_status', 'queue');
                }
              }
            }
          }
        }
      }
    }
  }

  public function mailpn_retrieve_password_message($message, $key, $user_login, $user_data) {
    ob_start();
    ?>
      <?php if (!empty(get_option('mailpn_password_retrieve_before'))): ?>
        <?php echo wp_kses_post(get_option('mailpn_password_retrieve_before')); ?>
      <?php else: ?>
        <p><?php esc_html_e('Hello', 'mailpn'); ?> [user-name].</p>

        <p><?php esc_html_e('We have received a request to reset the password for your account. If you made this request, click the link below to change your password:', 'mailpn'); ?></p>
      <?php endif ?>

      <div class="mailpn-text-align-center mailpn-mt-30 mailpn-mb-50">
        <a href="<?php echo esc_url(home_url('wp-login.php?action=rp&key=' . $key . '&login=') . rawurlencode($user_login)); ?>" class="mailpn-btn"><?php esc_html_e('Reset your password', 'mailpn'); ?></a>
      </div>

      <?php if (!empty(get_option('mailpn_password_retrieve_after'))): ?>
        <?php echo wp_kses_post(get_option('mailpn_password_retrieve_after')); ?>
      <?php else: ?>
        <p><?php esc_html_e('If you didn\'t make this request, ignore this email or', 'mailpn'); ?> <a href="mailto:<?php echo esc_html(get_option('admin_email')); ?>"><?php esc_html_e('please report it', 'mailpn'); ?></a>.</p>

        <p><?php esc_html_e('Thank you!', 'mailpn'); ?></p>
      <?php endif ?>
    <?php
    $mail_content = ob_get_contents(); 
    ob_end_clean(); 

    do_shortcode('[mailpn-sender mailpn_type="email_password_reset" mailpn_user_to="' . $user_data->ID . '" mailpn_subject="' . esc_html(__('Password reset', 'mailpn')) . ' ' . get_bloginfo('name') . '"]' . $mail_content . '[/mailpn-sender]');

    return '';
  }

  public function mailpn_wp_new_user_notification_email($wp_new_user_notification_email, $user, $blogname) {
    $user_login = stripslashes($user->user_login);
    $user_email = stripslashes($user->user_email);
    $login_url  = wp_login_url();

    ob_start();
    ?>
      <?php if (!empty(get_option('mailpn_password_new_before'))): ?>
        <?php echo wp_kses_post(get_option('mailpn_password_new_before')); ?>
      <?php else: ?>
        <p><?php esc_html_e('Hello', 'mailpn'); ?>.</p>

        <p><?php esc_html_e('You have a new user created on our platform. Please create a strong password to login.', 'mailpn'); ?></p>
        <p><?php esc_html_e('First of all, access the url below and use any of these credentials:', 'mailpn'); ?></p>
      <?php endif ?>

      <ul>
        <li><?php esc_html_e('Username', 'mailpn'); ?>: <?php echo esc_html($user_login); ?></li>
        <li><?php esc_html_e('Email', 'mailpn'); ?>: <?php echo esc_html($user_email); ?></li>
      </ul>

      <div class="mailpn-text-align-center mailpn-mt-30 mailpn-mb-50">
        <a href="<?php echo esc_url(wp_lostpassword_url(home_url())); ?>" class="mailpn-btn"><?php esc_html_e('Create new password', 'mailpn'); ?></a>
      </div>

      <?php if (!empty(get_option('mailpn_password_new_after'))): ?>
        <?php echo wp_kses_post(get_option('mailpn_password_new_after')); ?>
      <?php else: ?>
        <p><?php esc_html_e('Thank you!', 'mailpn'); ?></p>
      <?php endif ?>
    <?php
    $mail_content = ob_get_contents(); 
    ob_end_clean();

    $mailpn_socials = [];
    $mailpn_legal_name = get_option('mailpn_legal_name');
    $mailpn_legal_address = get_option('mailpn_legal_address');
    $mailpn_user_to = $user->ID;

    $wp_new_user_notification_email['subject'] = $blogname . ' ' . esc_html(__('New user', 'mailpn'));
    $wp_new_user_notification_email['headers'] = ['Content-Type: text/html; charset=UTF-8'];
    $wp_new_user_notification_email['message'] = $this->mailpn_template($blogname . ' ' . esc_html(__('New user', 'mailpn')), $mail_content, $mailpn_socials, $mailpn_legal_name, $mailpn_legal_address, $mailpn_user_to, 0);

    return $wp_new_user_notification_email;
  }

  public function mailpn_test_email_btn($atts) {
    if (!current_user_can('manage_options')) {
      return '';
    }

    ob_start();
    ?>
      <div class="mailpn-test-email-wrapper">
        <a class="mailpn-test-email-btn mailpn-btn mailpn-btn-mini">
          <?php esc_html_e('Send test email', 'mailpn'); ?>
        </a>
        <?php esc_html(MAILPN_Data::mailpn_loader()); ?>

        <span class="mailpn-test-email-result"></span>
      </div>
    <?php
    $mailpn_return_string = ob_get_contents();
    ob_end_clean();
    return $mailpn_return_string;
  }

  public function mailpn_wp_mail_wrapper($args) {
    $message = $args['message'];
    $subject = $args['subject'];
    $headers = $args['headers'];

    // Skip if already HTML (sent by MailPN or another plugin)
    if (stripos($message, '<!DOCTYPE') !== false || stripos($message, 'mailpn-table-main') !== false) {
      return $args;
    }

    // Convert plain text body to HTML
    $html = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    // URLs → clickable links
    $html = preg_replace(
      '/(https?:\/\/[^\s<>\)\"]+)/i',
      '<a href="$1" target="_blank" style="color:#3d731a;text-decoration:underline;">$1</a>',
      $html
    );
    // Double newlines → paragraphs, single newlines → <br>
    $paragraphs = preg_split('/\n\s*\n/', $html);
    $html = '<p>' . implode('</p><p>', array_map(function($p) {
      return nl2br(trim($p));
    }, $paragraphs)) . '</p>';

    // Clean subject: remove [Site Name] prefix
    $clean_subject = preg_replace('/^\[.+?\]\s*/', '', $subject);

    // Wrap with MailPN template
    $mailpn_legal_name = get_option('mailpn_legal_name');
    $mailpn_legal_address = get_option('mailpn_legal_address');
    $wrapped_message = $this->mailpn_template($clean_subject, $html, [], $mailpn_legal_name, $mailpn_legal_address, 0, 0);

    // Set HTML content type header
    $new_headers = is_array($headers) ? $headers : (is_string($headers) && !empty($headers) ? explode("\n", $headers) : []);
    // Remove any existing content-type header
    $new_headers = array_filter($new_headers, function($h) {
      return stripos($h, 'content-type') === false;
    });
    $new_headers[] = 'Content-Type: text/html; charset=UTF-8';

    // Store data for logging
    $this->wrapped_email_data = [
      'to' => $args['to'],
      'subject' => $clean_subject,
      'message_html' => $wrapped_message,
      'message_text' => $message,
    ];

    $args['subject'] = $clean_subject;
    $args['message'] = $wrapped_message;
    $args['headers'] = $new_headers;

    return $args;
  }

  public function mailpn_log_wrapped_email($mail_data) {
    if ($this->wrapped_email_data === null) {
      return;
    }

    $data = $this->wrapped_email_data;
    $this->wrapped_email_data = null;

    $to = is_array($data['to']) ? $data['to'][0] : $data['to'];
    $user = get_user_by('email', $to);
    $user_id = $user ? $user->ID : 0;

    $post_functions = new MAILPN_Functions_Post();
    $post_functions->mailpn_insert_post($data['subject'], $data['message_html'], '', sanitize_title($data['subject']), 'mailpn_rec', 'publish', 1, 0, [], [], [
      'mailpn_rec_content' => $data['message_html'],
      'mailpn_rec_type' => 'wp_core_email',
      'mailpn_rec_to' => $user_id,
      'mailpn_rec_to_email' => $to,
      'mailpn_rec_attachments' => [],
      'mailpn_rec_mail_id' => 0,
      'mailpn_rec_mail_result' => 1,
      'mailpn_rec_post_id' => 0,
      'mailpn_rec_headers' => 'Content-Type: text/html; charset=UTF-8',
      'mailpn_rec_error' => '',
      'mailpn_rec_server_ip' => $_SERVER['SERVER_ADDR'] ?? '',
      'mailpn_rec_sent_datetime' => current_time('mysql'),
      'mailpn_rec_subject' => $data['subject'],
      'mailpn_rec_content_html' => $data['message_html'],
      'mailpn_rec_content_text' => $data['message_text'],
    ], false);
  }
}
