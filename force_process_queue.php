<?php
/**
 * Force process MAILPN email queue
 * 
 * This script will force the processing of pending emails in the queue
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    require_once('../../../wp-load.php');
}

// Check if user is admin
if (!current_user_can('manage_options')) {
    wp_die('Access denied');
}

echo "<h1>MAILPN Force Queue Processing</h1>";
echo "<p>Generated on: " . date('Y-m-d H:i:s') . "</p>";

// Get current queue status
$mail_queue = get_option('mailpn_queue');
$mail_queue_paused = get_option('mailpn_queue_paused');

echo "<h2>Current Queue Status</h2>";
echo "Queue Items: " . (is_array($mail_queue) ? count($mail_queue) : 0) . "<br>";
echo "Queue Paused: " . ($mail_queue_paused ? 'Yes' : 'No') . "<br>";

if (is_array($mail_queue) && !empty($mail_queue)) {
    echo "<h3>Queue Contents:</h3>";
    echo "<ul>";
    foreach ($mail_queue as $mail_id => $users) {
        echo "<li>Mail ID: $mail_id - Users: " . count($users) . "</li>";
    }
    echo "</ul>";
}

// Process queue if requested
if (isset($_POST['process_queue'])) {
    echo "<h2>Processing Queue...</h2>";
    
    // Unpause queue if paused
    if ($mail_queue_paused) {
        delete_option('mailpn_queue_paused');
        delete_option('mailpn_mails_sent_today');
        echo "✅ Queue unpaused<br>";
    }
    
    // Force process queue
    $mailing = new MAILPN_Mailing();
    $result = $mailing->mailpn_queue_process();
    
    if ($result) {
        echo "✅ Queue processed successfully<br>";
    } else {
        echo "❌ Queue processing failed or no emails to process<br>";
    }
    
    // Show updated queue status
    $updated_queue = get_option('mailpn_queue');
    echo "<h3>Updated Queue Status:</h3>";
    echo "Remaining Items: " . (is_array($updated_queue) ? count($updated_queue) : 0) . "<br>";
    
    if (is_array($updated_queue) && !empty($updated_queue)) {
        echo "<h4>Remaining Queue:</h4>";
        echo "<ul>";
        foreach ($updated_queue as $mail_id => $users) {
            echo "<li>Mail ID: $mail_id - Users: " . count($users) . "</li>";
        }
        echo "</ul>";
    }
}

// Clear queue if requested
if (isset($_POST['clear_queue'])) {
    echo "<h2>Clearing Queue...</h2>";
    delete_option('mailpn_queue');
    delete_option('mailpn_queue_paused');
    delete_option('mailpn_mails_sent_today');
    echo "✅ Queue cleared successfully<br>";
}

// Reset daily limits if requested
if (isset($_POST['reset_limits'])) {
    echo "<h2>Resetting Daily Limits...</h2>";
    delete_option('mailpn_mails_sent_today');
    delete_option('mailpn_queue_paused');
    echo "✅ Daily limits reset successfully<br>";
}

// Show action buttons
echo "<h2>Actions</h2>";
echo "<form method='post' style='margin: 10px 0;'>";
echo "<input type='submit' name='process_queue' value='Process Queue' style='margin: 5px; padding: 10px; background: #0073aa; color: white; border: none; cursor: pointer;'>";
echo "<input type='submit' name='clear_queue' value='Clear Queue' style='margin: 5px; padding: 10px; background: #dc3232; color: white; border: none; cursor: pointer;' onclick='return confirm(\"Are you sure you want to clear the queue?\")'>";
echo "<input type='submit' name='reset_limits' value='Reset Daily Limits' style='margin: 5px; padding: 10px; background: #46b450; color: white; border: none; cursor: pointer;'>";
echo "</form>";

// Show recent mail records
echo "<h2>Recent Mail Records</h2>";
$recent_records = get_posts([
    'post_type' => 'mailpn_rec',
    'post_status' => 'publish',
    'numberposts' => 10,
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
        
        echo "<tr>";
        echo "<td>" . $record->ID . "</td>";
        echo "<td>" . esc_html($subject) . "</td>";
        echo "<td>" . esc_html($to_email) . "</td>";
        echo "<td>" . esc_html($type) . "</td>";
        echo "<td>" . ($result ? '✅ Success' : '❌ Failed') . "</td>";
        echo "<td>" . $record->post_date . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No mail records found<br>";
}

// Show scheduled welcome emails
echo "<h2>Scheduled Welcome Emails</h2>";
$scheduled_emails = get_option('mailpn_scheduled_welcome_emails', []);
if (!empty($scheduled_emails)) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Email ID</th><th>User ID</th><th>Scheduled Time</th><th>Created Time</th></tr>";
    foreach ($scheduled_emails as $scheduled_email) {
        echo "<tr>";
        echo "<td>" . $scheduled_email['email_id'] . "</td>";
        echo "<td>" . $scheduled_email['user_id'] . "</td>";
        echo "<td>" . date('Y-m-d H:i:s', $scheduled_email['scheduled_time']) . "</td>";
        echo "<td>" . date('Y-m-d H:i:s', $scheduled_email['created_time']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No scheduled welcome emails found<br>";
}

// Show pending welcome registrations
echo "<h2>Pending Welcome Registrations</h2>";
$pending_registrations = get_option('mailpn_pending_welcome_registrations', []);
if (!empty($pending_registrations)) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>User ID</th><th>Email</th><th>Registration Time</th><th>Welcome Email ID</th></tr>";
    foreach ($pending_registrations as $registration) {
        $user = get_userdata($registration['user_id']);
        echo "<tr>";
        echo "<td>" . $registration['user_id'] . "</td>";
        echo "<td>" . ($user ? $user->user_email : 'User not found') . "</td>";
        echo "<td>" . date('Y-m-d H:i:s', $registration['registration_time']) . "</td>";
        echo "<td>" . $registration['welcome_email_id'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No pending welcome registrations found<br>";
}

echo "<hr>";
echo "<p><strong>Note:</strong> Use 'Process Queue' to manually trigger email sending. Use 'Clear Queue' to remove all pending emails. Use 'Reset Daily Limits' to reset the daily sending limits.</p>";
?> 