<?php
/**
 * Debug functionality for MailPN WooCommerce integration.
 *
 * This class provides debugging tools for WooCommerce email automation.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 * @package    MAILPN
 * @subpackage MAILPN/includes
 * @author     Padres en la Nube <info@padresenlanube.com>
 */
class MAILPN_Debug {
    
    /**
     * Debug WooCommerce cart abandonment functionality
     *
     * @since    1.0.0
     */
    public static function debug_cart_abandonment() {
        if (!class_exists('WooCommerce')) {
            self::log_debug('WooCommerce not active');
            return;
        }
        
        $debug_info = [];
        $debug_info['timestamp'] = current_time('mysql');
        $debug_info['current_time'] = time();
        
        // Check abandoned cart email templates
        $abandoned_cart_templates = get_posts([
            'post_type' => 'mailpn_mail',
            'post_status' => 'publish',
            'numberposts' => -1,
            'meta_query' => [
                [
                    'key' => 'mailpn_type',
                    'value' => 'email_woocommerce_abandoned_cart',
                    'compare' => '='
                ]
            ]
        ]);
        
        $debug_info['abandoned_cart_templates_count'] = count($abandoned_cart_templates);
        $debug_info['abandoned_cart_templates'] = [];
        
        foreach ($abandoned_cart_templates as $template) {
            $delay_value = get_post_meta($template->ID, 'mailpn_woocommerce_abandoned_cart_delay_value', true);
            $delay_unit = get_post_meta($template->ID, 'mailpn_woocommerce_abandoned_cart_delay_unit', true);
            
            $debug_info['abandoned_cart_templates'][] = [
                'id' => $template->ID,
                'title' => $template->post_title,
                'delay_value' => $delay_value,
                'delay_unit' => $delay_unit,
                'delay_seconds' => self::convert_delay_to_seconds($delay_value, $delay_unit)
            ];
        }
        
        // Check users with cart timestamps
        $users_with_cart = get_users([
            'meta_key' => 'mailpn_woocommerce_cart_timestamp',
            'fields' => 'ids'
        ]);
        
        $debug_info['users_with_cart_count'] = count($users_with_cart);
        $debug_info['users_with_cart'] = [];
        
        foreach ($users_with_cart as $user_id) {
            $cart_timestamp = get_user_meta($user_id, 'mailpn_woocommerce_cart_timestamp', true);
            $cart_items = get_user_meta($user_id, 'mailpn_woocommerce_cart_items', true);
            $user = get_userdata($user_id);
            
            $debug_info['users_with_cart'][] = [
                'user_id' => $user_id,
                'user_email' => $user ? $user->user_email : 'User not found',
                'cart_timestamp' => $cart_timestamp,
                'cart_timestamp_human' => $cart_timestamp ? date('Y-m-d H:i:s', $cart_timestamp) : 'Not set',
                'time_since_cart' => $cart_timestamp ? (time() - $cart_timestamp) : 'N/A',
                'cart_items_count' => is_array($cart_items) ? count($cart_items) : 0,
                'cart_items' => $cart_items
            ];
        }
        
        // Check cron schedule
        $debug_info['cron_schedule'] = [
            'ten_minutes_scheduled' => wp_next_scheduled('mailpn_cron_ten_minutes'),
            'ten_minutes_next_run' => wp_next_scheduled('mailpn_cron_ten_minutes') ? date('Y-m-d H:i:s', wp_next_scheduled('mailpn_cron_ten_minutes')) : 'Not scheduled',
            'cron_disabled' => defined('DISABLE_WP_CRON') && DISABLE_WP_CRON
        ];
        
        // Check WooCommerce cart functionality
        if (function_exists('WC')) {
            $debug_info['woocommerce_cart'] = [
                'cart_exists' => WC()->cart ? true : false,
                'cart_items_count' => WC()->cart ? WC()->cart->get_cart_contents_count() : 0,
                'cart_total' => WC()->cart ? WC()->cart->get_cart_total() : 'N/A'
            ];
        }
        
        // Log debug info
        self::log_debug('Cart Abandonment Debug Info', $debug_info);
        
        return $debug_info;
    }
    
