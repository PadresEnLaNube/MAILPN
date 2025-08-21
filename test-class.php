<?php
// Test file to check if the MAILPN_Functions_User class can be loaded

// Define the plugin directory constant
define('MAILPN_DIR', __DIR__ . '/');

// Include the class file
require_once MAILPN_DIR . 'includes/class-mailpn-functions-user.php';

// Check if the class exists
if (class_exists('MAILPN_Functions_User')) {
    echo "Class MAILPN_Functions_User exists\n";
    
    // Try to instantiate the class
    try {
        $user_class = new MAILPN_Functions_User();
        echo "Class instantiated successfully\n";
        
        // Check if the method exists
        if (method_exists($user_class, 'mailpn_newsletter_activation_hook')) {
            echo "Method mailpn_newsletter_activation_hook exists\n";
        } else {
            echo "Method mailpn_newsletter_activation_hook does NOT exist\n";
        }
        
        if (method_exists($user_class, 'mailpn_wp_login')) {
            echo "Method mailpn_wp_login exists\n";
        } else {
            echo "Method mailpn_wp_login does NOT exist\n";
        }
        
    } catch (Exception $e) {
        echo "Error instantiating class: " . $e->getMessage() . "\n";
    }
} else {
    echo "Class MAILPN_Functions_User does NOT exist\n";
}

// List all methods in the class
if (class_exists('MAILPN_Functions_User')) {
    $methods = get_class_methods('MAILPN_Functions_User');
    echo "Available methods:\n";
    foreach ($methods as $method) {
        echo "- $method\n";
    }
}
?>
