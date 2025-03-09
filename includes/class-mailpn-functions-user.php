<?php
/**
 * Define the users management functionality.
 *
 * Loads and defines the users management files for this plugin so that it is ready for user creation, edition or removal.
 *  
 * @link       padresenlanube.com/
 * @since      1.0.0
 * @package    mailpn
 * @subpackage mailpn/includes
 * @author     Padres en la Nube <info@padresenlanube.com>
 */
class MAILPN_Functions_User {
  public static function is_user_admin($user_id) {
    // MAILPN_Functions_User::is_user_admin($user_id)
    return user_can($user_id, 'administrator');
  }

  public static function get_user_name($user_id) {
    // MAILPN_Functions_User::get_user_name($user_id)
    if (!empty($user_id)) {
      $user_info = get_userdata($user_id);

      if (!empty($user_info->first_name) && !empty($user_info->last_name)) {
        return $user_info->first_name . ' ' . $user_info->last_name;
      }elseif (!empty($user_info->first_name)) {
        return $user_info->first_name;
      }else if (!empty($user_info->last_name)) {
        return $user_info->last_name;
      }else if (!empty($user_info->user_nicename)){
        return $user_info->user_nicename;
      }else if (!empty($user_info->user_login)){
        return $user_info->user_login;
      }else{
        return $user_info->user_email;
      }
    }
  }

  public static function get_user_age($user_id) {
    // MAILPN_Functions_User::get_user_age($user_id)
    $timestamp = get_user_meta($user_id, 'mailpn_child_birthdate', true);

    if (!empty($timestamp) && is_string($timestamp)) {
      $timestamp = strtotime($timestamp);

      $year = gmdate('Y', $timestamp);
      $age = gmdate('Y') - $year;

      if(strtotime('+' . $age . ' years', $timestamp) > time()) {
        $age--;
      }

      return $age;
    }

    return false;
  }

  public static function insert_user($mailpn_user_login, $mailpn_user_password, $mailpn_user_email = '', $mailpn_first_name = '', $mailpn_last_name = '', $mailpn_display_name = '', $mailpn_user_nicename = '', $mailpn_user_nickname = '', $mailpn_user_description = '', $mailpn_user_role = [], $mailpn_array_usermeta = [/*['mailpn_key' => 'mailpn_value'], */]) {
    /* $this->insert_user($mailpn_user_login, $mailpn_user_password, $mailpn_user_email = '', $mailpn_first_name = '', $mailpn_last_name = '', $mailpn_display_name = '', $mailpn_user_nicename = '', $mailpn_user_nickname = '', $mailpn_user_description = '', $mailpn_user_role = [], $mailpn_array_usermeta = [['mailpn_key' => 'mailpn_value'], ],); */

    $mailpn_user_array = [
      'first_name' => $mailpn_first_name,
      'last_name' => $mailpn_last_name,
      'display_name' => $mailpn_display_name,
      'user_nicename' => $mailpn_user_nicename,
      'nickname' => $mailpn_user_nickname,
      'description' => $mailpn_user_description,
    ];

    if (!empty($mailpn_user_email)) {
      if (!email_exists($mailpn_user_email)) {
        if (username_exists($mailpn_user_login)) {
          $user_id = wp_create_user($mailpn_user_email, $mailpn_user_password, $mailpn_user_email);
        }else{
          $user_id = wp_create_user($mailpn_user_login, $mailpn_user_password, $mailpn_user_email);
        }
      }else{
        $user_id = get_user_by('email', $mailpn_user_email)->ID;
      }
    }else{
      if (!username_exists($mailpn_user_login)) {
        $user_id = wp_create_user($mailpn_user_login, $mailpn_user_password);
      }else{
        $user_id = get_user_by('login', $mailpn_user_login)->ID;
      }
    }

    if ($user_id && !is_wp_error($user_id)) {
      wp_update_user(array_merge(['ID' => $user_id], $mailpn_user_array));
    }else{
      return false;
    }

    $user = new WP_User($user_id);
    if (!empty($mailpn_user_role)) {
      foreach ($mailpn_user_role as $new_role) {
        $user->add_role($new_role);
      }
    }

    if (!empty($mailpn_array_usermeta)) {
      foreach ($mailpn_array_usermeta as $mailpn_usermeta) {
        foreach ($mailpn_usermeta as $meta_key => $meta_value) {
          if ((!empty($meta_value) || !empty(get_user_meta($user_id, $meta_key, true))) && !is_null($meta_value)) {
            update_user_meta($user_id, $meta_key, $meta_value);
          }
        }
      }
    }

    return $user_id;
  }

  public function mailpn_wp_login($login) {
    $user = get_user_by('login', $login);
    $user_id = $user->ID;
    $current_login_time = get_user_meta($user_id, 'mailpn_current_login', true);

    if(!empty($current_login_time)){
      update_user_meta($user_id, 'mailpn_last_login', $current_login_time);
      update_user_meta($user_id, 'mailpn_current_login', current_time('timestamp'));
    }else {
      update_user_meta($user_id, 'mailpn_current_login', current_time('timestamp'));
      update_user_meta($user_id, 'mailpn_last_login', current_time('timestamp'));
    }

    update_user_meta($user_id, 'userswph_newsletter_active', true);
  }
}