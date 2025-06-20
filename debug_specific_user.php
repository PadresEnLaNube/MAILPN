<?php
/**
 * Debug specific user email status
 * 
 * This script will check and process emails for a specific user
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    require_once('../../../wp-load.php');
}

// Check if user is admin
if (!current_user_can('manage_options')) {
    wp_die('Access denied');
}

echo "<h1>MAILPN Specific User Debug</h1>";
echo "<p>Generated on: " . date('Y-m-d H:i:s') . "</p>";

// Process user ID from form
$user_id = null;
$user = null;

if (isset($_POST['user_id']) && !empty($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);
    $user = get_userdata($user_id);
}

// Show user input form
echo "<h2>1. Select User</h2>";
echo "<form method='post'>";
echo "<input type='number' name='user_id' placeholder='Enter User ID' value='" . ($user_id ?: '') . "' required>";
echo "<input type='submit' value='Check User' style='margin: 5px; padding: 10px; background: #0073aa; color: white; border: none; cursor: pointer;'>";
echo "</form>";

if ($user) {
    echo "<h2>2. User Information</h2>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Field</th><th>Value</th></tr>";
    echo "<tr><td>User ID</td><td>" . $user->ID . "</td></tr>";
    echo "<tr><td>Username</td><td>" . $user->user_login . "</td></tr>";
    echo "<tr><td>Email</td><td>" . $user->user_email . "</td></tr>";
    echo "<tr><td>Display Name</td><td>" . $user->display_name . "</td></tr>";
    echo "<tr><td>First Name</td><td>" . $user->first_name . "</td></tr>";
    echo "<tr><td>Last Name</td><td>" . $user->last_name . "</td></tr>";
    echo "<tr><td>Roles</td><td>" . implode(', ', $user->roles) . "</td></tr>";
    echo "<tr><td>Registration Date</td><td>" . $user->user_registered . "</td></tr>";
    echo "<tr><td>Notifications Enabled</td><td>" . (get_user_meta($user->ID, 'userspn_notifications', true) === 'on' ? 'Yes' : 'No') . "</td></tr>";
    echo "</table>";

    // Check if user is in pending registrations
    echo "<h2>3. Pending Registration Status</h2>";
    $pending_registrations = get_option('mailpn_pending_welcome_registrations', []);
    $user_pending = null;
    
    foreach ($pending_registrations as $registration) {
        if ($registration['user_id'] == $user_id) {
            $user_pending = $registration;
            break;
        }
    }
    
    if ($user_pending) {
        echo "<div style='background: #fff3cd; padding: 10px; border: 1px solid #ffeaa7;'>";
        echo "<strong>⚠️ User is in pending registrations</strong><br>";
        echo "Registration Time: " . date('Y-m-d H:i:s', $user_pending['registration_time']) . "<br>";
        echo "Welcome Email ID: " . $user_pending['welcome_email_id'] . "<br>";
        echo "Processed: " . ($user_pending['processed'] ? 'Yes' : 'No') . "<br>";
        
        echo "<form method='post'>";
        echo "<input type='hidden' name='user_id' value='" . $user_id . "'>";
        echo "<input type='submit' name='process_user' value='Process This User' style='margin: 5px; padding: 10px; background: #0073aa; color: white; border: none; cursor: pointer;'>";
        echo "<input type='submit' name='remove_user_pending' value='Remove from Pending' style='margin: 5px; padding: 10px; background: #dc3232; color: white; border: none; cursor: pointer;' onclick='return confirm(\"Remove this user from pending registrations?\")'>";
        echo "</form>";
        echo "</div>";
    } else {
        echo "<div style='background: #d4edda; padding: 10px; border: 1px solid #c3e6cb;'>";
        echo "<strong>✅ User is not in pending registrations</strong>";
        echo "</div>";
    }

    // Check if user has scheduled emails
    echo "<h2>4. Scheduled Email Status</h2>";
    $scheduled_emails = get_option('mailpn_scheduled_welcome_emails', []);
    $user_scheduled = [];
    
    foreach ($scheduled_emails as $index => $scheduled_email) {
        if ($scheduled_email['user_id'] == $user_id) {
            $user_scheduled[] = ['index' => $index, 'data' => $scheduled_email];
        }
    }
    
    if (!empty($user_scheduled)) {
        echo "<div style='background: #fff3cd; padding: 10px; border: 1px solid #ffeaa7;'>";
        echo "<strong>⚠️ User has scheduled emails</strong><br>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin-top: 10px;'>";
        echo "<tr><th>Email ID</th><th>Scheduled Time</th><th>Created Time</th><th>Actions</th></tr>";
        
        foreach ($user_scheduled as $scheduled) {
            echo "<tr>";
            echo "<td>" . $scheduled['data']['email_id'] . "</td>";
            echo "<td>" . date('Y-m-d H:i:s', $scheduled['data']['scheduled_time']) . "</td>";
            echo "<td>" . date('Y-m-d H:i:s', $scheduled['data']['created_time']) . "</td>";
            echo "<td>";
            echo "<form method='post' style='display: inline;'>";
            echo "<input type='hidden' name='user_id' value='" . $user_id . "'>";
            echo "<input type='hidden' name='send_scheduled' value='" . $scheduled['index'] . "'>";
            echo "<input type='submit' value='Send Now' style='margin: 2px; padding: 5px; background: #46b450; color: white; border: none; cursor: pointer;'>";
            echo "</form>";
            echo "<form method='post' style='display: inline;'>";
            echo "<input type='hidden' name='user_id' value='" . $user_id . "'>";
            echo "<input type='hidden' name='remove_scheduled' value='" . $scheduled['index'] . "'>";
            echo "<input type='submit' value='Remove' style='margin: 2px; padding: 5px; background: #dc3232; color: white; border: none; cursor: pointer;' onclick='return confirm(\"Remove this scheduled email?\")'>";
            echo "</form>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "</div>";
    } else {
        echo "<div style='background: #d4edda; padding: 10px; border: 1px solid #c3e6cb;'>";
        echo "<strong>✅ User has no scheduled emails</strong>";
        echo "</div>";
    }

    // Check user's email records
    echo "<h2>5. Email Records for This User</h2>";
    $user_email_records = get_posts([
        'post_type' => 'mailpn_rec',
        'post_status' => 'publish',
        'numberposts' => 10,
        'orderby' => 'ID',
        'order' => 'DESC',
        'meta_query' => [
            [
                'key' => 'mailpn_rec_to',
                'value' => $user_id,
                'compare' => '='
            ]
        ]
    ]);

    if (!empty($user_email_records)) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Subject</th><th>Type</th><th>Result</th><th>Date</th></tr>";
        foreach ($user_email_records as $record) {
            $subject = get_post_meta($record->ID, 'mailpn_rec_subject', true);
            $type = get_post_meta($record->ID, 'mailpn_rec_type', true);
            $result = get_post_meta($record->ID, 'mailpn_rec_mail_result', true);
            
            echo "<tr>";
            echo "<td>" . $record->ID . "</td>";
            echo "<td>" . esc_html($subject) . "</td>";
            echo "<td>" . esc_html($type) . "</td>";
            echo "<td>" . ($result ? '✅ Success' : '❌ Failed') . "</td>";
            echo "<td>" . $record->post_date . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No email records found for this user.</p>";
    }

    // Check welcome email templates
    echo "<h2>6. Welcome Email Templates</h2>";
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
        echo "<tr><th>ID</th><th>Title</th><th>Distribution</th><th>Delay</th><th>Actions</th></tr>";
        
        foreach ($welcome_emails as $email_id) {
            $email_post = get_post($email_id);
            $distribution = get_post_meta($email_id, 'mailpn_distribution', true);
            $delay_enabled = get_post_meta($email_id, 'mailpn_welcome_delay_enabled', true);
            $delay_value = get_post_meta($email_id, 'mailpn_welcome_delay_value', true);
            $delay_unit = get_post_meta($email_id, 'mailpn_welcome_delay_unit', true);
            
            $delay_text = 'No delay';
            if ($delay_enabled === 'on' && !empty($delay_value) && !empty($delay_unit)) {
                $delay_text = $delay_value . ' ' . $delay_unit;
            }
            
            echo "<tr>";
            echo "<td>" . $email_id . "</td>";
            echo "<td>" . $email_post->post_title . "</td>";
            echo "<td>" . $distribution . "</td>";
            echo "<td>" . $delay_text . "</td>";
            echo "<td>";
            echo "<form method='post' style='display: inline;'>";
            echo "<input type='hidden' name='user_id' value='" . $user_id . "'>";
            echo "<input type='hidden' name='send_welcome' value='" . $email_id . "'>";
            echo "<input type='submit' value='Send Now' style='margin: 2px; padding: 5px; background: #46b450; color: white; border: none; cursor: pointer;'>";
            echo "</form>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No welcome email templates found.</p>";
    }

    // Process actions
    if (isset($_POST['process_user'])) {
        echo "<h2>7. Processing User</h2>";
        
        $settings = new MAILPN_Settings();
        $result = $settings->mailpn_trigger_welcome_emails($user_id);
        
        if ($result) {
            echo "✅ Welcome emails triggered successfully<br>";
        } else {
            echo "❌ No welcome emails were triggered<br>";
        }
    }

    if (isset($_POST['remove_user_pending'])) {
        echo "<h2>7. Removing User from Pending</h2>";
        
        $pending_registrations = get_option('mailpn_pending_welcome_registrations', []);
        $updated_pending = [];
        
        foreach ($pending_registrations as $registration) {
            if ($registration['user_id'] != $user_id) {
                $updated_pending[] = $registration;
            }
        }
        
        update_option('mailpn_pending_welcome_registrations', $updated_pending);
        echo "✅ User removed from pending registrations<br>";
    }

    if (isset($_POST['send_scheduled'])) {
        $index = intval($_POST['send_scheduled']);
        echo "<h2>7. Sending Scheduled Email</h2>";
        
        $scheduled_emails = get_option('mailpn_scheduled_welcome_emails', []);
        if (isset($scheduled_emails[$index])) {
            $scheduled_email = $scheduled_emails[$index];
            $mailing = new MAILPN_Mailing();
            
            $result = $mailing->mailpn_queue_add($scheduled_email['email_id'], $scheduled_email['user_id']);
            
            if ($result) {
                echo "✅ Email added to queue for immediate sending<br>";
                unset($scheduled_emails[$index]);
                $scheduled_emails = array_values($scheduled_emails);
                update_option('mailpn_scheduled_welcome_emails', $scheduled_emails);
            } else {
                echo "❌ Failed to add email to queue<br>";
            }
        }
    }

    if (isset($_POST['remove_scheduled'])) {
        $index = intval($_POST['remove_scheduled']);
        echo "<h2>7. Removing Scheduled Email</h2>";
        
        $scheduled_emails = get_option('mailpn_scheduled_welcome_emails', []);
        if (isset($scheduled_emails[$index])) {
            unset($scheduled_emails[$index]);
            $scheduled_emails = array_values($scheduled_emails);
            update_option('mailpn_scheduled_welcome_emails', $scheduled_emails);
            echo "✅ Scheduled email removed<br>";
        }
    }

    if (isset($_POST['send_welcome'])) {
        $email_id = intval($_POST['send_welcome']);
        echo "<h2>7. Sending Welcome Email</h2>";
        
        $mailing = new MAILPN_Mailing();
        $result = $mailing->mailpn_queue_add($email_id, $user_id);
        
        if ($result) {
            echo "✅ Welcome email added to queue for immediate sending<br>";
        } else {
            echo "❌ Failed to add welcome email to queue<br>";
        }
    }
}

echo "<hr>";
echo "<p><strong>Note:</strong> Use this tool to debug and manually process emails for a specific user.</p>";
?> 