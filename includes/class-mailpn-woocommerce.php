<?php
/**
 * WooCommerce integration for MailPN.
 *
 * This class handles WooCommerce integration for automated email sending.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 * @package    MAILPN
 * @subpackage MAILPN/includes
 * @author     Padres en la Nube <info@padresenlanube.com>
 */
class MAILPN_WooCommerce {
    
    /**
     * Initialize WooCommerce integration
     *
     * @since    1.0.0
     */
    public function __construct() {
        if (class_exists('WooCommerce')) {
            $this->init_hooks();
        }
    }
    
    /**
     * Initialize WooCommerce hooks
     *
     * @since    1.0.0
     */
    private function init_hooks() {
        // Hook for completed orders
        add_action('woocommerce_order_status_completed', [$this, 'handle_order_completed'], 10, 1);
        add_action('woocommerce_order_status_processing', [$this, 'handle_order_completed'], 10, 1);
        
        // Hook for cart updates
        add_action('woocommerce_add_to_cart', [$this, 'handle_cart_updated'], 10, 6);
        add_action('woocommerce_cart_item_removed', [$this, 'handle_cart_updated'], 10, 2);
        add_action('woocommerce_cart_item_restored', [$this, 'handle_cart_updated'], 10, 2);
        add_action('woocommerce_cart_item_set_quantity', [$this, 'handle_cart_updated'], 10, 3);
        
        // Hook for cart abandonment detection
        add_action('wp_ajax_mailpn_update_cart_timestamp', [$this, 'update_cart_timestamp']);
        add_action('wp_ajax_nopriv_mailpn_update_cart_timestamp', [$this, 'update_cart_timestamp']);
    }
    
    /**
     * Handle completed order
     *
     * @param int $order_id Order ID
     * @since    1.0.0
     */
    public function handle_order_completed($order_id) {
        $order = wc_get_order($order_id);
        
        if (!$order) {
            return;
        }
        
        $user_id = $order->get_user_id();
        
        if (!$user_id) {
            return;
        }
        
        // Set usermeta for completed purchase
        update_user_meta($user_id, 'mailpn_woocommerce_purchase_timestamp', time());
        update_user_meta($user_id, 'mailpn_woocommerce_purchase_order_id', $order_id);
        
        // Remove any existing abandoned cart meta since purchase was completed
        delete_user_meta($user_id, 'mailpn_woocommerce_cart_timestamp');
        delete_user_meta($user_id, 'mailpn_woocommerce_cart_items');
        
        // Reset purchase email status for this user
        $this->reset_purchase_email_status($user_id);
    }
    
    /**
     * Handle cart updates
     *
     * @param mixed $cart_item_key Cart item key
     * @param int $product_id Product ID
     * @param int $quantity Quantity
     * @param int $variation_id Variation ID
     * @param array $variation Variation data
     * @param array $cart_item_data Cart item data
     * @since    1.0.0
     */
    public function handle_cart_updated($cart_item_key = null, $product_id = null, $quantity = null, $variation_id = null, $variation = null, $cart_item_data = null) {
        if (!is_user_logged_in()) {
            return;
        }
        
        $user_id = get_current_user_id();
        
        // Get current cart items
        $cart_items = WC()->cart->get_cart();
        
        if (empty($cart_items)) {
            // Cart is empty, remove cart meta
            delete_user_meta($user_id, 'mailpn_woocommerce_cart_timestamp');
            delete_user_meta($user_id, 'mailpn_woocommerce_cart_items');
            return;
        }
        
        // Update cart timestamp and items
        update_user_meta($user_id, 'mailpn_woocommerce_cart_timestamp', time());
        update_user_meta($user_id, 'mailpn_woocommerce_cart_items', $cart_items);
        
        // Reset abandoned cart email status for this user
        $this->reset_abandoned_cart_email_status($user_id);
    }
    
    /**
     * Update cart timestamp via AJAX
     *
     * @since    1.0.0
     */
    public function update_cart_timestamp() {
        if (!is_user_logged_in()) {
            wp_die();
        }
        
        $user_id = get_current_user_id();
        
        // Get current cart items
        $cart_items = WC()->cart->get_cart();
        
        if (!empty($cart_items)) {
            update_user_meta($user_id, 'mailpn_woocommerce_cart_timestamp', time());
            update_user_meta($user_id, 'mailpn_woocommerce_cart_items', $cart_items);
        }
        
        wp_die();
    }
    
