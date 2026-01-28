<?php
/**
 * MAILPN Custom Selector.
 *
 * A custom select plugin with multiple selection and search capabilities.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 * @package    MAILPN
 * @subpackage MAILPN/includes
 * @author     Padres en la Nube <info@padresenlanube.com>
 */

if (!defined('ABSPATH')) {
    exit;
}

class MAILPN_Selector {
    private static $instance = null;

    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    public function enqueue_scripts() {
        wp_enqueue_style(
            'mailpn-selector',
            plugin_dir_url(__FILE__) . 'assets/css/mailpn-selector.css',
            array(),
            '1.0.0'
        );

        wp_enqueue_script(
            'mailpn-selector',
            plugin_dir_url(__FILE__) . 'assets/js/mailpn-selector.js',
            array('jquery'),
            '1.0.0',
            true
        );

        wp_localize_script('mailpn-selector', 'MAILPN_Selector', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('mailpn-selector-nonce')
        ));
    }
}