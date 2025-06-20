<?php
/**
 * Debug scheduled welcome emails
 * 
 * This script will check the status of scheduled welcome emails
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    require_once('../../../wp-load.php');
}

// Check if user is admin
if (!current_user_can('manage_options')) {
    wp_die('Access denied');
}

echo "<h1>MAILPN Scheduled Welcome Emails Debug</h1>";
echo "<p>Generated on: " . date('Y-m-d H:i:s') . "</p>";

// Get scheduled emails
$scheduled_emails = get_option('mailpn_scheduled_welcome_emails', []);
$scheduled_logs = get_option('mailpn_scheduled_welcome_logs', []);

echo "<h2>1. Current Status</h2>";
echo "Scheduled Welcome Emails: " . count($scheduled_emails) . "<br>";
echo "Scheduled Welcome Logs: " . count($scheduled_logs) . "<br>";

// Show scheduled emails
if (!empty($scheduled_emails)) {
    echo "<h2>2. Scheduled Emails</h2>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Index</th><th>Email ID</th><th>User ID</th><th>Scheduled Time</th><th>Created Time</th><th>Status</th></tr>";
    
    foreach ($scheduled_emails as $index => $scheduled_email) {
        $email_post = get_post($scheduled_email['email_id']);
        $user = get_userdata($scheduled_email['user_id']);
        
        $email_title = $email_post ? $email_post->post_title : 'Unknown';
        $user_name = $user ? $user->display_name : 'Unknown';
        $scheduled_time = date('Y-m-d H:i:s', $scheduled_email['scheduled_time']);
        $created_time = date('Y-m-d H:i:s', $scheduled_email['created_time']);
        
        $current_time = time();
        $status = ($scheduled_email['scheduled_time'] <= $current_time) ? 'Ready to send' : 'Scheduled';
        
        echo "<tr>";
        echo "<td>$index</td>";
        echo "<td>{$scheduled_email['email_id']} ($email_title)</td>";
        echo "<td>{$scheduled_email['user_id']} ($user_name)</td>";
        echo "<td>$scheduled_time</td>";
        echo "<td>$created_time</td>";
        echo "<td>$status</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<h2>2. No Scheduled Emails</h2>";
    echo "<p>No scheduled welcome emails found.</p>";
}

// Show recent logs
if (!empty($scheduled_logs)) {
    echo "<h2>3. Recent Sent Emails (Last 10)</h2>";
    $recent_logs = array_slice($scheduled_logs, -10);
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Email ID</th><th>User ID</th><th>Scheduled Time</th><th>Sent Time</th></tr>";
    
    foreach ($recent_logs as $log) {
        $email_post = get_post($log['email_id']);
        $user = get_userdata($log['user_id']);
        
        $email_title = $email_post ? $email_post->post_title : 'Unknown';
        $user_name = $user ? $user->display_name : 'Unknown';
        $scheduled_time = date('Y-m-d H:i:s', $log['scheduled_time']);
        $sent_time = date('Y-m-d H:i:s', $log['sent_time']);
        
        echo "<tr>";
        echo "<td>{$log['email_id']} ($email_title)</td>";
        echo "<td>{$log['user_id']} ($user_name)</td>";
        echo "<td>$scheduled_time</td>";
        echo "<td>$sent_time</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<h2>3. No Sent Emails</h2>";
    echo "<p>No sent scheduled welcome emails found.</p>";
}

// Check welcome email templates
echo "<h2>4. Welcome Email Templates</h2>";
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
    echo "<p>No welcome email templates found.</p>";
}

// Test cron processing
echo "<h2>5. Test Cron Processing</h2>";
echo "<form method='post'>";
echo "<input type='submit' name='test_cron' value='Test Cron Processing' class='button button-primary'>";
echo "</form>";

if (isset($_POST['test_cron'])) {
    echo "<h3>Testing Cron Processing...</h3>";
    
    $cron = new MAILPN_Cron();
    $cron->mailpn_process_scheduled_welcome_emails();
    
    echo "<p>✅ Cron processing completed.</p>";
    
    // Refresh the page to show updated data
    echo "<script>setTimeout(function(){ window.location.reload(); }, 2000);</script>";
}

// Manual actions
echo "<h2>6. Manual Actions</h2>";
if (!empty($scheduled_emails)) {
    echo "<form method='post'>";
    echo "<select name='action_email_index'>";
    foreach ($scheduled_emails as $index => $scheduled_email) {
        $email_post = get_post($scheduled_email['email_id']);
        $user = get_userdata($scheduled_email['user_id']);
        $email_title = $email_post ? $email_post->post_title : 'Unknown';
        $user_name = $user ? $user->display_name : 'Unknown';
        echo "<option value='$index'>$index - $email_title to $user_name</option>";
    }
    echo "</select>";
    echo "<input type='submit' name='send_now' value='Send Now' class='button button-secondary'>";
    echo "<input type='submit' name='remove_scheduled' value='Remove' class='button button-secondary'>";
    echo "</form>";
}

// Handle manual actions
if (isset($_POST['send_now'])) {
    $index = intval($_POST['action_email_index']);
    if (isset($scheduled_emails[$index])) {
        echo "<h3>Sending Email Now...</h3>";
        
        $scheduled_email = $scheduled_emails[$index];
        $mailing = new MAILPN_Mailing();
        
        // Add to queue for immediate sending
        $result = $mailing->mailpn_queue_add($scheduled_email['email_id'], $scheduled_email['user_id']);
        
        if ($result) {
            echo "✅ Email added to queue for immediate sending<br>";
            // Remove from scheduled list
            unset($scheduled_emails[$index]);
            $scheduled_emails = array_values($scheduled_emails); // Reindex array
            update_option('mailpn_scheduled_welcome_emails', $scheduled_emails);
        } else {
            echo "❌ Failed to add email to queue<br>";
        }
    }
}

if (isset($_POST['remove_scheduled'])) {
    $index = intval($_POST['action_email_index']);
    if (isset($scheduled_emails[$index])) {
        echo "<h3>Removing Scheduled Email...</h3>";
        
        unset($scheduled_emails[$index]);
        $scheduled_emails = array_values($scheduled_emails); // Reindex array
        update_option('mailpn_scheduled_welcome_emails', $scheduled_emails);
        
        echo "✅ Scheduled email removed successfully<br>";
    }
}

echo "<h2>7. System Information</h2>";
echo "WordPress Version: " . get_bloginfo('version') . "<br>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Current Time: " . date('Y-m-d H:i:s') . "<br>";
echo "Timezone: " . wp_timezone_string() . "<br>";

// Check if cron is working
$next_cron = wp_next_scheduled('mailpn_cron_ten_minutes');
if ($next_cron) {
    echo "Next 10-minute cron: " . date('Y-m-d H:i:s', $next_cron) . "<br>";
} else {
    echo "❌ 10-minute cron not scheduled<br>";
}

echo "<br><a href='admin.php?page=mailpn-scheduled-welcome'>← Back to Scheduled Welcome Emails</a>";
?> 