    /**
     * Check if user has valid cart items
     *
     * @param int $user_id User ID
     * @return bool
     * @since    1.0.0
     */
    public static function user_has_cart_items($user_id) {
        $cart_items = get_user_meta($user_id, 'mailpn_woocommerce_cart_items', true);
        
        if (empty($cart_items) || !is_array($cart_items)) {
            return false;
        }
        
        // Check if cart items still exist and are valid
        foreach ($cart_items as $cart_item) {
            $product_id = $cart_item['product_id'];
            $product = wc_get_product($product_id);
            
            if (!$product || !$product->is_purchasable()) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Check if order still exists and is valid
     *
     * @param int $user_id User ID
     * @return bool
     * @since    1.0.0
     */
    public static function user_order_still_exists($user_id) {
        $order_id = get_user_meta($user_id, 'mailpn_woocommerce_purchase_order_id', true);
        
        if (empty($order_id)) {
            return false;
        }
        
        $order = wc_get_order($order_id);
        
        if (!$order) {
            return false;
        }
        
        // Check if order is still in a valid status
        $valid_statuses = ['completed', 'processing', 'on-hold'];
        
        return in_array($order->get_status(), $valid_statuses);
    }
    
    /**
     * Get cart abandonment timestamp for user
     *
     * @param int $user_id User ID
     * @return int|false
     * @since    1.0.0
     */
    public static function get_cart_abandonment_timestamp($user_id) {
        return get_user_meta($user_id, 'mailpn_woocommerce_cart_timestamp', true);
    }
    
    /**
     * Get purchase timestamp for user
     *
     * @param int $user_id User ID
     * @return int|false
     * @since    1.0.0
     */
    public static function get_purchase_timestamp($user_id) {
        return get_user_meta($user_id, 'mailpn_woocommerce_purchase_timestamp', true);
    }
    
    /**
     * Remove cart abandonment meta for user
     *
     * @param int $user_id User ID
     * @since    1.0.0
     */
    public static function remove_cart_abandonment_meta($user_id) {
        delete_user_meta($user_id, 'mailpn_woocommerce_cart_timestamp');
        delete_user_meta($user_id, 'mailpn_woocommerce_cart_items');
    }
    
    /**
     * Reset abandoned cart email status for user
     *
     * @param int $user_id User ID
     * @since    1.0.0
     */
    private function reset_abandoned_cart_email_status($user_id) {
        // Get all abandoned cart email templates
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
            // Reset the email status for this user and template
            $this->reset_email_status_for_user($template->ID, $user_id);
        }
    }
    
    /**
     * Reset purchase email status for user
     *
     * @param int $user_id User ID
     * @since    1.0.0
     */
    private function reset_purchase_email_status($user_id) {
        // Get all purchase email templates
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
        
        foreach ($purchase_templates as $template) {
            // Reset the email status for this user and template
            $this->reset_email_status_for_user($template->ID, $user_id);
        }
    }
    
    /**
     * Reset email status for specific user and template
     *
     * @param int $template_id Template ID
     * @param int $user_id User ID
     * @since    1.0.0
     */
    private function reset_email_status_for_user($template_id, $user_id) {
        // Get the list of users who have already received this email
        $sent_to_users = get_post_meta($template_id, 'mailpn_sent_to_users', true);
        
        if (empty($sent_to_users) || !is_array($sent_to_users)) {
            $sent_to_users = [];
        }
        
        // Remove this user from the list if they were in it
        if (in_array($user_id, $sent_to_users)) {
            $sent_to_users = array_diff($sent_to_users, [$user_id]);
            update_post_meta($template_id, 'mailpn_sent_to_users', $sent_to_users);
            
            // Reset the global email status to allow sending again
            update_post_meta($template_id, 'mailpn_status', '');
            
            // Also reset the timestamp if it exists
            delete_post_meta($template_id, 'mailpn_timestamp_sent');
        }
    }
    
    /**
     * Remove purchase meta for user
     *
     * @param int $user_id User ID
     * @since    1.0.0
     */
    public static function remove_purchase_meta($user_id) {
        delete_user_meta($user_id, 'mailpn_woocommerce_purchase_timestamp');
        delete_user_meta($user_id, 'mailpn_woocommerce_purchase_order_id');
    }
}
