<?php
/**
 * Load the plugin Ajax functions.
 *
 * Load the plugin Ajax functions to be executed in background.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 * @package    MAILPN
 * @subpackage MAILPN/includes
 * @author     Padres en la Nube <info@padresenlanube.com>
 */
class MAILPN_Ajax {
	/**
	 * Load ajax functions.
	 *
	 * @since    1.0.0
	 */
	public function mailpn_ajax_server() {
    if (array_key_exists('mailpn_ajax_type', $_POST)) {
      // Always require nonce verification
      if (!array_key_exists('mailpn_ajax_nonce', $_POST)) {
        echo wp_json_encode([
          'error_key' => 'mailpn_ajax_nonce_error_required',
          'error_content' => esc_html(__('Security check failed: Nonce is required.', 'mailpn')),
        ]);

        exit();
      }

      if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['mailpn_ajax_nonce'])), 'mailpn-nonce')) {
        echo wp_json_encode([
          'error_key' => 'mailpn_ajax_nonce_error_invalid',
          'error_content' => esc_html(__('Security check failed: Invalid nonce.', 'mailpn')),
        ]);

        exit();
      }

      $mailpn_ajax_type = MAILPN_Forms::mailpn_sanitizer(wp_unslash($_POST['mailpn_ajax_type']));
      $mailpn_ajax_keys = !empty($_POST['mailpn_ajax_keys']) ? wp_unslash($_POST['mailpn_ajax_keys']) : [];
      $mailpn_mail_id = !empty($_POST['mailpn_mail_id']) ? MAILPN_Forms::mailpn_sanitizer(wp_unslash($_POST['mailpn_mail_id'])) : 0;
      $mailpn_key_value = [];

      if (!empty($mailpn_ajax_keys)) {
        foreach ($mailpn_ajax_keys as $mailpn_key) {
          if (strpos($mailpn_key['id'], '[]') !== false) {
            $mailpn_clear_key = str_replace('[]', '', $mailpn_key['id']);
            ${$mailpn_clear_key} = $mailpn_key_value[$mailpn_clear_key] = [];

            if (!empty($_POST[$mailpn_clear_key])) {
              foreach (wp_unslash($_POST[$mailpn_clear_key]) as $multi_key => $multi_value) {
                $final_value = !empty($_POST[$mailpn_clear_key][$multi_key]) ? MAILPN_Forms::mailpn_sanitizer(wp_unslash($_POST[$mailpn_clear_key][$multi_key]), $mailpn_key['node'], $mailpn_key['type']) : '';
                ${$mailpn_clear_key}[$multi_key] = $mailpn_key_value[$mailpn_clear_key][$multi_key] = $final_value;
              }
            }else{
              ${$mailpn_clear_key} = '';
              $mailpn_key_value[$mailpn_clear_key][$multi_key] = '';
            }
          }else{
            $mailpn_key_id = !empty($_POST[$mailpn_key['id']]) ? MAILPN_Forms::mailpn_sanitizer(wp_unslash($_POST[$mailpn_key['id']]), $mailpn_key['node'], $mailpn_key['type']) : '';
            ${$mailpn_key['id']} = $mailpn_key_value[$mailpn_key['id']] = $mailpn_key_id;
          }
        }
      }

      switch ($mailpn_ajax_type) {
        case 'mailpn_mail_check':
          if (!empty($mailpn_mail_id)) {
            $plugin_post_type_mail = new MAILPN_Post_Type_Mail();
            echo wp_json_encode([
              'error_key' => '', 
              'html' => $plugin_post_type_mail->mailpn_mail_check($mailpn_mail_id), 
            ]);

            exit();
          }else{
            echo wp_json_encode([
              'error_key' => 'mailpn_mail_check_error', 
              'error_content' => esc_html(__('An error occurred while checking the Mail.', 'mailpn')), 
              ]);

            exit();
          }
          break;
        case 'mailpn_get_errors_list':
          if (!empty($mailpn_mail_id)) {
            // Get limit parameter (default 20 for initial load, -1 for all)
            $limit = !empty($_POST['limit']) ? intval($_POST['limit']) : 20;
            $error_list = [];

            // Get errors from mailpn_error option (pre-send validation errors)
            $mailpn_errors = get_option('mailpn_error');
            if (!empty($mailpn_errors[$mailpn_mail_id])) {
              foreach ($mailpn_errors[$mailpn_mail_id] as $unique_id => $mailpn_error) {
                $user_info = get_userdata($mailpn_error['mailpn_user_to']);
                if (!empty($user_info)) {
                  $error_list[] = [
                    'user_id' => $mailpn_error['mailpn_user_to'],
                    'name' => trim($user_info->first_name . ' ' . $user_info->last_name),
                    'email' => $user_info->user_email,
                    'type' => 'validation',
                  ];
                } else {
                  $error_list[] = [
                    'user_id' => $mailpn_error['mailpn_user_to'],
                    'name' => __('Unknown user', 'mailpn'),
                    'email' => $mailpn_error['user_email'] ?? '',
                    'type' => 'validation',
                  ];
                }
              }
            }

            // Get errors from mailpn_rec (actual send failures with error details)
            // BUT exclude users who already have a successful send
            $error_recs = get_posts([
              'fields'      => 'ids',
              'numberposts' => -1,
              'post_type'   => 'mailpn_rec',
              'post_status' => 'publish',
              'meta_query'  => [
                'relation' => 'AND',
                ['key' => 'mailpn_rec_mail_id', 'value' => $mailpn_mail_id],
                ['key' => 'mailpn_rec_error', 'value' => '', 'compare' => '!='],
              ],
            ]);

            if (!empty($error_recs)) {
              foreach ($error_recs as $rec_id) {
                $user_id = get_post_meta($rec_id, 'mailpn_rec_to', true);
                $user_email = get_post_meta($rec_id, 'mailpn_rec_to_email', true);

                // Get ALL records for this user and mail_id
                $all_user_recs = get_posts([
                  'fields'      => 'ids',
                  'numberposts' => -1,
                  'post_type'   => 'mailpn_rec',
                  'post_status' => 'publish',
                  'meta_query'  => [
                    'relation' => 'AND',
                    ['key' => 'mailpn_rec_mail_id', 'value' => $mailpn_mail_id],
                    ['key' => 'mailpn_rec_to', 'value' => $user_id],
                  ],
                ]);

                // Check if any of these records has no error (successful send)
                $has_successful = false;
                foreach ($all_user_recs as $user_rec_id) {
                  $rec_error = get_post_meta($user_rec_id, 'mailpn_rec_error', true);
                  if (empty($rec_error)) {
                    $has_successful = true;
                    break;
                  }
                }

                // Skip this user if they have a successful send
                if ($has_successful) {
                  continue;
                }

                $user_info = is_numeric($user_id) ? get_userdata($user_id) : null;

                if (!empty($user_info)) {
                  $error_list[] = [
                    'user_id' => $user_id,
                    'name' => trim($user_info->first_name . ' ' . $user_info->last_name),
                    'email' => $user_email,
                    'type' => 'send_failure',
                  ];
                } else {
                  $error_list[] = [
                    'user_id' => $user_id,
                    'name' => __('Unknown user', 'mailpn'),
                    'email' => $user_email,
                    'type' => 'send_failure',
                  ];
                }
              }
            }

            $total_errors = count($error_list);

            // Apply limit if specified
            $limited_list = $error_list;
            $showing_all = true;
            if ($limit > 0 && $limit < $total_errors) {
              $limited_list = array_slice($error_list, 0, $limit);
              $showing_all = false;
            }

            echo wp_json_encode([
              'error_key' => '',
              'error_list' => $limited_list,
              'total_errors' => $total_errors,
              'showing_count' => count($limited_list),
              'showing_all' => $showing_all,
            ]);
          } else {
            echo wp_json_encode([
              'error_key' => 'mailpn_get_errors_list_error',
              'error_content' => esc_html(__('An error occurred while getting the errors list.', 'mailpn')),
            ]);
          }
          exit();
          break;
        case 'mailpn_resend_errors':
          if (!empty($mailpn_mail_id)) {
            $plugin_mailing = new MAILPN_Mailing();
            $plugin_mailing->mailpn_resend_errors($mailpn_mail_id);

            update_post_meta($mailpn_mail_id, 'mailpn_status', 'queue');
          }else{
            echo wp_json_encode(['error_key' => 'mailpn_resend_errors_error', 'error_content' => esc_html(__('An error occurred while resending the errors.', 'mailpn')), ]);exit();
          }

          echo wp_json_encode(['error_key' => '', ]);exit();
          break;
        case 'mailpn_resend_all':
          if (!empty($mailpn_mail_id)) {
            $plugin_mailing = new MAILPN_Mailing();
            $plugin_mailing->mailpn_resend_all($mailpn_mail_id);

            update_post_meta($mailpn_mail_id, 'mailpn_status', 'queue');
          }else{
            echo wp_json_encode(['error_key' => 'mailpn_resend_all_error', 'error_content' => esc_html(__('An error occurred while resending the mail.', 'mailpn')), ]);exit();
          }

          echo wp_json_encode(['error_key' => '', ]);exit();
          break;
        case 'mailpn_retry_email':
          if (!current_user_can('manage_options')) {
            echo wp_json_encode(['error_key' => 'unauthorized', 'message' => esc_html__('Unauthorized access', 'mailpn')]);
            exit();
          }

          $rec_id = !empty($_POST['rec_id']) ? intval($_POST['rec_id']) : 0;

          if (empty($rec_id)) {
            echo wp_json_encode(['error_key' => 'missing_id', 'message' => esc_html__('Missing record ID', 'mailpn')]);
            exit();
          }

          // Get record data
          $mail_id = get_post_meta($rec_id, 'mailpn_rec_mail_id', true);
          $user_id = get_post_meta($rec_id, 'mailpn_rec_to', true);

          if (empty($mail_id) || empty($user_id)) {
            echo wp_json_encode(['error_key' => 'invalid_data', 'message' => esc_html__('Invalid record data', 'mailpn')]);
            exit();
          }

          // Check if template is published
          $mail_post = get_post($mail_id);
          if (!$mail_post || $mail_post->post_status !== 'publish') {
            echo wp_json_encode(['error_key' => 'invalid_template', 'message' => esc_html__('Email template is not available or not published', 'mailpn')]);
            exit();
          }

          // Get current queue
          $mailpn_queue = get_option('mailpn_queue', []);

          // Ensure the mail_id has an array in the queue
          if (!isset($mailpn_queue[$mail_id]) || !is_array($mailpn_queue[$mail_id])) {
            $mailpn_queue[$mail_id] = [];
          }

          // Add user to the FIRST position of the queue (beginning of array)
          array_unshift($mailpn_queue[$mail_id], $user_id);

          // Remove duplicates if any
          $mailpn_queue[$mail_id] = array_unique($mailpn_queue[$mail_id]);

          // Save updated queue
          update_option('mailpn_queue', $mailpn_queue);

          // Delete the error record
          wp_delete_post($rec_id, true);

          // Return success message
          echo wp_json_encode([
            'error_key' => '',
            'message' => esc_html__('Email added to queue for retry. It will be sent at the next processing cycle.', 'mailpn')
          ]);
          exit();
          break;
        case 'mailpn_force_send_periodic':
          if (!current_user_can('manage_options')) {
            echo wp_json_encode(['error_key' => 'unauthorized', 'error_content' => esc_html__('Unauthorized access', 'mailpn')]);
            exit();
          }
          if (!empty($mailpn_mail_id)) {
            $mail_type = get_post_meta($mailpn_mail_id, 'mailpn_type', true);
            if ($mail_type !== 'email_periodic') {
              echo wp_json_encode(['error_key' => 'invalid_type', 'error_content' => esc_html__('This email is not a periodic email.', 'mailpn')]);
              exit();
            }
            $plugin_mailing = new MAILPN_Mailing();
            $users_to = MAILPN_Mailing::mailpn_get_users_to($mailpn_mail_id);
            if (!empty($users_to)) {
              foreach ($users_to as $user_id) {
                $plugin_mailing->mailpn_queue_add($mailpn_mail_id, $user_id);
              }
              update_post_meta($mailpn_mail_id, 'mailpn_status', 'queue');
              // Process queue immediately so emails start sending now
              $plugin_mailing->mailpn_queue_process();
            }
            echo wp_json_encode(['error_key' => '']);
          } else {
            echo wp_json_encode(['error_key' => 'missing_id', 'error_content' => esc_html__('Missing email ID.', 'mailpn')]);
          }
          exit();
          break;
        case 'mailpn_test_email_send':
          if (!current_user_can('manage_options')) {
            echo wp_json_encode(['error_key' => 'mailpn_test_email_send_error', 'error_content' => esc_html__('Unauthorized access', 'mailpn')]);
            exit();
          }

          $admin_user_id = get_current_user_id();

          // Enable notifications for the user performing the test
          update_user_meta($admin_user_id, 'userspn_notifications', 'on');

          $subject = esc_html__('Test email from MAILPN', 'mailpn');

          ob_start();
          ?>
            <h1><?php esc_html_e('Test Email - Main Title (H1)', 'mailpn'); ?></h1>
            <h2><?php esc_html_e('Secondary Subtitle (H2)', 'mailpn'); ?></h2>
            <h3><?php esc_html_e('Tertiary Heading (H3)', 'mailpn'); ?></h3>

            <p><?php esc_html_e('Hello', 'mailpn'); ?> [user-name],</p>

            <p><?php esc_html_e('This is a', 'mailpn'); ?> <strong><?php esc_html_e('test email', 'mailpn'); ?></strong> <?php esc_html_e('sent from the MAILPN plugin. You can customize the', 'mailpn'); ?> <em><?php esc_html_e('fonts, sizes, colors', 'mailpn'); ?></em> <?php esc_html_e('and spacing to match your brand.', 'mailpn'); ?></p>

            <p><?php esc_html_e('Changes will be reflected in', 'mailpn'); ?> <a href="<?php echo esc_url(admin_url('admin.php?page=mailpn_options')); ?>"><?php esc_html_e('real-time', 'mailpn'); ?></a> <?php esc_html_e('while you adjust the options.', 'mailpn'); ?></p>

            <h4><?php esc_html_e('Features included:', 'mailpn'); ?></h4>
            <ul>
              <li><?php esc_html_e('Typography customization', 'mailpn'); ?></li>
              <li><?php esc_html_e('Color schemes', 'mailpn'); ?></li>
              <li><?php esc_html_e('Responsive design', 'mailpn'); ?></li>
            </ul>

            <p style="text-align: center; margin: 30px 0;">
              <a href="<?php echo esc_url(admin_url('admin.php?page=mailpn_options')); ?>" class="wp-block-button__link wp-element-button" style="background-color: #86b3ac; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 4px; display: inline-block; font-weight: 600;">
                <?php esc_html_e('Button Example', 'mailpn'); ?>
              </a>
            </p>

            <p><small><?php esc_html_e('This is a test to verify all formatting styles are applied correctly.', 'mailpn'); ?></small></p>
          <?php
          $content = ob_get_contents();
          ob_end_clean();

          // Clear previous errors
          $GLOBALS['mailpn_last_error'] = null;

          // Verify basic configuration before attempting to send
          $config_errors = [];

          // Verify SMTP configuration if enabled
          if (get_option('mailpn_smtp_enabled') === 'on') {
            $smtp_host = get_option('mailpn_smtp_host');
            $smtp_port = get_option('mailpn_smtp_port');
            $smtp_auth = get_option('mailpn_smtp_auth');
            $smtp_username = get_option('mailpn_smtp_username');
            $smtp_password = get_option('mailpn_smtp_password');

            if (empty($smtp_host)) {
              $config_errors[] = esc_html__('SMTP Host is empty', 'mailpn');
            }
            if (empty($smtp_port)) {
              $config_errors[] = esc_html__('SMTP Port is empty', 'mailpn');
            }
            if ($smtp_auth === 'on') {
              if (empty($smtp_username)) {
                $config_errors[] = esc_html__('SMTP Username is empty', 'mailpn');
              }
              if (empty($smtp_password)) {
                $config_errors[] = esc_html__('SMTP Password is empty', 'mailpn');
              }
            }
          } else {
            // Verify server configuration for sending without SMTP
            if (!ini_get('sendmail_path') && !function_exists('mail')) {
              $config_errors[] = esc_html__('No mail server configured (sendmail_path or mail() function)', 'mailpn');
            }
          }

          // If there are configuration errors, show them immediately
          if (!empty($config_errors)) {
            $error_message = esc_html__('Email configuration error', 'mailpn') . ': ' . implode('; ', $config_errors);
            self::mailpn_log_email_error('test_email_send', $error_message, [
              'user_id' => $admin_user_id,
              'config_errors' => $config_errors,
            ]);
            echo wp_json_encode(['error_key' => 'mailpn_test_email_send_error', 'error_content' => $error_message]);exit();
          }

          // Enable PHPMailer error capturing
          $phpmailer_error = '';
          $original_error_handler = set_error_handler(function($severity, $message, $file, $line) use (&$phpmailer_error) {
            if (strpos($message, 'PHPMailer') !== false || strpos($message, 'SMTP') !== false || strpos($message, 'mail') !== false) {
              $phpmailer_error = $message;
            }
            return false; // Let PHP handle the error normally
          });

          $result = do_shortcode('[mailpn-sender mailpn_type="email_coded" mailpn_user_to="' . $admin_user_id . '" mailpn_subject="' . $subject . '"]' . $content . '[/mailpn-sender]');

          // Restore the original error handler
          if ($original_error_handler) {
            restore_error_handler();
          }

          if ($result) {
            echo wp_json_encode(['error_key' => '', 'error_content' => esc_html__('Test email sent successfully', 'mailpn')]);exit();
          } else {
            // Build detailed error message
            $error_message = esc_html__('Failed to send test email', 'mailpn');
            $error_details = [];

            // Check for errors captured by the error handler
            if (!empty($phpmailer_error)) {
              $error_message = esc_html__('Email sending failed', 'mailpn') . ': ' . $phpmailer_error;
              $error_details['phpmailer_error'] = $phpmailer_error;
            } else {
              // Check globally stored error information
              if (isset($GLOBALS['mailpn_last_error']) && is_array($GLOBALS['mailpn_last_error'])) {
                $last_error = $GLOBALS['mailpn_last_error'];
                if (!empty($last_error['message'])) {
                  $error_message = esc_html__('Email sending failed', 'mailpn') . ': ' . $last_error['message'];
                  $error_details = $last_error;
                }
              } else {
                // Check PHPMailer errors directly
                if (isset($GLOBALS['phpmailer']) && is_object($GLOBALS['phpmailer'])) {
                  $phpmailer_error_info = $GLOBALS['phpmailer']->ErrorInfo;
                  if (!empty($phpmailer_error_info)) {
                    $error_message = esc_html__('Email sending failed', 'mailpn') . ': ' . $phpmailer_error_info;
                    $error_details['phpmailer_error_info'] = $phpmailer_error_info;
                  }
                }
              }
            }

            // If no specific error, add diagnostic information
            if ($error_message === esc_html__('Failed to send test email', 'mailpn')) {
              $error_message = esc_html__('Email sending failed', 'mailpn') . ': ' . esc_html__('Check SMTP settings or server mail configuration', 'mailpn');
            }

            // Log the error
            self::mailpn_log_email_error('test_email_send', $error_message, array_merge([
              'user_id' => $admin_user_id,
              'subject' => $subject,
            ], $error_details));

            echo wp_json_encode(['error_key' => 'mailpn_test_email_send_error', 'error_content' => $error_message]);exit();
          }

          break;
        case 'mailpn_send_test_email_campaign':
          if (!current_user_can('manage_options')) {
            echo wp_json_encode(['error_key' => 'mailpn_test_email_send_error', 'error_content' => esc_html__('Unauthorized access', 'mailpn')]);
            exit();
          }
          
          $post_id = !empty($_POST['post_id']) ? intval($_POST['post_id']) : 0;
          $user_id = !empty($_POST['user_id']) ? intval($_POST['user_id']) : 0;
          
          if (empty($post_id) || empty($user_id)) {
            echo wp_json_encode(['error_key' => 'mailpn_test_email_send_error', 'error_content' => esc_html__('Missing required parameters', 'mailpn')]);
            exit();
          }
          
          // Get the user's email
          $user_data = get_userdata($user_id);
          if (!$user_data) {
            echo wp_json_encode(['error_key' => 'mailpn_test_email_send_error', 'error_content' => esc_html__('User not found', 'mailpn')]);
            exit();
          }
          
          $user_email = $user_data->user_email;
          
          // Enable notifications for the user performing the test
          update_user_meta($user_id, 'userspn_notifications', 'on');
          
          // Get the post content
          $post = get_post($post_id);
          if (!$post) {
            echo wp_json_encode(['error_key' => 'mailpn_test_email_send_error', 'error_content' => esc_html__('Post not found', 'mailpn')]);
            exit();
          }
          
          $subject = get_the_title($post_id);
          $content = $post->post_content;
          
          // Clear previous errors
          $GLOBALS['mailpn_last_error'] = null;
          
          // Verify basic configuration before attempting to send
          $config_errors = [];
          
          // Verify SMTP configuration if enabled
          if (get_option('mailpn_smtp_enabled') === 'on') {
            $smtp_host = get_option('mailpn_smtp_host');
            $smtp_port = get_option('mailpn_smtp_port');
            $smtp_auth = get_option('mailpn_smtp_auth');
            $smtp_username = get_option('mailpn_smtp_username');
            $smtp_password = get_option('mailpn_smtp_password');
            
            if (empty($smtp_host)) {
              $config_errors[] = 'SMTP Host is empty';
            }
            if (empty($smtp_port)) {
              $config_errors[] = 'SMTP Port is empty';
            }
            if ($smtp_auth === 'on') {
              if (empty($smtp_username)) {
                $config_errors[] = 'SMTP Username is empty';
              }
              if (empty($smtp_password)) {
                $config_errors[] = 'SMTP Password is empty';
              }
            }
          } else {
            // Verify server configuration for sending without SMTP
            if (!ini_get('sendmail_path') && !function_exists('mail')) {
              $config_errors[] = 'No mail server configured (sendmail_path or mail() function)';
            }
          }
          
          // If there are configuration errors, show them immediately
          if (!empty($config_errors)) {
            $error_message = esc_html__('Email configuration error', 'mailpn') . ': ' . implode('; ', $config_errors);
            echo wp_json_encode(['error_key' => 'mailpn_test_email_send_error', 'error_content' => $error_message]);exit();
          }
          
          // Enable PHPMailer error capturing
          $phpmailer_error = '';
          $original_error_handler = set_error_handler(function($severity, $message, $file, $line) use (&$phpmailer_error) {
            if (strpos($message, 'PHPMailer') !== false || strpos($message, 'SMTP') !== false || strpos($message, 'mail') !== false) {
              $phpmailer_error = $message;
            }
            return false; // Let PHP handle the error normally
          });
          
          // Initialize test variables
          $test_result = false;
          $test_error = '';
          
          // Configure PHPMailer for SMTP if enabled
          if (get_option('mailpn_smtp_enabled') === 'on') {
            add_action('phpmailer_init', function($phpmailer) {
              $phpmailer->isSMTP();
              $phpmailer->Host = get_option('mailpn_smtp_host');
              $phpmailer->Port = get_option('mailpn_smtp_port');
              $phpmailer->SMTPSecure = get_option('mailpn_smtp_secure');
              $phpmailer->SMTPAuth = get_option('mailpn_smtp_auth') === 'on';
              if ($phpmailer->SMTPAuth) {
                $phpmailer->Username = get_option('mailpn_smtp_username');
                $phpmailer->Password = get_option('mailpn_smtp_password');
              }
              $phpmailer->Timeout = 10; // Short timeout for test sends
            });
          }
          
          // Send the actual email using the full template with mailpn_id so that
          // content shortcodes ([new-books], [new-contents]) receive the period
          // configuration from the mail template.
          $result = do_shortcode('[mailpn-sender mailpn_user_to="' . $user_id . '" mailpn_id="' . $post_id . '"]');

          // If it fails, capture the PHPMailer error
          if (!$result) {
            if (isset($GLOBALS['phpmailer']) && is_object($GLOBALS['phpmailer'])) {
              $test_error = $GLOBALS['phpmailer']->ErrorInfo;
            }
          }

          // Restore the original error handler
          if ($original_error_handler) {
            restore_error_handler();
          }

          if ($result) {
            echo wp_json_encode(['error_key' => '', 'error_content' => esc_html__('Test email sent successfully to', 'mailpn') . ' ' . $user_email]);exit();
          } else {
            // Build detailed error message
            $error_message = esc_html__('Failed to send test email', 'mailpn');
            $error_details = [];

            // Use the direct test error if available
            if (!empty($test_error)) {
              $error_message = esc_html__('Email sending failed', 'mailpn') . ': ' . $test_error;
              $error_details['phpmailer_error'] = $test_error;
            } else {
              // Check for errors captured by the error handler
              if (!empty($phpmailer_error)) {
                $error_message = esc_html__('Email sending failed', 'mailpn') . ': ' . $phpmailer_error;
                $error_details['phpmailer_error'] = $phpmailer_error;
              } else {
                // Check globally stored error information
                if (isset($GLOBALS['mailpn_last_error']) && is_array($GLOBALS['mailpn_last_error'])) {
                  $last_error = $GLOBALS['mailpn_last_error'];
                  if (!empty($last_error['message'])) {
                    $error_message = esc_html__('Email sending failed', 'mailpn') . ': ' . $last_error['message'];
                    $error_details = array_merge($error_details, $last_error);
                  }
                } else {
                  // Check PHPMailer errors directly
                  if (isset($GLOBALS['phpmailer']) && is_object($GLOBALS['phpmailer'])) {
                    $phpmailer_error_info = $GLOBALS['phpmailer']->ErrorInfo;
                    if (!empty($phpmailer_error_info)) {
                      $error_message = esc_html__('Email sending failed', 'mailpn') . ': ' . $phpmailer_error_info;
                      $error_details['phpmailer_error_info'] = $phpmailer_error_info;
                    }
                  }
                }
              }
            }

            // If no specific error, add diagnostic information
            if ($error_message === esc_html__('Failed to send test email', 'mailpn')) {
              $error_message = esc_html__('Email sending failed', 'mailpn') . ': ' . esc_html__('Check SMTP settings or server mail configuration', 'mailpn');
            }

            // Log the error with full details
            self::mailpn_log_email_error('campaign_test_email', $error_message, array_merge([
              'post_id' => $post_id,
              'user_id' => $user_id,
              'user_email' => $user_email,
              'subject' => $subject,
              'config_check_passed' => empty($config_errors),
            ], $error_details));

            echo wp_json_encode(['error_key' => 'mailpn_test_email_send_error', 'error_content' => $error_message]);exit();
          }
          
          break;
        case 'mailpn_update_user_role':
          if (!current_user_can('manage_options')) {
            echo wp_json_encode(['error_key' => 'mailpn_role_error', 'error_content' => esc_html__('Unauthorized access.', 'mailpn')]);
            exit();
          }

          $role_action = !empty($_POST['role_action']) ? sanitize_text_field(wp_unslash($_POST['role_action'])) : '';
          $role = !empty($_POST['role']) ? sanitize_text_field(wp_unslash($_POST['role'])) : '';
          $user_ids = !empty($_POST['user_ids']) ? array_map('intval', wp_unslash($_POST['user_ids'])) : [];
          $role_nonce = !empty($_POST['role_nonce']) ? sanitize_text_field(wp_unslash($_POST['role_nonce'])) : '';

          if (!wp_verify_nonce($role_nonce, 'mailpn-role-assignment')) {
            echo wp_json_encode(['error_key' => 'mailpn_role_nonce_error', 'error_content' => esc_html__('Security check failed.', 'mailpn')]);
            exit();
          }

          $plugin_roles = ['mailpn_role_manager'];
          $role_labels = ['mailpn_role_manager' => __('Mailing Manager - PN', 'mailpn')];

          if (!in_array($role, $plugin_roles)) {
            echo wp_json_encode(['error_key' => 'mailpn_role_invalid', 'error_content' => esc_html__('Invalid role specified.', 'mailpn')]);
            exit();
          }

          if (empty($user_ids)) {
            echo wp_json_encode(['error_key' => 'mailpn_role_no_users', 'error_content' => esc_html__('No users selected.', 'mailpn')]);
            exit();
          }

          $updated_count = 0;
          foreach ($user_ids as $user_id) {
            $user = get_user_by('id', $user_id);
            if ($user) {
              if ($role_action === 'assign') {
                $user->add_role($role);
                $updated_count++;
              } elseif ($role_action === 'remove') {
                $user->remove_role($role);
                $updated_count++;
              }
            }
          }

          $role_label_text = isset($role_labels[$role]) ? $role_labels[$role] : $role;
          if ($role_action === 'assign') {
            $message = sprintf(__('%d user(s) have been assigned the %s role.', 'mailpn'), $updated_count, $role_label_text);
          } else {
            $message = sprintf(__('%d user(s) have been removed from the %s role.', 'mailpn'), $updated_count, $role_label_text);
          }

          echo wp_json_encode(['error_key' => '', 'error_content' => $message]);
          exit();
          break;
        case 'mailpn_dashboard_stats_period':
          if (!current_user_can('manage_options')) {
            echo wp_json_encode(['error_key' => 'no_permission', 'error_content' => esc_html__('You do not have permission.', 'mailpn')]);
            exit();
          }
          MAILPN_Dashboard::ajax_dashboard_stats_period();
          exit();
          break;

        case 'mailpn_status_details':
          if (!current_user_can('manage_options')) {
            echo wp_json_encode(['error_key' => 'no_permission', 'error_content' => esc_html__('You do not have permission.', 'mailpn')]);
            exit();
          }
          $detail_post_id = !empty($_POST['post_id']) ? intval($_POST['post_id']) : 0;
          if (empty($detail_post_id)) {
            echo wp_json_encode(['error_key' => 'missing_id', 'error_content' => esc_html__('Missing email ID.', 'mailpn')]);
            exit();
          }
          echo wp_json_encode([
            'error_key' => '',
            'html' => MAILPN_Post_Type_Mail::mailpn_get_status_popup_html($detail_post_id),
          ]);
          exit();
          break;

        case 'mailpn_settings_export':
          if (!current_user_can('manage_options')) {
            echo wp_json_encode(['error_key' => 'permission_denied']);
            exit();
          }

          $settings  = new MAILPN_Settings();
          $options   = $settings->mailpn_get_options();
          $export    = [];

          foreach ($options as $key => $config) {
            if (!isset($config['input']) || in_array($config['input'], ['html_multi'])) continue;
            if (isset($config['type']) && in_array($config['type'], ['nonce', 'submit'])) continue;
            if (isset($config['section'])) continue;

            $value = get_option($key, '');
            if ($value !== '') {
              $export[$key] = $value;
            }
          }

          echo wp_json_encode(['error_key' => '', 'settings' => $export]);
          exit();
          break;

        case 'mailpn_settings_import':
          if (!current_user_can('manage_options')) {
            echo wp_json_encode(['error_key' => 'permission_denied']);
            exit();
          }

          $raw = isset($_POST['settings']) ? wp_unslash($_POST['settings']) : '';
          $import = json_decode($raw, true);

          if (!is_array($import) || empty($import)) {
            echo wp_json_encode(['error_key' => 'invalid_data', 'error_content' => 'Invalid settings data.']);
            exit();
          }

          $settings  = new MAILPN_Settings();
          $options   = $settings->mailpn_get_options();
          $allowed   = array_keys($options);
          $count     = 0;

          foreach ($import as $key => $value) {
            if (in_array($key, $allowed)) {
              update_option($key, sanitize_text_field($value));
              $count++;
            }
          }

          echo wp_json_encode(['error_key' => '', 'count' => $count]);
          exit();
          break;

        case 'mailpn_install_plugin':
          if (!current_user_can('install_plugins')) {
            echo wp_json_encode(['error_key' => 'permission_denied']);
            exit;
          }

          $slug = isset($_POST['slug']) ? sanitize_text_field($_POST['slug']) : '';
          $allowed_slugs = ['pn-customers-manager', 'userspn', 'pn-tasks-manager', 'pn-cookies-manager'];

          if (!in_array($slug, $allowed_slugs, true)) {
            echo wp_json_encode(['error_key' => 'invalid_slug']);
            exit;
          }

          include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
          include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
          include_once ABSPATH . 'wp-admin/includes/plugin.php';

          $api = plugins_api('plugin_information', [
            'slug'   => $slug,
            'fields' => ['sections' => false],
          ]);

          if (is_wp_error($api)) {
            echo wp_json_encode(['error_key' => 'api_error', 'error_content' => $api->get_error_message()]);
            exit;
          }

          $upgrader = new Plugin_Upgrader(new WP_Ajax_Upgrader_Skin());
          $result   = $upgrader->install($api->download_link);

          if (is_wp_error($result)) {
            echo wp_json_encode(['error_key' => 'install_error', 'error_content' => $result->get_error_message()]);
            exit;
          }

          if ($result === false) {
            echo wp_json_encode(['error_key' => 'install_failed', 'error_content' => 'Installation failed.']);
            exit;
          }

          echo wp_json_encode(['error_key' => '']);
          exit;
          break;

        case 'mailpn_activate_plugin':
          if (!current_user_can('activate_plugins')) {
            echo wp_json_encode(['error_key' => 'permission_denied']);
            exit;
          }

          $slug = isset($_POST['slug']) ? sanitize_text_field($_POST['slug']) : '';
          $plugin_files = [
            'pn-customers-manager' => 'pn-customers-manager/pn-customers-manager.php',
            'userspn'              => 'userspn/userspn.php',
            'pn-tasks-manager'     => 'pn-tasks-manager/pn-tasks-manager.php',
            'pn-cookies-manager'   => 'pn-cookies-manager/pn-cookies-manager.php',
          ];

          if (!isset($plugin_files[$slug])) {
            echo wp_json_encode(['error_key' => 'invalid_slug']);
            exit;
          }

          $plugin_file = $plugin_files[$slug];
          $result = activate_plugin($plugin_file);

          if (is_wp_error($result)) {
            echo wp_json_encode(['error_key' => 'activate_error', 'error_content' => $result->get_error_message()]);
            exit;
          }

          echo wp_json_encode(['error_key' => '']);
          exit;
          break;
        case 'mailpn_get_queue_details':
          if (!current_user_can('manage_options')) {
            echo wp_json_encode(['error_key' => 'permission_denied']);
            exit;
          }

          $mail_id = !empty($_POST['mail_id']) ? intval($_POST['mail_id']) : 0;

          $queue_data = get_option('mailpn_queue', []);
          $queue_paused = get_option('mailpn_queue_paused');
          $paused_by_errors = get_option('mailpn_queue_paused_by_errors');
          $consecutive_errors = get_option('mailpn_consecutive_errors_count', 0);
          $consecutive_limit = get_option('mailpn_consecutive_errors_limit', 10);
          $mails_sent_today = get_option('mailpn_mails_sent_today', 0);
          $daily_limit = get_option('mailpn_sent_every_day', 500);
          $rate_limit = get_option('mailpn_sent_every_ten_minutes', 5);
          $paused_daily_limit = $mails_sent_today >= $daily_limit;

          // Calculate total pending across ALL templates (global queue)
          $total_pending = 0;
          $pending_list = [];
          $preview_limit = 30; // Show up to 30 emails from global queue

          foreach ($queue_data as $template_id => $users) {
            if (!empty($users)) {
              $total_pending += count($users);

              // Add users from this template to the list
              foreach ($users as $user_id) {
                if (count($pending_list) >= $preview_limit) break 2;

                $user = get_userdata($user_id);
                $template = get_post($template_id);

                if ($user && $template) {
                  $pending_list[] = [
                    'id' => $user_id,
                    'name' => trim($user->first_name . ' ' . $user->last_name),
                    'email' => $user->user_email,
                    'template_id' => $template_id,
                    'template_title' => $template->post_title,
                  ];
                }
              }
            }
          }

          // Check if we've hit the daily limit
          $remaining_today = $daily_limit - $mails_sent_today;
          $hit_daily_limit = $remaining_today <= 0;

          // Calculate percentage of daily limit used
          $daily_percentage = ($mails_sent_today / $daily_limit) * 100;

          // Determine if emails will send tomorrow
          $will_send_tomorrow = $hit_daily_limit ||
                                $paused_daily_limit ||
                                (!empty($queue_paused) && $daily_percentage >= 85);

          // Determine if system is effectively paused
          $is_paused = !empty($queue_paused) || $hit_daily_limit || $will_send_tomorrow;

          echo wp_json_encode([
            'error_key' => '',
            'is_paused' => $is_paused,
            'paused_by_errors' => !empty($paused_by_errors),
            'paused_daily_limit' => $paused_daily_limit,
            'hit_daily_limit' => $hit_daily_limit,
            'pause_timestamp' => $queue_paused,
            'consecutive_errors' => $consecutive_errors,
            'consecutive_limit' => $consecutive_limit,
            'mails_sent_today' => $mails_sent_today,
            'daily_limit' => $daily_limit,
            'rate_limit' => $rate_limit,
            'remaining_today' => max(0, $remaining_today),
            'total_pending' => $total_pending,
            'pending_list' => $pending_list,
            'showing_sample' => $total_pending > $preview_limit,
          ]);
          exit;
          break;
        case 'mailpn_resume_queue':
          if (!current_user_can('manage_options')) {
            echo wp_json_encode(['error_key' => 'permission_denied']);
            exit;
          }

          // Resume queue by removing pause flags
          delete_option('mailpn_queue_paused');
          delete_option('mailpn_queue_paused_by_errors');
          delete_option('mailpn_consecutive_errors_count');

          echo wp_json_encode(['error_key' => '']);
          exit;
          break;
        case 'mailpn_pause_queue':
          if (!current_user_can('manage_options')) {
            echo wp_json_encode(['error_key' => 'permission_denied']);
            exit;
          }

          // Pause queue by setting pause flag
          update_option('mailpn_queue_paused', time());
          // Don't set paused_by_errors flag as this is a manual pause

          echo wp_json_encode(['error_key' => '']);
          exit;
          break;
        case 'mailpn_get_error_details':
          if (!current_user_can('manage_options')) {
            echo wp_json_encode(['error_key' => 'permission_denied']);
            exit;
          }

          $rec_id = !empty($_POST['rec_id']) ? intval($_POST['rec_id']) : 0;
          if (!$rec_id) {
            echo wp_json_encode(['error_key' => 'invalid_rec_id']);
            exit;
          }

          // Get all error information
          $error_message = get_post_meta($rec_id, 'mailpn_rec_error', true);
          $to_email = get_post_meta($rec_id, 'mailpn_rec_to_email', true);
          $to_user_id = get_post_meta($rec_id, 'mailpn_rec_to', true);
          $subject = get_post_meta($rec_id, 'mailpn_rec_subject', true);
          $mail_id = get_post_meta($rec_id, 'mailpn_rec_mail_id', true);
          $sent_datetime = get_post_meta($rec_id, 'mailpn_rec_sent_datetime', true);
          $headers = get_post_meta($rec_id, 'mailpn_rec_headers', true);
          $server_ip = get_post_meta($rec_id, 'mailpn_rec_server_ip', true);

          // Parse error message into sections
          $error_lines = !empty($error_message) ? explode("\n", $error_message) : [];

          // Get user info
          $user_info = null;
          if ($to_user_id) {
            $user = get_userdata($to_user_id);
            if ($user) {
              $user_info = [
                'id' => $to_user_id,
                'name' => trim($user->first_name . ' ' . $user->last_name),
                'email' => $user->user_email,
              ];
            }
          }

          // Get template info
          $template_info = null;
          if ($mail_id) {
            $template = get_post($mail_id);
            if ($template) {
              $template_info = [
                'id' => $mail_id,
                'title' => $template->post_title,
              ];
            }
          }

          echo wp_json_encode([
            'error_key' => '',
            'rec_id' => $rec_id,
            'error_message' => $error_message,
            'error_lines' => $error_lines,
            'to_email' => $to_email,
            'subject' => $subject,
            'sent_datetime' => $sent_datetime,
            'headers' => $headers,
            'server_ip' => $server_ip,
            'user_info' => $user_info,
            'template_info' => $template_info,
          ]);
          exit;
          break;
        case 'mailpn_get_global_queue_status':
          if (!current_user_can('manage_options')) {
            echo wp_json_encode(['error_key' => 'permission_denied']);
            exit;
          }

          $queue_data = get_option('mailpn_queue', []);
          $queue_paused = get_option('mailpn_queue_paused');
          $paused_by_errors = get_option('mailpn_queue_paused_by_errors');
          $mails_sent_today = get_option('mailpn_mails_sent_today', 0);
          $daily_limit = get_option('mailpn_sent_every_day', 500);
          $rate_limit = get_option('mailpn_sent_every_ten_minutes', 5);
          $paused_daily_limit = $mails_sent_today >= $daily_limit;

          // Calculate total pending across all templates
          $total_pending = 0;
          $templates_in_queue = [];
          foreach ($queue_data as $mail_id => $users) {
            if (!empty($users)) {
              $count = count($users);
              $total_pending += $count;
              $template = get_post($mail_id);
              if ($template) {
                $templates_in_queue[] = [
                  'id' => $mail_id,
                  'title' => $template->post_title,
                  'pending' => $count,
                ];
              }
            }
          }

          // Get next batch to send (up to 3x rate limit for preview)
          $next_batch = [];
          $batch_count = 0;
          $preview_limit = $rate_limit * 3; // Show up to 3 batches
          $current_time = current_time('timestamp');

          // Check if we've hit the daily limit
          $remaining_today = $daily_limit - $mails_sent_today;
          $hit_daily_limit = $remaining_today <= 0;

          // Calculate percentage of daily limit used
          $daily_percentage = ($mails_sent_today / $daily_limit) * 100;

          // Determine if emails will send tomorrow:
          // 1. If we hit the daily limit (no quota left) → tomorrow
          // 2. If paused and we've used 85%+ of daily limit → likely tomorrow
          // 3. If paused by daily limit explicitly → tomorrow
          $will_send_tomorrow = $hit_daily_limit ||
                                $paused_daily_limit ||
                                (!empty($queue_paused) && $daily_percentage >= 85);

          // If we hit the daily limit or queue is paused with high usage, calculate from tomorrow
          $base_time = $current_time;
          if ($will_send_tomorrow) {
            // Set base time to tomorrow at the same time
            $base_time = strtotime('tomorrow', $current_time);
          }

          foreach ($queue_data as $mail_id => $users) {
            if ($batch_count >= $preview_limit) break;
            foreach ($users as $user_id) {
              if ($batch_count >= $preview_limit) break;
              $user = get_userdata($user_id);
              if ($user) {
                // Calculate estimated send time based on position in queue
                // Each batch of rate_limit emails takes 10 minutes

                if ($will_send_tomorrow) {
                  // All emails start from tomorrow
                  $batch_number = floor($batch_count / $rate_limit);
                  $estimated_minutes = $batch_number * 10;
                  $estimated_timestamp = $base_time + ($estimated_minutes * 60);
                  $is_tomorrow = true;
                } else {
                  // Check if this batch will fit in today's remaining quota
                  $batch_number = floor($batch_count / $rate_limit);
                  $emails_before_this = $batch_count;

                  if ($emails_before_this < $remaining_today) {
                    // This email can be sent today
                    $estimated_minutes = $batch_number * 10;
                    $estimated_timestamp = $base_time + ($estimated_minutes * 60);
                    $is_tomorrow = false;
                  } else {
                    // This email will be sent tomorrow
                    $tomorrow = strtotime('tomorrow', $current_time);
                    $emails_tomorrow = $emails_before_this - $remaining_today;
                    $batch_number_tomorrow = floor($emails_tomorrow / $rate_limit);
                    $estimated_minutes = $batch_number_tomorrow * 10;
                    $estimated_timestamp = $tomorrow + ($estimated_minutes * 60);
                    $is_tomorrow = true;
                  }
                }

                $next_batch[] = [
                  'user_id' => $user_id,
                  'name' => trim($user->first_name . ' ' . $user->last_name),
                  'email' => $user->user_email,
                  'template_id' => $mail_id,
                  'template_title' => get_the_title($mail_id),
                  'estimated_send_time' => $estimated_timestamp,
                  'estimated_send_formatted' => date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $estimated_timestamp),
                  'batch_number' => $batch_number,
                  'sends_tomorrow' => $is_tomorrow,
                ];
                $batch_count++;
              }
            }
          }

          // Determine if system is effectively paused
          $is_paused = !empty($queue_paused) || $hit_daily_limit || $will_send_tomorrow;

          echo wp_json_encode([
            'error_key' => '',
            'is_active' => !empty($queue_data) && !$is_paused,
            'is_paused' => $is_paused,
            'paused_by_errors' => !empty($paused_by_errors),
            'paused_daily_limit' => $paused_daily_limit,
            'hit_daily_limit' => $hit_daily_limit,
            'remaining_today' => max(0, $remaining_today),
            'total_pending' => $total_pending,
            'templates_in_queue' => $templates_in_queue,
            'next_batch' => $next_batch,
            'mails_sent_today' => $mails_sent_today,
            'daily_limit' => $daily_limit,
            'rate_limit' => $rate_limit,
            'resume_tomorrow' => $hit_daily_limit || $will_send_tomorrow ? date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime('tomorrow', $current_time)) : '',
          ]);
          exit;
          break;
        case 'mailpn_check_deliverability':
          if (!current_user_can('manage_options')) {
            echo wp_json_encode(['error_key' => 'permission_denied']);
            exit;
          }

          $checks = [];
          $score = 100;
          $from_email = get_option('mailpn_smtp_from_email');
          if (empty($from_email)) {
            $from_email = get_option('mailpn_from_email');
          }
          if (empty($from_email)) {
            $from_email = get_option('admin_email');
          }

          // Extract domain from email
          $domain = '';
          if (!empty($from_email) && strpos($from_email, '@') !== false) {
            $domain = substr($from_email, strpos($from_email, '@') + 1);
          }

          // Check 1: SPF Record
          if (!empty($domain)) {
            $spf_record = dns_get_record($domain, DNS_TXT);
            $has_spf = false;
            foreach ($spf_record as $record) {
              if (isset($record['txt']) && strpos($record['txt'], 'v=spf1') !== false) {
                $has_spf = true;
                break;
              }
            }
            $checks['spf'] = [
              'name' => 'SPF Record',
              'status' => $has_spf ? 'passed' : 'failed',
              'message' => $has_spf ? __('SPF record found and configured', 'mailpn') : __('SPF record not found. Add an SPF record to authorize your server.', 'mailpn'),
              'suggestion' => !$has_spf ? __('Add a TXT record to your DNS: "v=spf1 mx a ~all" or contact your hosting provider to configure SPF.', 'mailpn') : '',
            ];
            if (!$has_spf) $score -= 25;
          } else {
            $checks['spf'] = [
              'name' => 'SPF Record',
              'status' => 'failed',
              'message' => __('No email configured. Configure email in settings first.', 'mailpn'),
              'suggestion' => __('Go to Settings → Email contents and configure "From Email" field.', 'mailpn'),
            ];
            $score -= 25;
          }

          // Check 2: DKIM Record
          if (!empty($domain)) {
            $dkim_selectors = ['default', 'selector1', 'selector2', 'mail', 'k1'];
            $has_dkim = false;
            foreach ($dkim_selectors as $selector) {
              $dkim_domain = $selector . '._domainkey.' . $domain;
              $dkim_record = @dns_get_record($dkim_domain, DNS_TXT);
              if (!empty($dkim_record)) {
                foreach ($dkim_record as $record) {
                  if (isset($record['txt']) && strpos($record['txt'], 'v=DKIM1') !== false) {
                    $has_dkim = true;
                    break 2;
                  }
                }
              }
            }
            $checks['dkim'] = [
              'name' => 'DKIM Record',
              'status' => $has_dkim ? 'passed' : 'warning',
              'message' => $has_dkim ? __('DKIM record found', 'mailpn') : __('DKIM record not found. DKIM helps prevent email spoofing.', 'mailpn'),
              'suggestion' => !$has_dkim ? __('Contact your email provider or hosting to enable DKIM signing. They will provide DNS records to add to your domain.', 'mailpn') : '',
            ];
            if (!$has_dkim) $score -= 15;
          }

          // Check 3: DMARC Record
          if (!empty($domain)) {
            $dmarc_domain = '_dmarc.' . $domain;
            $dmarc_record = @dns_get_record($dmarc_domain, DNS_TXT);
            $has_dmarc = false;
            if (!empty($dmarc_record)) {
              foreach ($dmarc_record as $record) {
                if (isset($record['txt']) && strpos($record['txt'], 'v=DMARC1') !== false) {
                  $has_dmarc = true;
                  break;
                }
              }
            }
            $checks['dmarc'] = [
              'name' => 'DMARC Record',
              'status' => $has_dmarc ? 'passed' : 'warning',
              'message' => $has_dmarc ? __('DMARC policy configured', 'mailpn') : __('DMARC not configured. Recommended for better deliverability.', 'mailpn'),
              'suggestion' => !$has_dmarc ? __('Add a TXT record at "_dmarc.' . $domain . '" with value: "v=DMARC1; p=none; rua=mailto:' . $from_email . '"', 'mailpn') : '',
            ];
            if (!$has_dmarc) $score -= 10;
          }

          // Check 4: MX Records
          if (!empty($domain)) {
            $mx_records = @dns_get_record($domain, DNS_MX);
            $has_mx = !empty($mx_records);
            $checks['mx'] = [
              'name' => 'MX Records',
              'status' => $has_mx ? 'passed' : 'failed',
              'message' => $has_mx ? __('MX records configured', 'mailpn') : __('MX records not found', 'mailpn'),
              'suggestion' => !$has_mx ? __('Contact your hosting provider to configure MX records for your domain.', 'mailpn') : '',
            ];
            if (!$has_mx) $score -= 20;
          }

          // Check 5: SMTP Configuration
          $smtp_enabled = get_option('mailpn_smtp_enabled') == 'on';
          $smtp_host = get_option('mailpn_smtp_host');
          $smtp_port = get_option('mailpn_smtp_port');
          $smtp_secure = get_option('mailpn_smtp_secure');

          if ($smtp_enabled && !empty($smtp_host) && !empty($smtp_port)) {
            $checks['smtp'] = [
              'name' => 'SMTP Configuration',
              'status' => 'passed',
              'message' => sprintf(__('SMTP enabled (%s:%s with %s)', 'mailpn'), $smtp_host, $smtp_port, $smtp_secure),
              'suggestion' => '',
            ];
          } else {
            $checks['smtp'] = [
              'name' => 'SMTP Configuration',
              'status' => 'warning',
              'message' => __('SMTP not configured. Using PHP mail() function may have lower deliverability.', 'mailpn'),
              'suggestion' => __('Configure SMTP in Settings → SMTP Configuration. Use services like Gmail, SendGrid, or Mailgun for better deliverability.', 'mailpn'),
            ];
            $score -= 15;
          }

          // Check 6: From Email Configuration
          if (!empty($from_email) && filter_var($from_email, FILTER_VALIDATE_EMAIL)) {
            $checks['from_email'] = [
              'name' => 'From Email',
              'status' => 'passed',
              'message' => sprintf(__('Configured: %s', 'mailpn'), $from_email),
              'suggestion' => '',
            ];
          } else {
            $checks['from_email'] = [
              'name' => 'From Email',
              'status' => 'failed',
              'message' => __('From email not configured or invalid', 'mailpn'),
              'suggestion' => __('Go to Settings → Email contents and configure a valid email address in "From Email" field.', 'mailpn'),
            ];
            $score -= 15;
          }

          // Check 7: Open Tracking (JavaScript in emails)
          $open_tracking_enabled = get_option('mailpn_open_tracking') == 'on';
          if (!$open_tracking_enabled) {
            $checks['open_tracking'] = [
              'name' => 'Email JavaScript',
              'status' => 'passed',
              'message' => __('Open tracking disabled - no JavaScript in emails', 'mailpn'),
              'suggestion' => '',
            ];
          } else {
            $checks['open_tracking'] = [
              'name' => 'Email JavaScript',
              'status' => 'warning',
              'message' => __('Open tracking enabled - uses inline JavaScript which may affect spam score', 'mailpn'),
              'suggestion' => __('Consider disabling "Enable open tracking" in Settings → Email mechanics to improve deliverability. Spam filters penalize JavaScript in emails.', 'mailpn'),
            ];
            $score -= 10;
          }

          // Check 8: List-Unsubscribe Header
          // Always present for user IDs with UsersPN, or generic for direct emails
          $checks['list_unsubscribe'] = [
            'name' => 'List-Unsubscribe Header',
            'status' => 'passed',
            'message' => __('List-Unsubscribe header configured for all emails', 'mailpn'),
            'suggestion' => '',
          ];

          // Check 9: Text/Plain Version
          // Note: Currently only sends HTML. Adding text/plain would improve score
          $checks['text_version'] = [
            'name' => 'Text/Plain Version',
            'status' => 'warning',
            'message' => __('Emails sent as HTML only', 'mailpn'),
            'suggestion' => __('Consider adding a text/plain version alongside HTML for better compatibility and deliverability. Many spam filters prefer multipart emails.', 'mailpn'),
          ];
          $score -= 5;

          $score = max(0, $score);

          echo wp_json_encode([
            'error_key' => '',
            'score' => $score,
            'checks' => $checks,
            'domain' => $domain,
          ]);
          exit;
          break;
        case 'mailpn_analyze_headers':
          if (!current_user_can('manage_options')) {
            echo wp_json_encode(['error_key' => 'permission_denied']);
            exit;
          }

          $headers = !empty($_POST['headers']) ? sanitize_textarea_field($_POST['headers']) : '';

          if (empty($headers)) {
            echo wp_json_encode([
              'error_key' => 'empty_headers',
              'error_content' => __('No headers provided', 'mailpn'),
            ]);
            exit;
          }

          $analysis = [];
          $score = 100;

          // Check SPF
          if (preg_match('/spf=(\w+)/i', $headers, $matches)) {
            $spf_result = strtolower($matches[1]);
            $spf_passed = in_array($spf_result, ['pass', 'neutral']);
            $analysis['spf'] = [
              'name' => 'SPF Validation',
              'status' => $spf_passed ? 'passed' : 'failed',
              'message' => sprintf(__('SPF Result: %s', 'mailpn'), $spf_result),
              'suggestion' => !$spf_passed ? __('SPF validation failed. Check your SPF records and ensure your sending server is authorized.', 'mailpn') : '',
            ];
            if (!$spf_passed) $score -= 30;
          } else {
            $analysis['spf'] = [
              'name' => 'SPF Validation',
              'status' => 'warning',
              'message' => __('SPF result not found in headers', 'mailpn'),
              'suggestion' => __('Could not detect SPF validation in headers. This might indicate the headers are incomplete.', 'mailpn'),
            ];
            $score -= 10;
          }

          // Check DKIM
          if (preg_match('/dkim=(\w+)/i', $headers, $matches)) {
            $dkim_result = strtolower($matches[1]);
            $dkim_passed = ($dkim_result === 'pass');
            $analysis['dkim'] = [
              'name' => 'DKIM Signature',
              'status' => $dkim_passed ? 'passed' : 'failed',
              'message' => sprintf(__('DKIM Result: %s', 'mailpn'), $dkim_result),
              'suggestion' => !$dkim_passed ? __('DKIM signature verification failed. Ensure DKIM is properly configured on your email server.', 'mailpn') : '',
            ];
            if (!$dkim_passed) $score -= 25;
          } else {
            $analysis['dkim'] = [
              'name' => 'DKIM Signature',
              'status' => 'warning',
              'message' => __('DKIM result not found in headers', 'mailpn'),
              'suggestion' => __('DKIM signature not detected. Consider enabling DKIM signing for better email authentication.', 'mailpn'),
            ];
            $score -= 15;
          }

          // Check DMARC
          if (preg_match('/dmarc=(\w+)/i', $headers, $matches)) {
            $dmarc_result = strtolower($matches[1]);
            $dmarc_passed = ($dmarc_result === 'pass');
            $analysis['dmarc'] = [
              'name' => 'DMARC Policy',
              'status' => $dmarc_passed ? 'passed' : 'failed',
              'message' => sprintf(__('DMARC Result: %s', 'mailpn'), $dmarc_result),
              'suggestion' => !$dmarc_passed ? __('DMARC validation failed. Review your DMARC policy and alignment.', 'mailpn') : '',
            ];
            if (!$dmarc_passed) $score -= 20;
          }

          // Check for spam headers
          $spam_indicators = [
            'X-Spam-Flag: YES' => 'Email marked as spam',
            'X-Spam-Status: Yes' => 'Spam filters detected spam patterns',
          ];

          foreach ($spam_indicators as $indicator => $message) {
            if (stripos($headers, $indicator) !== false) {
              $analysis['spam_flag'] = [
                'name' => 'Spam Detection',
                'status' => 'failed',
                'message' => __($message, 'mailpn'),
                'suggestion' => __('Your email triggered spam filters. Review content for spam keywords, ensure proper authentication, and check blacklists.', 'mailpn'),
              ];
              $score -= 30;
              break;
            }
          }

          // Check for Return-Path
          if (preg_match('/Return-Path:\s*<?([^>\r\n]+)>?/i', $headers, $matches)) {
            $return_path = trim($matches[1], '<> ');
            $analysis['return_path'] = [
              'name' => 'Return-Path',
              'status' => 'passed',
              'message' => sprintf(__('Return-Path configured: %s', 'mailpn'), $return_path),
              'suggestion' => '',
            ];
          } else {
            $analysis['return_path'] = [
              'name' => 'Return-Path',
              'status' => 'warning',
              'message' => __('Return-Path not found', 'mailpn'),
              'suggestion' => __('Configure Return-Path header for better bounce handling.', 'mailpn'),
            ];
            $score -= 10;
          }

          $score = max(0, $score);

          echo wp_json_encode([
            'error_key' => '',
            'score' => $score,
            'analysis' => $analysis,
          ]);
          exit;
          break;
        case 'mailpn_view_email_error_log':
          if (!current_user_can('manage_options')) {
            echo wp_json_encode(['error_key' => 'permission_denied', 'error_content' => esc_html__('Unauthorized access', 'mailpn')]);
            exit;
          }

          $lines = !empty($_POST['lines']) ? intval($_POST['lines']) : 100;
          $log_content = MAILPN_Debug::get_email_error_log($lines);
          $stats = MAILPN_Debug::get_email_error_stats();

          echo wp_json_encode([
            'error_key' => '',
            'log_content' => $log_content,
            'stats' => $stats,
          ]);
          exit;
          break;

        case 'mailpn_clear_email_error_log':
          if (!current_user_can('manage_options')) {
            echo wp_json_encode(['error_key' => 'permission_denied', 'error_content' => esc_html__('Unauthorized access', 'mailpn')]);
            exit;
          }

          MAILPN_Debug::clear_email_error_log();

          echo wp_json_encode([
            'error_key' => '',
            'message' => esc_html__('Email error log cleared successfully', 'mailpn'),
          ]);
          exit;
          break;

        case 'mailpn_send_test_email_external':
          if (!current_user_can('manage_options')) {
            echo wp_json_encode(['error_key' => 'permission_denied']);
            exit;
          }

          $to_email = !empty($_POST['to_email']) ? sanitize_email($_POST['to_email']) : '';

          if (empty($to_email) || !is_email($to_email)) {
            echo wp_json_encode([
              'error_key' => 'invalid_email',
              'error_content' => __('Please provide a valid email address', 'mailpn'),
            ]);
            exit;
          }

          // Get from email
          $from_email = get_option('mailpn_smtp_from_email');
          if (empty($from_email)) {
            $from_email = get_option('mailpn_from_email');
          }
          if (empty($from_email)) {
            $from_email = get_option('admin_email');
          }

          $from_name = get_option('mailpn_smtp_from_name');
          if (empty($from_name)) {
            $from_name = get_option('mailpn_from_name');
          }
          if (empty($from_name)) {
            $from_name = get_bloginfo('name');
          }

          $subject = sprintf(__('Deliverability Test from %s', 'mailpn'), get_bloginfo('name'));

          $message = '<html><head><meta charset="UTF-8"></head><body>';
          $message .= '<h2>' . __('Email Deliverability Test', 'mailpn') . '</h2>';
          $message .= '<p>' . sprintf(__('This is a test email sent from %s to verify email deliverability.', 'mailpn'), '<strong>' . get_bloginfo('name') . '</strong>') . '</p>';
          $message .= '<hr>';
          $message .= '<h3>' . __('Email Configuration:', 'mailpn') . '</h3>';
          $message .= '<ul>';
          $message .= '<li><strong>' . __('From:', 'mailpn') . '</strong> ' . esc_html($from_name) . ' &lt;' . esc_html($from_email) . '&gt;</li>';
          $message .= '<li><strong>' . __('Site:', 'mailpn') . '</strong> ' . get_bloginfo('name') . ' (' . get_site_url() . ')</li>';
          $message .= '<li><strong>' . __('Date:', 'mailpn') . '</strong> ' . date_i18n(get_option('date_format') . ' ' . get_option('time_format')) . '</li>';

          $smtp_enabled = get_option('mailpn_smtp_enabled') == 'on';
          if ($smtp_enabled) {
            $smtp_host = get_option('mailpn_smtp_host');
            $smtp_port = get_option('mailpn_smtp_port');
            $smtp_secure = get_option('mailpn_smtp_secure');
            $message .= '<li><strong>' . __('SMTP:', 'mailpn') . '</strong> ' . esc_html($smtp_host) . ':' . esc_html($smtp_port) . ' (' . esc_html($smtp_secure) . ')</li>';
          } else {
            $message .= '<li><strong>' . __('Mail Method:', 'mailpn') . '</strong> PHP mail()</li>';
          }

          $message .= '</ul>';
          $message .= '<hr>';
          $message .= '<p style="color: #666; font-size: 12px;">' . __('This email was sent using MailPN plugin for WordPress.', 'mailpn') . '</p>';
          $message .= '</body></html>';

          $headers = [
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $from_name . ' <' . $from_email . '>',
          ];

          $sent = wp_mail($to_email, $subject, $message, $headers);

          if ($sent) {
            echo wp_json_encode([
              'error_key' => '',
              'message' => sprintf(__('Test email sent successfully to %s. Check Mail-Tester for results.', 'mailpn'), $to_email),
            ]);
          } else {
            echo wp_json_encode([
              'error_key' => 'send_failed',
              'error_content' => __('Failed to send test email. Check your email configuration.', 'mailpn'),
            ]);
          }
          exit;
          break;
      }

      echo wp_json_encode([
        'error_key' => 'mailpn_save_error',
        'error_content' => esc_html__('Unknown AJAX type', 'mailpn'),
      ]);

      exit();
    } else {
      // No mailpn_ajax_type found
      echo wp_json_encode([
        'error_key' => 'mailpn_ajax_type_missing',
        'error_content' => esc_html__('AJAX type not specified', 'mailpn'),
      ]);
      exit();
    }
  }
  
  /**
   * Handle cart timestamp update AJAX request
   *
   * @since    1.0.0
   */
  public function mailpn_update_cart_timestamp() {
    // Check if user is logged in
    if (!is_user_logged_in()) {
      wp_die();
    }
    
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'mailpn-nonce')) {
      wp_die();
    }
    
    $user_id = get_current_user_id();
    
    // Get current cart items
    if (class_exists('WooCommerce')) {
      $cart_items = WC()->cart->get_cart();
      
      if (!empty($cart_items)) {
        update_user_meta($user_id, 'mailpn_woocommerce_cart_timestamp', time());
        update_user_meta($user_id, 'mailpn_woocommerce_cart_items', $cart_items);
      } else {
        // Cart is empty, remove cart meta
        delete_user_meta($user_id, 'mailpn_woocommerce_cart_timestamp');
        delete_user_meta($user_id, 'mailpn_woocommerce_cart_items');
      }
    }
    
    wp_die();
  }

  /**
   * Mark notification as read
   *
   * @since    1.0.0
   */
  public function mailpn_mark_notification_read() {
    // Check if user is logged in
    if (!is_user_logged_in()) {
      wp_send_json_error('User not logged in');
    }

    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'mailpn_notification_nonce')) {
      wp_send_json_error('Invalid nonce');
    }

    $notification_id = intval($_POST['notification_id']);
    $notifications_manager = new MAILPN_Notifications_Manager();
    
    $success = $notifications_manager->mark_notification_read($notification_id);
    
    if ($success) {
      wp_send_json_success('Notification marked as read');
    } else {
      wp_send_json_error('Unauthorized access');
    }
  }

  /**
   * Mark notification as unread
   *
   * @since    1.0.0
   */
  public function mailpn_mark_notification_unread() {
    // Check if user is logged in
    if (!is_user_logged_in()) {
      wp_send_json_error('User not logged in');
    }

    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'mailpn_notification_nonce')) {
      wp_send_json_error('Invalid nonce');
    }

    $notification_id = intval($_POST['notification_id']);
    $notifications_manager = new MAILPN_Notifications_Manager();
    
    $success = $notifications_manager->mark_notification_unread($notification_id);
    
    if ($success) {
      wp_send_json_success('Notification marked as unread');
    } else {
      wp_send_json_error('Unauthorized access');
    }
  }

  /**
   * Mark all notifications as read for a user
   *
   * @since    1.0.0
   */
  public function mailpn_mark_all_notifications_read() {
    // Check if user is logged in
    if (!is_user_logged_in()) {
      wp_send_json_error('User not logged in');
    }

    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'mailpn_notification_nonce')) {
      wp_send_json_error('Invalid nonce');
    }

    $user_id = intval($_POST['user_id']);
    $current_user_id = get_current_user_id();

    // Verify the user is marking their own notifications
    if ($user_id != $current_user_id) {
      wp_send_json_error('Unauthorized access');
    }

    $notifications_manager = new MAILPN_Notifications_Manager();
    $marked_count = $notifications_manager->mark_all_notifications_read($user_id);

    wp_send_json_success(array(
      'message' => 'All notifications marked as read',
      'count' => $marked_count
    ));
  }

  /**
   * Log email sending errors with detailed information
   *
   * @param string $context Context of the error (e.g., 'test_email_send', 'campaign_test')
   * @param string $error_message The error message
   * @param array $additional_data Additional data to log
   * @since    1.0.55
   */
  private static function mailpn_log_email_error($context, $error_message, $additional_data = []) {
    $log_entry = [
      'timestamp' => current_time('mysql'),
      'context' => $context,
      'error_message' => $error_message,
      'smtp_config' => [
        'smtp_enabled' => get_option('mailpn_smtp_enabled'),
        'smtp_host' => get_option('mailpn_smtp_host'),
        'smtp_port' => get_option('mailpn_smtp_port'),
        'smtp_secure' => get_option('mailpn_smtp_secure'),
        'smtp_auth' => get_option('mailpn_smtp_auth'),
        'smtp_from_email' => get_option('mailpn_smtp_from_email'),
      ],
      'server_info' => [
        'php_version' => PHP_VERSION,
        'wordpress_version' => get_bloginfo('version'),
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'server_ip' => $_SERVER['SERVER_ADDR'] ?? 'Unknown',
      ],
      'additional_data' => $additional_data,
    ];

    // Log to WordPress error log
    error_log('[MAILPN Email Error] ' . $context . ': ' . $error_message);

    // Log to custom mailpn log file
    $log_file = WP_CONTENT_DIR . '/mailpn-email-errors.log';
    $log_message = sprintf(
      "[%s] %s: %s\nDetails: %s\n\n",
      $log_entry['timestamp'],
      $context,
      $error_message,
      print_r($log_entry, true)
    );

    file_put_contents($log_file, $log_message, FILE_APPEND | LOCK_EX);

    // Optionally send to admin if configured
    if (get_option('mailpn_errors_to_admin') == 'on') {
      $admin_email = get_option('admin_email');
      $site_name = get_bloginfo('name');
      $subject = sprintf('[%s] Email Test Error', $site_name);

      $body = sprintf(
        "An email sending error occurred on %s\n\n" .
        "Context: %s\n" .
        "Time: %s\n" .
        "Error: %s\n\n" .
        "SMTP Configuration:\n" .
        "- SMTP Enabled: %s\n" .
        "- SMTP Host: %s\n" .
        "- SMTP Port: %s\n" .
        "- SMTP Secure: %s\n\n" .
        "Additional Information:\n%s\n",
        $site_name,
        $context,
        $log_entry['timestamp'],
        $error_message,
        $log_entry['smtp_config']['smtp_enabled'] === 'on' ? 'Yes' : 'No',
        $log_entry['smtp_config']['smtp_host'],
        $log_entry['smtp_config']['smtp_port'],
        $log_entry['smtp_config']['smtp_secure'],
        print_r($additional_data, true)
      );

      wp_mail($admin_email, $subject, $body, ['Content-Type: text/plain; charset=UTF-8']);
    }
  }
}