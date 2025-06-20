<?php
/**
 * Debug pending welcome registrations
 * 
 * This script will check and process pending welcome registrations
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    require_once('../../../wp-load.php');
}

// Check if user is admin
if (!current_user_can('manage_options')) {
    wp_die('Access denied');
}

echo "<h1>MAILPN Pending Welcome Registrations Debug</h1>";
echo "<p>Generated on: " . date('Y-m-d H:i:s') . "</p>";

// Get pending registrations
$pending_registrations = get_option('mailpn_pending_welcome_registrations', []);
$scheduled_emails = get_option('mailpn_scheduled_welcome_emails', []);

echo "<h2>1. Current Status</h2>";
echo "Pending Registrations: " . count($pending_registrations) . "<br>";
echo "Scheduled Welcome Emails: " . count($scheduled_emails) . "<br>";

// Show pending registrations
if (!empty($pending_registrations)) {
    echo "<h2>2. Pending Registrations</h2>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>User ID</th><th>Email</th><th>Registration Time</th><th>Welcome Email ID</th><th>Actions</th></tr>";
    
    foreach ($pending_registrations as $index => $registration) {
        $user = get_userdata($registration['user_id']);
        echo "<tr>";
        echo "<td>" . $registration['user_id'] . "</td>";
        echo "<td>" . ($user ? $user->user_email : 'User not found') . "</td>";
        echo "<td>" . date('Y-m-d H:i:s', $registration['registration_time']) . "</td>";
        echo "<td>" . $registration['welcome_email_id'] . "</td>";
        echo "<td>";
        echo "<form method='post' style='display: inline;'>";
        echo "<input type='hidden' name='process_registration' value='" . $index . "'>";
        echo "<input type='submit' value='Process Now' style='margin: 2px; padding: 5px; background: #0073aa; color: white; border: none; cursor: pointer;'>";
        echo "</form>";
        echo "<form method='post' style='display: inline;'>";
        echo "<input type='hidden' name='remove_registration' value='" . $index . "'>";
        echo "<input type='submit' value='Remove' style='margin: 2px; padding: 5px; background: #dc3232; color: white; border: none; cursor: pointer;' onclick='return confirm(\"Remove this registration?\")'>";
        echo "</form>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No pending registrations found.</p>";
}

// Show scheduled emails
if (!empty($scheduled_emails)) {
    echo "<h2>3. Scheduled Welcome Emails</h2>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Email ID</th><th>User ID</th><th>Scheduled Time</th><th>Created Time</th><th>Actions</th></tr>";
    
    foreach ($scheduled_emails as $index => $scheduled_email) {
        echo "<tr>";
        echo "<td>" . $scheduled_email['email_id'] . "</td>";
        echo "<td>" . $scheduled_email['user_id'] . "</td>";
        echo "<td>" . date('Y-m-d H:i:s', $scheduled_email['scheduled_time']) . "</td>";
        echo "<td>" . date('Y-m-d H:i:s', $scheduled_email['created_time']) . "</td>";
        echo "<td>";
        echo "<form method='post' style='display: inline;'>";
        echo "<input type='hidden' name='send_now' value='" . $index . "'>";
        echo "<input type='submit' value='Send Now' style='margin: 2px; padding: 5px; background: #46b450; color: white; border: none; cursor: pointer;'>";
        echo "</form>";
        echo "<form method='post' style='display: inline;'>";
        echo "<input type='hidden' name='remove_scheduled' value='" . $index . "'>";
        echo "<input type='submit' value='Remove' style='margin: 2px; padding: 5px; background: #dc3232; color: white; border: none; cursor: pointer;' onclick='return confirm(\"Remove this scheduled email?\")'>";
        echo "</form>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No scheduled welcome emails found.</p>";
}

// Process specific registration
if (isset($_POST['process_registration'])) {
    $index = intval($_POST['process_registration']);
    if (isset($pending_registrations[$index])) {
        echo "<h2>4. Processing Registration</h2>";
        
        $registration = $pending_registrations[$index];
        $settings = new MAILPN_Settings();
        
        // Process this specific registration
        $result = $settings->mailpn_process_specific_welcome_registration($registration);
        
        if ($result) {
            echo "✅ Registration processed successfully<br>";
            // Remove from pending list
            unset($pending_registrations[$index]);
            $pending_registrations = array_values($pending_registrations); // Reindex array
            update_option('mailpn_pending_welcome_registrations', $pending_registrations);
        } else {
            echo "❌ Failed to process registration<br>";
        }
    }
}

// Remove specific registration
if (isset($_POST['remove_registration'])) {
    $index = intval($_POST['remove_registration']);
    if (isset($pending_registrations[$index])) {
        echo "<h2>4. Removing Registration</h2>";
        
        unset($pending_registrations[$index]);
        $pending_registrations = array_values($pending_registrations); // Reindex array
        update_option('mailpn_pending_welcome_registrations', $pending_registrations);
        
        echo "✅ Registration removed successfully<br>";
    }
}

// Send scheduled email now
if (isset($_POST['send_now'])) {
    $index = intval($_POST['send_now']);
    if (isset($scheduled_emails[$index])) {
        echo "<h2>4. Sending Email Now</h2>";
        
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

// Remove scheduled email
if (isset($_POST['remove_scheduled'])) {
    $index = intval($_POST['remove_scheduled']);
    if (isset($scheduled_emails[$index])) {
        echo "<h2>4. Removing Scheduled Email</h2>";
        
        unset($scheduled_emails[$index]);
        $scheduled_emails = array_values($scheduled_emails); // Reindex array
        update_option('mailpn_scheduled_welcome_emails', $scheduled_emails);
        
        echo "✅ Scheduled email removed successfully<br>";
    }
}

// Process all pending registrations
if (isset($_POST['process_all'])) {
    echo "<h2>4. Processing All Pending Registrations</h2>";
    
    $settings = new MAILPN_Settings();
    $processed_count = 0;
    
    foreach ($pending_registrations as $registration) {
        $result = $settings->mailpn_process_specific_welcome_registration($registration);
        if ($result) {
            $processed_count++;
        }
    }
    
    echo "✅ Processed $processed_count registrations<br>";
    
    // Clear pending registrations
    update_option('mailpn_pending_welcome_registrations', []);
}

// Clear all data
if (isset($_POST['clear_all'])) {
    echo "<h2>4. Clearing All Data</h2>";
    
    update_option('mailpn_pending_welcome_registrations', []);
    update_option('mailpn_scheduled_welcome_emails', []);
    
    echo "✅ All pending registrations and scheduled emails cleared<br>";
}

// Show action buttons
echo "<h2>5. Actions</h2>";
echo "<form method='post' style='margin: 10px 0;'>";
echo "<input type='submit' name='process_all' value='Process All Pending' style='margin: 5px; padding: 10px; background: #0073aa; color: white; border: none; cursor: pointer;'>";
echo "<input type='submit' name='clear_all' value='Clear All Data' style='margin: 5px; padding: 10px; background: #dc3232; color: white; border: none; cursor: pointer;' onclick='return confirm(\"Are you sure you want to clear all data?\")'>";
echo "</form>";

// Show welcome email settings
echo "<h2>6. Welcome Email Settings</h2>";
$welcome_email_enabled = get_option('mailpn_welcome_email_enabled');
$welcome_email_delay = get_option('mailpn_welcome_email_delay', 0);
$welcome_email_id = get_option('mailpn_welcome_email_id');

echo "Welcome Email Enabled: " . ($welcome_email_enabled === 'on' ? 'Yes' : 'No') . "<br>";
echo "Welcome Email Delay: " . $welcome_email_delay . " minutes<br>";
echo "Welcome Email ID: " . ($welcome_email_id ?: 'Not set') . "<br>";

// Show recent mail records for welcome emails
echo "<h2>7. Recent Welcome Email Records</h2>";
$recent_welcome_records = get_posts([
    'post_type' => 'mailpn_rec',
    'post_status' => 'publish',
    'numberposts' => 10,
    'orderby' => 'ID',
    'order' => 'DESC',
    'meta_query' => [
        [
            'key' => 'mailpn_rec_type',
            'value' => 'email_welcome',
            'compare' => '='
        ]
    ]
]);

if (!empty($recent_welcome_records)) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Subject</th><th>To</th><th>Result</th><th>Date</th></tr>";
    foreach ($recent_welcome_records as $record) {
        $subject = get_post_meta($record->ID, 'mailpn_rec_subject', true);
        $to_email = get_post_meta($record->ID, 'mailpn_rec_to_email', true);
        $result = get_post_meta($record->ID, 'mailpn_rec_mail_result', true);
        
        echo "<tr>";
        echo "<td>" . $record->ID . "</td>";
        echo "<td>" . esc_html($subject) . "</td>";
        echo "<td>" . esc_html($to_email) . "</td>";
        echo "<td>" . ($result ? '✅ Success' : '❌ Failed') . "</td>";
        echo "<td>" . $record->post_date . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No welcome email records found<br>";
}

echo "<hr>";
echo "<p><strong>Note:</strong> Use this tool to debug and manually process pending welcome registrations.</p>";
?> 