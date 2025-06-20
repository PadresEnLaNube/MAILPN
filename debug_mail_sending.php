<?php
/**
 * Debug script for MAILPN email sending issues
 * 
 * This script will help diagnose why emails are not being sent
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    require_once('../../../wp-load.php');
}

// Check if user is admin
if (!current_user_can('manage_options')) {
    wp_die('Access denied');
}

echo "<h1>MAILPN Email Debug Report</h1>";
echo "<p>Generated on: " . date('Y-m-d H:i:s') . "</p>";

// 1. Check WordPress mail function
echo "<h2>1. WordPress Mail Function</h2>";
if (function_exists('wp_mail')) {
    echo "✅ wp_mail() function exists<br>";
} else {
    echo "❌ wp_mail() function does not exist<br>";
}

// 2. Check PHPMailer
echo "<h2>2. PHPMailer Status</h2>";
if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    echo "✅ PHPMailer class exists<br>";
} else {
    echo "❌ PHPMailer class not found<br>";
}

// 3. Check SMTP settings
echo "<h2>3. SMTP Settings</h2>";
$smtp_enabled = get_option('mailpn_smtp_enabled');
$smtp_host = get_option('mailpn_smtp_host');
$smtp_port = get_option('mailpn_smtp_port');
$smtp_secure = get_option('mailpn_smtp_secure');
$smtp_auth = get_option('mailpn_smtp_auth');
$smtp_username = get_option('mailpn_smtp_username');
$smtp_password = get_option('mailpn_smtp_password');

echo "SMTP Enabled: " . ($smtp_enabled === 'on' ? 'Yes' : 'No') . "<br>";
echo "SMTP Host: " . ($smtp_host ?: 'Not set') . "<br>";
echo "SMTP Port: " . ($smtp_port ?: 'Not set') . "<br>";
echo "SMTP Secure: " . ($smtp_secure ?: 'Not set') . "<br>";
echo "SMTP Auth: " . ($smtp_auth === 'on' ? 'Yes' : 'No') . "<br>";
echo "SMTP Username: " . ($smtp_username ? 'Set' : 'Not set') . "<br>";
echo "SMTP Password: " . ($smtp_password ? 'Set' : 'Not set') . "<br>";

// 4. Check mail queue
echo "<h2>4. Mail Queue Status</h2>";
$mail_queue = get_option('mailpn_queue');
$mail_queue_paused = get_option('mailpn_queue_paused');
$mails_sent_today = get_option('mailpn_mails_sent_today', 0);
$mails_sent_every_ten_minutes = get_option('mailpn_sent_every_ten_minutes', 5);
$mails_sent_every_day = get_option('mailpn_sent_every_day', 500);

echo "Queue: " . (is_array($mail_queue) ? count($mail_queue) . ' items' : 'Empty') . "<br>";
echo "Queue Paused: " . ($mail_queue_paused ? 'Yes (until ' . date('Y-m-d H:i:s', $mail_queue_paused) . ')' : 'No') . "<br>";
echo "Mails Sent Today: " . $mails_sent_today . "<br>";
echo "Mails Sent Every 10 Minutes: " . $mails_sent_every_ten_minutes . "<br>";
echo "Mails Sent Every Day: " . $mails_sent_every_day . "<br>";

// 5. Check cron status
echo "<h2>5. Cron Status</h2>";
$next_cron_daily = wp_next_scheduled('mailpn_cron_daily');
$next_cron_ten_minutes = wp_next_scheduled('mailpn_cron_ten_minutes');

echo "Next Daily Cron: " . ($next_cron_daily ? date('Y-m-d H:i:s', $next_cron_daily) : 'Not scheduled') . "<br>";
echo "Next 10-Minute Cron: " . ($next_cron_ten_minutes ? date('Y-m-d H:i:s', $next_cron_ten_minutes) : 'Not scheduled') . "<br>";

// 6. Check recent mail records
echo "<h2>6. Recent Mail Records</h2>";
$recent_records = get_posts([
    'post_type' => 'mailpn_rec',
    'post_status' => 'publish',
    'numberposts' => 5,
    'orderby' => 'ID',
    'order' => 'DESC'
]);

if (!empty($recent_records)) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Subject</th><th>To</th><th>Type</th><th>Result</th><th>Date</th></tr>";
    foreach ($recent_records as $record) {
        $subject = get_post_meta($record->ID, 'mailpn_rec_subject', true);
        $to_email = get_post_meta($record->ID, 'mailpn_rec_to_email', true);
        $type = get_post_meta($record->ID, 'mailpn_rec_type', true);
        $result = get_post_meta($record->ID, 'mailpn_rec_mail_result', true);
        $error = get_post_meta($record->ID, 'mailpn_rec_error', true);
        
        echo "<tr>";
        echo "<td>" . $record->ID . "</td>";
        echo "<td>" . esc_html($subject) . "</td>";
        echo "<td>" . esc_html($to_email) . "</td>";
        echo "<td>" . esc_html($type) . "</td>";
        echo "<td>" . ($result ? '✅ Success' : '❌ Failed') . "</td>";
        echo "<td>" . $record->post_date . "</td>";
        echo "</tr>";
        
        if (!$result && $error) {
            echo "<tr><td colspan='6' style='background: #ffe6e6; padding: 10px;'>";
            echo "<strong>Error:</strong> " . esc_html($error);
            echo "</td></tr>";
        }
    }
    echo "</table>";
} else {
    echo "No mail records found<br>";
}

// 7. Test email sending
echo "<h2>7. Test Email Sending</h2>";
if (isset($_POST['test_email'])) {
    $test_email = sanitize_email($_POST['test_email']);
    if ($test_email) {
        echo "Sending test email to: " . $test_email . "<br>";
        
        $mailing = new MAILPN_Mailing();
        $result = do_shortcode('[mailpn-sender mailpn_user_to="' . $test_email . '" mailpn_subject="Test Email from MAILPN" mailpn_type="test"]<p>This is a test email from MAILPN plugin.</p>[/mailpn-sender]');
        
        if ($result) {
            echo "✅ Test email sent successfully<br>";
        } else {
            echo "❌ Test email failed to send<br>";
        }
    }
}

echo "<form method='post'>";
echo "<input type='email' name='test_email' placeholder='Enter email address' required>";
echo "<input type='submit' value='Send Test Email'>";
echo "</form>";

// 8. Check server mail configuration
echo "<h2>8. Server Mail Configuration</h2>";
echo "PHP Version: " . PHP_VERSION . "<br>";
echo "WordPress Version: " . get_bloginfo('version') . "<br>";
echo "Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "<br>";
echo "Sendmail Path: " . (ini_get('sendmail_path') ?: 'Not configured') . "<br>";
echo "SMTP Host: " . (ini_get('SMTP') ?: 'Not configured') . "<br>";
echo "SMTP Port: " . (ini_get('smtp_port') ?: 'Not configured') . "<br>";

// 9. Check plugin settings
echo "<h2>9. Plugin Settings</h2>";
$legal_name = get_option('mailpn_legal_name');
$legal_address = get_option('mailpn_legal_address');
$click_tracking = get_option('mailpn_click_tracking');
$errors_to_admin = get_option('mailpn_errors_to_admin');

echo "Legal Name: " . ($legal_name ?: 'Not set') . "<br>";
echo "Legal Address: " . ($legal_address ?: 'Not set') . "<br>";
echo "Click Tracking: " . ($click_tracking === 'on' ? 'Enabled' : 'Disabled') . "<br>";
echo "Errors to Admin: " . ($errors_to_admin === 'on' ? 'Enabled' : 'Disabled') . "<br>";

// 10. Check for errors in error log
echo "<h2>10. Recent Error Log</h2>";
$error_log_file = WP_CONTENT_DIR . '/debug.log';
if (file_exists($error_log_file)) {
    $log_content = file_get_contents($error_log_file);
    $lines = explode("\n", $log_content);
    $mailpn_errors = [];
    
    foreach ($lines as $line) {
        if (strpos($line, 'mailpn') !== false || strpos($line, 'MAILPN') !== false) {
            $mailpn_errors[] = $line;
        }
    }
    
    if (!empty($mailpn_errors)) {
        echo "<div style='background: #f5f5f5; padding: 10px; max-height: 300px; overflow-y: auto;'>";
        foreach (array_slice($mailpn_errors, -10) as $error) {
            echo esc_html($error) . "<br>";
        }
        echo "</div>";
    } else {
        echo "No MAILPN-related errors found in debug log<br>";
    }
} else {
    echo "Debug log file not found<br>";
}

echo "<hr>";
echo "<p><strong>Note:</strong> If emails are still not sending, check your server's mail configuration and ensure SMTP settings are correct.</p>";
?> 