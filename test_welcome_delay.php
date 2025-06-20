<?php
/**
 * Test Welcome Delay System
 * 
 * This script tests the welcome email delay functionality
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    require_once('../../../wp-load.php');
}

// Check if user is admin
if (!current_user_can('manage_options')) {
    wp_die('Access denied');
}

echo "<h1>MAILPN Welcome Delay System Test</h1>";
echo "<p>Generated on: " . date('Y-m-d H:i:s') . "</p>";

// Test 1: Check welcome email templates
echo "<h2>1. Welcome Email Templates</h2>";
$welcome_emails = get_posts([
    'fields' => 'ids',
    'numberposts' => -1,
    'post_type' => 'mailpn_mail',
    'post_status' => 'publish',
    'meta_query' => [
        [
            'key' => 'mailpn_type',
            'value' => 'email_welcome',
            'compare' => '='
        ]
    ]
]);

if (!empty($welcome_emails)) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Email ID</th><th>Title</th><th>Delay Enabled</th><th>Delay Value</th><th>Delay Unit</th><th>Distribution</th></tr>";
    
    foreach ($welcome_emails as $email_id) {
        $email_post = get_post($email_id);
        $delay_enabled = get_post_meta($email_id, 'mailpn_welcome_delay_enabled', true);
        $delay_value = get_post_meta($email_id, 'mailpn_welcome_delay_value', true);
        $delay_unit = get_post_meta($email_id, 'mailpn_welcome_delay_unit', true);
        $distribution = get_post_meta($email_id, 'mailpn_distribution', true);
        
        echo "<tr>";
        echo "<td>$email_id</td>";
        echo "<td>" . $email_post->post_title . "</td>";
        echo "<td>" . ($delay_enabled === 'on' ? 'Yes' : 'No') . "</td>";
        echo "<td>$delay_value</td>";
        echo "<td>$delay_unit</td>";
        echo "<td>$distribution</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>❌ No welcome email templates found.</p>";
}

// Test 2: Check current scheduled emails
echo "<h2>2. Current Scheduled Emails</h2>";
$scheduled_emails = get_option('mailpn_scheduled_welcome_emails', []);
echo "Total scheduled emails: " . count($scheduled_emails) . "<br>";

if (!empty($scheduled_emails)) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Email ID</th><th>User ID</th><th>Scheduled Time</th><th>Created Time</th><th>Status</th></tr>";
    
    foreach ($scheduled_emails as $scheduled_email) {
        $scheduled_time = date('Y-m-d H:i:s', $scheduled_email['scheduled_time']);
        $created_time = date('Y-m-d H:i:s', $scheduled_email['created_time']);
        $current_time = time();
        $status = ($scheduled_email['scheduled_time'] <= $current_time) ? 'Ready' : 'Scheduled';
        
        echo "<tr>";
        echo "<td>{$scheduled_email['email_id']}</td>";
        echo "<td>{$scheduled_email['user_id']}</td>";
        echo "<td>$scheduled_time</td>";
        echo "<td>$created_time</td>";
        echo "<td>$status</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No scheduled emails found.</p>";
}

// Test 3: Check recent logs
echo "<h2>3. Recent Sent Emails</h2>";
$scheduled_logs = get_option('mailpn_scheduled_welcome_logs', []);
echo "Total sent logs: " . count($scheduled_logs) . "<br>";

if (!empty($scheduled_logs)) {
    $recent_logs = array_slice($scheduled_logs, -5);
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Email ID</th><th>User ID</th><th>Scheduled Time</th><th>Sent Time</th></tr>";
    
    foreach ($recent_logs as $log) {
        $scheduled_time = date('Y-m-d H:i:s', $log['scheduled_time']);
        $sent_time = date('Y-m-d H:i:s', $log['sent_time']);
        
        echo "<tr>";
        echo "<td>{$log['email_id']}</td>";
        echo "<td>{$log['user_id']}</td>";
        echo "<td>$scheduled_time</td>";
        echo "<td>$sent_time</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No sent emails found.</p>";
}

// Test 4: Test delay calculation
echo "<h2>4. Delay Calculation Test</h2>";
$settings = new MAILPN_Settings();

$test_cases = [
    ['value' => 1, 'unit' => 'hours'],
    ['value' => 2, 'unit' => 'days'],
    ['value' => 1, 'unit' => 'weeks'],
    ['value' => 1, 'unit' => 'months'],
    ['value' => 1, 'unit' => 'years'],
];

echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Value</th><th>Unit</th><th>Seconds</th><th>Human Readable</th></tr>";

foreach ($test_cases as $test) {
    $seconds = $settings->mailpn_calculate_delay_seconds($test['value'], $test['unit']);
    $human_readable = '';
    
    if ($seconds >= YEAR_IN_SECONDS) {
        $human_readable = round($seconds / YEAR_IN_SECONDS, 1) . ' years';
    } elseif ($seconds >= MONTH_IN_SECONDS) {
        $human_readable = round($seconds / MONTH_IN_SECONDS, 1) . ' months';
    } elseif ($seconds >= WEEK_IN_SECONDS) {
        $human_readable = round($seconds / WEEK_IN_SECONDS, 1) . ' weeks';
    } elseif ($seconds >= DAY_IN_SECONDS) {
        $human_readable = round($seconds / DAY_IN_SECONDS, 1) . ' days';
    } elseif ($seconds >= HOUR_IN_SECONDS) {
        $human_readable = round($seconds / HOUR_IN_SECONDS, 1) . ' hours';
    } else {
        $human_readable = round($seconds / 60, 1) . ' minutes';
    }
    
    echo "<tr>";
    echo "<td>{$test['value']}</td>";
    echo "<td>{$test['unit']}</td>";
    echo "<td>$seconds</td>";
    echo "<td>$human_readable</td>";
    echo "</tr>";
}
echo "</table>";

// Test 5: Test scheduling a delayed email
echo "<h2>5. Test Scheduling Delayed Email</h2>";
echo "<form method='post'>";
echo "<input type='submit' name='test_schedule' value='Test Schedule Delayed Email' class='button button-primary'>";
echo "</form>";

if (isset($_POST['test_schedule'])) {
    echo "<h3>Testing Email Scheduling...</h3>";
    
    if (!empty($welcome_emails)) {
        $email_id = $welcome_emails[0]; // Use first welcome email
        $test_user_id = 1; // Use admin user for testing
        
        // Schedule for 5 minutes from now
        $scheduled_time = time() + (5 * 60);
        
        $scheduled_emails = get_option('mailpn_scheduled_welcome_emails', []);
        if (!is_array($scheduled_emails)) {
            $scheduled_emails = [];
        }
        
        $scheduled_emails[] = [
            'email_id' => $email_id,
            'user_id' => $test_user_id,
            'scheduled_time' => $scheduled_time,
            'created_time' => time()
        ];
        
        update_option('mailpn_scheduled_welcome_emails', $scheduled_emails);
        
        echo "✅ Test email scheduled for " . date('Y-m-d H:i:s', $scheduled_time) . "<br>";
        echo "Email ID: $email_id<br>";
        echo "User ID: $test_user_id<br>";
    } else {
        echo "❌ No welcome email templates available for testing.<br>";
    }
}

// Test 6: Test cron processing
echo "<h2>6. Test Cron Processing</h2>";
echo "<form method='post'>";
echo "<input type='submit' name='test_cron' value='Test Cron Processing' class='button button-secondary'>";
echo "</form>";

if (isset($_POST['test_cron'])) {
    echo "<h3>Testing Cron Processing...</h3>";
    
    $cron = new MAILPN_Cron();
    $cron->mailpn_process_scheduled_welcome_emails();
    
    echo "✅ Cron processing completed.<br>";
    echo "Check the scheduled emails list above to see if any were processed.<br>";
}

echo "<h2>7. System Status</h2>";
echo "WordPress Version: " . get_bloginfo('version') . "<br>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Current Time: " . date('Y-m-d H:i:s') . "<br>";
echo "Timezone: " . wp_timezone_string() . "<br>";

$next_cron = wp_next_scheduled('mailpn_cron_ten_minutes');
if ($next_cron) {
    echo "Next 10-minute cron: " . date('Y-m-d H:i:s', $next_cron) . "<br>";
} else {
    echo "❌ 10-minute cron not scheduled<br>";
}

echo "<br><a href='admin.php?page=mailpn-scheduled-welcome'>← Back to Scheduled Welcome Emails</a>";
?> 