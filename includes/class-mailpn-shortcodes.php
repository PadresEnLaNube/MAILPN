<?php
/**
 * Platform shortcodes.
 *
 * This class defines all shortcodes of the platform.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 * @package    MAILPN
 * @subpackage MAILPN/includes
 * @author     Padres en la Nube <info@padresenlanube.com>
 */
class MAILPN_Shortcodes {
	/**
	 * Manage the shortcodes in the platform.
	 *
	 * @since    1.0.0
	 */
  public function mailpn_call_to_action($atts) {
    // echo do_shortcode('[mailpn-call-to-action mailpn_call_to_action_icon="error_outline" mailpn_call_to_action_title="' . esc_html(__('Default title', 'mailpn')) . '" mailpn_call_to_action_content="' . esc_html(__('Default content', 'mailpn')) . '" mailpn_call_to_action_button_link="#" mailpn_call_to_action_button_text="' . esc_html(__('Button text', 'mailpn')) . '" mailpn_call_to_action_button_class="mailpn-class"]');
    $a = extract(shortcode_atts(array(
      'mailpn_call_to_action_class' => '',
      'mailpn_call_to_action_icon' => '',
      'mailpn_call_to_action_title' => '',
      'mailpn_call_to_action_content' => '',
      'mailpn_call_to_action_button_link' => '#',
      'mailpn_call_to_action_button_text' => '',
      'mailpn_call_to_action_button_class' => '',
      'mailpn_call_to_action_button_data_key' => '',
      'mailpn_call_to_action_button_data_value' => '',
      'mailpn_call_to_action_button_blank' => 0,
    ), $atts));

    ob_start();
    ?>
      <div class="mailpn-call-to-action mailpn-text-align-center mailpn-pt-30 mailpn-pb-50 <?php echo esc_attr($mailpn_call_to_action_class); ?>">
        <div class="mailpn-call-to-action-icon">
          <i class="material-icons-outlined mailpn-font-size-75 mailpn-color-main-0"><?php echo esc_html($mailpn_call_to_action_icon); ?></i>
        </div>

        <h4 class="mailpn-call-to-action-title mailpn-text-align-center mailpn-mt-10 mailpn-mb-20"><?php echo esc_html($mailpn_call_to_action_title); ?></h4>
        
        <?php if (!empty($mailpn_call_to_action_content)): ?>
          <p class="mailpn-text-align-center"><?php echo esc_html($mailpn_call_to_action_content); ?></p>
        <?php endif ?>

        <?php if (!empty($mailpn_call_to_action_button_text)): ?>
          <div class="mailpn-text-align-center mailpn-mt-20">
            <a class="mailpn-btn mailpn-btn-transparent mailpn-margin-auto <?php echo esc_attr($mailpn_call_to_action_button_class); ?>" <?php echo ($mailpn_call_to_action_button_blank) ? 'target="_blank"' : ''; ?> href="<?php echo esc_url($mailpn_call_to_action_button_link); ?>" <?php echo (!empty($mailpn_call_to_action_button_data_key) && !empty($mailpn_call_to_action_button_data_value)) ? esc_attr($mailpn_call_to_action_button_data_key) . '="' . esc_attr($mailpn_call_to_action_button_data_value) . '"' : ''; ?>><?php echo esc_html($mailpn_call_to_action_button_text); ?></a>
          </div>
        <?php endif ?>
      </div>
    <?php 
    $mailpn_return_string = ob_get_contents(); 
    ob_end_clean(); 
    return $mailpn_return_string;
  }

  public function mailpn_test($atts) {
  }
  
  /**
   * Debug cart abandonment shortcode
   *
   * @param array $atts Shortcode attributes
   * @return string Debug output
   * @since    1.0.0
   */
  public function mailpn_debug_cart_shortcode($atts) {
    if (!current_user_can('administrator')) {
      return '<p>Access denied. Administrator privileges required.</p>';
    }
    
    $debug_info = MAILPN_Debug::debug_cart_abandonment();
    
    ob_start();
    ?>
    <div style="background: #f1f1f1; padding: 20px; margin: 20px 0; border: 1px solid #ccc;">
      <h3>MailPN Cart Abandonment Debug</h3>
      <pre><?php echo esc_html(print_r($debug_info, true)); ?></pre>
    </div>
    <?php
    return ob_get_clean();
  }
  
  /**
   * Debug purchase emails shortcode
   *
   * @param array $atts Shortcode attributes
   * @return string Debug output
   * @since    1.0.0
   */
  public function mailpn_debug_purchase_shortcode($atts) {
    if (!current_user_can('administrator')) {
      return '<p>Access denied. Administrator privileges required.</p>';
    }
    
    $debug_info = MAILPN_Debug::debug_purchase_emails();
    
    ob_start();
    ?>
    <div style="background: #f1f1f1; padding: 20px; margin: 20px 0; border: 1px solid #ccc;">
      <h3>MailPN Purchase Email Debug</h3>
      <pre><?php echo esc_html(print_r($debug_info, true)); ?></pre>
    </div>
    <?php
    return ob_get_clean();
  }
  
  /**
   * Test cart abandonment processing shortcode
   *
   * @param array $atts Shortcode attributes
   * @return string Debug output
   * @since    1.0.0
   */
  public function mailpn_test_cart_processing_shortcode($atts) {
    if (!current_user_can('administrator')) {
      return '<p>Access denied. Administrator privileges required.</p>';
    }
    
    $results = MAILPN_Debug::test_cart_abandonment_processing();
    
    ob_start();
    ?>
    <div style="background: #f1f1f1; padding: 20px; margin: 20px 0; border: 1px solid #ccc;">
      <h3>MailPN Cart Abandonment Processing Test</h3>
      <pre><?php echo esc_html(print_r($results, true)); ?></pre>
    </div>
    <?php
    return ob_get_clean();
  }
}