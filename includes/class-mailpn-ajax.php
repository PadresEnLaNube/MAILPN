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
            <h2><?php esc_html_e('MAILPN Test email', 'mailpn'); ?></h2>           
            <p><?php esc_html_e('Hello', 'mailpn'); ?> [user-name].</p>
            <p><?php esc_html_e('This is a test email sent from the MAILPN plugin.', 'mailpn'); ?></p>
          <?php
          $content = ob_get_contents(); 
          ob_end_clean(); 
          
          $result = do_shortcode('[mailpn-sender mailpn_type="email_coded" mailpn_user_to="' . $admin_user_id . '" mailpn_subject="' . $subject . '"]' . $content . '[/mailpn-sender]');

          if ($result) {
            echo wp_json_encode(['error_key' => '', 'error_content' => esc_html__('Test email sent successfully', 'mailpn')]);exit();
          } else {
            echo wp_json_encode(['error_key' => 'mailpn_test_email_send_error', 'error_content' => esc_html__('Failed to send test email', 'mailpn')]);exit();
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
          
          // Obtener el email del usuario
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
          
          // Limpiar errores anteriores
          $GLOBALS['mailpn_last_error'] = null;
          
          // Verificar configuración básica antes de intentar enviar
          $config_errors = [];
          
          // Verificar configuración SMTP si está habilitado
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
            // Verificar configuración del servidor para envío sin SMTP
            if (!ini_get('sendmail_path') && !function_exists('mail')) {
              $config_errors[] = 'No mail server configured (sendmail_path or mail() function)';
            }
          }
          
          // Si hay errores de configuración, mostrarlos inmediatamente
          if (!empty($config_errors)) {
            $error_message = esc_html__('Email configuration error', 'mailpn') . ': ' . implode('; ', $config_errors);
            echo wp_json_encode(['error_key' => 'mailpn_test_email_send_error', 'error_content' => $error_message]);exit();
          }
          
          // Habilitar captura de errores de PHPMailer
          $phpmailer_error = '';
          $original_error_handler = set_error_handler(function($severity, $message, $file, $line) use (&$phpmailer_error) {
            if (strpos($message, 'PHPMailer') !== false || strpos($message, 'SMTP') !== false || strpos($message, 'mail') !== false) {
              $phpmailer_error = $message;
            }
            return false; // Let PHP handle the error normally
          });
          
          // Intentar envío directo primero para obtener errores más específicos
          $test_result = false;
          $test_error = '';
          
          // Configurar PHPMailer para SMTP si está habilitado
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
              $phpmailer->Timeout = 10; // Timeout corto para pruebas
            });
          }
          
          // Intentar envío directo con wp_mail
          $test_result = wp_mail($user_email, 'Test: ' . $subject, 'This is a test email from MAILPN plugin.');
          
          // Si el envío directo falla, capturar el error
          if (!$test_result) {
            if (isset($GLOBALS['phpmailer']) && is_object($GLOBALS['phpmailer'])) {
              $test_error = $GLOBALS['phpmailer']->ErrorInfo;
            }
          }
          
          // Si el envío directo funciona, usar el shortcode completo
          if ($test_result) {
            $result = do_shortcode('[mailpn-sender mailpn_type="email_coded" mailpn_user_to="' . $user_id . '" mailpn_subject="' . $subject . '"]' . $content . '[/mailpn-sender]');
          } else {
            $result = false;
          }
          
          // Restaurar el manejador de errores original
          if ($original_error_handler) {
            set_error_handler($original_error_handler);
          }
          
          if ($result) {
            echo wp_json_encode(['error_key' => '', 'error_content' => esc_html__('Test email sent successfully to', 'mailpn') . ' ' . $user_email]);exit();
          } else {
            // Construir mensaje de error detallado
            $error_message = esc_html__('Failed to send test email', 'mailpn');
            
            // Usar el error del test directo si está disponible
            if (!empty($test_error)) {
              $error_message = esc_html__('Email sending failed', 'mailpn') . ': ' . $test_error;
            } else {
              // Verificar si hay errores capturados por el error handler
              if (!empty($phpmailer_error)) {
                $error_message = esc_html__('Email sending failed', 'mailpn') . ': ' . $phpmailer_error;
              } else {
                // Verificar información de error almacenada globalmente
                if (isset($GLOBALS['mailpn_last_error']) && is_array($GLOBALS['mailpn_last_error'])) {
                  $last_error = $GLOBALS['mailpn_last_error'];
                  if (!empty($last_error['message'])) {
                    $error_message = esc_html__('Email sending failed', 'mailpn') . ': ' . $last_error['message'];
                  }
                } else {
                  // Verificar errores de PHPMailer directamente
                  if (isset($GLOBALS['phpmailer']) && is_object($GLOBALS['phpmailer'])) {
                    $phpmailer_error_info = $GLOBALS['phpmailer']->ErrorInfo;
                    if (!empty($phpmailer_error_info)) {
                      $error_message = esc_html__('Email sending failed', 'mailpn') . ': ' . $phpmailer_error_info;
                    }
                  }
                }
              }
            }
            
            // Si no hay error específico, agregar información de diagnóstico
            if ($error_message === esc_html__('Failed to send test email', 'mailpn')) {
              $error_message = esc_html__('Email sending failed', 'mailpn') . ': Check SMTP settings or server mail configuration';
            }
            
            echo wp_json_encode(['error_key' => 'mailpn_test_email_send_error', 'error_content' => $error_message]);exit();
          }
          
          break;
      }

      echo wp_json_encode([
        'error_key' => 'mailpn_save_error', 
        'error_content' => esc_html__('Unknown AJAX type', 'mailpn'),
      ]);

      exit();
    } else {
      // No mailpn_ajax_type found
      error_log('MAILPN AJAX Request - No mailpn_ajax_type found in POST data');
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
}