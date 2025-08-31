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
          
          // Obtener el contenido del post
          $post = get_post($post_id);
          if (!$post) {
            echo wp_json_encode(['error_key' => 'mailpn_test_email_send_error', 'error_content' => esc_html__('Post not found', 'mailpn')]);
            exit();
          }
          
          $subject = get_the_title($post_id);
          $content = $post->post_content;
          
          // Enviar el correo de prueba usando el shortcode mailpn-sender
          $result = do_shortcode('[mailpn-sender mailpn_type="email_coded" mailpn_user_to="' . $user_id . '" mailpn_subject="' . $subject . '"]' . $content . '[/mailpn-sender]');

          if ($result) {
            echo wp_json_encode(['error_key' => '', 'error_content' => esc_html__('Test email sent successfully to', 'mailpn') . ' ' . $user_email]);exit();
          } else {
            echo wp_json_encode(['error_key' => 'mailpn_test_email_send_error', 'error_content' => esc_html__('Failed to send test email', 'mailpn')]);exit();
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
}