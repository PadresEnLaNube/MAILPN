<?php
/**
 * Load the plugin no private Ajax functions.
 *
 * Load the plugin no private Ajax functions to be executed in background.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 * @package    MAILPN
 * @subpackage MAILPN/includes
 * @author     Padres en la Nube <info@padresenlanube.com>
 */
class MAILPN_Ajax_Nopriv {
	/**
	 * Load the plugin templates.
	 *
	 * @since    1.0.0
	 */
	public function mailpn_ajax_nopriv_server() {
    if (array_key_exists('mailpn_ajax_nopriv_type', $_POST)) {
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

  		$mailpn_ajax_nopriv_type = MAILPN_Forms::mailpn_sanitizer(wp_unslash($_POST['mailpn_ajax_nopriv_type']));
      $ajax_keys = !empty($_POST['ajax_keys']) ? wp_unslash($_POST['ajax_keys']) : [];
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

      switch ($mailpn_ajax_nopriv_type) {
        case 'mailpn_form_save':
          $mailpn_form_type = !empty($_POST['mailpn_form_type']) ? MAILPN_Forms::mailpn_sanitizer(wp_unslash($_POST['mailpn_form_type'])) : '';

          if (!empty($key_value) && !empty($mailpn_form_type)) {
            $mailpn_form_id = !empty($_POST['mailpn_form_id']) ? MAILPN_Forms::mailpn_sanitizer(wp_unslash($_POST['mailpn_form_id'])) : 0;
            $mailpn_form_subtype = !empty($_POST['mailpn_form_subtype']) ? MAILPN_Forms::mailpn_sanitizer(wp_unslash($_POST['mailpn_form_subtype'])) : '';
            $user_id = !empty($_POST['mailpn_form_user_id']) ? MAILPN_Forms::mailpn_sanitizer(wp_unslash($_POST['mailpn_form_user_id'])) : 0;
            $post_id = !empty($_POST['mailpn_form_post_id']) ? MAILPN_Forms::mailpn_sanitizer(wp_unslash($_POST['mailpn_form_post_id'])) : 0;
            $post_type = !empty($_POST['mailpn_form_post_type']) ? MAILPN_Forms::mailpn_sanitizer(wp_unslash($_POST['mailpn_form_post_type'])) : '';

            if (($mailpn_form_type == 'user' && empty($user_id) && !in_array($mailpn_form_subtype, ['user_alt_new'])) || ($mailpn_form_type == 'post' && (empty($post_id) && !(!empty($mailpn_form_subtype) && in_array($mailpn_form_subtype, ['post_new', 'post_edit'])))) || ($mailpn_form_type == 'option' && !is_user_logged_in())) {
              session_start();

              $_SESSION['mailpn_form'] = [];
              $_SESSION['mailpn_form'][$mailpn_form_id] = [];
              $_SESSION['mailpn_form'][$mailpn_form_id]['form_type'] = $mailpn_form_type;
              $_SESSION['mailpn_form'][$mailpn_form_id]['values'] = $key_value;

              if (!empty($post_id)) {
                $_SESSION['mailpn_form'][$mailpn_form_id]['post_id'] = $post_id;
              }

              echo wp_json_encode(['error_key' => 'mailpn_form_save_error_unlogged', ]);exit();
            }else{
              switch ($mailpn_form_type) {
                case 'user':
                  if (!in_array($mailpn_form_subtype, ['user_alt_new'])) {
                    if (empty($user_id)) {
                      if (MAILPN_Functions_User::is_user_admin(get_current_user_id())) {
                        $user_login = !empty($_POST['user_login']) ? MAILPN_Forms::mailpn_sanitizer(wp_unslash($_POST['user_login'])) : 0;
                        $user_password = !empty($_POST['user_password']) ? MAILPN_Forms::mailpn_sanitizer(wp_unslash($_POST['user_password'])) : 0;
                        $user_email = !empty($_POST['user_email']) ? MAILPN_Forms::mailpn_sanitizer(wp_unslash($_POST['user_email'])) : 0;

                        $user_id = MAILPN_Functions_User::insert_user($user_login, $user_password, $user_email);
                      }
                    }

                    if (!empty($user_id)) {
                      foreach ($key_value as $key => $value) {
                        update_user_meta($user_id, $key, $value);
                      }
                    }
                  }

                  do_action('mailpn_form_save', $user_id, $key_value, $mailpn_form_type, $mailpn_form_subtype);
                  break;
                case 'post':
                  if (empty($mailpn_form_subtype) || in_array($mailpn_form_subtype, ['post_new', 'post_edit'])) {
                    if (empty($post_id)) {
                      if (MAILPN_Functions_User::is_user_admin(get_current_user_id())) {
                        $post_functions = new MAILPN_Functions_Post();
                        $title = !empty($_POST[$post_type . '_title']) ? MAILPN_Forms::mailpn_sanitizer(wp_unslash($_POST[$post_type . '_title'])) : '';
                        $description = !empty($_POST[$post_type . '_description']) ? MAILPN_Forms::mailpn_sanitizer(wp_unslash($_POST[$post_type . '_description'])) : '';
                        
                        $post_id = $post_functions->mailpn_insert_post($title, $description, '', sanitize_title($title), $post_type, 'publish', get_current_user_id());
                      }
                    }

                    if (!empty($post_id)) {
                      foreach ($key_value as $key => $value) {
                        if ($key == $post_type . '_title') {
                          wp_update_post([
                            'ID' => $post_id,
                            'post_title' => esc_html($value),
                          ]);
                        }

                        if ($key == $post_type . '_description') {
                          wp_update_post([
                            'ID' => $post_id,
                            'post_content' => esc_html($value),
                          ]);
                        }

                        update_post_meta($post_id, $key, $value);
                      }
                    }
                  }

                  do_action('mailpn_form_save', $post_id, $key_value, $mailpn_form_type, $mailpn_form_subtype);
                  break;
                case 'option':
                  if (MAILPN_Functions_User::is_user_admin(get_current_user_id())) {
                    foreach ($key_value as $key => $value) {
                      update_option($key, $value);
                    }
                  }

                  do_action('mailpn_form_save', 0, $key_value, $mailpn_form_type, $mailpn_form_subtype);
                  break;
              }

              $popup_close = in_array($mailpn_form_subtype, ['post_new', 'post_edit', 'user_alt_new']) ? true : '';
              $update_list = in_array($mailpn_form_subtype, ['post_new', 'post_edit', 'user_alt_new']) ? true : '';

              if ($update_list && !empty($post_type)) {
                switch ($post_type) {
                  case 'mailpn_basecpt':
                    $plugin_post_type_basecpt = new MAILPN_Post_Type_BaseCPT();
                    $update_html = $plugin_post_type_basecpt->mailpn_basecpt_list();
                    break;
                }
              }else{
                $update_html = '';
              }

              echo wp_json_encode(['error_key' => '', 'popup_close' => $popup_close, 'update_list' => $update_list, 'update_html' => $update_html]);exit();
            }
          }else{
            echo wp_json_encode(['error_key' => 'mailpn_form_save_error', ]);exit();
          }
          break;
      }

      echo wp_json_encode(['error_key' => '', ]);exit();
  	}
  }
}