    /**
     * Debug purchase email functionality
     *
     * @since    1.0.0
     */
    public static function debug_purchase_emails() {
        if (!class_exists('WooCommerce')) {
            self::log_debug('WooCommerce not active');
            return;
        }
        
        $debug_info = [];
        $debug_info['timestamp'] = current_time('mysql');
        $debug_info['current_time'] = time();
        
        // Check purchase email templates
        $purchase_templates = get_posts([
            'post_type' => 'mailpn_mail',
            'post_status' => 'publish',
            'numberposts' => -1,
            'meta_query' => [
                [
                    'key' => 'mailpn_type',
                    'value' => 'email_woocommerce_purchase',
                    'compare' => '='
                ]
            ]
        ]);
        
        $debug_info['purchase_templates_count'] = count($purchase_templates);
        $debug_info['purchase_templates'] = [];
        
        foreach ($purchase_templates as $template) {
            $delay_value = get_post_meta($template->ID, 'mailpn_woocommerce_purchase_delay_value', true);
            $delay_unit = get_post_meta($template->ID, 'mailpn_woocommerce_purchase_delay_unit', true);
            
            $debug_info['purchase_templates'][] = [
                'id' => $template->ID,
                'title' => $template->post_title,
                'delay_value' => $delay_value,
                'delay_unit' => $delay_unit,
                'delay_seconds' => self::convert_delay_to_seconds($delay_value, $delay_unit)
            ];
        }
        
        // Check users with purchase timestamps
        $users_with_purchase = get_users([
            'meta_key' => 'mailpn_woocommerce_purchase_timestamp',
            'fields' => 'ids'
        ]);
        
        $debug_info['users_with_purchase_count'] = count($users_with_purchase);
        $debug_info['users_with_purchase'] = [];
        
        foreach ($users_with_purchase as $user_id) {
            $purchase_timestamp = get_user_meta($user_id, 'mailpn_woocommerce_purchase_timestamp', true);
            $order_id = get_user_meta($user_id, 'mailpn_woocommerce_purchase_order_id', true);
            $user = get_userdata($user_id);
            $order = $order_id ? wc_get_order($order_id) : null;
            
            $debug_info['users_with_purchase'][] = [
                'user_id' => $user_id,
                'user_email' => $user ? $user->user_email : 'User not found',
                'purchase_timestamp' => $purchase_timestamp,
                'purchase_timestamp_human' => $purchase_timestamp ? date('Y-m-d H:i:s', $purchase_timestamp) : 'Not set',
                'time_since_purchase' => $purchase_timestamp ? (time() - $purchase_timestamp) : 'N/A',
                'order_id' => $order_id,
                'order_exists' => $order ? true : false,
                'order_status' => $order ? $order->get_status() : 'N/A'
            ];
        }
        
        self::log_debug('Purchase Email Debug Info', $debug_info);
        
        return $debug_info;
    }
    
