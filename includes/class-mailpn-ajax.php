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
      if (array_key_exists('ajax_nonce', $_POST) && !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['ajax_nonce'])), 'mailpn-nonce')) {
        echo wp_json_encode(['error_key' => 'mailpn_nonce_error', ]);exit();
      }

      $mailpn_ajax_type = MAILPN_Forms::sanitizer(wp_unslash($_POST['mailpn_ajax_type']));
      $ajax_keys = !empty($_POST['ajax_keys']) ? wp_unslash($_POST['ajax_keys']) : [];
      $mail_id = !empty($_POST['mail_id']) ? MAILPN_Forms::sanitizer(wp_unslash($_POST['mail_id'])) : 0;
      $key_value = [];

      if (!empty($ajax_keys)) {
        foreach ($ajax_keys as $key) {
          if (strpos($key['id'], '[]') !== false) {
            $clear_key = str_replace('[]', '', $key['id']);
            ${$clear_key} = $key_value[$clear_key] = [];

            if (!empty($_POST[$clear_key])) {
              foreach (wp_unslash($_POST[$clear_key]) as $multi_key => $multi_value) {
                $final_value = !empty($_POST[$clear_key][$multi_key]) ? MAILPN_Forms::sanitizer(wp_unslash($_POST[$clear_key][$multi_key]), $key['node'], $key['type']) : '';
                ${$clear_key}[$multi_key] = $key_value[$clear_key][$multi_key] = $final_value;
              }
            }else{
              ${$clear_key} = '';
              $key_value[$clear_key][$multi_key] = '';
            }
          }else{
            $key_id = !empty($_POST[$key['id']]) ? MAILPN_Forms::sanitizer(wp_unslash($_POST[$key['id']]), $key['node'], $key['type']) : '';
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
            echo wp_json_encode(['error_key' => '', ]);exit();
          }else{
            echo wp_json_encode(['error_key' => 'mailpn_options_save_error', ]);exit();
          }
          break;
        case 'mailpn_mail_view':
          if (!empty($mail_id)) {
            $plugin_post_type_mail = new MAILPN_Post_Type_Mail();
            echo wp_json_encode(['error_key' => '', 'html' => $plugin_post_type_mail->mail_view($mail_id), ]);exit();
          }else{
            echo wp_json_encode(['error_key' => 'mailpn_mail_view_error', 'error_' => esc_html(__('An error occurred while showing the baseCPT.', 'mailpn')), ]);exit();
          }
          break;
        case 'mailpn_mail_edit':
          if (!empty($mail_id)) {
            $plugin_post_type_mail = new MAILPN_Post_Type_Mail();
            echo wp_json_encode(['error_key' => '', 'html' => $plugin_post_type_mail->mail_edit($mail_id), ]);exit();
          }else{
            echo wp_json_encode(['error_key' => 'mailpn_mail_edit_error', 'error_' => esc_html(__('An error occurred while showing the baseCPT.', 'mailpn')), ]);exit();
          }
          break;
        case 'mailpn_mail_new':
            $plugin_post_type_mail = new MAILPN_Post_Type_Mail();
            echo wp_json_encode(['error_key' => '', 'html' => $plugin_post_type_mail->mail_new($mail_id), ]);exit();
          break;
        case 'mailpn_mail_check':
          if (!empty($mail_id)) {
            $plugin_post_type_mail = new MAILPN_Post_Type_Mail();
            echo wp_json_encode(['error_key' => '', 'html' => $plugin_post_type_mail->mail_check($mail_id), ]);exit();
          }else{
            echo wp_json_encode(['error_key' => 'mailpn_mail_check_error', 'error_' => esc_html(__('An error occurred while checking the baseCPT.', 'mailpn')), ]);exit();
          }
          break;
        case 'mailpn_mail_duplicate':
          if (!empty($mail_id)) {
            $plugin_post_type_post = new MAILPN_Functions_Post();
            $plugin_post_type_post->duplicate_post($mail_id, 'publish');
            
            $plugin_post_type_mail = new MAILPN_Post_Type_Mail();
            echo wp_json_encode(['error_key' => '', 'html' => $plugin_post_type_mail->mails_list(), ]);exit();
          }else{
            echo wp_json_encode(['error_key' => 'mailpn_mail_duplicate_error', 'error_' => esc_html(__('An error occurred while duplicating the baseCPT.', 'mailpn')), ]);exit();
          }
          break;
        case 'mailpn_mail_remove':
          if (!empty($mail_id)) {
            wp_delete_post($mail_id, true);
            $plugin_post_type_mail = new MAILPN_Post_Type_Mail();
            echo wp_json_encode(['error_key' => '', 'html' => $plugin_post_type_mail->mails_list(), ]);exit();
          }else{
            echo wp_json_encode(['error_key' => 'mailpn_mail_remove_error', 'error_' => esc_html(__('An error occurred while removing the baseCPT.', 'mailpn')), ]);exit();
          }
          break;
      }

      echo wp_json_encode(['error_key' => 'mailpn_save_error', ]);exit();
    }
	}
}