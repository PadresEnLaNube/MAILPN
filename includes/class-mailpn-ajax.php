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
      if (!array_key_exists('ajax_nonce', $_POST)) {
        echo wp_json_encode([
          'error_key' => 'mailpn_nonce_error',
          'error_content' => esc_html(__('Security check failed: Nonce is required.', 'mailpn')),
        ]);

        exit();
      }

      if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['ajax_nonce'])), 'mailpn-nonce')) {
        echo wp_json_encode([
          'error_key' => 'mailpn_nonce_error',
          'error_content' => esc_html(__('Security check failed: Invalid nonce.', 'mailpn')),
        ]);

        exit();
      }

      $mailpn_ajax_type = MAILPN_Forms::mailpn_sanitizer(wp_unslash($_POST['mailpn_ajax_type']));
      $ajax_keys = !empty($_POST['ajax_keys']) ? wp_unslash($_POST['ajax_keys']) : [];
      $mailpn_basecpt_id = !empty($_POST['mailpn_basecpt_id']) ? MAILPN_Forms::mailpn_sanitizer(wp_unslash($_POST['mailpn_basecpt_id'])) : 0;
      $key_value = [];

      if (!empty($ajax_keys)) {
        foreach ($ajax_keys as $key) {
          if (strpos($key['id'], '[]') !== false) {
            $clear_key = str_replace('[]', '', $key['id']);
            ${$clear_key} = $key_value[$clear_key] = [];

            if (!empty($_POST[$clear_key])) {
              foreach (wp_unslash($_POST[$clear_key]) as $multi_key => $multi_value) {
                $final_value = !empty($_POST[$clear_key][$multi_key]) ? MAILPN_Forms::mailpn_sanitizer(wp_unslash($_POST[$clear_key][$multi_key]), $key['node'], $key['type']) : '';
                ${$clear_key}[$multi_key] = $key_value[$clear_key][$multi_key] = $final_value;
              }
            }else{
              ${$clear_key} = '';
              $key_value[$clear_key][$multi_key] = '';
            }
          }else{
            $key_id = !empty($_POST[$key['id']]) ? MAILPN_Forms::mailpn_sanitizer(wp_unslash($_POST[$key['id']]), $key['node'], $key['type']) : '';
            ${$key['id']} = $key_value[$key['id']] = $key_id;
          }
        }
      }

      switch ($mailpn_ajax_type) {
        case 'mailpn_options_save':
          if (!empty($key_value)) {
            foreach ($key_value as $key => $value) {
              if (!in_array($key, ['action', 'mailpn_ajax_type'])) {
                update_option($key, $value);
              }
            }

            update_option('mailpn_options_changed', true);
            echo wp_json_encode([
              'error_key' => '', 
            ]);

            exit();
          }else{
            echo wp_json_encode([
              'error_key' => 'mailpn_options_save_error', 
            ]);

            exit();
          }
          break;
        case 'mailpn_basecpt_check':
          if (!empty($mailpn_basecpt_id)) {
            $plugin_post_type_basecpt = new MAILPN_Post_Type_BaseCPT();
            echo wp_json_encode([
              'error_key' => '', 
              'html' => $plugin_post_type_basecpt->mailpn_basecpt_check($mailpn_basecpt_id), 
            ]);

            exit();
          }else{
            echo wp_json_encode([
              'error_key' => 'mailpn_basecpt_check_error', 
              'error_content' => esc_html(__('An error occurred while checking the BaseCPT.', 'mailpn')), 
              ]);

            exit();
          }
          break;
          case 'mailpn_resend_errors':
            if (!empty($mail_id)) {
              $plugin_mailing = new MAILPN_Mailing();
              $plugin_mailing->mailpn_resend_errors($mail_id);
  
              update_post_meta($mail_id, 'mailpn_status', 'queue');
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
        
            $admin_email = get_current_user_id();
            $subject = esc_html__('Test email from MAILPN', 'mailpn');
            $content = '<p>' . esc_html__('This is a test email sent from the MAILPN plugin.', 'mailpn') . '</p>';
            
            $result = do_shortcode('[mailpn-sender mailpn_type="email_coded" mailpn_user_to="' . $admin_email . '" mailpn_subject="' . $subject . '"]' . $content . '[/mailpn-sender]');
  
            if ($result) {
              echo wp_json_encode(['error_key' => '', 'error_content' => esc_html__('Test email sent successfully', 'mailpn')]);exit();
            } else {
              echo wp_json_encode(['error_key' => 'mailpn_test_email_send_error', 'error_content' => esc_html__('Failed to send test email', 'mailpn')]);exit();
            }
            
            break;
      }

      echo wp_json_encode([
        'error_key' => 'mailpn_save_error', 
      ]);

      exit();
    }
	}
}