    /**
     * Test cart abandonment processing manually
     *
     * @since    1.0.0
     */
    public static function test_cart_abandonment_processing() {
        if (!class_exists('WooCommerce')) {
            return ['error' => 'WooCommerce not active'];
        }
        
        $cron_instance = new MAILPN_Cron();
        $current_time = time();
        $mailing_plugin = new MAILPN_Mailing();
        
        $results = [];
        
        // Get abandoned cart templates
        $abandoned_cart_templates = get_posts([
            'post_type' => 'mailpn_mail',
            'post_status' => 'publish',
            'numberposts' => -1,
            'meta_query' => [
                [
                    'key' => 'mailpn_type',
                    'value' => 'email_woocommerce_abandoned_cart',
                    'compare' => '='
                ]
            ]
        ]);
        
        foreach ($abandoned_cart_templates as $template) {
            $delay_value = get_post_meta($template->ID, 'mailpn_woocommerce_abandoned_cart_delay_value', true);
            $delay_unit = get_post_meta($template->ID, 'mailpn_woocommerce_abandoned_cart_delay_unit', true);
            
            if (empty($delay_value) || empty($delay_unit)) {
                continue;
            }
            
            $delay_seconds = self::convert_delay_to_seconds($delay_value, $delay_unit);
            
            if ($delay_seconds === false) {
                continue;
            }
            
            // Get users with cart timestamps
            $users_with_cart = get_users([
                'meta_key' => 'mailpn_woocommerce_cart_timestamp',
                'fields' => 'ids'
            ]);
            
            $processed_users = [];
            
            foreach ($users_with_cart as $user_id) {
                // Check if user still exists
                if (!get_userdata($user_id)) {
                    MAILPN_WooCommerce::remove_cart_abandonment_meta($user_id);
                    continue;
                }
                
                // Check if user still has cart items (use saved cart data)
                $saved_cart_items = get_user_meta($user_id, 'mailpn_woocommerce_cart_items', true);
                
                if (empty($saved_cart_items) || !is_array($saved_cart_items)) {
                    MAILPN_WooCommerce::remove_cart_abandonment_meta($user_id);
                    continue;
                }
                
                $cart_timestamp = MAILPN_WooCommerce::get_cart_abandonment_timestamp($user_id);
                
                if (!$cart_timestamp) {
                    continue;
                }
                
                $time_since_cart = $current_time - $cart_timestamp;
                $should_send = ($cart_timestamp + $delay_seconds) <= $current_time;
                
                $processed_users[] = [
                    'user_id' => $user_id,
                    'cart_timestamp' => $cart_timestamp,
                    'time_since_cart' => $time_since_cart,
                    'delay_seconds' => $delay_seconds,
                    'should_send' => $should_send,
                    'user_exists' => get_userdata($user_id) ? true : false,
                    'has_cart_items' => !empty($saved_cart_items) && is_array($saved_cart_items)
                ];
                
                if ($should_send) {
                    // Add to queue
                    $mailing_plugin->mailpn_queue_add($template->ID, $user_id);
                    
                    // Remove meta to prevent duplicate sending
                    MAILPN_WooCommerce::remove_cart_abandonment_meta($user_id);
                }
            }
            
            $results[] = [
                'template_id' => $template->ID,
                'template_title' => $template->post_title,
                'delay_value' => $delay_value,
                'delay_unit' => $delay_unit,
                'delay_seconds' => $delay_seconds,
                'processed_users' => $processed_users
            ];
        }
        
        self::log_debug('Manual Cart Abandonment Processing Test', $results);
        
        return $results;
    }
    
    /**
     * Convert delay value and unit to seconds
     *
     * @param int $value Delay value
     * @param string $unit Delay unit (minutes, hours, days)
     * @return int|false Delay in seconds or false if invalid
     * @since       1.0.0
     */
    private static function convert_delay_to_seconds($value, $unit) {
        if (!is_numeric($value) || $value <= 0) {
            return false;
        }
        
        switch ($unit) {
            case 'minutes':
                return $value * 60;
            case 'hours':
                return $value * 3600;
            case 'days':
                return $value * 86400;
            default:
                return false;
        }
    }
    
    /**
     * Log debug information
     *
     * @param string $message Debug message
     * @param mixed $data Debug data
     * @since    1.0.0
     */
    private static function log_debug($message, $data = null) {
        $log_message = '[MAILPN DEBUG] ' . $message;
        
        if ($data !== null) {
            $log_message .= ': ' . print_r($data, true);
        }
        
        error_log($log_message);
        
        // Also save to a custom debug file
        $debug_file = WP_CONTENT_DIR . '/mailpn-debug.log';
        $timestamp = current_time('Y-m-d H:i:s');
        $log_entry = "[{$timestamp}] {$log_message}\n";
        
        file_put_contents($debug_file, $log_entry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Get debug log content
     *
     * @return string Debug log content
     * @since    1.0.0
     */
    public static function get_debug_log() {
        $debug_file = WP_CONTENT_DIR . '/mailpn-debug.log';
        
        if (!file_exists($debug_file)) {
            return 'Debug log file does not exist.';
        }
        
        $content = file_get_contents($debug_file);
        
        if ($content === false) {
            return 'Could not read debug log file.';
        }
        
        return $content;
    }
    
    /**
     * Clear debug log
     *
     * @since    1.0.0
     */
    public static function clear_debug_log() {
        $debug_file = WP_CONTENT_DIR . '/mailpn-debug.log';
        
        if (file_exists($debug_file)) {
            unlink($debug_file);
        }
    }
}
