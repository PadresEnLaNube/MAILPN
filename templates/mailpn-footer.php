<?php
/**
 * Provide a common footer area view for the plugin
 *
 * This file is used to markup the common footer facing aspects of the plugin.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 *
 * @package    MAILPN
 * @subpackage MAILPN/common/templates
 */

  if (!defined('ABSPATH')) exit; // Exit if accessed directly

  // Ensure the global variable exists
  if (!isset($GLOBALS['mailpn_data'])) {
    $GLOBALS['mailpn_data'] = array(
      'user_id' => get_current_user_id(),
      'post_id' => is_admin() ? (!empty($GLOBALS['_REQUEST']['post']) ? $GLOBALS['_REQUEST']['post'] : 0) : get_the_ID()
    );
  }
  
  $mailpn_data = $GLOBALS['mailpn_data'];
?>

<div id="mailpn-main-message" class="mailpn-main-message mailpn-display-none-soft mailpn-z-index-top" style="display:none;" data-user-id="<?php echo esc_attr($mailpn_data['user_id']); ?>" data-post-id="<?php echo esc_attr($mailpn_data['post_id']); ?>">
  <span id="mailpn-main-message-span"></span><i class="material-icons-outlined mailpn-vertical-align-bottom mailpn-ml-20 mailpn-cursor-pointer mailpn-color-white mailpn-close-icon">close</i>

  <div id="mailpn-bar-wrapper">
  	<div id="mailpn-bar"></div>
  </div>
</div>
